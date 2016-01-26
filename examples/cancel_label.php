<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put the ID of the label you wish to cancel
$label = NULL;

if(!isset($label)) {
	echo "\$label is not set, modify file cancel_label.php\n";
}

try {
	$api = new Postmen($key, $region);
	$result = $api->cancelLabel($label);
	echo "RESULT:\n";
	print_r($result);
} catch (exception $e) {
	echo "ERROR:\n";
	echo $e->getCode() . "\n";      // error code
	echo $e->getMessage() . "\n";   // error message
	print_r($e->getDetails());      // error details
}
?>
