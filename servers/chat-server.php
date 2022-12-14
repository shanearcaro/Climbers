#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');
require_once('../logging/logPublish.php');

require_once('../models/Database.php');


// Check to see if the database connection was successful
try {
	$db = new Database("127.0.0.1", "IT490", "it490user", "it490pass");
} catch (PDOException $e) {
	return array(
		"returnCode" => 0,
		"message" => "[Chat Server] database connection failed."
	);
}

// Fill data array with all arguments
$data = array();
foreach($argv as $arg) {
	array_push($data, $arg);
}

/**
 * Join a chat room as a user. If the chat room that the user is trying to join
 * doesn't exist, create it then join in.
 * @param mixed $data request information + chat information
 * @return array
 * Always returns a returnCode and message. ChatId is also returned if the
 * user is successfully added to the chat
 */
function joinGroup($data): array
{
	global $db;
	// Check if the correct number of variables are given
	if (count($data) != 3) {
		return array(
			"returnCode" => -1,
			"message" => "Failed to join group: required 3 arguments, " . count($data) . " were supplied."
		);
	}

	// Attempt to join group: area, time, userid
	// Chatid will be a number if the chat is found, false if it doesn't exist
	$chatid = $db->getChatId($data[0], $data[1]);

	// If the chat doesn't exist attempt to create it
	if (!$chatid) {
		if (!createChat(array_slice($data, 0, 2))) {
			// If the chat could not be created return the error
			return array(
				"returnCode" => -2,
				"message" => "Chat could not be created."
			);
		}
	}

	// Check to see if the user is already in the chat room
	if (!$db->isUserInChat($data[2], $chatid)) {
		// If user is not in chat room, insert
		if ($db->insertUserIntoChat($data[2], $chatid)) {
			// User succesfully added to chat
			return array(
				"returnCode" => 3,
				"message" => "User successfully inserted into chat",
				"chatid" => $chatid
			);
		}
		else {
			// User unsuccesfully added to chat
			return array(
				"returnCode" => -3,
				"message" => "User failed to insert into chat"
			);
		}
	}

	// User already in chat
	return array(
		"returnCode" => -4,
		"message" => "User already in chat",
		"chatid" => $chatid
	);
}

/**
 * Create a new chat based on an area and a time
 * @param mixed $data request information + chat information
 * @return bool
 * Returns **true** if the chat was created successfully, **false** otherwise
 */
function createChat($data): bool
{
	global $db;
	// Check if the correct number of variables are given
	if (count($data) != 2)
		return false;

	// Check to see if chat was created successfully
	if ($db->createChat($data[0], $data[1]))
		return true;

	// If this point is reached that's because the chat was not created successfully
	return false;
}

/**
 * Get a list of all chat messages sent within a chat based on it's chat id
 * @param mixed $data request information + chat information
 * @return array
 * Always returns a returnCode and message. chatmessages are also returned if the
 * messages are successfully pulled 
 */
function getRoomMessages($data): array
{
	global $db;

	// Check if the correct number of variables are given
	if (count($data) != 1) {
		return array(
			"returnCode" => -1,
			"message" => "Failed to get messages: required 3 arguments, " . count($data) . " were supplied."
		);
	}

	// Return array of all chat messages
	// Messages array can be empty if no messages were sent yet or the chat doesn't exist
	return array(
		"returnCode" => 2,
		"message" => "All chat messages based on chat id: " . $data[0],
		"chatmessages" => $db->getAllChatMessages($data[0])
	);
}

/**
 * Get all blocked users based on a user id
 * @param mixed $data request information + user id
 * @return array
 * Always returns a returnCode and message. Blocked users are also returned if the
 * function was called properly
 */
function getBlockedUsers($data): array
{
	global $db;
	// Check if the correct number of variables are given
	if (count($data) != 1) {
		return array(
			"returnCode" => -1,
			"message" => "Failed to get blocked users: required 1 arguments, " . count($data) . " were supplied."
		);
	}

	// Return array of all blocked users
	// Blocked users array can be empty if the user in question has no one blocked or the user doesn't exist
	return array(
		"returnCode" => 2,
		"message" => "All blocked users based on user id: " . $data[0],
		"blockedusers" => $db->getBlockedUsers($data[0])
	);
}

