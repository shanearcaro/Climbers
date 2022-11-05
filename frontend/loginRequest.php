#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is localhost for testing
$client = new rabbitMQClient("../config/rabbitConf.ini","testServer");

//This is for cross VM communication
//$client = new rabbitMQClient("../config/newConfig.ini","testServer");

//There should always be 3 arguments, the script name, username, and password
if($argc != 3){
	echo "Incorrect number of arguments!".PHP_EOL."Usage: loginRequest.php <username> <hash>".PHP_EOL;
	exit();
}

//Build the request
$request['type'] = "login";
$request['username'] = $argv[1];
$request['hash'] = $argv[2];

//Send the request
$response = $client->send_request($request);
//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
//echo $response["returnCode"];

print_r(json_encode($response));
//echo "\n\n";

//echo $argv[0]." END".PHP_EOL;
?>
