#!/usr/bin/php
<?php
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

if($argc != 3){
	echo "Incorrect number of arguments! Usage: testRabbitMQClient.php <username> <hash>".PHP_EOL;
	exit();
}
//Save agruments to variables
$username = $argv[1];
$hash = $argv[2];

//Build the request
$request['type'] = "login";
$request['username'] = $username;
$request['hash'] = $hash;

//Send the request
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

