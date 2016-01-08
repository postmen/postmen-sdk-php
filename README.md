## Postmen SDK PHP
PHP for Postmen API.
This extension helps developers to integrate with Postmen easily.


## About Postmen


### Changes


## Installation
#### Using Composer

0. If you don't yet have Composer, to download and Install Composer, open [here](https://getcomposer.org/download/)
1. You have 2 options to download the Postmen PHP SDK

Run the following command to require Postmen PHP SDK
```
composer require postmen/sdk-php
```
OR `git pull` this repo and run the following command
```
composer install
```
2. Autoload the `postmen-php-sdk` package.

```php
$loader = require __DIR__ . '/vendor/autoload.php';

$key = 'your_api_key';

$rates = new Postmen\Rates($key, ['region' => 'US-West-2']);
$labels = new Postmen\Labels($key, ['region' => 'US-West-2']);
$manifests = new Postmen\Manifests($key, ['region' => 'US-West-2']);
```

#### Manual installation

1. Download this repository as zip and extract where you desire OR `git clone` it.
2. Reference desired API class from withthin your PHP source.
```php
require('.../path/to/repository/src/Postmen/Rates.php');
require('.../path/to/repository/src/Postmen/Labels.php');
require('.../path/to/repository/src/Postmen/Manifests.php');

$key = 'your_api_key';

$rates = new Postmen\Rates($key, ['region' => 'US-West-2']);
$labels = new Postmen\Labels($key, ['region' => 'US-West-2']);
$manifests = new Postmen\Manifests($key, ['region' => 'US-West-2']);
```

## Usage

#### Rates

##### Calculate rates
example todo
##### List all rates
example todo
##### Retreive rates
```php
$rates = new Postmen\Rates($key, ['region' => 'US-West-2']);
$result = $rates->retreive('rate_id');
```
#### Labels
##### create label
```php
$labels = new Postmen\Labels($key, ['region' => 'US-West-2']);
$parcel = array(
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
				'value' => 0.59999999999999998,
				'unit' => 'kg',
			),
			'sku' => 'Epic_Food_Bar',
			'hs_code' => '11111111',
		),
	),
);
$sender = array (
	'contact_name' => 'Nottingham Inc.',
	'company_name' => 'Nottingham Inc.',
	'street1' => '2511 S. Main St.',
	'city' => 'Grove',
	'state' => 'OK',
	'postal_code' => '74344',
	'country' => 'USA',
	'phone' => '1-403-504-5496',
	'email' => 'test@test.com',
	'type' => 'business',
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
	'type' => 'residential',
);
$shipment = array (
	'is_document' => false,
	'async' => false,
	'return_shipment' => false,
	'paper_size' => '4x8',
	'service_type' => 'dhl_express_0900',
	'customs' => array (
		'billing' => array (
			'paid_by' => 'shipper',
			'method' => array (
				'account_number' => '950000002',
				'type' => 'account',
			),
		),
		'purpose' => 'gift',
	),
	'shipper_account' => array (
		'id' => '00000000-0000-0000-0000-000000000000',
	),
	'shipment' => array (
		'parcels' => array (
			0 => $parcel
		),
		'ship_from' => $sender,
		'ship_to' => $receiver 
	),
);

$labels->create($create));
```


#### Manifests
##### Create a manifest
example todo
##### List all manifests
example todo
##### Retreive a manifest
example todo
#### Cancel Labels
##### Cancel a label
example todo
##### List all cancel labels
example todo
##### Retreive a cancel label
example todo

## The License (MIT)
Released under the MIT license. See the LICENSE file for the complete wording.


## Contributor
- Sunny Chow - [view contributions](https://github.com/postmen/sdk-php/commits?author=sunnychow)
- Marek Narozniak - [view contributions](https://github.com/postmen/sdk-php/commits?author=marekyggdrasil)
