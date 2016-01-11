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
	public function create($fields, $options = array())  {
		$required = array('paper_size', 'service_type', 'is_document', 'shipper_account', 'shipment');
		$accepted = array('async', 'return_shipment', 'ship_date', 'service_options', 'invoice', 'reference', 'billing', 'customs');
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
		return $this->POST('/v3/labels', $request, $options);
	}

	public function retrieve($id, $options = array()) {
		return $this->GET("/v3/labels/$id", $options);
	}

	public function list_labels($options = array()) {
		return $this->GET('/v3/labels', $options);
	}
}
