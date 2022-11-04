#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

$config = parse_ini_file("userConfig.ini");

//This is for cross VM communication
$client = new rabbitMQClient("../config/".$config['name']."_logConfig.ini","testServer");

//This is a function-ized version of the code below that is used by
//server scripts to send messages to the logging exchange
function processLog($message){
	//Build the request
	$request['message'] = $message;
	//Publish the log message
	$response = $client->publish($request);
	return $response;
}

//This checks if the script is being run directly or if it's being 
//included by another script
if(get_included_files()[0] == __FILE__) {
	//There should always be 2 arguments, the script name and the log message
	if($argc != 2){
		echo "Incorrect number of arguments!".PHP_EOL."Usage: logPublish.php <log_message>".PHP_EOL.PHP_EOL;
		exit();
	}

	//Build the request
	$request['message'] = $argv[1];

	//Publish the log message
	$response = $client->publish($request);

	//echo "client received response: ".PHP_EOL;
	echo $response.PHP_EOL;
}
?>
