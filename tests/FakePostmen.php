<?php

namespace Postmen;

class FakePostmen extends Postmen
{
	public function call($method, $path, $parameters = array()) {
		$call = array(
			'method' => $method,
			'path' => $path,
			'parameters' => $parameters
		);
		return $call;
	}
}

?>
