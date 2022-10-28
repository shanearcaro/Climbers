#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is localhost for testing
//$client = new rabbitMQClient("../config/rabbitConf.ini","testServer");

//This is for cross VM communication
$client = new rabbitMQClient("../config/newConfig.ini","testServer");

//There should always be 5 arguments: 
//the script name, username, email, hash, and salt
if($argc != 5){
	echo "Incorrect number of arguments!".PHP_EOL
    ."Usage: userAddRequesst.php <username> <email> <hash> <salt>".PHP_EOL;
	exit();
}

//Save agruments to variables
$username = $argv[1];
$email = $argv[2];
$hash = $argv[3];
$salt = $argv[4];

//Build the request
$request['type'] = "useradd";
$request['username'] = $username;
$request['email'] = $email;
$request['hash'] = $hash;
$request['salt'] = $salt;

//Send the request
$response = $client->send_request($request);

//echo the return code from the server
echo $response['returnCode'];
?>
