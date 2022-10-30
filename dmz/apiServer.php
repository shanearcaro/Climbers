#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

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
      $state = $request['state']
	  $
  }
  return array("returnCode" => '0', 'message'=>"Server received request, but no valid type was specified");
}

$server = new rabbitMQServer("../config/newConfig.ini","testServer");
//$server = new rabbitMQServer("../config/rabbitConf.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>
