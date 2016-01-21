<?php

use Postmen\PostmenException;
use Postmen\Postmen;

class PostmenTest extends PHPUnit_Framework_TestCase {
	/** Checks if exception will be raised in case if
	 *  returned meta code is different than 200
	 */
	public function testRaiseException() {
		$handler = new Postmen('', '');

		$curl_response = '{"meta":{"code":200,"message":"OK","details":[]},"data":{}}';

		$mock_curl = new PHPUnit_Extensions_MockFunction('curl_exec', $handler);
		$mock_curl->expects($this->atLeastOnce() )->will($this->returnValue($curl_response));

		try {
			$handler->get('labels', '');
		} catch (Exception $exception) {
			$this->fail("Expected not to raise any exceptions when returned meta code is 200");
		}
	}
	/** Checks if exception will be raised in case if
	 *  returned object is not a valid JSON
	 */
	public function testNonSerializableJSON() {
		$handler = new Postmen('', '');

		$exceptionClass = get_class(new PostmenException('', 200, '', ''));
		$this->setExpectedException($exceptionClass);

		$curl_response = 'THIS STRING IS NOT A VALID JSON OBJECT';

		$mock_curl = new PHPUnit_Extensions_MockFunction('curl_exec', $handler);
		$mock_curl->expects($this->atLeastOnce() )->will($this->returnValue($curl_response));

		$result = $handler->get('labels', '');
	}
	/** Checks if safe mode will prevent of throwing an exception
	 *  also verifies if invalid JSON response exception will
	 *  contain correct error code, message and details
	 */
	public function testSafeModeEnabled() {
		$handler = new Postmen('', '');

		$curl_response = 'NOT VALID JSON, BUT EXCEPTION IS NOT GOING TO BE RAISED';

		$mock_curl = new PHPUnit_Extensions_MockFunction('curl_exec', $handler);
		$mock_curl->expects($this->atLeastOnce() )->will($this->returnValue($curl_response));

		$result = $handler->get('labels', '', array('safe' => true));
		$exception = $handler->getError();

		$this->assertNull($result);
		$this->assertEquals($exception->getCode(), 500);
		$this->assertEquals($exception->isRetryable(), false);
		$this->assertEquals($exception->getMessage(), 'Something went wrong on Postmen\'s end');
		$this->assertEquals(count($exception->getDetails()), 0);
	}
	/** Checks if meta code different than 200 raises an exception
	 *  verifies if there is a match on exception code and message
	 */
	public function testRateLimitExceeded() {
		$message = 'THIS IS ERROR MESSAGE RETURNED FROM API';
		$code = 999;
		$handler = new Postmen('', '');

		$curl_response = '{"meta":{"code":' . $code . ',"message":"' . $message . '","details":[]},"data":{}}';

		$mock_curl = new PHPUnit_Extensions_MockFunction('curl_exec', $handler);
		$mock_curl->expects($this->atLeastOnce())->will($this->returnValue($curl_response));

		try{
			$result = $handler->get('labels', '');
			$this->fail("Expected exception not thrown");
		} catch (Exception $exception){
			$this->assertEquals($exception->getCode(), $code);
			$this->assertEquals($exception->getMessage(), $message);
		}
	}

	/**
	 *  test if request method is correct, body fields according
	 *  to request method and URL field
	 */ 
	public function testCurlParamsMethod() {
		$handler = new Postmen('', 'region');
		$body = 'THIS IS THE BODY';
		$path = '/path';
		$parameters = array(
			'body' => $body
		);

		$method = 'GET';
		$get = $handler->buildCurlParams($method, $path, $parameters);
		try {
			$this->assertEquals(isset($get[CURLOPT_POSTFIELDS]), false);
		} catch(Exception $e) {
			$this->fail('GET request method, CURLOPT_POSTFIELDS must be not set');
		}

		$method = 'POST';
		$post = $handler->buildCurlParams($method, $path, $parameters);
		try {
			$this->assertEquals($post[CURLOPT_POSTFIELDS], $body);
		} catch(Exception $e) {
			$this->fail('POST request method, CURLOPT_POSTFIELDS must contain request body');
		}

		$method = 'PUT';
		$put = $handler->buildCurlParams($method, $path, $parameters);
		try {
			$this->assertEquals($put[CURLOPT_POSTFIELDS], $body);
		} catch(Exception $e) {
			$this->fail('PUT request method, CURLOPT_POSTFIELDS must contain request body');
		}

		$method = 'DELETE';
		$del = $handler->buildCurlParams($method, $path, $parameters);
		try {
			$this->assertEquals($del[CURLOPT_POSTFIELDS], $body);
		} catch(Exception $e) {
			$this->fail('DELETE request method, CURLOPT_POSTFIELDS must contain request body');
		}
		try {
			$this->assertEquals($del[CURLOPT_URL], 'https://region-api.postmen.com/path');
		} catch(Exception $e) {
			$this->fail('CURLOPT_URL is not correct');
		}

	}

