<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of a particular label
$label = "00000000-0000-0000-0000-000000000000";

$api = new Postmen($key, $region);

// get all the cancelled labels
print_r($api->get('cancel-labels'));

// get a particular cancelled label
print_r($api->get('cancel-labels', $label));
?>
