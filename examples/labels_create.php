<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of your shipper account
$shipper = NULL;

if(!isset($shipper)) {
	echo "\$shipper is not set, modify file labels_create.php\n";
}

$parcel = array(
	'description' => 'info about the parcel',
	'box_type' => 'custom',
	'weight' => array (
		'value' => 1.5,
		'unit' => 'kg',
	),
	'dimension' => array (
		'width' => 20,
		'height' => 30,
		'depth' => 40,
		'unit' => 'cm',
	),
	'items' => array (
		0 => array (
			'description' => 'Food Bar',
			'origin_country' => 'USA',
			'quantity' => 2,
			'price' => array (
				'amount' => 50,
				'currency' => 'USD',
			),
			'weight' => array (
				'value' => 0.6,
				'unit' => 'kg',
			),
		),
	),
);

$sender = array (
	'contact_name' => 'your name',
	'company_name' => 'name of your company',
	'street1' => 'your address',
	'street2' => null,
	'street3' => null,
	'city' => 'your city',
	'state' => 'your state',
	'postal_code' => 'your postal code',
	'country' => 'HKG',
	'phone' => '1-403-504-5496',
	'fax' => '1-403-504-5497',
	'tax_id' => null,
	'email' => 'test@test.com',
	'type' => 'business'
);

$receiver = array (
	'contact_name' => 'Rick McLeod (RM Consulting)',
	'street1' => '71 Terrace Crescent NE',
	'street2' => 'This is the second streeet',
	'city' => 'Medicine Hat',
	'state' => 'Alberta',
	'postal_code' => 'T1C1Z9',
	'country' => 'CAN',
	'phone' => '1-403-504-5496',
	'email' => 'test@test.test',
	'type' => 'residential'
);

$payload = array (
	'is_document' => false,
	'return_shipment' => false,
	'paper_size' => 'default',
	'service_type' => 'hong-kong-post_air_parcel',
	'customs' => array (
		'billing' => array (
			'paid_by' => 'shipper',
			'method' => array (
				'account_number' => '950000002',
				'type' => 'account',
			),
		),
		'purpose' => 'gift'
	),
	'shipper_account' => 
	array (
		'id' => $shipper,
	),
	'shipment' => array (
		'parcels' => array (
			0 => $parcel
		),
		'ship_from' => $sender,
		'ship_to' => $receiver
	),
);

try {
	$api = new Postmen($key, $region);
	$result = $api->create('labels', $payload);
	echo "RESULT:\n";
	print_r($result);
} catch (exception $e) {
	echo "ERROR:\n";
	echo $e->getCode() . "\n";      // error code
	echo $e->getMessage() . "\n";   // error message
	print_r($e->getDetails());      // error details
}
?>
