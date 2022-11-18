#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

$config = parse_ini_file("userConfig.ini");
echo "Log service started..." . PHP_EOL;

function logProcessor($request)
{
    //echo "received request".PHP_EOL;
    var_dump($request);
    
    file_put_contents('logs/log-'.date("Y-m-d").'.txt', "[".date("h:i:s")."]".$request['message'].PHP_EOL , FILE_APPEND | LOCK_EX);
    echo PHP_EOL.$request['message'].PHP_EOL;
}

$server = new rabbitMQServer("../config/".$config['name']."_logConfig.ini","testServer");

$server->process_requests('logProcessor');
exit();
?>
