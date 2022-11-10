#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is localhost for testing
$client = new rabbitMQClient("../config/chatConfig.ini", "testServer");

//This is for cross VM communication
//$client = new rabbitMQClient("../config/newConfig.ini","testServer");

//There should always be 4 arguments: 
//the script name, userid, chatid, message
if ($argc != 3) {
	echo "Incorrect number of arguments!" . PHP_EOL
		. "Usage: createMessageRequest.php <userid> <chatid>" . PHP_EOL;
	exit();
}

//Build the request
$request['type'] = "get_messages";
$request['userid'] = $argv[1];
$request['chatid'] = $argv[2];

//Send the request
$response = $client->send_request($request);

//echo the return code from the server
//echo $response['returnCode'];
print_r(json_encode($response));
?>