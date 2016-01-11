<?php

namespace Postmen;

use Exception;

/**
 * Class Labels
 *
 * @package Postmen
 */
class Labels extends Handler
{
	public function create($request, $options = array())  {
		return $this->POST('/v3/labels', $request, $options);
	}

	public function retrieve($id, $options = array()) {
		return $this->GET("/v3/labels/$id", $options);
	}

	public function list_all($options = array()) {
		return $this->GET('/v3/labels', $options);
	}
}
