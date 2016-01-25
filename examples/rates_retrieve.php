<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of a particular rate
$rate = "00000000-0000-0000-0000-000000000000";

$api = new Postmen($key, $region);

// retrieve all the rates
print_r($api->get('rates'));

// retrieve a particular rate
print_r($api->get('rates', $rate));
?>
