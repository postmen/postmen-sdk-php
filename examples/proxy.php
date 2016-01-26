<?php
require('credentials.php');

use Postmen\Postmen;

$proxy = array(
	"host" => "proxyserver.com",	// required
	"port" => 9999,			// optional
	"username" => "user",		// optional
	"password" => "pass"		// optional
);

// putting proxy in the constructor sets
// it by default for all calls
$api = new Postmen($key, $region, array('proxy' => $proxy));

// putting the proxy object in a call
// will make it be used only once, this
// can also be used to disable it for purpose
// of a single call
$result = $api->get('labels', array('proxy' => $proxy));
?>
