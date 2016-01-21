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

	public function __construct($api_key, $region, $config = array())
	{
		if (!isset($api_key)) {
			throw new PostmenException('required argument is unset', 201, false);
		}
		$this->_version = "0.1.0";
		$this->_api_key = $api_key;
		if (isset($config['proxy'])) {
			$this->_proxy = $config['proxy'];
		}
		if (isset($config['endpoint'])) {
			$this->_url = $config['endpoint'];
		} else if (!isset($region)) {
			throw new PostmenException('missing required field', 200, false);
		} else {
			$this->_url = "https://$region-api.postmen.com";
		}
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
			CURLOPT_HTTPHEADER => $headers
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
			$curl_params[CURLOPT_HEADER] = false; 	
		}
		if ($method != 'GET') {
			$curl_params[CURLOPT_POSTFIELDS] = $body;
		}
		return $curl_params;
	}

	public function call($method, $path, $parameters = array()) {
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
		$response = curl_exec($curl);
		$err = curl_error($curl);
		if ($err) {
			$error = new PostmenException("failed to request: $err" , 100, true, array());
			if ($safe) {
				$this->_error = $error;
				return undefined;
			} else {
				throw $error;
			}
		}
		curl_close($curl);
		return $this->processCurlResponse($response, $safe, $raw);
	}

	public function processCurlResponse($response, $safe, $raw) {
		$parsed = json_decode($response);
		if ($parsed != NULL) {
			if($raw) {
				return $response;
			}
			return $this->handle($parsed, $safe);
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

	public function handle($parsed, $safe) {
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
			return $this->handleError($err_message, $err_code, $err_retryable, $err_details, $safe);
		} else {
			return $parsed->data;
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

	public function callDELETE($path, $parameters = array()) {
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
		$payload['async'] = false;
		return $this->callPOST("/v3/$resource/", $payload, $parameters);
	}

	public function cancel($id, $parameters = array()) {
		$payload = array (
			'label' => array (
				'id' => $id
			),
			'async' => false
		);
		return $this->callPOST("/v3/cancel-label/", $payload, $parameters);
	}

	public function getError() {
		return $this->_error;
	}
}
?>
