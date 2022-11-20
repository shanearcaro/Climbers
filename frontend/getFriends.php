#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is localhost for testing
$client = new rabbitMQClient("../config/dataConfig.ini", "testServer");

//This is for cross VM communication
//$client = new rabbitMQClient("../config/newConfig.ini","testServer");

//There should always be 2 arguments: 
//the script name, userid
if ($argc != 2) {
	echo "Incorrect number of arguments!" . PHP_EOL
		. "Usage: getFriends.php <userid>" . PHP_EOL;
	exit();
}

//Build the request
$request['type'] = "get_userstats";
$request['userid'] = $argv[1];

//Send the request
$response = $client->send_request($request);

//echo the return code from the server
//echo $response['returnCode'];
print_r(json_encode($response));
?>