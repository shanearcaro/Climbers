#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is localhost for testing
$client = new rabbitMQClient("../config/loginConf.ini","testServer");

//This is for cross VM communication
//$client = new rabbitMQClient("../config/newConfig.ini","testServer");

//There should always be 5 arguments: 
//the script name, username, email, password
if($argc != 4){
	echo "Incorrect number of arguments!".PHP_EOL
    ."Usage: userAddRequesst.php <username> <email> <password>".PHP_EOL;
	exit();
}

//Build the request
$request['type'] = "useradd";
$request['username'] = $argv[1];
$request['email'] = $argv[2];
$request['hash'] = $argv[3];

//Send the request
$response = $client->send_request($request);

//echo the return code from the server
//echo $response['returnCode'];

print_r(json_encode($response));
?>
