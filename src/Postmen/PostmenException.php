<?php

namespace Postmen;

use Exception;

/**
 * Class Rates
 *
 * @package Postmen
 */
class PostmenException extends Exception
{
	private $retryable;
	private $details;

	/**
	 * @var \stdClass|array|null If the error happened during running an api request and the api responded, then this
	 *  is the related response. Depending on whether the requests was done with "array: true/false" parameter, the response
	 *  is either a \stdClass object or an array.
	 */
	private $response_data;

	public function __construct($message, $code, $retryable, $details, Exception $previous = null, $response_data = null) {
		$this->retryable = $retryable;
		$this->details = $details;
		$this->response_data = $response_data;
		parent::__construct($message, $code, $previous);
	}

	public function isRetryable() {
		return $this->retryable;
	}

	public function getDetails() {
		return $this->details;
	}

	public function getResponseData() {
		return $this->response_data;
	}

}
