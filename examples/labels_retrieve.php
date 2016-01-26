<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of a particular label
$label = NULL;

if(!isset($label)) {
	echo "\$label is not set, modify file labels_retrieve.php\n";
}

try {
	$api = new Postmen($key, $region);
	// get all the labels
	$result_all = $api->get('labels');
	// get a particular label
	$result_particular = $api->get('labels', $label);
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
