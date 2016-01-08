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
	
	public function __construct($message, $code, $retryable, Exception $previous = null) {
		$this->retryable = $retryable;
		parent::__construct($message, $code, $previous);
	}

	public function isRetryable() {
		return $this->retryable;
	}

}
