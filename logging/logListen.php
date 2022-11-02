#!usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//This is for cross VM communication
$client = new rabbitMQClient("../config/dataConfig.ini","testServer");

function logProcessor($request)
{
    //echo "received request".PHP_EOL;
    var_dump($request);
    
    return array("returnCode" => '1', 'message'=>"Log updated");

    //return array("returnCode" => '0', 'message'=>"Write to log failed!");
}

$server = new rabbitMQServer("../config/logConfig.ini","testServer");
//$server = new rabbitMQServer("../config/rabbitConf.ini","testServer");

$server->process_requests('logProcessor');
exit();
?>