<?php

//require "models/config.php";
require_once "models/funcs.php";

/*
 *Test class for map.php favorites; Functions used for map.php
 *are included in funcs.php.  These are the functions
 *that will be unit tested.  
 *
 ********Issues with PHPUnit require that
 *db setup is managed within this test file instead of using
 *config.php.
 *
 *Functions to test:
 *	-addFavorite
 *	-deleteFavorite
 *	-fetchFavorites
 */
class FavoritesTest extends PHPUnit_Framework_TestCase
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
		
		//insert test favorite tuples
		insertTestFavorite(-1, 1);
		insertTestFavorite(-1, 2);
		
		$GLOBALS['all_favorites'] = getAllFavorites();
	}
	
	//check that favorites are fetched correctly
	public function testFetchFavorites_AllForUser()
	{
		//inform the user
		echo("------------------------\n");
		echo("FavoritesTest Case 1: testFetchFavorites_AllForUser\n");
		
		//test user id
		$user_id = -1;
		
		//fetch favorites of a given user
		$favorites = fetchFavorites($user_id);
		
		//result of the function
		$result = false;
		
		//assert that at least one tuple exists
		$this->assertTrue(isset($favorites[0]));
		echo("fetchFavorites() fetches at least one favorite for test user:\n");
		echo((isset($favorites[0]))?"PASS\n":"FAIL\n");
		
		//if at least one tuple returned
		if(isset($favorites[0]))
		{
			$result = true;
			
			//for each favorite
			foreach($favorites as $favorite)
			{
				//if the apartment id does not match
				if(!($favorite['apartment_id'] == 1 || $favorite['apartment_id'] == 2))
				{
					//the expected result was not achieved
					$result = false;
				}
				
			}
			
			//assert that both test tuples exist
			$this->assertTrue($result);
			echo("The correct tuples are fetched:\n");
			echo(($result)?"PASS":"FAIL");
			echo("\n\n");
		}
	}
	
	//check that all favorites attributes are returned
	public function testFetchFavorites_AllAttributes()
	{
		//inform the user
		echo("------------------------\n");
		echo("FavoritesTest Case 2: testFetchFavorites_AllAttributes\n");
		
		//test user id
		$user_id = -1;
		
		//fetch favorites of a given user
		$favorites = fetchFavorites($user_id);
		
		//assert that at least one tuple exists
		$this->assertTrue(isset($favorites[0]));
		echo("fetchFavorites() fetches at least one favorite for test user:\n");
		echo((isset($favorites[0]))?"PASS\n":"FAIL\n");
		
		//if at least one tuple returned
		if(isset($favorites[0]))
		{
			$result = false;
			
			if(isset($favorites[0]['apartment_id']) && isset($favorites[0]['latitude']) && isset($favorites[0]['longitude']) && isset($favorites[0]['address']))
			{
				$result = true;
			}
			
			//assert that both test tuples exist
			$this->assertTrue($result);
			echo("The correct attributes are fetched:\n");
			echo(($result)?"PASS":"FAIL");
			echo("\n\n");
		}
	}
	
	//add a favorite and check that it is there
	public function testAddFavorite()
	{
		//inform the user
		echo("------------------------\n");
		echo("FavoritesTest Case 3: testAddFavorite\n");
		
		$user_id = -1;
		$apartment_id = 3;
		
		//fetch favorites of a given user
		$favorites1 = fetchFavorites($user_id);
		
		//add favorite to said user
		addFavorite($user_id, $apartment_id);
		
		//fetch the user's favorites again
		$favorites2 = fetchFavorites($user_id);
		
		//delete new favorite
		deleteTestFavorite($user_id, $apartment_id);
		
		//assert that the first has one less tuples than the 2nd
		$this->assertTrue((count($favorites1) == (count($favorites2) - 1)));
		echo("One more tuple is in the DB:\n");
		echo((count($favorites1) == (count($favorites2) - 1))?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that a tuple can be deleted
	public function testDeleteFavorite()
	{
		//inform the user
		echo("------------------------\n");
		echo("FavoritesTest Case 4: testDeleteFavorite\n");
		
		$user_id = -1;
		$apartment_id = 1;
		
		//fetch favorites of a given user
		$favorites1 = fetchFavorites($user_id);
		
		//fetch the apartments with no radius (default)
		deleteFavorite($user_id, $apartment_id);
		
		//fetch the user's favorites again
		$favorites2 = fetchFavorites($user_id);
		
		//add the test favorite back so teardown can delete it
		insertTestFavorite($user_id, $apartment_id);
		
		//assert that the first has one less tuple than the 2nd
		$this->assertTrue((count($favorites1) == (count($favorites2) + 1)));
		echo("One less tuple is in the DB:\n");
		echo((count($favorites1) == (count($favorites2) + 1))?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//reset the database after each test
	public function teardown()
	{
		//remove test favorite tuple
		deleteTestFavorite(-1, 1);
		deleteTestFavorite(-1, 2);
	}
}

/*
 *insertTestFavorite: inserts one favorite tuple
 */
function insertTestFavorite($user_id, $apartment_id)
{
	global $mysqli, $db_table_prefix;
	//insert favorite
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."favorites (
		user_id,
		apartment_id)
		VALUES (
		?,
		?)");		
	$stmt->bind_param("ii", $user_id, $apartment_id);
	$result = $stmt->execute();
	$stmt->close();	
}

/*
 * getAllFavorites: returns all favorites
 */
function getAllFavorites()
{
	global $mysqli, $db_table_prefix;
	
	//return favorite tuples
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."favorites
		");
	$stmt->execute();
	$stmt->bind_result($favorite_id, $apartment_id, $user_id);
	
	$row = NULL;
	
	while ($stmt->fetch())
	{
		$row[] = array('user_id' => $user_id, 'apartment_id' => $apartment_id);
	}
	$stmt->close();
	return $row;
}

/*
 * getTestFavorites: returns all favorites of a test user
 */
function getTestFavorites($user_id)
{
	global $mysqli, $db_table_prefix;
	
	//return favorite tuples
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."favorites
		WHERE user_id = ?
		");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($id, $apartment_id);
	
	$row = NULL;
	
	while ($stmt->fetch())
	{
		$row[] = array('user_id' => $id, 'apartment_id' => $apartment_id);
	}
	$stmt->close();
	return $row;
}

/*
 *deleteTestFavorite: deletes a favorite tuple
 */
function deleteTestFavorite($user_id, $apartment_id)
{
	global $mysqli, $db_table_prefix;
	
	//delete apartment
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."favorites
		WHERE user_id = ? AND apartment_id = ?");		
	$stmt->bind_param("ii", $user_id, $apartment_id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

?>