	/**
	 *  test proxy parameters
	 */ 
	public function testCurlParamsProxy() {
		$handler = new Postmen('', '');
		$method = 'GET';
		$path = '/path';
		$proxy_host = 'proxyserver.com';
		$proxy_user = 'person';
		$proxy_pass = 'topsecret';
		$proxy_port = 9999;
		$parameters = array(
			'proxy' => array(
				'host' => $proxy_host,
				'port' => $proxy_port,
				'username' => $proxy_user,
				'password' => $proxy_pass
			)
		);
		$params = $handler->buildCurlParams($method, $path, $parameters);
		try {
			$this->assertEquals($params[CURLOPT_PROXY], $proxy_host);
		} catch(Exception $e) {
			$this->fail('CURLOPT_PROXY must contain proxy server hostname');
		}
		try {
			$this->assertEquals($params[CURLOPT_PROXYUSERPWD], "$proxy_user:$proxy_pass");
		} catch(Exception $e) {
			$this->fail('CURLOPT_PROXYUSERPWD must contain authentication credentials in form user:password');
		}
		try {
			$this->assertEquals($params[CURLOPT_PROXYPORT], $proxy_port);
		} catch(Exception $e) {
			$this->fail('CURLOPT_PROXYPORT must contain the port number');
		}
		try {
			$this->assertEquals($params[CURLOPT_HEADER], false);
		} catch(Exception $e) {
			$this->fail('CURLOPT_HEADER must be set to false as it will cause JSON serialization issues');
		}
		try {
			$this->assertEquals($params[CURLOPT_FOLLOWLOCATION], true);
		} catch(Exception $e) {
			$this->fail('CURLOPT_FOLLOWLOCATION must be set to true as it is required for proxy to work correctly');
		}
	}

	/**
	 *  test if headers are correct
	 */ 
	public function testCurlParamsHeaders() {
		$handler = new Postmen('1234567890', 'region');
		$method = 'GET';
		$path = '/path';
		$parameters = array();
		$params = $handler->buildCurlParams($method, $path, $parameters);
		try {
			$this->assertEquals($params[CURLOPT_HTTPHEADER][0], 'content-type: application/json');
		} catch(Exception $e) {
			$this->fail('CURLOPT_HTTPHEADER has incorrect content-type field');
		}
		try {
			$this->assertEquals($params[CURLOPT_HTTPHEADER][1], 'postmen-api-key: 1234567890');
		} catch(Exception $e) {
			$this->fail('CURLOPT_HTTPHEADER has incorrect API key field');
		}
		try {
			$this->assertRegExp('/x-postmen-agent\:\sphp-sdk-[0-9]*.[0-9]*.[0-9]*/', $params[CURLOPT_HTTPHEADER][2]);
		} catch(Exception $e) {
			$this->fail('CURLOPT_HTTPHEADER has incorrect x-postmen-agent field');
		}
	}

	/**
	 *  test if headers are correct
	 */ 
	public function testCurlGetQuery() {
		$handler = new Postmen('1234567890', 'region');
		$method = 'GET';
		$path = '/path';
		$query = array(
			'a' => 'alpha',
			'b' => 'beta'
		);
		$parameters = array(
			'query' => $query
		);
		$params = $handler->buildCurlParams($method, $path, $parameters);
		try {
			$this->assertEquals($params[CURLOPT_URL], 'https://region-api.postmen.com/path?a=alpha&b=beta');
		} catch(Exception $e) {
			$this->fail('CURLOPT_URL must contain a valid HTTP get query string in its URL');
		}
	}

	/** Checks if GET query is correctly generated from PHP array object
	 *  also verifies if it will be ignored for POST query
	 */
	public function testGetQuery() {
		$handler = new Postmen('', '');
		$query = array(
			'a' => 'alpha',
			'b' => 'beta'
		);
		$expected = 'http://example.com/path?a=alpha&b=beta';
		$path = '/path';
		$base = 'http://example.com';
		$this->assertEquals($handler->generateURL($base, $path, 'GET', $query), $expected);
		$expected = 'http://example.com/path';
		$this->assertEquals($handler->generateURL($base, $path, 'POST', $query), $expected);
	}
}
?>
