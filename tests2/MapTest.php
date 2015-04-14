<?php

//require "models/config.php";
require_once "models/funcs.php";

/*
 *Test class for map.php; Functions used for map.php
 *are included in funcs.php.  These are the functions
 *that will be unit tested.  
 *
 ********Issues with PHPUnit require that
 *db setup is managed within this test file instead of using
 *config.php.
 *
 *Functions to test:
 *	-fetchListingsWithTerms
 *	-fetchIowaCityApartments
 *	-mathGeoProximity
 *	-mathGeoDistance
 *	-geocode
 */
class MapTest extends PHPUnit_Framework_TestCase
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
		
		//Add a Wisconsin address apartment
		$wisconsin = insertTestApartment("Wisconsin");
		$GLOBALS['wisconsin'] = $wisconsin;
		//Add an Iowa City address apartment
		$iowacity = insertTestApartment("Iowa City");
		$GLOBALS['iowacity'] = $iowacity;
		
		////////////Get the total number of apartments currently//////////
		
		//save all apartments in the db to global variable
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
			FROM ".$db_table_prefix."apartments");
		$stmt->execute();
		$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
		
		while ($stmt->fetch())
		{
			$row[] = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
		}
		$stmt->close();
		
		$GLOBALS['all_apartments'] = $row;
	}
	
	//make sure the function works with no terms
	public function testFetchListingsWithTerms_NoTerms()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 1: testFetchListingsWithTerms_NoTerms\n");
		
		global $all_apartments;
		
		//get all apartments
		$listings = fetchListingsWithTerms();
		
		//check if the function actually got all apartments
		$this->assertTrue(count($all_apartments) == count($listings));
		
		echo("fetchListingsWithTerms(NULL) returns all listings:\n");
		echo((count($all_apartments) == count($listings))?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//make sure the function works with the limit term set 
	//(only possible term currently)
	public function testFetchListingsWithTerms_WithLimit()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 2: testFetchListingsWithTerms_WithLimit\n");
		
		$terms = array("limit"=>1);
		//get all apartments
		$listings = fetchListingsWithTerms($terms);
		
		//check if the function actually got all apartments
		$this->assertTrue(count($listings) == 1);
		
		echo("fetchListingsWithTerms(terms) returns a limited subset:\n");
		echo((count($listings) == 1)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//make sure function works for a set radius
	public function testFetchIowaCityApartmentsSetRadius()
	{	
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 3: testFetchIowaCityApartmentsSetRadius\n");
		
		global $all_apartments, $iowacity, $wisconsin;
		
		//fetch the apartments with a radius chosen
		$apartments = fetchIowaCityApartments(20);
		
		//assert that the radius limited the results
		$this->assertTrue(count($all_apartments) > count($apartments));
		
		echo("A subset of all listings is returned for 20 mile radius:\n");
		echo((count($all_apartments) > count($apartments))?"PASS\n":"FAIL\n");
		
		//result of the function
		$result1 = true;
		$result2 = false;
		
		//check results for the two apartments created above
		foreach($apartments as $apartment)
		{
			if($apartment['apartment_id'] == $wisconsin['apartment_id'])
			{
				$result1 = false;
			}
			else if($apartment['apartment_id'] == $iowacity['apartment_id'])
			{
				$result2 = true;
			}
		}
		
		//if Wisconsin wasn't returned but an Iowa City address was, radius worked
		$this->assertTrue($result1 && $result2);
		
		echo("The Wisconsin test tuple is not present, but the Iowa City test tuple is:\n");
		echo(($result1 && $result2)?"PASS":"FAIL");	
		echo("\n\n");
	}
	
	//fetch all possible apartments when radius isn't set
	public function testFetchIowaCityApartmentsNoRadius()
	{	
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 4: testFetchIowaCityApartmentsNoRadius\n");
		
		//all apartments in db
		global $all_apartments;
		
		//fetch the apartments with no radius (default)
		$apartments = fetchIowaCityApartments();
		
		//all tuples returned
		$this->assertTrue(count($all_apartments) == count($apartments));
		
		echo("fetchIowaCityApartments(NULL) returns all apartments:\n");
		echo((count($all_apartments) == count($apartments))?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that all expected attributes are received
	public function testFetchIowaCityApartmentsAllAttributes()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 5: testFetchIowaCityApartmentsAllAttributes\n");
		
		//fetch the apartments with no radius (default)
		$apartments = fetchIowaCityApartments();
		
		//result of test
		$result = false;
		
		//check that all of the expected attributes are set for the first tuple
		if(isset($apartments[0]['apartment_id']) && isset($apartments[0]['latitude']) && isset($apartments[0]['longitude']) && isset($apartments[0]['address']))
		{
			$result = true;
		}
		
		$this->assertTrue($result);
		
		echo("All expected attributes are set in result tuples:\n");
		echo(($result)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that proximity in miles is returned correctly
	public function testMathGeoProximityMiles()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 6: testMathGeoProximityMiles\n");
		
		//fetch the proximity
		$proximity = mathGeoProximity( 30, 30, 20, true);
		
		//result of test
		$result = false;
		
		//if the proximity attributes are approximately as expected
		if(number_format($proximity['longitudeMin'],3) == 29.665
		&& number_format($proximity['longitudeMax'],3) == 30.335
		&& number_format($proximity['latitudeMin'],3) == 29.710
		&& number_format($proximity['latitudeMax'],3) == 30.290)
		{
			$result = true;
		}
		
		$this->assertTrue($result);
		
		echo("The proximity calculations in miles are correct for the test radius:\n");
		echo(($result)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that proximity in km is returned correctly
	public function testMathGeoProximityKm()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 7: testMathGeoProximityKm\n");
		
		$proximity = mathGeoProximity( 30, 30, 20);
		
		//result of test
		$result = false;
		
		//if the proximity attributes are approximately as expected
		if(number_format($proximity['longitudeMin'],3) == 29.792
		&& number_format($proximity['longitudeMax'],3) == 30.208
		&& number_format($proximity['latitudeMin'],3) == 29.820
		&& number_format($proximity['latitudeMax'],3) == 30.180)
		{
			$result = true;
		}
		
		$this->assertTrue($result);
		
		echo("The proximity calculations in kms are correct for the test radius:\n");
		echo(($result)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that all fields are returned as expected
	public function testMathGeoProximityAllFields()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 8: testMathGeoProximityAllFields\n");
		
		$proximity = mathGeoProximity( 30, 30, 20, true);
		
		//result of test
		$result = false;
		
		//check that all of the expected attributes are set for the first tuple
		if(isset($proximity['latitudeMin']) && isset($proximity['latitudeMax']) && isset($proximity['longitudeMin']) && isset($proximity['longitudeMax']))
		{
			$result = true;
		}
		
		$this->assertTrue($result);
		
		echo("All expected attributes are set in result tuples:\n");
		echo(($result)?"PASS":"FAIL");
		
		echo("\n\n");
	}
	
	//test that the distance is correct (miles)
	public function testMathGeoDistanceMiles()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 9: testMathGeoDistanceMiles\n");
		
		//get dist in miles
		$dist1 = mathGeoDistance( 30, 30, 20, 20, true);
		
		$this->assertTrue(number_format($dist1,2) == 931.76);
		
		echo("The test distance is calculated correctly (miles):\n");
		echo((number_format($dist1,2) == 931.76)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that the distance is correct (km)
	public function testMathGeoDistanceKm()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 10: testMathGeoDistanceKm\n");
		
		//get dist in miles
		$dist1 = mathGeoDistance( 30, 30, 20, 20, true);
		
		//get distance in km
		$dist2 = mathGeoDistance( 30, 30, 20, 20);
		
		//assert that km converted to miles is the same result as
		//getting miles from the function
		$this->assertTrue(($dist2 * 0.621371192) == $dist1);
		
		echo("The test distance is calculated correctly (kms):\n");
		echo((($dist2 * 0.621371192) == $dist1)?"PASS":"FAIL");	
		echo("\n\n");
	}
	
	//test to see if geocode returns the right coordinates
	public function testGeocodeIowaCityCoordinates()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 11: testGeocodeIowaCityCoordinates\n");
		
		//get the coordinates for Iowa City
		$location = geocode("Iowa City");
		
		//make sure they match the known coordinates
		$this->assertTrue($location[0] == 41.6611277 && $location[1] == -91.5301683);
		
		echo("geocode() returns the correct coordinates for Iowa City:\n");
		echo(($location[0] == 41.6611277 && $location[1] == -91.5301683)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test to see if no result is returned for nonsense query
	public function testGeocodeNoResult()
	{
		//inform the user
		echo("------------------------\n");
		echo("MapTest Case 12: testGeocodeNoResult\n");
		
		//get the coordinates for Iowa City
		$location = geocode("bsjfbjsbjbfkjqwbefkbkjsbadbf");
		
		//no coordinates should be returned
		$this->assertTrue($location == NULL);
		
		echo("No coordinates are returned for nonsense address:\n");
		echo(($location == NULL)?"PASS":"FAIL");
		
		echo("\n\n");
	}
	
	//reset the database after each test
	public function teardown()
	{	
		global $wisconsin, $iowacity;

		//delete test apartments
		deleteTestApartment($wisconsin['apartment_id']);
		deleteTestApartment($iowacity['apartment_id']);
	}
}

/*
 *insertTestApartment: inserts apartment with given address and returns 
 *					   resulting tuple
 */
function insertTestApartment($address)
{
	global $mysqli, $db_table_prefix;
	//insert apartment
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."apartments (
		name,
		address,
		num_bedrooms,
		num_bathrooms,
		landlord_id,
		price,
		description,
		status
		)
		VALUES (
		'DeleteMe',
		?,
		1,
		1,
		1,
		200.00,
		'I should not be (testing)',
		'unavailable'
		)");		
	$stmt->bind_param("s", $address);
	$result = $stmt->execute();
	$stmt->close();	
	
	//return apartment tuple
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
		FROM ".$db_table_prefix."apartments
		WHERE address = ?
		");
	$stmt->bind_param("s", $address);
	$stmt->execute();
	$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
	
	$row = NULL;
	
	while ($stmt->fetch())
	{
		$row = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
	}
	$stmt->close();
	return $row;
}

/*
 *deleteTestApartment: deletes apartment specified
 */
function deleteTestApartment($id)
{
	global $mysqli, $db_table_prefix;
	
	//delete apartment
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."apartments 
		WHERE apartment_id = ?");		
	$stmt->bind_param("i", $id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

?>