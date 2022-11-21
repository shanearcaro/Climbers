#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');
require_once('../logging/logPublish.php');

//Create database connection
$mydb = new mysqli('127.0.0.1', 'root', 'toor1029', 'IT490');

//Check connection
if ($mydb->errno != 0) {
    echo "failed to connect to database: " . $mydb->error . PHP_EOL;
    exit(0);
  }
echo "Connected to database [Stats server]" . PHP_EOL;

function requestProcessor($request){
    global $mydb;

    echo "Recieved Request [Stats Server]".PHP_EOL;
    var_dump($request);
    
    if(!isset($request['type'])){
        processLog("Request received with invalid type ".$request['type']);
        return array(
            "returnCode" => '0', 
            'message'=>"[Stats Server] Received request, but no valid type was specified"
        );
    }
}


// $server = new rabbitMQServer("../config/newConfig.ini","testServer");
$server = new rabbitMQServer("../config/loginConfig.ini", "statServer");
echo "Stats service started..." . PHP_EOL;

$server->process_requests('requestProcessor');
exit();
?>
