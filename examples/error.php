<?php
require('credentials.php');

use Postmen\Postmen;

// most obvious way would be to surround our
// call using try{}catch{} section
// pay attention to details attribute of the
// exception which will inform you what
// is wrong with your payload

echo "using try ... catch\n";
try {
	$api = new Postmen('THIS IS NOT A VALID API KEY', $region);
	$result = $api->get('labels');
} catch (exception $e) {
	echo "ERROR:\n";
	echo $e->getCode() . "\n";      // error code
	echo $e->getMessage() . "\n";   // error message
	print_r($e->getDetails());      // error details
}

// we also can enable the safe mode,
// this way try{}catch(){} is no
// longer required

echo "using safe mode\n";
$api = new Postmen('THIS IS NOT A VALID API KEY', $region);
$result = $api->get('labels', NULL, array('safe' => true));
if (!$result) {
	$e = $api->getError();
	echo "ERROR:\n";
	echo $e->getCode() . "\n";      // error code
	echo $e->getMessage() . "\n";   // error message
	print_r($e->getDetails());      // error details
}
?>
