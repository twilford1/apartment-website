<?php

//require "models/config.php";
require_once "models/funcs.php";

/*
 *Test class for roommates.php; Functions used for roommates.php
 *are included in funcs.php.  These are the functions
 *that will be unit tested.  
 *
 ********Issues with PHPUnit require that
 *db setup is managed within this test file instead of using
 *config.php.
 *
 *Functions to test:
 *	-addRoommate
 *	-removeRoommate
 *	-fetchRoommates (already tested in CostTest.php)
 */
class RoommatesTest extends PHPUnit_Framework_TestCase
{	
	//set up each test
	public function setUp()
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
			exit();
		}
		
		/////////////Modify the test database for test cases///////////////
		
		//add test roommates
		insertTestRoommate(-1, -2);
		insertTestRoommate(-1, -3);
		insertTestRoommate(-2, -3);
		insertTestRoommate(-2, -1);
		insertTestRoommate(-3, -1);
		insertTestRoommate(-3, -2);
		
		//save all roommates of test logged-in user
		$GLOBALS['all_roommates'] = getTestRoommates(-1);
		$GLOBALS['user_id'] = -1;
	}
	
	//test that roommates can be added to the db
	public function testAddRoommateToDB()
	{
		//inform the user
		echo("------------------------\n");
		echo("RoommatesTest Case 1: testAddRoommateToDB\n");
		
		//get all roommates and user id
		global $all_roommates, $user_id;
		
		//add a roommate
		addRoommate(-1, -4);
		
		//get all roommates again
		$somanyroommates = getTestRoommates($user_id);
		
		//delete test roommate
		deleteTestRoommate(-1, -4);
		
		//assert that the number of roommates has gone up
		$this->assertTrue((count($all_roommates)) == count($somanyroommates)-1);
		
		echo("Roommate is added to the DB for a test user:\n");
		echo(((count($all_roommates)) == count($somanyroommates)-1)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that existing roommates can be deleted
	public function testRemoveRoommateFromDB()
	{
		//inform the user
		echo("------------------------\n");
		echo("RoommateTest Case 3: testRemoveRoommateFromDB\n");
		
		global $user_id;
		
		//add a roommate to test
		insertTestRoommate(-1, -4);
		
		//get all roommates
		$somanyroommates = getTestRoommates($user_id);
		
		//try to delete the roommate
		removeRoommate(-1,-4);
		
		//get all roommates again
		$somanyroommates2 = getTestRoommates($user_id);
		
		//make sure there is one less tuple
		$this->assertTrue((count($somanyroommates) - 1) == count($somanyroommates2));
		
		echo("Roommate was deleted successfully:\n");
		echo(((count($somanyroommates) - 1) == count($somanyroommates2))?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//reset the database after each test
	public function teardown()
	{
		//remove test roommates
		deleteTestRoommate(-1, -2);
		deleteTestRoommate(-1, -3);
		deleteTestRoommate(-2, -3);
		deleteTestRoommate(-2, -1);
		deleteTestRoommate(-3, -1);
		deleteTestRoommate(-3, -2);
	}
}

/*
 *insertTestRoommate: inserts one roommate tuple
 */
function insertTestRoommate($user_id, $roommate_id)
{
	global $mysqli, $db_table_prefix;
	//insert apartment
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."roommates (
		user_id,
		roommate_id)
		VALUES (
		?,
		?)");		
	$stmt->bind_param("ii", $user_id, $roommate_id);
	$result = $stmt->execute();
	$stmt->close();	
}

/*
 * getTestRoommate: returns all roommates of a test user
 */
function getTestRoommates($user_id)
{
	global $mysqli, $db_table_prefix;
	
	//return apartment tuple
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."roommates
		WHERE user_id = ?
		");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($id, $roommate_id);
	
	$row = NULL;
	
	while ($stmt->fetch())
	{
		$row[] = array('user_id' => $user_id, 'roommate_id' => $roommate_id);
	}
	$stmt->close();
	return $row;
}

/*
 *deleteTestRoommate: deletes a roommate tuple
 */
function deleteTestRoommate($user_id, $roommate_id)
{
	global $mysqli, $db_table_prefix;
	
	//delete apartment
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."roommates
		WHERE user_id = ? AND roommate_id = ?");		
	$stmt->bind_param("ii", $user_id, $roommate_id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

?>