## Introduction
PHP for [Postmen API](https://docs.postmen.com/).
This extension helps developers to integrate with [Postmen](https://www.postmen.com/) easily.

#### Contents

- [Introduction](#intoduction)
		- [Contents](#contents)
- [Installation](#installation)
		- [Manual installation](#manual installation)
		- [Using Composer](#using Composer)
		- [Requirements](#requirements)
- [Quick Start](#quick Start)
- [Error Handling](#error Handling)
- [Examples](#examples)
- [Handler constructor documentation](#handler constructor documentation)
- [Member functions documentation](#member functions documentation)
	- [.create](#create)
	- [.get](#get)
	- [.cancelLabel](#cancelLabel)
	- [.callGET](#callGET)
	- [.callPOST](#callPOST)
	- [.callPUT](#callPUT)
	- [.callDELETE](#callDELETE)
- [Automatic retry on retryable error](#automatic retry on retryable error)
- [Automatic rate limiting](#automatic rate limiting)
- [Testing](#testing)
- [Licence](#licence)
- [Contributors](#contributors)

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

Information about how to get API key and how to choose region can be found in the documentation under [this](https://docs.postmen.com/overview.html) link.

```php
use Postmen\Postmen;

$key = '';	// your API key, if you don't have one, generate!
$region = '';	// region of Postmen instance you are going to access

// initiate Postmen handler object
$api = new Postmen($key, $region);

try {
	// as an example we request all the labels
	$result = $api->get('labels');
} catch (exception $e) {
	// if error occurs we can access all
	// the details in following way
	echo $e->getCode() . "\n";	// error code
	echo $e->getMessage() . "\n";	// error message
	print_r($e->getDetails());	// details, array that can help if our payload is incorrect
}
```

## Error Handling

Please refer to [error.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/error.php) file which contains full example of how errors should be handled using this SDK.

Our custom exception type contains following methods.

| Method            | Return type | Description                                                                  |
|-------------------|-------------|------------------------------------------------------------------------------|
| .getCode()        | Integer     | Error code (eg, 4104)                                                        |
| .isRetryable()    | Boolean     | Indicates if error is retryable or not                                       |
| .getMessage()     | String      | Error message (eg, "The request was invalid or cannot be otherwise served.") |
| .getDetails()     | Array       | Array of error details (eg, "Destination country must be RUS or KAZ"         |

In case of using `safe mode` it is possible to retrieve error without `try...catch...` blocks using `$api->getError()` method, to learn more about this please refer to following [line](https://github.com/postmen/postmen-sdk-php/blob/master/examples/error.php#L29) example.

More info about Postmen errors can be found in the [documentation](https://docs.postmen.com/errors.html).

## Examples

For each API method we provide a PHP wrapper, all can be referenced via examples in separated files, easily accessible from following table.

| Model \ Action | create                                                                                                                      | get all                                                                                                                    | get by id                                                                                                                 |
|----------------|-----------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------|
| rates          | [.create('rates', $payload, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_calculate.php)     | [.get('rates', NULL, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_retrieve.php#L16)        | [.get('rates', $id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_retrieve.php#L18)        |
| labels         | [.create('labels', $payload, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_create.php)      | [.get('labels', NULL, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_retrieve.php#L16)      | [.get('labels', $id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_retrieve.php#L18)      |
| manifest       | [.create('manifest', $payload, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_create.php) | [.get('manifest', NULL, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php#L16) | [.get('manifest', $id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php#L18) |
| cancel-labels  | [.cancelLabel($label_id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_label.php)                 | [.get('cancel-labels', NULL, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_label_retrieve.php#L16)                                                                                      | [.get('cancel-labels', $id, $opt)](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_label_retrieve.php#L18)                                                                                      |

For usage of optional arguments several examples are provided as well as sample `$opt` object definition.

```php
// all are optional
$opt = array(
	'proxy' => $proxy, // ARRAY, contains proxy credentials, overrides one passed in constructor
	'retry' => $retry // BOOL, overrides the one passed to the constructor or default one
	'raw' => $rate, // BOOL, default: false, return raw JSON response
	'safe' => $safe // BOOL, default: false, do not raise exception if occurs, just return NULL
);
```

| File         | Description                                                      |
|--------------|------------------------------------------------------------------|
| [proxy.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/proxy.php)    | Connecting to Postmen via proxy server                           |
| [error.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/error.php)    | Different ways of handling errors from Postmen                   |
| [response.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/response.php) | Shows how to specify output type as array object or raw response |

## Constructor

Initiation of Postmen SDK object requires providing an API key and region of the server instance, also several others optional parameters can be included.

```php
// all are optional
$opt = array(
	'endpoint' => $endpoint, // STRING, describes custom URL endpoint
	'proxy' => $proxy, // ARRAY, contains proxy credentials
	'retry' => $retry, // BOOL, default: true, automatic retry if retryable error occurs
	'rate' => $rate, // BOOL, default: true, wait if rate limit exceeded
	'array' => $arr // BOOL, default: false, return array instead of object
);
```

Optional parameters are passed as the last argument and can be skipped.

```php
// initiate Postmen handler object
$api = new Postmen($key, $region);

```

## Member functions documentation

### .create

Creates a `$model` object based on `$payload`, returns an `Object` of created resource.

| Argument | Req? | Type             | Default | Description                                       |
|----------|------|------------------|---------|---------------------------------------------------|
| $model   | YES  | String           | N / A   | New object model ('rates', 'labels', 'manifests') |
| $payload | YES  | Object or String | N / A   | Payload according to API                          |
| $options | NO   | Object           | NULL    | Options as documented [above](#constructor)       |

**Example:** [labels_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_create.php)

### .get

Retrieves single or list of `$model` objects, depending if `$id` of particular object is provided.

| Argument | Req? | Type             | Default | Description                                       |
|----------|------|------------------|---------|---------------------------------------------------|
| $model   | YES  | String           | N / A   | Object model ('rates', 'labels', 'manifests')     |
| $id      | YES  | String           | N / A   | ID of particular instance of object               |
| $options | NO   | Object           | NULL    | Options as documented  [above](#constructor)      |

**Example:** [manifests_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php)

### .cancelLabel

Cancels a label, accepts `$id` of a label, returns an `Object` containg API response.

| Argument | Req? | Type             | Default | Description                                       |
|----------|------|------------------|---------|---------------------------------------------------|
| $id      | YES  | String           | N / A   | ID of a label                                     |
| $options | NO   | Object           | NULL    | Options as documented  [above](#constructor)      |

**Example:** [cancel_label.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_label.php)

### .callGET

Performs HTTP GET request, accepts `$path` representing API URL path, returns an `Object` holding API response.

| Argument | Req? | Type             | Default | Description                                       |
|----------|------|------------------|---------|---------------------------------------------------|
| $path    | YES  | String           | N / A   | url path (eg, '/v3/' for example.com/v3/ )        |
| $options | NO   | Object           | NULL    | Options as documented  [above](#constructor)      |

### .callPOST

Performs HTTP POST request, accepts `$path` representing API URL path and `$body` holding POST request body,  returns an `Object` holding API response.

| Argument | Req? | Type             | Default | Description                                       |
|----------|------|------------------|---------|---------------------------------------------------|
| $path    | YES  | String           | N / A   | url path (eg, '/v3/' for example.com/v3/ )        |
| $body    | YES  | Object or String | N / A   | Body of POST request                              |
| $options | NO   | Object           | NULL    | Options as documented  [above](#constructor)      |

### .callPUT

Performs HTTP PUT request, accepts `$path` representing API URL path,  returns an `Object` holding API response.

| Argument | Req? | Type             | Default | Description                                       |
|----------|------|------------------|---------|---------------------------------------------------|
| $path    | YES  | String           | N / A   | url path (eg, '/v3/' for example.com/v3/ )        |
| $body    | YES  | Object or String | N / A   | Body of PUT request                               |
| $options | NO   | Object           | NULL    | Options as documented  [above](#constructor)      |

### .callDELETE

Performs HTTP DELETE request, accepts `$path` representing API URL path,  returns an `Object` holding API response.

| Argument | Req? | Type             | Default | Description                                       |
|----------|------|------------------|---------|---------------------------------------------------|
| $path    | YES  | String           | N / A   | url path (eg, '/v3/' for example.com/v3/ )        |
| $body    | YES  | Object or String | N / A   | Body of DELETE request                            |
| $options | NO   | Object           | NULL    | Options as documented  [above](#constructor)      |

## Automatic retry on retryable error

In case if API returns an error that is marked as retryable, SDK instead of raising an exception will wait a while and try one more time.
After each try delay time until next attempt is doubled. Maximum amount of attempts is equal to 5.

To disable this option initiate handler object with `retry` option set to false.

```php
$api = new Postmen($key, $region, array('retry' => false));

```

## Automatic rate limiting

By default set to true, in case if we exceed number of calls per time frame, instead of raising an exception SDK will wait until next call becomes available.

In order to disable automatic rate limiting set `rate` option to false.
```php
$api = new Postmen($key, $region, array('rate' => false));
```

## Testing
If you contribute it is recommended to run automated test before you pull request your changes.

`phpunit --bootstrap tests/bootstrap.php tests/Postmen.php`

## License
Released under the MIT license. See the LICENSE file for the complete wording.

## Contributors
- Sunny Chow - [view contributions](https://github.com/postmen/sdk-php/commits?author=sunnychow)
- Marek Narozniak - [view contributions](https://github.com/postmen/sdk-php/commits?author=marekyggdrasil)

