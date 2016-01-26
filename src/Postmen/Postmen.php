<?php

namespace Postmen;

require('PostmenException.php');

use Postmen\PostmenException;

/**
 * Class Handler
 *
 * @package Postmen
 */
class Postmen
{
	private $_api_key;
	private $_url;
	private $_version;
	private $_error;
	private $_proxy;
	private $_array;

	// auto-retry if retryable attributes
	private $_retry;
	private $_delay;
	private $_retries;
	private $_max_retries;

	// rate limiting attributes
	private $_rate;

	public function __construct($api_key, $region, $config = array())
	{
		// set all the context attributes
		if (!isset($api_key)) {
			throw new PostmenException('API key is required', 999, false);
		}
		$this->_version = "0.5.0";
		$this->_api_key = $api_key;
		if (isset($config['proxy'])) {
			$this->_proxy = $config['proxy'];
		}
		if (isset($config['endpoint'])) {
			$this->_url = $config['endpoint'];
		} else if (!isset($region)) {
			throw new PostmenException('missing required field', 999, false);
		} else {
			$this->_url = "https://$region-api.postmen.com";
		}
		$this->_retry = true;
		if (isset($config['retry'])) {
			$this->_retry = $config['retry'];
		}
		$this->_rate = true;
		if (isset($config['rate'])) {
			$this->_rate = $config['rate'];
		}
		$this->_array = false;
		if (isset($config['array'])) {
			$this->_array = $config['array'];
		}
		// set attributes concerning ratelimiting and auto-retry
		$this->_delay = 1;
		$this->_retries = 0;
		$this->_max_retries = 5;
		$this->_calls_left = NULL;
	}

