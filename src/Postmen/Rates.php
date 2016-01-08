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
	public function calculate($fields, $options = array())  {
		$required = array('is_document', 'shipment');
		$accepted = array('async', 'shipper_accounts');
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
		return $this->POST('/v3/rates', $request, $options);
	}

	public function retreive($id, $options = array()) {
		return $this->GET("/v3/rates/$id", $options);
	}

	public function list_rates($fields, $options = array()) {
		$accepted = array('status', 'limit', 'created_at_min', 'created_at_max', 'next_token');
		$request = array();
		foreach ($fields as $key => $value) {
			if (!in_array($key, $accepted)) {
				throw new Exception("Unsupported argument '$key'");
			} else {
				$request[$key] = $value;
			}
		}
		return $this->POST('/v3/rates', $request, $options);
	}
}
