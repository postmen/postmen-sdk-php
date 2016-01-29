<?php
require('credentials.php');

use Postmen\Postmen;

try {
	$api = new Postmen($key, $region);
	// get a raw json string
	$output_json = $api->get('labels', NULL, NULL, array('raw' => true));
	// get a std object
	$output_object = $api->get('labels');
	// to get an array it requires to initiate
	// handler object with that option
	$array_api = new Postmen($key, $region, array('array' => true));
	$output_array = $array_api->get('labels');
	echo "RESULT:\n";
	echo "raw json output:\n";
	echo $output_json . "\n\n";
	echo "std object output:\n";
	print_r($output_object);
	echo "\narray object output\n";
	print_r($output_array);
} catch (exception $e) {
	echo "ERROR:\n";
	echo $e->getCode() . "\n";      // error code
	echo $e->getMessage() . "\n";   // error message
	print_r($e->getDetails());      // error details
}
?>
