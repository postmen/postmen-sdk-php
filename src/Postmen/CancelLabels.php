<?php

namespace Postmen;

use Exception;

/**
 * Class CancelLabels
 *
 * @package Postmen
 */
class CancelLabels extends Handler
{
	public function cancel($request, $options = array())  {
		return $this->POST('/v3/cancel-labels', $request, $options);
	}

	public function retrieve($id, $options = array()) {
		return $this->GET("/v3/cancel-labels/$id", $options);
	}

	public function list_all($request = array(), $options = array()) {
		if (count($request) > 0) {
			$options['query'] = $request;
		}
		return $this->GET('/v3/labels', $options);
	}
}
