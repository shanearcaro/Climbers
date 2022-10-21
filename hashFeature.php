<?php

function hashPasswd($passwd, $salt)
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
?>
