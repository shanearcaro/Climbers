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

  $input = array("area"=>$area, "time"=>$time, "userid"=>$userid);
  var_dump($input);

  //  Check if the chat exists
  $query = "SELECT chatid FROM Chats 
            WHERE area='$area' AND time='$time'";
  $id_query = $mydb->query($query);
  $row = $id_query->fetch_assoc();
  $chatid = $row['chatid'];

  echo "CHAT ID: " . $chatid . PHP_EOL;

  // If chat not found
  if ($id_query->num_rows == 0) {
    // Chat doesn't exist, create
    $query = "INSERT INTO Chats(area, time) 
              VALUES('$area', '$time')";
    $response = $mydb->query($query);

    // Check to see if chat was created
    if (!$response)
      return array("returnCode" => '3', 'message' => "Failed creating chat group");

    // Get chatid
    $query = "SELECT chatid FROM Chats 
              ORDER BY chatid 
              DESC LIMIT 1";
    $response = $mydb->query($query);

    // Check to see if chatid was retrieved
    if (!$response)
      return array("returnCode" => '4', 'message' => "Failed retrieving chatid");
    $row = $response->fetch_assoc();
    $chatid = $row['chatid'];

    // Insert user into created chat
    $query = "INSERT INTO ChatMembers(userid, chatid) 
              VALUES('$userid', '$chatid')";
    $response = $mydb->query($query);

    // Check if user was added, exit
    if ($response)
      return array("returnCode" => '1', 'message' => "User added to chat", 'chatid' => $chatid);
    else
      return array("returnCode" => '2', 'message' => "Failed adding user into chat");
  }

  // else, chat exists
  // Check if user is in chatroom
  $query = "SELECT userid FROM ChatMembers 
            WHERE userid='$userid' AND chatid='$chatid'";
  $response = $mydb->query($query);

  // If user not in chatroom
  if ($response->num_rows == 0) {
    // Add user
    $query = "INSERT INTO ChatMembers(userid, chatid) 
              VALUES('$userid', '$chatid')";
    $response = $mydb->query($query);

    // Check if user was added, exit
    if ($response)
      return array("returnCode" => '1', 'message' => "User added to chat", 'chatid' => $chatid);
    else
      return array("returnCode" => '2', 'message' => "Failed adding user into chat");
  }

  // else, user is in chatroom already
  return array("returnCode" => '5', 'message' => "User in chatroom already!", 'chatid' => $chatid);
}

function doMessage($userid, $chatid, $message)
{
  global $mydb;
  var_dump($userid, $chatid, $message);
  // Insert message
  $query = sprintf("INSERT INTO ChatMessages(userid, chatid, message) 
            VALUES('$userid', '$chatid', '%s')",
            mysqli_real_escape_string($mydb, $message));
  $response = $mydb->query($query);


  // TODO: Before returning, need the server to send a fanout exchange to every client 
  if ($response)
    return array("returnCode" => '1', 'message' => "Message added.");
  return array("returnCode" => '2', 'message' => "Message failed to add");
}

function getMessages($userid, $chatid) {
  global $mydb;

  // var_dump($userid, $chatid);
  // Get all messages
  $query = "SELECT cm.userid, cm.message, cm.timestamp, u.username FROM ChatMessages AS cm 
            INNER JOIN Users AS u ON cm.userid=u.userid 
            WHERE chatid='$chatid'
            ORDER BY cm.timestamp";
  $response = $mydb->query($query);

  // Get all data
  $data = array();
  while ($row = $response->fetch_assoc()) {
    array_push($data, $row['userid']);
    array_push($data, $row['message']);
    array_push($data, $row['timestamp']);
    array_push($data, $row['username']);
  }
  array_push($data, $userid);

  if ($response)
    return array("returnCode" => '1', 'message' => "Message retrieved.", 'data' => $data);
  return array("returnCode" => '2', 'message' => "Message failed to load");
}

function requestProcessor($request)
{
  global $mydb;

  // echo "Received Request[CHAT SERVER]" . PHP_EOL;
  if (!isset($request['type'])) {
    return array("returnCode" => '0', 'message' => "Server received request, but no valid type was specified");
  }
  switch ($request['type']) {
    case "create_chat":
      return doChatGroup($request['area'], $request['time'], $request['userid']);
    case "create_message":
      return doMessage($request['userid'], $request['chatid'], $request['message']);
    case "get_messages":
      return getMessages($request['userid'], $request['chatid']);
  }
  return array("returnCode" => '0', 'message' => "Server received request, but no valid type was specified");
}

$server = new rabbitMQServer("../config/chatConfig.ini", "testServer");

$server->process_requests('requestProcessor');
exit();
?>