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
			////////////Setup the test database///////////////////
		
			$db_host = "localhost"; //Host address (most likely localhost)
			$db_name = "website_test"; //Name of Database
			$db_user = "websiteUser"; //Name of database user
			$db_pass = "4WPXGzCUm2y2TeG7"; //Password for database user
			$db_table_prefix = "apt_";
			$GLOBALS['db_table_prefix'] = $db_table_prefix;

			$errors = array();
			$successes = array();
			$GLOBALS['errors'] = $errors;
			$GLOBALS['successes'] = $successes;

			//Create a new mysqli object with database connection parameters
			$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
			$GLOBALS['mysqli'] = $mysqli;

			if(mysqli_connect_errno())
			{
				echo "Connection Failed: " . mysqli_connect_errno();
				$this->assertTrue(false);
				exit();
			}

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
			////////////Setup the test database///////////////////
		
			$db_host = "localhost"; //Host address (most likely localhost)
			$db_name = "website_test"; //Name of Database
			$db_user = "websiteUser"; //Name of database user
			$db_pass = "4WPXGzCUm2y2TeG7"; //Password for database user
			$db_table_prefix = "apt_";
			$GLOBALS['db_table_prefix'] = $db_table_prefix;

			$errors = array();
			$successes = array();
			$GLOBALS['errors'] = $errors;
			$GLOBALS['successes'] = $successes;

			//Create a new mysqli object with database connection parameters
			$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
			$GLOBALS['mysqli'] = $mysqli;

			if(mysqli_connect_errno())
			{
				echo "Connection Failed: " . mysqli_connect_errno();
				$this->assertTrue(false);
				exit();
			}

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
			////////////Setup the test database///////////////////
		
			$db_host = "localhost"; //Host address (most likely localhost)
			$db_name = "website_test"; //Name of Database
			$db_user = "websiteUser"; //Name of database user
			$db_pass = "4WPXGzCUm2y2TeG7"; //Password for database user
			$db_table_prefix = "apt_";
			$GLOBALS['db_table_prefix'] = $db_table_prefix;

			$errors = array();
			$successes = array();
			$GLOBALS['errors'] = $errors;
			$GLOBALS['successes'] = $successes;

			//Create a new mysqli object with database connection parameters
			$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
			$GLOBALS['mysqli'] = $mysqli;

			if(mysqli_connect_errno())
			{
				echo "Connection Failed: " . mysqli_connect_errno();
				$this->assertTrue(false);
				exit();
			}

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
			////////////Setup the test database///////////////////
		
			$db_host = "localhost"; //Host address (most likely localhost)
			$db_name = "website_test"; //Name of Database
			$db_user = "websiteUser"; //Name of database user
			$db_pass = "4WPXGzCUm2y2TeG7"; //Password for database user
			$db_table_prefix = "apt_";
			$GLOBALS['db_table_prefix'] = $db_table_prefix;

			$errors = array();
			$successes = array();
			$GLOBALS['errors'] = $errors;
			$GLOBALS['successes'] = $successes;

			//Create a new mysqli object with database connection parameters
			$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
			$GLOBALS['mysqli'] = $mysqli;

			if(mysqli_connect_errno())
			{
				echo "Connection Failed: " . mysqli_connect_errno();
				$this->assertTrue(false);
				exit();
			}

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
			////////////Setup the test database///////////////////
		
			$db_host = "localhost"; //Host address (most likely localhost)
			$db_name = "website_test"; //Name of Database
			$db_user = "websiteUser"; //Name of database user
			$db_pass = "4WPXGzCUm2y2TeG7"; //Password for database user
			$db_table_prefix = "apt_";
			$GLOBALS['db_table_prefix'] = $db_table_prefix;

			$errors = array();
			$successes = array();
			$GLOBALS['errors'] = $errors;
			$GLOBALS['successes'] = $successes;

			//Create a new mysqli object with database connection parameters
			$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
			$GLOBALS['mysqli'] = $mysqli;

			if(mysqli_connect_errno())
			{
				echo "Connection Failed: " . mysqli_connect_errno();
				$this->assertTrue(false);
				exit();
			}

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
			////////////Setup the test database///////////////////
		
			$db_host = "localhost"; //Host address (most likely localhost)
			$db_name = "website_test"; //Name of Database
			$db_user = "websiteUser"; //Name of database user
			$db_pass = "4WPXGzCUm2y2TeG7"; //Password for database user
			$db_table_prefix = "apt_";
			$GLOBALS['db_table_prefix'] = $db_table_prefix;

			$errors = array();
			$successes = array();
			$GLOBALS['errors'] = $errors;
			$GLOBALS['successes'] = $successes;

			//Create a new mysqli object with database connection parameters
			$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
			$GLOBALS['mysqli'] = $mysqli;

			if(mysqli_connect_errno())
			{
				echo "Connection Failed: " . mysqli_connect_errno();
				$this->assertTrue(false);
				exit();
			}

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