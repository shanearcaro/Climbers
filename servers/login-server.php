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
	if (count($data) != 3) {
		return array(
			"returnCode" => -1,
			"message" => "User failed to add: required 3 arguments, " . count($data) . " were supplied."
		);
	}

	// Attempt to create a new user: username, email, password
	$success = $db->insertUser($data[0], $data[1], $data[2]);

	// Respond to user insert success or failure
	if ($success) {
		return array(
			"returnCode" => 1,
			"message" => "User added successfully.",
			"userid" => $db->getUserId($data[0])
		);
	}

	// If this point is reached that's because a user was not created successfully
	return array(
		"returnCode" => -1, 
		"message" => "User failed to add."
	);
}

/**
 * Authenticate a user based on a supplied username and password.
 * @param mixed $data request information + user information
 * @return array
 * Always returns a returnCode and message. UserId is also returned if the
 * user is successfully authenticated
 */
function authenticateUser($data): array
{
	global $db;
	// Check if the correct number of variables are given
	if (count($data) != 2) {
		return array(
			"returnCode" => -1,
			"message" => "User failed to add: required 2 arguments, " . count($data) . " were supplied."
		);
	}

	// Authenticate user with username and password
	if ($db->authenticateUser($data[0], $data[1])) {
		return array(
			"returnCode" => 2,
			"message" => "User authenticated.",
			"userid" => $db->getUserId($data[0])
		);
	}
	else {
		return array(
			"returnCode" => -2,
			"message" => "User failed to authenticate.",
		);
	}
}

function requestProcessor($requestData)
{
	// Check to see if the request type exists
	if (!isset($requestData[0])) {
		return array(
			"returnCode" => -1,
			"message" => "[Login Server] received request but not valid type was provided."
		);
	}

	// Get the request	
	$request = $requestData[1];
	$sendData = array_slice($requestData, 2);

	switch ($request) {
		case "create_user":
			return createUser($sendData);
		case "authenticate_user":
			return authenticateUser($sendData);
		default:
			// If the request type doesn't match any set case
			return array(
				"returnCode" => -1,
				"message" => "Request type: " . $request . " is unknown."
			);
	}
}

// Start the server
$server = new rabbitMQServer("../config/config.ini", "login");
echo "Login service started..." . PHP_EOL;

// Wait for messages to be sent to the server and handle the request appropriately
$server->process_requests('requestProcessor');
exit();