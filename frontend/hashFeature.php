#!/usr/bin/php
<?php

//There should always be 3 arguments, the script name, username, and password
if($argc != 2){
	echo "Incorrect number of arguments!".PHP_EOL."Usage: hashFeature.php <password_text>".PHP_EOL;
	exit();
}

//Save agrument to variable
$password_text = $argv[1];

function hashPassword($passwd, $salt)
{
	$passwd .=$salt;
	$passwd = hash('sha256', $passwd);
	return $passwd;
}

function getSalt() 
{
	$charSet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/\\][{}\'";:?.>,<!@#$%^&*()-_=+|';
	$saltLen = 5;

	$salt = "";	
	for ($i = 0; $i < $saltLen; $i++) 
     	{
        	$salt .= $charSet[mt_rand(0, strlen($charSet) - 1)];
     	}
	
	return $salt;
}

echo hashPassword($password_text, getSalt());
?>