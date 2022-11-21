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
echo "Connected to database [Social server]" . PHP_EOL;


#-----------Begin-Chat-Requests---------------------------------------
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
    $query = "SELECT chatid, area, time FROM Chats 
              ORDER BY chatid 
              DESC LIMIT 1";
    $response = $mydb->query($query);

    // Check to see if chatid was retrieved
    if (!$response)
      return array("returnCode" => '4', 'message' => "Failed retrieving chatid");
    $row = $response->fetch_assoc();
    $chatid = $row['chatid'];
    $area = $row['area'];
    $time = $row['time'];

    // Insert user into created chat
    $query = "INSERT INTO ChatMembers(userid, chatid) 
              VALUES('$userid', '$chatid')";
    $response = $mydb->query($query);

    // Check if user was added, exit
    if ($response)
      return array("returnCode" => '1', 'message' => "User added to chat", 'chatid' => $chatid, 'area' => $area, 'time' => $time);
    else
      return array("returnCode" => '2', 'message' => "Failed adding user into chat");
  }

  // else, chat exists
  // Check if user is in chatroom
  $query = "SELECT cm.userid, c.area, c.time FROM ChatMembers AS cm
            INNER JOIN Chats AS c ON cm.chatid=c.chatid
            WHERE cm.userid='$userid' AND cm.chatid='$chatid'";
  $response = $mydb->query($query);
  $row = $response->fetch_assoc();

  // If user not in chatroom
  if ($response->num_rows == 0) {
    $area = $row['area'];
    $time = $row['time'];

    // Add user
    $query = "INSERT INTO ChatMembers(userid, chatid) 
              VALUES('$userid', '$chatid')";
    $response = $mydb->query($query);
    $row = $response->fetch_assoc();
    $area = $row['area'];
    $time = $row['time'];

    // Check if user was added, exit
    if ($response)
      return array("returnCode" => '1', 'message' => "User added to chat", 'chatid' => $chatid, 'area' => $area, 'time' => $time);
    else
      return array("returnCode" => '2', 'message' => "Failed adding user into chat");
  }

  // else, user is in chatroom already
  return array("returnCode" => '5', 'message' => "User in chatroom already!", 'chatid' => $chatid, 'area' => $area, 'time' => $time);
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

function getBlockedUsers($userid)
{
  global $mydb;

  // Get all blocked users
  $query = "SELECT blockid FROM UserBlocks 
            WHERE userid='$userid'";
  $response = $mydb->query($query);

  if ($response)
    return array("returnCode" => '1', 'message' => "Blocked Users retrieved.", 'data' => $response->fetch_all());
  return array("returnCode" => '2', 'message' => "Blocked Users failed to load");
}

function getChatrooms($userid)
{
  global $mydb;

  // Get all chats user is in
  $query = "SELECT cm.userid, cm.chatid, c.area, c.time FROM ChatMembers AS cm
            INNER JOIN Chats AS c ON cm.chatid=c.chatid 
            WHERE cm.userid='$userid'";
  $response = $mydb->query($query);

  if ($response)
    return array("returnCode" => '1', 'message' => "Chats retrieved.", 'data' => $response->fetch_all());
  return array("returnCode" => '2', 'message' => "Chats failed to load");
}
#-------------End-Chat-Requests---------------------------------------

#---------Begin-Social-Requests---------------------------------------
function getFriends($userid){
    global $mydb;
    
    #Query to retrieve all rows with $userid in either column
    $query = "";
    $response = $mydb->query($query);

    #Should return a list of all of this users friend's ids
    #Some processing will have to be done here
    if ($response)
      return array("returnCode" => '1', 'data' => '');
     
      
   
    return array("returnCode" => '2', 'message' => '');
}

fucntion getStats($userid)
{
    global $mydb;

    #Query to retrieve all stat entries for this user
    $query = "";
    $response = $mydb->query($query);

    if ($response)
        #Should return a json object containing all the stat entries
        #for this user
        return array("returnCode" => '1', 'data' => '');
    return array("returnCode" => '2', 'message' => '');

}
#-----------End-Social-Requests---------------------------------------

#Request Processor is the heart of the server script, all other 
#functions are called from here
function requestProcessor($request){
    global $mydb;

    echo "Recieved Request [Social Server]".PHP_EOL;
    var_dump($request);
    
    #Guard against unset type
    if(!isset($request['type'])){
        processLog("Request received with invalid type ".$request['type']);
        return array(
            "returnCode" => '0', 
            'message'=>"[Stats Server] Received request, but no valid type was specified"
        );
    }

    #Check what the request is for and do that thing
    switch ($request['type']) {
        #Chat Request Types
        case "create_chat":
          return doChatGroup($request['area'], $request['time'], $request['userid']);
        case "create_message":
          return doMessage($request['userid'], $request['chatid'], $request['message']);
        case "get_messages":
          return getMessages($request['userid'], $request['chatid']);
        case "get_blocked":
          return getBlockedUsers($request['userid']);
        case "get_chatrooms":
          return getChatrooms($request['userid']);

        #Social Page Requests
        case "get_friends":
            return;
        case "get_stats":
            return;
      }

      #If none of the switch options are executed, the set type was
      #invalid/unsupported 
      return array("returnCode" => '0', 'message' => "Server received request, but no valid type was specified");

}


// $server = new rabbitMQServer("../config/newConfig.ini","testServer");
$server = new rabbitMQServer("../config/loginConfig.ini", "statServer");
echo "Social service started..." . PHP_EOL;

$server->process_requests('requestProcessor');
exit();
?>