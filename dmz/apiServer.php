#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

function runPythonScript($path){
    $cmd = escapeshellcmd("python3 $path");
    $output = shell_exec($cmd);
    return $output;
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return array("returnCode" => '0', 'message'=>"Server received request, but no valid type was specified");
  }
  switch ($request['type'])
  {
    case "bottomareas":
      return array("returnCode" => '1', 'message'=>runPythonScript("getLowestAreas.py"));
  }
  return array("returnCode" => '0', 'message'=>"Unhandled error");
}

$server = new rabbitMQServer("../config/dataConfig.ini","testServer");
//$server = new rabbitMQServer("../config/rabbitConf.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>
