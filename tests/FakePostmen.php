<?php

namespace Postmen;

class FakePostmen extends Postmen
{
	public function call($method, $path, $config = array()) {
		$call = array(
			'method' => $method,
			'path' => $path,
			'parameters' => $config,
			'curl' => $this->buildCurlParams($method, $path, $config)
		);
		return $call;
	}
}

?>
