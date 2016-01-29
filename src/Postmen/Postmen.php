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
	private $_version;
	private $_error;

	private $_config;

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
		$this->_version = "0.6.0";
		$this->_api_key = $api_key;
		$this->_config = array();
		$this->_config['endpoint'] = "https://$region-api.postmen.com";
		$this->_config['retry'] = true;
		$this->_config['rate'] = true;
		$this->_config['array'] = false;
		$this->_config['raw'] = false;
		$this->_config['safe'] = false;
		$this->_config['proxy'] = array();
		$this->_config = $this->mergeArray($config);
		// set attributes concerning ratelimiting and auto-retry
		$this->_delay = 1;
		$this->_retries = 0;
		$this->_max_retries = 5;
		$this->_calls_left = NULL;
	}

	public function buildCurlParams($method, $path, $config = array()) {
		$parameters = $this->mergeArray($config);
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
		$url = $this->generateURL($parameters['endpoint'], $path, $method, $query);
		$curl_params = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_HEADER => true	
		);
		$proxy = $parameters['proxy'];
		if (count($proxy) > 0) {
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

	public function call($method, $path, $config = array()) {
		$this->_retries += 1;
		$parameters = $this->mergeArray($config);
		if (!isset($method)) {
			$method = $parameters['method'];
		} else {
			$parameters['method'] = $method;
		}
		if (!isset($path)) {
			$path = $parameters['path'];
		} else {
			$parameters['path'] = $path;
		}
		$retry = $parameters['retry'];
		$raw = $parameters['raw'];
		$safe = $parameters['safe'];
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
		$parameters['now'] = $now;
		$parameters['reset'] = $reset;
		if ($err) {
			$error = new PostmenException("failed to request: $err" , 100, true, array());
			if ($safe) {
				$this->_error = $error;
				return undefined;
			} else {
				throw $error;
			}
		}
		return $this->processCurlResponse($response_body, $parameters);
	}

	public function processCurlResponse($response, $parameters) {
		$parsed = json_decode($response);
		if ($parsed) {
			if($parameters['raw']) {
				return $response;
			}
			return $this->handle($parsed, $parameters);
		} else {
			$err_message = 'Something went wrong on Postmen\'s end';
			$err_code = 500;
			$err_retryable = false;
			$err_details = array();
			return $this->handleError($err_message, $err_code, $err_retryable, $err_details, $parameters);
		}
	}

	public function handleError($err_message, $err_code, $err_retryable, $err_details, $parameters) {
		$error = new PostmenException($err_message, $err_code, $err_retryable, $err_details);
		if ($parameters['safe']) {
			$this->_error = $error;
		} else {
			throw $error;
		}
		return NULL;
	}

	public function handle($parsed, $parameters) {
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
			if ($parameters['rate'] && $err_code === 429) {
				$delay = $parameters['reset'] - $parameters['now'];
				if ($delay > 0) {
					sleep($delay);
				}
				return $this->call(NULL, NULL, $parameters);
			}
			// apply automatic retry if error is retry-able
			if ($parameters['retry'] && $err_retryable) {
				$retried = $this->handleRetry($parameters);
				if ($retried !== NULL) {
					return $retried;
				}
			}
			return $this->handleError($err_message, $err_code, $err_retryable, $err_details, $parameters);
		} else {
			if ($parameters['array']) {
				$parsed_array = json_decode(json_encode($parsed), true);
				return $parsed_array['data'];
			} else {
				return $parsed->data;
			}
		}
	}

	/** takes an associative array $config as argument
	 *  returns merged array with local $this->_config
	 *  values from $config are prioritary
	 */
	public function mergeArray($config) {
		$parameters = $this->_config;
		foreach ($config as $key => $value) {
			$parameters[$key] = $value;
		}
		return $parameters;
	}

	// allow query as a string
	public function generateURL($url, $path, $method, $query) {
		if ($method == 'GET') {
			if (is_string($query)) {
				return $url . $path . $query;
			}
			if (isset($query)) {
				return $url . $path . '?' . http_build_query($query);
			}	
		}
		return $url . $path;
	}

	public function handleRetry($parameters) {
		if ($this->_retries < $this->_max_retries) {
			sleep($this->_delay);
			$this->_delay = $this->_delay * 2;
			return $this->call(NULL, NULL, $parameters);
		} else {
			$this->_retries = 0;
			$this->_delay = 1;
			return NULL;
		}
	}

	public function callGET($path, $config = array()) {
		return $this->call('GET', $path, $config);
	}

	public function callPOST($path, $body, $config = array()) {
		$config['body'] = $body;
		return $this->call('POST', $path, $config);
	}

	public function callPUT($path, $body, $config = array()) {
		$config['body'] = $body;
		return $this->call('PUT', $path, $config);
	}

	public function callDELETE($path, $body, $config = array()) {
		$config['body'] = $body;
		return $this->call('DELETE', $path, $config);
	}

	public function get($resource, $id = NULL, $config = array()) {
		if ($id !== NULL) {
			return $this->callGET("/v3/$resource/$id", $config);
		} else {
			return $this->callGET("/v3/$resource", $config);
		}
	}

	public function create($resource, $payload, $config = array()) {
		if (!is_string($payload)) {
			$payload['async'] = false;
		}
		return $this->callPOST("/v3/$resource", $payload, $config);
	}

	public function cancelLabel($id, $config = array()) {
		$payload = array (
			'label' => array (
				'id' => $id
			),
			'async' => false
		);
		return $this->callPOST("/v3/cancel-labels", $payload, $config);
	}

	public function getError() {
		return $this->_error;
	}
}
?>
