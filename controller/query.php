#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

$data = array();

// Fill data array with all arguments
foreach($argv as $arg) {
	array_push($data, $arg);
}

// Get the type of request from the data sent
$request_code = $data[1];

// Determine which server to use
switch ($request_code)
{
	case "create_user":
	case "authenticate_user":
	case "reset_password":
		$data[0] = "login";
		break;
	case "join_group":
	case "get_room_messages":
	case "get_blocked_users":
	case "create_message":
	case "get_user_rooms":
	case "get_room_info":
		$data[0] = "chat";
		break;
	default:
		// Quit if request is not recognized
		echo json_encode("Unknown request: " . $request_code);
		exit();
}
// Create the client dynamically based on the request type
$client = new rabbitMQClient("../config/config.ini", $data[0]);

// Send the request
$response = $client->send_request($data);
print_r(json_encode($response));