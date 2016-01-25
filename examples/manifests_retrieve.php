<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put ID of a particular manifest
$manifest = "00000000-0000-0000-0000-000000000000";

$api = new Postmen($key, $region);

// retrieve all the manifests
print_r($api->get('manifests'));

// retrieve a particular manifest
print_r($api->get('manifests', $manifest));
?>
