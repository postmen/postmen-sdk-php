<?php

namespace Postmen;

use Exception;

/**
 * Class Manifests
 *
 * @package Postmen
 */
class Manifests extends Handler
{
	public function create($request, $options = array())  {
		return $this->POST('/v3/manifests', $request, $options);
	}

	public function retreive($id, $options = array()) {
		return $this->GET("/v3/manifests/$id", $options);
	}

	public function list_all($request = array(), $options = array()) {
		if (count($fields) > 0) {
			$options['query'] = $request;
		}
		return $this->GET('/v3/manifests', $options);
	}
}
