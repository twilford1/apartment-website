<?php
	require_once("models/funcs.php");
	/*
	 * Test class for apartment_post.php
	 * functions in func.php
	 *
	 *Functions to test:
	 *	-createApartment
	 *  -fetchApartmentID
	 *  -deleteApartment
	 */
	 
	class ApartmentPostTest extends PHPUnit_Framework_TestCase
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
			/*
			//Add a Wisconsin address apartment
			$wisconsin = insertTestApartment("Wisconsin");
			$GLOBALS['wisconsin'] = $wisconsin;
			//Add an Iowa City address apartment
			$iowacity = insertTestApartment("Iowa City");
			$GLOBALS['iowacity'] = $iowacity;*/
			
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
		}  // end setUp
		

				
		public function testCreateApartment()
		{
			$test_apartment = createApartment("TESTAPT", "603 S. Dodge St, Iowa City, 52242", NULL, NULL, 1, 1, 1,999, NULL, "TEST", "available");
			
			$result = true;
			
			if(!isset($test_apartment))
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
		
		public function test_FetchApartmentID_DeleteApartment()
		{
			$aptID = fetchApartmentID("TESTAPT");
			
			$result = true;
			
			if(!isset($aptID))
			{
				$result = false;
			}
			
			$apt_delete = deleteApartment($aptID);
			if(!isset($apt_delete))
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
		
		/*//reset the database after each test
		public function teardown()
		{
			global $wisconsin, $iowacity;

			//delete test apartments
			deleteTestApartment($wisconsin['apartment_id']);
			deleteTestApartment($iowacity['apartment_id']);
		}*/
		
	} // end test

?>