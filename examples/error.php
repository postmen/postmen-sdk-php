<?php
require('credentials.php');

use Postmen\Postmen;

// most obvious way would be to surround our
// call using try{}catch{} section
// pay attention to details attribute of the
// exception which will inform you what
// is wrong with your payload

$api = new Postmen('THIS IS NOT A VALID API KEY', $region);

echo "using try ... catch\n";
try {
	print_r($api->get('labels'));
} catch (exception $e) {
	echo "code: " . $e->getCode() . "\n";
	echo "message: " . $e->getMessage() . "\n";
	echo "array with details:\n";
	print_r($e->getDetails());
	echo "\n";
}

// we also can enable the safe mode,
// this way try{}catch(){} is no
// longer required

echo "using safe mode\n";
$result = $api->get('labels', NULL, array('safe' => true));
if (!$result) {
	$e = $api->getError();
	echo "code: " . $e->getCode() . "\n";
	echo "message: " . $e->getMessage() . "\n";
	echo "array with details:\n";
	print_r($e->getDetails());	
}
?>
