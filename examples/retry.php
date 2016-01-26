<?php
require('credentials.php');

use Postmen\Postmen;

// by default automatic retry is enabled
// if API returns erro which is retryable
// SDK will wait wait a while and try again
// maximum five times
// first delay is 1s and is multiply by
// the factor of 2 wich each retry
$enabled = new Postmen($key, $region);

// disable automatic retry
// no matter if error will be retryable or not
// SDK will raise an exception
$disabled = new Postmen($key, $region, array('retry' => false));
?>
