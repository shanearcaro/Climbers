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

  var_dump($area, $time, $userid);
  //Check if user is in this group
  $query = "SELECT c.chatid FROM Chats AS c 
    JOIN ChatMembers as cm on c.chatid=cm.chatid 
    WHERE c.area='$area' AND c.time='$time' AND cm.userid='$userid'";
  $response = $mydb->query($query);
  $row = $response->fetch_assoc();
  $chatid = $row['chatid'];

  echo "Chat Operations[CHAT SERVER]" . PHP_EOL;
  // User is already in this group
  if ($response->num_rows > 0)
    return array("returnCode" => '2', 'message' => "User already in chat", 'chatid' => $chatid);

  // Check if group exists
  $query = "SELECT chatid FROM Chats WHERE area='$area' AND time='$time'";
  $response = $mydb->query($query);

  // Group exists, join
  if ($response->num_rows > 0) {
    $row = $response->fetch_assoc();
    $chatid = $row['chatid'];

    $query = "INSERT INTO ChatMembers(userid, chatid) VALUES('$userid', '$chatid')";
    $response = $mydb->query($query);

    // User added
    if ($response)
      return array("returnCode" => '1', 'message' => "User added to chat", 'chatid' => $chatid);
    else
      return array("returnCode" => '3', 'message' => "Failed adding user into chat");
  }

  // Chat doesn't exist, create and join
  $query = "INSERT INTO Chats(area, time) VALUES('$area', '$time')";
  $response = $mydb->query($query);

  // Check to see if chat was created
  if (!$response)
    return array("returnCode" => '4', 'message' => "Failed creating chat group");

  $query = "SELECT chatid FROM Chats ORDER BY chatid DESC LIMIT 1";
  $response = $mydb->query($query);

  // Check to see if chat was retrieved
  if (!$response)
    return array("returnCode" => '5', 'message' => "Failed retrieving chatid");
  $row = $response->fetch_assoc();
  $chatid = $row['chatid'];

  // Insert user into created chat
  $query = "INSERT INTO ChatMembers(userid, chatid) VALUES('$userid', '$chatid')";
  $response = $mydb->query($query);

  if ($response)
    return array("returnCode" => '1', 'message' => "User added to chat", 'chatid' => $chatid);
  else
    return array("returnCode" => '3', 'message' => "Failed adding user into chat");
}

function doMessage($userid, $chatid, $message)
{
  global $mydb;

  var_dump($userid, $chatid, $message);
  // Insert message
  $query = "INSERT INTO ChatMessages(userid, chatid, message) VALUES('$userid', '$chatid', '$message')";
  $response = $mydb->query($query);

  if ($response)
    return array("returnCode" => '1', 'message' => "Message added.");
  return array("returnCode" => '2', 'message' => "Message failed to add");
}

function getMessages($userid, $chatid) {
  global $mydb;

  var_dump($userid, $chatid);
  // Get all messages
  $query = "SELECT cm.userid, cm.message, cm.timestamp, u.username FROM ChatMessages cm INNER JOIN Users as u on cm.userid=u.userid WHERE chatid='$chatid'";
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

  echo "Received Request[CHAT SERVER]" . PHP_EOL;
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