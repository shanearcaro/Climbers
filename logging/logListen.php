#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is for cross VM communication
$client = new rabbitMQClient("../config/dataConfig.ini","testServer");

function logProcessor($request)
{
    //echo "received request".PHP_EOL;
    //var_dump($request);
    
    file_put_contents('logs/log-'.date("m-d-Y-h:i:s").'.txt', $request[message].PHP_EOL , FILE_APPEND | LOCK_EX);
    echo $request['message'];
    
}

$server = new rabbitMQServer("../config/logConfig.ini","testServer");

$server->process_requests('logProcessor');
exit();
?>
