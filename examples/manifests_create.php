<?php
require('credentials.php');

use Postmen\Postmen;

$payload = array (
	'shipper_account' =>
	array (
		// TODO put ID of your shipper account
		'id' => '00000000-0000-0000-0000-000000000000',
	),
	'async' => false
);

$api = new Postmen($key, $region, array('endpoint' => $endpoint));
print_r($api->create('manifests', $payload));
?>
