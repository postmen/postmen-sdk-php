## Postmen SDK PHP
PHP for Postmen API.
This extension helps developers to integrate with Postmen easily.


## About Postmen


### Changes


## Installation
#### Manual installation

1. Download this repository as zip and extract where you desire OR `git clone` it.
2. Reference desired API class from withthin your PHP source. (very important to also include the `Handler.php` file)
```php
require('.../path/to/repository/src/Postmen/Postmen.php');

use Postmen\Postmen;

$key = 'your_api_key';
$region = 'us-west';

$api = new Postmen($key, $region);
```
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

use Postmen\Postmen;

$key = 'your_api_key';
$region = 'us-west';

$api = new Postmen($key, $region);
```

#### Requirements

Minimum PHP version required to use this SDK is `5.2` for just using the SDK. For development PHP `5.6` is required (to run automated tests).

Tested on PHP 5.3, 5.4, 5.5, 5.6.

## Quick Start

```php
use Postmen\Postmen;

$key = '';	// your API key, if you don't have one, generate!
$region = '';	// region of Postmen instance you are going to access

// initiate Postmen handler object
$api = new Postmen($key, $region);

try {
	// as an example we request all the labels
	$result $api->get('labels');
} catch (exception $e) {
	// if error occurs we can access all
	// the details in following way
	echo $e->getCode() . "\n";	// error code
	echo $e->getMessage() . "\n";	// error message
	print_r($e->getDetails());	// details, array that can help if our payload is incorrect
}
```

## Error Handling

Please refer to [error.php](https://github.com/postmen/postmen-sdk-php/blog/master/examples/error.php) file which contains full example of how errors should be handled using this SDK.

## Examples

| Model \ Action | create                                                                                                                      | get all                                                                                                                    | get by id                                                                                                                 |
|----------------|-----------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------|
| rates          | [.create('rates', $payload, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_calculate.php)     | [.get('rates', NULL, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_retrieve.php#L12)        | [.get('rates', $id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_retrieve.php#L15)        |
| labels         | [.create('labels', $payload, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_create.php)      | [.get('labels', NULL, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_retrieve.php#L12)      | [.get('labels', $id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_retrieve.php#L15)      |
| manifest       | [.create('manifest', $payload, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_create.php) | [.get('manifest', NULL, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php#L12) | [.get('manifest', $id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php#L15) |
| cancel-labels  | [.cancelLabel($label_id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_label.php)                 | [.get('cancel-labels', NULL, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_label_retrieve.php#L12)                                                                                      | [.get('cancel-labels', $id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_label_retrieve.php#L15)                                                                                      |


| File         | Description                                                      |
|--------------|------------------------------------------------------------------|
| [proxy.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/proxy.php)    | Connecting to Postmen via proxy server                           |
| [error.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/error.php)    | Different ways of handling errors from Postmen                   |
| [rate.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rate.php)     | How to enable / disable automatic rate limiting                  |
| [retry.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/retry.php)    | How to enable / disable automatic retry on error                 |
| [response.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/response.php) | Shows how to specify output type as array object or raw response |

## Handler constructor documentation
todo

## Member functions documentation

### .create
todo

### .get
todo

### .cancel
todo

### .callGET
todo

### .callPOST
todo

### .callPUT
todo

### .callDELETE

## Testing
If you contribute it is recommended to run automated test before you pull request your changes.

`phpunit --bootstrap tests/bootstrap.php tests/Postmen.php`

## The License (MIT)
Released under the MIT license. See the LICENSE file for the complete wording.

## Contributor
- Sunny Chow - [view contributions](https://github.com/postmen/sdk-php/commits?author=sunnychow)
- Marek Narozniak - [view contributions](https://github.com/postmen/sdk-php/commits?author=marekyggdrasil)

