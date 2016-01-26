<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of a particular manifest
$manifest = NULL;

if(!isset($manifest)) {
	echo "\$manifest is not set, modify file manifests_retrieve.php\n";
}

try {
	$api = new Postmen($key, $region);
	// retrieve all the manifests
	$result_all = $api->get('manifests');
	// retrieve a particular manifest
	$result_particular = $api->get('manifests', $manifest);
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
