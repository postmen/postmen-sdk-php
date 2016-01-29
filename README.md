## Introduction
PHP SDK for [Postmen API](https://docs.postmen.com/).
For problems and suggestions please open [GitHub issue](https://github.com/postmen/postmen-sdk-php/issues)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [Installation](#installation)
    - [Requirements](#requirements)
    - [Manual installation](#manual-installation)
    - [Using Composer](#using-composer)
- [Quick Start](#quick-start)
- [class Postmen](#class-postmen)
    - [Postmen($api_key, $region, $config = array())](#postmenapi_key-region-config--array)
    - [create($resource, $payload, $config = array())](#createresource-payload-config--array)
    - [get($resource, $id = NULL, $query = NULL, $config = array())](#getresource-id--null-query--null-config--array)
    - [getError()](#geterror)
    - [callGET($path, $query, $options = array())](#callgetpath-query-options--array)
    - [callPOST($path, $body, $options = array())](#callpostpath-body-options--array)
    - [callPUT($path, $body, $options = array())](#callputpath-body-options--array)
    - [callDELETE($path, $body, $options = array())](#calldeletepath-body-options--array)
- [Error Handling](#error-handling)
    - [class PostmenException](#class-postmenexception)
    - [Automatic retry on retryable error](#automatic-retry-on-retryable-error)
- [Examples](#examples)
    - [Full list](#full-list)
    - [How to run](#how-to-run)
    - [Navigation table](#navigation-table)
- [Testing](#testing)
- [License](#license)
- [Contributors](#contributors)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Installation

#### Requirements

PHP version `>= 5.3` is required. For SDK development PHP `5.6` is required (to run automated tests).

Tested on PHP 5.3, 5.4, 5.5, 5.6.

#### Manual installation

- Download the source code.
- Reference API class.
```php
require('.../path/to/repository/src/Postmen/Postmen.php');
```

#### Using Composer

- If you don't have Composer, [download and install](https://getcomposer.org/download/)
- You have 2 options to download the Postmen PHP SDK

Run the following command to require Postmen PHP SDK
```
composer require postmen/sdk-php
```

OR download the sorce code and run
```
composer install
```

- Autoload the `postmen-php-sdk` package.
```php
$loader = require __DIR__ . '/vendor/autoload.php';
```

## Quick Start

In order to get API key and choose a region refer to the [documentation](https://docs.postmen.com/overview.html).

```php
use Postmen\Postmen;

$api_key = 'YOUR_API_KEY';
$region = 'sandbox';

// create Postmen API handler object

$api = new Postmen($api_key, $region);

try {
	// as an example we request all the labels
	
	$result = $api->get('labels');
	echo "RESULT:\n";
	print_r($result);
} catch (exception $e) {
	// if error occurs we can access all
	// the details in following way
	
	echo "ERROR:\n";
	echo $e->getCode() . "\n";	// error code
	echo $e->getMessage() . "\n";	// error message
	print_r($e->getDetails());	// details
}
```

## class Postmen

#### Postmen($api_key, $region, $config = array())

Initiate Postmen SDK object.
In order to get API key and choose a region refer to the [documentation](https://docs.postmen.com/overview.html).

| Argument                       | Required                               | Type    | Default   | Description                                       |
|--------------------------------|----------------------------------------|---------|-----------|---------------------------------------------------|
| `$api_key`                     | YES                                    | String  | N / A     | API key                                           |
| `$region`                      | NO if `$config['endpoint']` is set     | String  | N / A     | API region (`sandbox`, `production`)              |
| `$config`                      | NO                                     | Array   | `array()` | Options                                           |
| `$config['endpoint']`          | —                                      | String  | N / A     | Custom URL API endpoint                           |
| `$config['retry']`             | —                                      | Boolean | `TRUE`    | Automatic retry on retryable errors               |
| `$config['rate']`              | —                                      | Boolean | `TRUE`    | Wait before API call if rate limit exceeded or retry on 429 error |
| `$config['safe']`              | —                                      | Boolean | `FALSE`   | Suppress exceptions on errors, NULL would be returned instead, check [Error Handling](#error-handling) |
| `$config['raw']`               | —                                      | Boolean | `FALSE`   | To return API response as a raw string            |
| `$config['proxy']`             | —                                      | Array   | `array()` | Proxy credentials                                 |
| `$config['proxy']['host']`     | YES if `$config['proxy']` is not empty | String  | N / A     | Proxy host                                        |
| `$config['proxy']['port']`     | NO                                     | Integer | N / A     | Proxy post                                        |
| `$config['proxy']['username']` | NO                                     | String  | N / A     | Proxy user name                                   |
| `$config['proxy']['password']` | NO                                     | String  | N / A     | Proxy password                                    |

#### create($resource, $payload, $config = array())

Creates API `$resource` object, returns new object payload as `Array`.

| Argument    | Required | Type            | Default   | Description                                           |
|-------------|----------|-----------------|-----------|-------------------------------------------------------|
| `$resource` | YES      | String          | N / A     | Postmen API resourse ('rates', 'labels', 'manifests') |
| `$payload`  | YES      | Array or String | N / A     | Payload according to API                              |
| `$config`   | NO       | Array           | `array()` | Override constructor [config](#postmenapi_key-region-config--array) |

**API Docs:**
- [POST /rates](https://docs.postmen.com/#rates-calculate-rates)
- [POST /labels](https://docs.postmen.com/#labels-create-a-label)
- [POST /manifests](https://docs.postmen.com/#manifests-create-a-manifest)
- [POST /cancel-labels](https://docs.postmen.com/#cancel-labels-cancel-a-label)

**Examples:**
- [rates_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_create.php)
- [labels_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_create.php)
- [manifests_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_create.php)
- [cancel_labels_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_labels_create.php)

#### get($resource, $id = NULL, $query = NULL, $config = array())

Gets API `$resource` objects (list or a single objects).

| Argument    | Required | Type            | Default   | Description                                           |
|-------------|----------|-----------------|-----------|-------------------------------------------------------|
| `$resource` | YES      | String          | N / A     | Postmen API resourse ('rates', 'labels', 'manifests') |
| `$id`       | NO       | String          | `NULL`    | Object ID, if not set 'list all' API method is used   |
| `$query`    | NO       | Array or String | `NULL`    | Optional parameters for 'list all' API method         |
| `$config`   | NO       | Array           | `array()` | Override constructor [config](#postmenapi_key-region-config--array) |

**API Docs:**
- [GET /rates](https://docs.postmen.com/#rates-list-all-rates)
- [GET /rates/:id](https://docs.postmen.com/#rates-retrieve-rates)
- [GET /labels](https://docs.postmen.com/#labels-list-all-labels)
- [GET /labels/:id](https://docs.postmen.com/#labels-retrieve-a-label)
- [GET /manifests](https://docs.postmen.com/#manifests-list-all-manifests)
- [GET /manifests/:id](https://docs.postmen.com/#manifests-retrieve-a-manifest)
- [GET /cancel-labels](https://docs.postmen.com/#cancel-labels-list-all-cancel-labels)
- [GET /cancel-labels/:id](https://docs.postmen.com/#cancel-labels-retrieve-a-cancel-label)

**Examples:**
- [rates_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_retrieve.php)
- [labels_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_retrieve.php)
- [manifests_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php)
- [cancel_labels_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_labels_retrieve.php)

#### getError()

Returns SDK error, (PostmenException type)[#class-postmenexception] if `$conifg['safe'] = TRUE;` was set.

Check [Error Handling](#error-handling) for details.

#### callGET($path, $query, $options = array())

Performs HTTP GET request, returns an `Array` object holding API response.

| Argument   | Required | Type            | Default   | Description                                       |
|------------|----------|-----------------|-----------|---------------------------------------------------|
| `$path`    | YES      | String          | N / A     | URL path (e.g. 'v3/labels' for `https://sandbox-api.postmen.com/v3/labels` ) |
| `$query`   | NO       | Array or String | `array()` | HTTP GET request query string                     |
| `$config`  | NO       | Array           | `array()` | Override constructor [config](#postmenapi_key-region-config--array) |

#### callPOST($path, $body, $options = array())
#### callPUT($path, $body, $options = array())
#### callDELETE($path, $body, $options = array())

Performs HTTP POST/PUT/DELETE request, returns an `Array` object holding API response.

| Argument   | Required | Type            | Default   | Description                                       |
|------------|----------|-----------------|-----------|---------------------------------------------------|
| `$path`    | YES      | String          | N / A     | URL path (e.g. 'v3/labels' for `https://sandbox-api.postmen.com/v3/labels` ) |
| `$body`    | YES      | Array or String | N / A     | HTTP POST/PUT/DELETE request body                 |
| `$config`  | NO       | Array           | `array()` | Override constructor [config](#postmenapi_key-region-config--array) |

## Error Handling

Particular error details are listed in the [documentation](https://docs.postmen.com/errors.html).

All SDK methods may throw an exception described below.

#### class PostmenException
| Method        | Return type | Description                                                                  |
|---------------|-------------|------------------------------------------------------------------------------|
| getCode()     | Integer     | Error code                                                                   |
| isRetryable() | Boolean     | Indicates if error is retryable                                              |
| getMessage()  | String      | Error message (e.g. `The request was invalid or cannot be otherwise served`) |
| getDetails()  | Array       | Error details (e.g. `Destination country must be RUS or KAZ`)                |

In case of `$conifg['safe'] = TRUE;` SDK would not throw exceptions, [getError()](#geterror) must be used instead.

Example: [error.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/error.php)

#### Automatic retry on retryable error

If API error is retryable, SDK will wait for delay and retry. Delay starts from 1 second. After each try, delay time is doubled. Maximum number of attempts is 5.

To disable this option set `$conifg['retry'] = FALSE;`

## Examples

#### Full list
All examples avalible listed in the table below.

| File                                                                                                                     | Description                        |
|--------------------------------------------------------------------------------------------------------------------------|------------------------------------|
| [rates_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_create.php)                     | `rates` object creation            |
| [rates_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_retrieve.php)                 | `rates` object(s) retrieve         |
| [labels_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_create.php)                   | `labels` object creation           |
| [labels_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_retrieve.php)               | `labels` object(s) retrieve        |
| [manifests_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_create.php)             | `manifests` object creation        |
| [manifests_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php)         | `manifests` object(s) retrieve     |
| [cancel_labels_create.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_labels_create.php)     | `cancel-labels` object creation    |
| [cancel_labels_retrieve.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_labels_retrieve.php) | `cancel-labels` object(s) retrieve |
| [proxy.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/proxy.php)                                   | Proxy usage                        |
| [error.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/error.php)                                   | Avalible ways to catch/get errors  |
| [response.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/response.php)                             | Avalible output types              |

#### How to run

Download the source code, go to `examples` directory.

Put your API key and region to [credentials.php](https://github.com/postmen/postmen-sdk-php/blob/master/examples/credentials.php)

Check the file you want to run before run. Some require you to set additional variables.

#### Navigation table

For each API method SDK provides PHP wrapper. Use the table below to find SDK method and example that match your need.

<table>
  <tr>
    <th>Model \ Action</th>
    <th>create</th>
    <th>get all</th>
    <th>get by id</th>
  </tr>
  <tr>
    <th>rates</th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_create.php">
      <code>.create('rates', $payload, $opt)</code>
    </a></sub></th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_retrieve.php#L16">
      <code>.get('rates', NULL, $opt)</code>
    </a></sub></th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/rates_retrieve.php#L18">
      <code>.get('rates', $id, $opt)</code>
    </a></sub></th>
  </tr>
  <tr>
    <th>labels</th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_create.php">
      <code>.create('labels', $payload, $opt)</code>
    </a></sub></th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_retrieve.php#L16">
      <code>.get('labels', NULL, $opt)</code>
    </a></sub></th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/labels_retrieve.php#L18">
      <code>.get('labels', $id, $opt)</code>
    </a></sub></th>
  </tr>
  <tr>
    <th>manifest</th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_create.php">
      <code>.create('manifest', $payload, $opt)</code>
    </a></sub></th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php#L16">
      <code>.get('manifest', NULL, $opt)</code>
    </a></sub></th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/manifests_retrieve.php#L18">
      <code>.get('manifest', $id, $opt)</code>
    </a></sub></th>
  </tr>
  <tr>
    <th>cancel-labels</th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_labels_create.php">
      <code>.create('cancel-labels', $payload, $opt)</code>
    </a></sub></th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_labels_retrieve.php#L16">
      <code>.get('cancel-labels', NULL, $opt)</code>
    </a></sub></th>
    <th><sub><a href="https://github.com/postmen/postmen-sdk-php/blob/master/examples/cancel_labels_retrieve.php#L18">
      <code>.get('cancel-labels', $id, $opt)</code>
    </a></sub></th>
  </tr>
</table>

## Testing
If you contribute to SDK, run automated test before you make pull request.

`phpunit --bootstrap tests/bootstrap.php tests/Postmen.php`

## License
Released under the MIT license. See the LICENSE file for details.

## Contributors
- Sunny Chow - [view contributions](https://github.com/postmen/sdk-php/commits?author=sunnychow)
- Marek Narozniak - [view contributions](https://github.com/postmen/sdk-php/commits?author=marekyggdrasil)
