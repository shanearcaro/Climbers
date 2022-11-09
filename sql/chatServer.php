#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//Create database connection
$mydb = new mysqli('127.0.0.1', 'it490user', 'it490pass', 'IT490');

//Check connection
if ($mydb->errno != 0) {
  echo "failed to connect to database: " . $mydb->error . PHP_EOL;
  exit(0);
}
echo "Connected to database [Chat server]" . PHP_EOL;

function doChatGroup($area, $time, $userid)
{
  global $mydb;

  //Check if user already has a schedule for this area
  $query = "INSERT INTO MessageGroup(area, time, userid), VALUES ('$area', 'time', '')";
  $response = $mydb->query($query);
  if ($response->num_rows > 0) {
    //return error if user already has a schedule for this area
    return array("returnCode" => '2', 'message' => "Message Group Already Exists for this time");
  }

  //Add schedule to database
//   $query = "INSERT INTO Schedules (userid,areauuid,goaldate) VALUES ($userid,'$areauuid','$goaldate');";
//   $response = $mydb->query($query);
//   if ($response) {
//     //Return success
//     return array("returnCode" => '1', 'message' => "Schedule added successfully");
//   } else {
//     //Return failure{
//   }
}

function requestProcessor($request)
{
  global $mydb;

  echo "received request" . PHP_EOL;
  var_dump($request);
  if (!isset($request['type'])) {
    return array("returnCode" => '0', 'message' => "Server received request, but no valid type was specified");
  }
  switch ($request['type']) {
  }
  return array("returnCode" => '0', 'message' => "Server received request, but no valid type was specified");
}

$server = new rabbitMQServer("../config/chatConfig.ini", "testServer");

$server->process_requests('requestProcessor');
exit();
?>