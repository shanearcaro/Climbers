#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');
require_once('../logging/logPublish.php');

require_once('../models/Database.php');

// Check to see if the database connection was successful$db = new Database("127.0.0.1", "IT490", "it490user", "it490pass");
try {
	$db = new Database("127.0.0.1", "IT490", "it490user", "it490pass");
} catch (PDOException $e) {
	return array(
		"returnCode" => 0,
		"message" => "[Login Server] database connection failed."
	);
}

// Fill data array with all arguments
$data = array();
foreach($argv as $arg) {
	array_push($data, $arg);
}

/**
 * Add a new user into the database based off the username, email address,
 * and password provided. The username and email address must be unique, if it
 * already exists in the database the user will not be created.
 * @param mixed $data request information + user information
 * @return array
 * Always returns a returnCode and message. UserId is also returned if the
 * user is successfully created
 */
function createUser($data): array
{
	global $db;

	// Check if the correct number of variables are given
	if (count($data) != 5) {
		return array(
			"returnCode" => -1,
			"message" => "User failed to add: required 5 arguments, " . count($data) . " were supplied."
		);
	}

	// Attempt to create a new user: username, email, password
	$success = $db->insertUser($data[2], $data[3], $data[4]);

	// Respond to user insert success or failure
	if ($success) {
		return array(
			"returnCode" => 1,
			"message" => "User added successfully.",
			"userid" => $db->getUserId($data[2])
		);
	}

	// If this point is reached that's because a user was not created successfully
	return array(
		"returnCode" => -1, 
		"message" => "User failed to add."
	);
}

function requestProcessor($data)
{
	// Check to see if the request type exists
	if (!isset($data[0])) {
		return array(
			"returnCode" => -1,
			"message" => "[Login Server] received request but not valid type was provided."
		);
	}

	// Get the request	
	$request = $data[1];

	switch ($request) {
		case "create_user":
			return createUser($data);
		default:
			// If the request type doesn't match any set case
			return array(
				"returnCode" => -1,
				"message" => "Request type: " . $request . " is unknown."
			);
	}
}

// Start the server
$server = new rabbitMQServer("../config/login-config.ini", "login");
echo "Login service started..." . PHP_EOL;

// Wait for messages to be sent to the server and handle the request appropriately
$server->process_requests('requestProcessor');
exit();