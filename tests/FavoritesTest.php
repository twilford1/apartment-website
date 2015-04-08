<?php

/*
 *Test class for functions having to do with favorites which are included
 *in funcs.php.  Because of issues with phpunit,
 *the functions from funcs.php MUST be copied from there
 *to this class to be tested.  Has to do with global variable
 *issues that I am unable to solve in any other way as of yet.
 *
 */
class FavoritesTest extends PHPUnit_Framework_TestCase
{	
/*
	//Database Information
	public $db_host = "localhost"; //Host address (most likely localhost)
	public $db_name = "website_test"; //Name of Database
	public $db_user = "websiteUser"; //Name of database user
	public $db_pass = "4WPXGzCUm2y2TeG7"; //Password for database user
	public $db_table_prefix = "apt_";

	public $errors = array();
	public $successes = array();

	//Create a new mysqli object with database connection parameters
	public $mysqli;
	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
*/
	
	//add a favorite and check that it is there
	public function testAddFavorite()
	{
		$user_id = 6;
		$apartment_id = 1;
		
		//fetch favorites of a given user
		$favorites1 = fetchFavorites($user_id);
		
		//add favorite to said user
		addFavorite($user_id, $apartment_id);
		
		//fetch the user's favorites again
		$favorites2 = fetchFavorites($user_id);
		
		//assert that the first has one less tuple than the 2nd
		$this->assertTrue((count($favorites1) == (count($favorites2) - 1)));
	}

	//check that the favorites added in previous test are fetched
	public function testFetchFavorites()
	{
		$user_id = 6;
		$apartment_id = 1;
		
		//fetch favorites of a given user
		$favorites = fetchFavorites($user_id);
		
		//result of the function
		$result = false;
		
		//for each favorite
		foreach($favorites as $favorite)
		{
			//if the user id and apartment id match
			if($favorite['apartment_id'] == $apartment_id && $user_id == $favorite['user_id'])
			{
				//the expected result was achieved
				$result = true;
			}
		}
		
		$this->assertTrue($result);
	}
	
	//test that the attribute previously added can be deleted
	public function testDeleteFavorite()
	{
		$user_id = 6;
		$apartment_id = 1;
		
		//fetch favorites of a given user
		$favorites1 = fetchFavorites($user_id);
		
		//fetch the apartments with no radius (default)
		deleteFavorite($user_id, $apartment_id);
		
		//fetch the user's favorites again
		$favorites2 = fetchFavorites($user_id);
		
		//assert that the first has one less tuple than the 2nd
		$this->assertTrue((count($favorites1) == (count($favorites2) + 1)));
	}
}

/************************FAVORITES FUNCTIONS***********************************/
//Fetches a user's favorite listings using their user id
function fetchFavorites($user_id)
{
	// Return all favorites for this user
	global $mysqli, $db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		apartment_id,
		name,
		address,
		latitude,
		longitude,
		num_bedrooms,
		num_bathrooms,
		landlord_id,
		price,
		deposit,
		description,
		status,
		last_updated
		FROM (SELECT 
		apartment_id
		FROM ".$db_table_prefix."favorites
		WHERE user_id = ?) as table1 NATURAL JOIN ".$db_table_prefix."apartments");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
	
	while ($stmt->fetch())
	{
		$row[] = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
	}
	$stmt->close();
	return ($row);
}

//Add a new favorite for the user and apartment specified
function addFavorite($user_id, $apartment_id)
{
	global $mysqli, $db_table_prefix;
	//Insert the message into the database
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."favorites (
		user_id,
		apartment_id
		)
		VALUES (
		?,
		?
		)");		
	$stmt->bind_param("ii", $user_id, $apartment_id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Delete the specified favorite
function deleteFavorite($user_id, $apartment_id)
{
	global $mysqli, $db_table_prefix; 
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."favorites 
		WHERE user_id = ? AND apartment_id = ?");
	$stmt->bind_param("ii", $user_id, $apartment_id);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

?>