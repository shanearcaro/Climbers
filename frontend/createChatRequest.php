#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is localhost for testing
$client = new rabbitMQClient("../config/chatConfig.ini", "testServer");

//This is for cross VM communication
//$client = new rabbitMQClient("../config/newConfig.ini","testServer");

//There should always be 4 arguments: 
//the script name, area, time, and userid
if ($argc != 4) {
	echo "Incorrect number of arguments!" . PHP_EOL
		. "Usage: createChatRequest.php <area> <time> <userid>" . PHP_EOL;
	exit();
}

//Build the request
$request['type'] = "create_chat";
$request['area'] = $argv[1];
$request['time'] = $argv[2];
$request['userid'] = $argv[3];

//Send the request
$response = $client->send_request($request);

//echo the return code from the server
//echo $response['returnCode'];
print_r(json_encode($response));
?>