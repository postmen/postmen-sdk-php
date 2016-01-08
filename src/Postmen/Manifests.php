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
	public function create($fields, $options = array())  {
		$required = array('shipper_account');
		$accepted = array('async');
		$accepted = array_merge($accepted, $required);
		$request = array();
		foreach ($required as $key => $value) {
			if (!isset($fields[$value])) {
				throw new Exception("missing required argument '$value'");
			}
		}
		foreach ($fields as $key => $value) {
			if (!in_array($key, $accepted)) {
				throw new Exception("Unsupported argument '$key'");
			} else {
				$request[$key] = $value;
			}
		}
		return $this->POST('/v3/manifests', $request, $options);
	}

	public function retreive($id, $options = array()) {
		return $this->GET("/v3/manifests/$id", $options);
	}

	public function list_manifests($fields, $options = array()) {
		$accepted = array('shipper_account_id', 'status', 'limit', 'created_at_min', 'created_at_max', 'next_token');
		$request = array();
		foreach ($fields as $key => $value) {
			if (!in_array($key, $accepted)) {
				throw new Exception("Unsupported argument '$key'");
			} else {
				$request[$key] = $value;
			}
		}
		return $this->POST('/v3/manifests', $request, $options);
	}
}