/**
 * Insert a chat message by a user into a chat
 * @param mixed $data request information + user and message info
 * @return array
 * Always returns a returnCode and message. 
 */
function createMessage($data): array
{
	global $db;
	// Check if the correct number of variables are given
	if (count($data) != 3) {
		return array(
			"returnCode" => -1,
			"message" => "Failed to get messages: required 3 arguments, " . count($data) . " were supplied."
		);
	}

	// Attempt to insert a message into the chat room
	if ($db->insertMessage($data[0], $data[1], $data[2])) {
		// Message was successfully inserted
		return array(
			"returnCode" => 2,
			"message" => "Message successfully inserted into chat"
		);
	}
	else {
		// Message failed to insert, either user is not in the chat room or the chat room doesn't exist 
		return array(
			"returnCode" => -2,
			"message" => "Message failed to insert into chat. Is user in the chat?"
		);
	}
}

/**
 * Get all the chat rooms that a user is currently a member of
 * @param mixed $data Array that contains only the userid
 * @return array
 * Always returns a returnCode and message. Chats array is also returned but it may
 * be empty if the user is not in any chats or the user doesn't exist
 */
function getUserRooms($data): array
{
	global $db;

	// Check if the correct number of variables are given
	if (count($data) != 1) {
		return array(
			"returnCode" => -1,
			"message" => "Failed to get messages: required 1 argument, " . count($data) . " were supplied."
		);
	}

	// Return array of all chats a user is in
	// Chats array may be empty if the user is not in any chats or the user doesn't exist
	return array(
		"returnCode" => 2,
		"message" => "All chat rooms based on user id: " . $data[0],
		"chats" => $db->getAllUserChats($data[0])
	);
}

/**
 * Get the area and time of a chat room based on the chat room id
 * @param mixed $data Array that contains only the chatid
 * @return array
 * Always returns a returnCode and message. Area and time are also returned if
 * the chat room with the supplied chat id exists
 */
function getRoomInfo($data): array
{
	global $db;
	// Check if the correct number of variables are given
	if (count($data) != 1) {
		return array(
			"returnCode" => -1,
			"message" => "Failed to get room info: required 1 argument, " . count($data) . " were supplied."
		);
	}

	// Attempt to find the area and time of a chat
	$roomInfo = $db->getChatInfo($data[0]);

	// If the chat exists
	if ($roomInfo) {
		// Return the area and time of the chat
		return array(
			"returnCode" => 2,
			"message" => "Area and time for chat room with id: " . $data[0],
			"area" => $roomInfo["area"],
			"time" => $roomInfo["time"]
		);
	}
	else {
		// Return the area and time of the chat
		return array(
			"returnCode" => -2,
			"message" => "Chat room with id: " . $data[0] . " does not exist."
		);	
	}

}

/**
 * Given a request type and information to accompany the request, perform
 * a series of steps to honor whatever the client requested.
 * @param mixed $requestData request information + chat information
 * @return array
 * Respond to the client appropriately
 */
function requestProcessor($requestData): array
{
	// Check to see if the request type exists
	if (!isset($requestData[0])) {
		return array(
			"returnCode" => -1,
			"message" => "[Chat Server] received request but not valid type was provided."
		);
	}

	// Get the request	
	$request = $requestData[1];
	$sendData = array_slice($requestData, 2);

	switch ($request) {
		case "join_group":
			return joinGroup($sendData);
		case "get_room_messages":
			return getRoomMessages($sendData);
		case "get_blocked_users":
			return getBlockedUsers($sendData);
		case "create_message":
			return createMessage($sendData);
		case "get_user_rooms":
			return getUserRooms($sendData);
		case "get_room_info":
			return getRoomInfo($sendData);
		default:
			// If the request type doesn't match any set case
			return array(
				"returnCode" => -1,
				"message" => "Request type: " . $request . " is unknown."
			);
	}
}

// Start the server
$server = new rabbitMQServer("../config/config.ini", "chat");
echo "Chat service started..." . PHP_EOL;

// Wait for messages to be sent to the server and handle the request appropriately
$server->process_requests('requestProcessor');
exit();