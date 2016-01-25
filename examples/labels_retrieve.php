<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of a particular label
$label = "00000000-0000-0000-0000-000000000000";

$api = new Postmen($key, $region);

// get all the labels
print_r($api->get('labels'));

// get a particular label
print_r($api->get('labels', $label));
?>