	public function buildCurlParams($method, $path, $parameters = array()) {
		$body = '';
		if (isset($parameters['body'])) {
			$body = $parameters['body'];
		}
		if (!is_string($body)) {
			$body = json_encode($body);
		}
		$headers = array(
			"content-type: application/json",
			"postmen-api-key: $this->_api_key",
			"x-postmen-agent: php-sdk-$this->_version"
		);
		$query = NULL;
		if (isset($parameters['query'])) {
			$query = $parameters['query'];
		}
		$url = $this->generateURL($this->_url, $path, $method, $query);
		$curl_params = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_HEADER => true	
		);
		$proxy = $this->_proxy;
		if (isset($parameters['proxy'])) {
			$proxy = $parameters['proxy'];
		}
		if (isset($proxy)) {
			$curl_params[CURLOPT_PROXY] = $proxy['host'];
			if (isset($proxy['username'])) {
				$auth = $proxy['username'] . ':' . $proxy['password'];
				$curl_params[CURLOPT_PROXYUSERPWD] = $auth;
			}
			if (isset($proxy['port'])) {
				$curl_params[CURLOPT_PROXYPORT] = $proxy['port'];
			}
			$curl_params[CURLOPT_FOLLOWLOCATION] = true;
		}
		if ($method != 'GET') {
			$curl_params[CURLOPT_POSTFIELDS] = $body;
		}
		return $curl_params;
	}

	public function call($method, $path, $parameters = array()) {
		$this->_retries += 1;
		$retry = $this->_retry;
		if (!isset($retry)) {
			if (isset($parameters['retry'])) {
				if($parameters['retry']) {
					$retry = true;
				}
			} else {
				$retry = false;
			}
		}
		$raw = false;
		if(isset($parameters['raw'])) {
			if($parameters['raw']) {
				$raw = true;
			}
		}
		$safe = false;
		if (isset($parameters['safe'])) {
			$safe = $parameters['safe'];
		}
		$curl = curl_init();
		$curl_params = $this->buildCurlParams($method, $path, $parameters);
		curl_setopt_array($curl, $curl_params);
		// make call
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		curl_close($curl);
		// convert headers string to an array
		$response_headers = substr($response, 0, $header_size);
		$response_body = substr($response, $header_size);
		$response_headers_array = array();
		foreach (explode("\r\n", $response_headers) as $line) {
			list($key, $value) = array_pad(explode(': ', $line, 2), 2, null);
			$response_headers_array[$key] = $value;
		}
		$headers_date = $response_headers_array['Date'];
		$calls_left = 0;
		if (isset($response_headers_array['X-RateLimit-Remaining'])) {
			$calls_left = (int)$response_headers_array['X-RateLimit-Remaining'];
		}
		$reset = 0;
		if (isset($response_headers_array['X-RateLimit-Reset'])) {
			$reset = (int)(((int)$response_headers_array['X-RateLimit-Reset']) / 1000);
		}
		// convert headers date to timestamp, please refer to
		// https://tools.ietf.org/html/rfc7231#section-7.1.1.1
		$date = new \DateTime($headers_date, new \DateTimeZone('GMT'));
		$now = (int) $date->format('U');
		// process the response
		$call = array(
			'retry' => $retry,
			'method' => $method,
			'path' => $path,
			'parameters' => $parameters,
			// options for automatic rate limiting
			'now' => $now,
			'reset' => $reset
		);
		if ($err) {
			$error = new PostmenException("failed to request: $err" , 100, true, array());
			if ($safe) {
				$this->_error = $error;
				return undefined;
			} else {
				throw $error;
			}
		}
		return $this->processCurlResponse($response_body, $safe, $raw, $call);
	}

	public function processCurlResponse($response, $safe, $raw, $call) {
		$parsed = json_decode($response);
		if ($parsed) {
			if($raw) {
				return $response;
			}
			return $this->handle($parsed, $safe, $call);
		} else {
			$err_message = 'Something went wrong on Postmen\'s end';
			$err_code = 500;
			$err_retryable = false;
			$err_details = array();
			return $this->handleError($err_message, $err_code, $err_retryable, $err_details, $safe);
		}

	}

	public function handleError($err_message, $err_code, $err_retryable, $err_details, $safe) {
		$error = new PostmenException($err_message, $err_code, $err_retryable, $err_details);
		if ($safe) {
			$this->_error = $error;
		} else {
			throw $error;
		}
		return NULL;
	}

	public function handle($parsed, $safe, $call) {
		if ($parsed->meta->code != 200) {
			$err_code = 0; 
			$err_message = 'Postmen server side error occured';
			$err_details = array();
			$err_retryable = false;
			if (isset($parsed->meta->code)) {
				$err_code = $parsed->meta->code;
			}
			if (isset($parsed->meta->message)) {
				$err_message = $parsed->meta->message;
			}
			if (isset($parsed->meta->details)) {
				$err_details = $parsed->meta->details;
			}
			if (isset($parsed->meta->retryable)) {
				$err_retryable = $parsed->meta->retryable;
			}
			// apply rate limiting if error 429 occurs
			if ($this->_rate && $err_code === 429) {
				$delay = $call['reset'] - $call['now'];
				if ($delay > 0) {
					sleep($delay);
				}
				return $this->call($call['method'], $call['path'], $call['parameters']);
			}
			// apply automatic retry if error is retry-able
			if ($call['retry'] && $err_retryable) {
				$retried = $this->handleRetry($call);
				if ($retried !== NULL) {
					return $retried;
				}
			}
			return $this->handleError($err_message, $err_code, $err_retryable, $err_details, $safe);
		} else {
			if ($this->_array) {
				$parsed_array = json_decode(json_encode($parsed), true);
				return $parsed_array['data'];
			} else {
				return $parsed->data;
			}
		}
	}

	public function generateURL($url, $path, $method, $query) {
		if ($method == 'GET') {
			if (isset($query)) {
				return $url . $path . '?' . http_build_query($query);
			}	
		}
		return $url . $path;
	}

	public function handleRetry($call) {
		if ($this->_retries < $this->_max_retries) {
			sleep($this->_delay);
			$this->_delay = $this->_delay * 2;
			return $this->call($call['method'], $call['path'], $call['parameters']);
		} else {
			$this->_retries = 0;
			$this->_delay = 1;
			return NULL;
		}
	}

	public function callGET($path, $parameters = array()) {
		return $this->call('GET', $path, $parameters);
	}

	public function callPOST($path, $body, $parameters = array()) {
		$parameters['body'] = $body;
		return $this->call('POST', $path, $parameters);
	}

	public function callPUT($path, $body, $parameters = array()) {
		$parameters['body'] = $body;
		return $this->call('PUT', $path, $parameters);
	}

	public function callDELETE($path, $body, $parameters = array()) {
		$parameters['body'] = $body;
		return $this->call('DELETE', $path, $parameters);
	}

	public function get($resource, $id = NULL, $parameters = array()) {
		if ($id !== NULL) {
			return $this->callGET("/v3/$resource/$id", $parameters);
		} else {
			return $this->callGET("/v3/$resource", $parameters);
		}
	}

	public function create($resource, $payload, $parameters = array()) {
		if (!is_string($payload)) {
			$payload['async'] = false;
		}
		return $this->callPOST("/v3/$resource", $payload, $parameters);
	}

	public function cancelLabel($id, $parameters = array()) {
		$payload = array (
			'label' => array (
				'id' => $id
			),
			'async' => false
		);
		return $this->callPOST("/v3/cancel-labels", $payload, $parameters);
	}

	public function getError() {
		return $this->_error;
	}
}
?>
