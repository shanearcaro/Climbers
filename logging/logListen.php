#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

$config = parse_ini_file("userConfig.ini");

function logProcessor($request)
{
    //echo "received request".PHP_EOL;
    var_dump($request);
    
    file_put_contents('logs/log-'.date("Y-m-d").'.txt', "[".date()."]".$request['message'].PHP_EOL , FILE_APPEND | LOCK_EX);
    echo $request['message'];
    
}

$server = new rabbitMQServer("../config/".$config['name']."_logConfig.ini","testServer");

$server->process_requests('logProcessor');
exit();
?>
