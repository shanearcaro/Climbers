#!/usr/bin/php
<?php
require_once('../../djmagic/rabbitMQLib.inc');

//This is localhost for testing
//$client = new rabbitMQClient("../config/rabbitConf.ini","testServer");

//This is for cross VM communication
$client = new rabbitMQClient("../config/dataConfig.ini","testServer");

//There should always be 2 arguments, the script name and the state
// if($argc != 2){
// 	echo "Incorrect number of arguments!".PHP_EOL."Usage: apiRequest.php <State>".PHP_EOL;
// 	exit();
// }

//Build the request
$request['type'] = "bottomareas";
$request['state'] = "New Jersey";
// $request['state'] = $argv[1];

//Send the request
$response = $client->send_request($request);
//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
echo $response["returnCode"];
//echo "\n\n";

//echo $argv[0]." END".PHP_EOL;
?>
