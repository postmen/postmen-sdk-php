<?php
require('credentials.php');

use Postmen\Postmen;

// TODO put the ID of the label you wish to cancel
$label = "00000000-0000-0000-0000-000000000000";

$api = new Postmen($key, $region);
print_r($api->cancel($id));
?>
