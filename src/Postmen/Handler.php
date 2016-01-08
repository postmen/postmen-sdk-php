<?php

namespace Postmen;

require('PostmenException.php');

use Postmen\PostmenException;

/**
 * Class Handler
 *
 * @package Postmen
 */
class Handler
{
	private $_api_key;
	private $_url;
	private $_config;
	private $_version;
	private $_error;

	public function __construct($api_key, array $config)
	{
		if (!isset($api_key)) {
			throw new PostmenException('required argument is unset', 201, false);
		}
		$this->_error = undefined;
		$this->_version = "0.0.1";
		$this->_api_key = $api_key;
		$fields = array(
			'region' => undefined,
			'endpoint' => 'https://sandbox-api.postmen.com',
			'proxy' => 'no',
			'retry' => true,
			'rate' => 'false'
		);
		$this->_url = $fields['endpoint'];
		foreach ($fields as $key => $default) {
			$$key = isset($config[$key]) ? $config[$key] : $default;
		}
		if ($config['region'] == undefined) {
			throw new PostmenException('missing required field', 200, false);
		}
		$this->_config = $fields;
	}

	public function call($method, $path, $parameters = array()) {
		$safe = false;
		if (isset($parameters['safe'])) {
			$safe = $parameters['safe'];
		}
		$body = $parameters['body'];
		if (!is_string($body)) {
			$body = json_encode($body);
		}
		$headers = array(
			"content-type: application/json",
			"postmen-api-key: $this->_api_key",
			"x-postmen-agent: php-sdk-$this->_version"
		);
		$url = $this->_url . $path;
		if ($method == 'GET') {
			if (isset($parameters['query'])) {
				$url = $url . http_build_query($parameters['query']);
			}	
		}
		$curl = curl_init();
		$curl_params = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => $headers
		);
		if ($method == 'POST') {
			$curl_params[CURLOPT_POSTFIELDS] = $body;
		}
		curl_setopt_array($curl, $curl_params);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		if ($err) {
			$error = new PostmenException("failed to request: $err" , 100, true);
			if ($safe) {
				$this->_error = $error;
				return undefined;
			} else {
				throw $error;
			}
		}
		$info = curl_getinfo($curl);
		$code = $info['http_code'];
		if ($code != 200) {
			$error =  new PostmenException("http response error: $code" , 101, true);
			if ($safe) {
				$this->_error = $error;
				return undefined;
			}
			else {
				throw $error;
			}
		}
		curl_close($curl);
		if(isset($parameters['raw'])) {
			if($parameters['raw']) {
				return $response;
			}
		}
		return json_decode($response);
	}

	public function GET($path, $parameters = array()) {
		return $this->call('GET', $path, $parameters);
	}

	public function POST($path, $body, $parameters = array()) {
		$parameters['body'] = $body;
		return $this->call('POST', $path, $parameters);
	}

	public function PUT($path, $body, $parameters = array()) {
		$parameters['body'] = $body;
		return $this->call('PUT', $path, $parameters);
	}

	public function DELETE($path, $parameters = array()) {
		return $this->call('DELETE', $path, $parameters);
	}

	public function getError() {
		return $this->_error;
	}
}
?>
