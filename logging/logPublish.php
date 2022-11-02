#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is localhost for testing
//$client = new rabbitMQClient("../config/rabbitConf.ini","testServer");

//This is for cross VM communication
$client = new rabbitMQClient("../config/logConfig.ini","testServer");

//There should always be 2 arguments, the script name and the log message
if($argc != 2){
	echo "Incorrect number of arguments!".PHP_EOL."Usage: logPublish.php <log_message>".PHP_EOL;
	exit();
}

//Build the request
$request['message'] = $argv[1];

//Send the request
//$response = $client->send_request($request);
$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
echo $response["returnCode"];
//echo "\n\n";

//echo $argv[0]." END".PHP_EOL;
?>
