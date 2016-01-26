<?php
require('credentials.php');

use Postmen\Postmen;

$api = new Postmen($key, $region);

// get a raw json string
$output_json = $api->get('labels', array('raw' => true);

// get a std object
$output_object = $api->get('labels');

// to get an array it requires to initiate
// handler object with that option
$array_api = new Postmen($key, $region, array('array' => true));
$output_array = $array_api->get('labels');
?>
