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
			"message" => "User failed to authenticate: required 2 arguments, " . count($data) . " were supplied."
		);
	}

	// Authenticate user with username and temp password
	if ($db->authenticateTempUser($data[0], $data[1])) {
		// Get the current time
		$date = new DateTime();

		// Get the time the reset password request was made
		$authTime = new DateTime($db->getTimestamp($db->getUserId($data[0]))["timestamp"]);

		// Temporary password lives for 15 minutes
		$authTime->modify("+15 minutes");

		// If current time is greater than max allowed time
		if ($date > $authTime) {
			// Temp password has expired
			return array(
				"returnCode" => -3,
				"message" => "Temp password expired.",
				"userid" => $db->getUserId($data[0])
			);
		}

		// Password has not expired yet
		return array(
			"returnCode" => 3,
			"message" => "User authenticated for password reset.",
			"userid" => $db->getUserId($data[0])
		);
	}
	// Authenticate user with username and password
	else if ($db->authenticateUser($data[0], $data[1])) {
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

/**
 * Send a reset password request email to a user based on their email address and username
 * @param mixed $data array containing the emailAddresss and username of a user
 * @return array
 * Always returns a returnCode and message.
 */
function sendResetEmail($data): array
{
	global $db;
	// Check if the correct number of variables are given
	if (count($data) != 1) {
		return array(
			"returnCode" => -1,
			"message" => "User failed to authenticate: required 2 arguments, " . count($data) . " were supplied."
		);
	}

	// Get the user id of the user assuming a username is given
	$userid = $db->getUserId($data[0]);

	/**
	 * Code below works and allows a user to reset their password using their email address instead
	 * of their username. As of now we are not planning on allowing people to log in with their email
	 * address instead of their username, so if you forgot your username and are trying to reset your
	 * password with your email address you still would be unable to sign in.
	 */
	// // If username not found, attempt to get id from email instead
	// if (!$userid)
	// 	$userid = $db->getUserIdFromEmail($data[0]);

	// If the user doesn't exist
	if (!$userid) {
		return array(
			"returnCode" => -1,
			"message" => "User does not exist."
		);
	}

	// Get the user information from the userid
	$user = $db->getUser($userid);

	// Extract email and username
	$emailAddress = $user["email"];
	$username = $user["username"];

	// Import the mail script and create a post object
	require_once "../mail/mail.php";
	$post = new Post();

	// Check to see if email address is valid
	// It should already be validated by front end at this point
	if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
		return array(
			"returnCode" => -2,
			"message" => "Email address is not valid."
		);
	}

    // Generate a random new password for the user to log in to
    $bytes = openssl_random_pseudo_bytes(32);
    $hash = base64_encode($bytes);

	// If email was successfully sent
	if ($post->sendResetPassword($emailAddress, $username, $hash)) {
		// Get the userid from the email
		$userid = $db->getUserIdFromEmail($emailAddress);

		// Set the temporary login password of the user
		$db->setTempPassword($userid, $hash);

		// Need to update the temp password for the user only if the email sends
		return array(
			"returnCode" => 3,
			"message" => "Reset password email was successfully sent.",
		);
	}

	// Email failed to send
	return array(
		"returnCode" => -3, 
		"message" => "Reset password email failed to send."
	);
}

/**
 * Given a user's id number and a new password update the user's password in the database
 * @param mixed $data array containing userid and new password
 * @return array
 * Always returns a returnCode and message.
 */
function resetPassword($data): array
{
	global $db;

	var_dump($data);
	// Check if the correct number of variables are given
	if (count($data) != 2) {
		return array(
			"returnCode" => -1,
			"message" => "User failed to authenticate: required 2 arguments, " . count($data) . " were supplied."
		);
	}

	// If password is updated
	if ($db->updatePassword($data[0], $data[1])) {
		// Reset the temporary password for the user
		$db->resetTempPassword($data[0]);

		// Return success array
		return array(
			"returnCode" => 2,
			"message" => "Password successfully reset"
		);
	}

	// If password didn't update properly
	return array(
		"returnCode" => -2,
		"message" => "Password failed to reset"
	);
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
		case "send_reset_email":
			return sendResetEmail($sendData);
		case "reset_password":
			return resetPassword($sendData);
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