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
	private $_proxy;

	public function __construct($api_key, $region, $config = array())
	{
		if (!isset($api_key)) {
			throw new PostmenException('required argument is unset', 201, false);
		}
		$this->_error = undefined;
		$this->_version = "0.0.1";
		$this->_api_key = $api_key;
		$fields = array(
			'proxy' => 'no',
			'retry' => true,
			'rate' => 'false'
		);
		foreach ($fields as $key => $default) {
			$$key = isset($config[$key]) ? $config[$key] : $default;
		}
		if (isset($config['proxy'])) {
			$this->_proxy = $config['proxy'];
		}
		if (isset($config['endpoint'])) {
			$this->_url = $config['endpoint'];
		} else if ($region == undefined) {
			throw new PostmenException('missing required field', 200, false);
		} else {
			$this->_url = "https://$region-api.postmen.com";
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
				$url = $url . '?' . http_build_query($parameters['query']);
			}	
		}
		$curl = curl_init();
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
			// TODO read error message from API
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
