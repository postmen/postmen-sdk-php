<?php
require('credentials.php');

use Postmen\Postmen;

$item = array (
	'description' => 'PS4',
	'origin_country' => 'JPN',
	'quantity' => 2,
	'price' => array (
		'amount' => 50,
		'currency' => 'JPY',
	),
	'weight' => array (
		'value' => 0.59999999999999998,
		'unit' => 'kg',
	),
	'sku' => 'PS4-2015',
);
$sender = array (
	'contact_name' => 'Yin Ting Wong',
	'street1' => 'Flat A, 30/F, Block 17 Laguna Verde',
	'city' => 'Hung Hom',
	'state' => 'Kowloon',
	'country' => 'HKG',
	'phone' => '96679797',
	'email' => 'test@test.test',
	'type' => 'residential',
);
$receiver = array (
	'contact_name' => 'Mike Carunchia',
	'street1' => '9504 W Smith ST',
	'city' => 'Yorktown',
	'state' => 'Indiana',
	'postal_code' => '47396',
	'country' => 'USA',
	'phone' => '7657168649',
	'email' => 'test@test.test',
	'type' => 'residential',
);
$payload = array (
	'async' => false,
	'shipper_accounts' => array (
		0 => array (
			// TODO put ID of your shipper account
			'id' => '00000000-0000-0000-0000-000000000000',
		),
	),
	'shipment' => array (
		'parcels' => array (
			0 => array (
				'box_type' => 'custom',
				'weight' => array (
					'value' => 0.5,
					'unit' => 'kg',
				),
				'dimension' => array (
					'width' => 20,
					'height' => 10,
					'depth' => 10,
					'unit' => 'cm',
				),
				'items' => array (
					0 => $item
				),
			),
		),
		'ship_from' => $sender,
		'ship_to' => $receiver,	
	),
	'is_document' => false
);

$api = new Postmen($key, $region);
print_r($api->create('rates', $payload));
?>
