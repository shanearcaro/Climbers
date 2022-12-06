#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

$data = array();

// Fill data array with all arguments
foreach($argv as $arg) {
	array_push($data, $arg);
}

// Get the type of request from the data sent
$request_code = $data[0];

// Determine which server to use
switch ($request_code)
{
	case "create_user":
		$data[0] = "login";
		break;
	case "join_group":
	case "get_room_messages":
	case "get_blocked_users":
	case "create_message":
	case "get_user_rooms":
		$data[0] = "chat";
		break;
	default:
		echo json_encode("Failed");
		exit();
}

// Create the client dynamically based on the request type
$client = new rabbitMQClient("../config/config.ini", $data[0]);

// Send the request
$response = $client->send_request($data);
print_r(json_encode($response));