<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of a particular rate
$rate = NULL;

if(!isset($rate)) {
	echo "\$rate is not set, modify file rates_retrieve.php\n";
}

try {
	$api = new Postmen($key, $region);
	// retrieve all the rates
	$result_all = $api->get('rates');
	// retrieve a particular rate
	$result_particular = $api->get('rates', $rate);
	echo "RESULT:\n";
	print_r($result_all);
	print_r($result_particular);
} catch (exception $e) {
	echo "ERROR:\n";
	echo $e->getCode() . "\n";      // error code
	echo $e->getMessage() . "\n";   // error message
	print_r($e->getDetails());      // error details
}
?>
