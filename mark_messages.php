<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	$userID = $_GET['id'];
	
	if(!isset($userID) || !userIdExists($userID))
	{
		header("Location: messages.php?m=inbox");
		die();
	}
	
	if($loggedInUser->user_id == $userID)
	{
		// read all messages
		readAllMessages($userID);
	}
	
	header("Location: messages.php?m=inbox");
	die();
?>