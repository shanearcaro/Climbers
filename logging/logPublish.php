#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

$config = parse_ini_file("userConfig.ini");

//This is for cross VM communication
$client = new rabbitMQClient("../config/LogConfig.ini", $config['name']);

//This is a function-ized version of the code below that is used by
//server scripts to send messages to the logging exchange
function writeLocalLog($message){
	//This is the same function call that is used by the logging server
	//Because we are pubishing the message when running this, we need to
	//locally write the log as well
    file_put_contents('logs/log-'.date("Y-m-d").'.txt', "[".date("h:i:s")."]".$message.PHP_EOL , FILE_APPEND | LOCK_EX);
}

function processLog($message){
	writeLocalLog($message);
	global $client;
	//Build the request
	$request['message'] = $message;
	//Publish the log message
	$response = $client->publish($request);
	return $response;
}

//This checks if the script is being run directly or if it's being 
//included by another script
if (get_included_files()[0] == __FILE__) {
	//There should always be 2 arguments, the script name and the log message
	if ($argc != 2) {
		echo "Incorrect number of arguments!" . PHP_EOL . "Usage: logPublish.php <log_message>" . PHP_EOL . PHP_EOL;
		exit();
	}

	//Write the log locally
	writeLocalLog($argv[1]);

	//Build the request
	$request['message'] = $argv[1];

	//Publish the log message
	$response = $client->publish($request);

	//echo "client received response: ".PHP_EOL;
	echo $response . PHP_EOL;
}
?>