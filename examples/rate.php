<?php
require('credentials.php');

use Postmen\Postmen;

// by default automatic rate limiting is enabled
// which means that if you exceed number of calls
// SDK will wait with processing until next
// call is available
$enabled = new Postmen($key, $region);

// disable automatic rate limiting
// in such case if we exceed number of calls
// SDK will raise an exception
$disabled = new Postmen($key, $region, array('rate' => false));
?>
