#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is localhost for testing
$client = new rabbitMQClient("../config/loginConfig.ini", "testServer");

//This is for cross VM communication
//$client = new rabbitMQClient("../config/newConfig.ini","testServer");

//There should always be 5 arguments: 
//the script name, username, email, password
if ($argc != 4) {
	echo "Incorrect number of arguments!" . PHP_EOL
		. "Usage: userAddRequesst.php <userid> <areauuid> <time>" . PHP_EOL;
	exit();
}

//Build the request
$request['type'] = "schedule";
$request['userid'] = $argv[1];
$request['areauuid'] = $argv[2];
$request['time'] = $argv[3];

//Send the request
$response = $client->send_request($request);

//echo the return code from the server
//echo $response['returnCode'];
print_r(json_encode($response));
?>