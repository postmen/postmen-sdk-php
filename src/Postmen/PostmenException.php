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
	
	public function __construct($message, $code, $retryable, $details, Exception $previous = null) {
		$this->retryable = $retryable;
		$this->details = array();
		parent::__construct($message, $code, $previous);
	}

	public function isRetryable() {
		return $this->retryable;
	}

	public function getDetails() {
		return $this->details;
	}

}
