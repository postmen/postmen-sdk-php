<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of a particular label
$label = NULL;

if(!isset($label)) {
	echo "\$label is not set, modify file cancel_label_retrieve.php\n";
}

try {
	$api = new Postmen($key, $region);
	// get all the cancelled labels
	$result_all = $api->get('cancel-labels');
	// get a particular cancelled label
	$result_particular = $api->get('cancel-labels', $label);
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

