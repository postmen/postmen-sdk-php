<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put your shipper account ID here
$shipper = NULL;

if(!isset($shipper)) {
	echo "\$shipper is not set, modify file manifests_create.php\n";
}

$payload = array (
	'shipper_account' =>
	array (
		'id' => $shipper,
	),
	'async' => false
);

try {
	$api = new Postmen($key, $region);
	$result = $api->create('manifests', $payload);
	echo "RESULT:\n";
	print_r($result);
} catch (exception $e) {
	echo "ERROR:\n";
	echo $e->getCode() . "\n";      // error code
	echo $e->getMessage() . "\n";   // error message
	print_r($e->getDetails());      // error details
}
?>
