<?php
	require_once("models/funcs.php");
	/*
	 * Test class for messages.php and message.php
	 * functions in func.php
	 *
	 *Functions to test:
	 *	-fetchUserID
	 *	-unreadCount
	 *	-fetchMessages
	 *	-fetchUsername
	 *
	 *	-messageIdExists
	 *	-fetchMessageDetails
	 */
	 
	class MessageTest extends PHPUnit_Framework_TestCase
	{	
		public function testFetchUserID()
		{
			$recipientID = fetchUserID("twilford");
			
			$result = true;
			
			if(!isset($recipientID) || $recipientID != 1)
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
		
		public function testUnreadCount()
		{
			$unreadCount = unreadCount(1);
			
			$result = true;
			
			if(!isset($unreadCount) || $unreadCount != 1)
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
				
		public function testFetchMessages()
		{
			$messages = fetchMessages(1, "inbox");
			
			$result = true;
			
			if(!isset($messages) || count($messages) != 4)
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
		
		public function testFetchUsername()
		{
			$testUsername = fetchUsername(1);
			
			$result = true;
			
			if(!isset($testUsername) || $testUsername != "twilford")
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
		
		public function testMessageIdExists()
		{
			$ifMessageExists = messageIdExists(1);
			
			$result = true;
			
			if(!isset($ifMessageExists) || !messageIdExists(1))
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
		
		public function testFetchMessageDetails()
		{
			$message = fetchMessageDetails(1);
			
			$result = true;
			
			if(!isset($message) || $message['subject'] != "Test Subject 1")
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
	}

?>