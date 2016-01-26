<?php
require('../src/Postmen/Postmen.php');

// TODO put your own API key here
$key = NULL;

// TODO region of the Postmen instance
$region = NULL;

// TODO if you need a custom endpoint
$endpoint = NULL;

if(!isset($key)) {
	echo "\$key is not set, modify file credentials.php\n";
}
if(!isset($region)) {
	echo "\$region is not set, modify file credentials.php\n";
}
?>
