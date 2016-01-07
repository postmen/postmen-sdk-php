<?php

namespace Postmen;

use Exception;

/**
 * Class Postmen
 *
 * @package Postmen
 */
class Postmen
{
	private $_api_key;
	private $_config;

	public function __construct($api_key, array $config)
	{
		if (!isset($api_key)) {
			throw new Exception('api_key is required field and must be defined');
		}
		$this->_api_key = $api_key;
		$fields = array(
			'region' => undefined,
			'endpoint' => 'no',
			'proxy' => 'no',
			'retry' => true,
			'rate' => 'false'
		);
		foreach ($fields as $key => $default) {
			$$key = isset($config[$key]) ? $config[$key] : $default;
		}
		if ($fields['region'] == undefined) {
			throw new Exception('region is required field and must be defined');
		}
		$this->_config = $fields;
	}


	// test
	//

	public function getfields() {
		return $this->_config;
	}
}

?>
