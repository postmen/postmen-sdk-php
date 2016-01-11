<?php

namespace Postmen;

use Exception;

/**
 * Class Rates
 *
 * @package Postmen
 */
class Rates extends Handler
{
	public function calculate($request, $options = array())  {
		return $this->POST('/v3/rates', $request, $options);
	}

	public function retrieve($id, $options = array()) {
		return $this->GET("/v3/rates/$id", $options);
	}

	public function list_all($options = array()) {
		return $this->GET('/v3/rates', $options);
	}
}
