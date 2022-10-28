#!/usr/bin/php
<?php
require_once('../djmagic/rabbitMQLib.inc');

//Create database connection
$mydb = new mysqli('127.0.0.1','root','toor1029','IT490');

//Check connection
if ($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
}
echo "successfully connected to database".PHP_EOL;

function doLogin($username,$hash)
{
  global $mydb;

  //Lookup hash for provided username in database
  $query = "SELECT hash FROM Users WHERE username='$username';";

  //Execute query
  $response = $mydb->query($query);

  //If query returns anything other than 1 row, return false
  //This would mean 2 users have the same name or the user does not exist
  if($response->num_rows <= 0 || $response->num_rows > 1){
    return false;
  }  

  //If query returns 1 row, check if the hash matches
  else{
    $row = $response->fetch_assoc();
    $hash_db = $row['hash'];
    if (strcmp($hash_db, $hash) == 0){
      return true;
    }
    else{
      return false;
    }
  }
}

function doUserAdd($username,$email,$hash,$salt){
  global $mydb;

  //Check if username already exists
  $query = "SELECT username FROM Users WHERE username='$username';";
  $response = $mydb->query($query);
  if($response->num_rows > 0){
    //return error if username already exists
    return array("returnCode" => '2', 'message'=>"Account with that username already exists");
  }

  //Check if email already exists
  $query = "SELECT email FROM Users WHERE email='$email';";
  $response = $mydb->query($query);
  if($response->num_rows > 0){
    //return error if email does exist
    return array("returnCode" => '2', 'message'=>"Account with that email already exists");
  }

  //Add user to database
  $query = "INSERT INTO Users (username,email,hash,salt) VALUES ('$username','$email','$hash','$salt');";
  $response = $mydb->query($query);
  if($response){
    //Return success
    return array("returnCode" => '1', 'message'=>"User added successfully");
  }
  else{
    //Return failure
    return array("returnCode" => '2', 'message'=>"User add failed");
  }
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return array("returnCode" => '0', 'message'=>"Server received request, but no valid type was specified");
  }
  switch ($request['type'])
  {
    case "login":
      $succ = doLogin($request['username'],$request['hash']);
      if($succ){
        return array("returnCode" => '1', 'message'=>"Login successful!");
      }
      else{
        return array("returnCode" => '2', 'message'=>"Login failed!");
      }
    case "useradd":
      return doUserAdd($request['username'],
                       $request['email'],
                       $request['hash'],
                       $request['salt']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request, but no valid type was specified");
}

$server = new rabbitMQServer("../config/newConfig.ini","testServer");
//$server = new rabbitMQServer("../config/rabbitConf.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>
