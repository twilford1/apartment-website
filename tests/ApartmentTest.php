<?php
	require_once "models/funcs.php";
	
	/*
	 * Test class for apartment_listings.php and apartment_listing.php
	 * functions in func.php
	 *
	 *Functions to test:
	 *	-fetchListings
	 *	-fetchListingDetails
	 */
	class ApartmentTest extends PHPUnit_Framework_TestCase
	{	
		
		
		public function testFetchListings()
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
			
			$listings = fetchListings(null);
			
			$result = true;
			
			if(!isset($listings))
			{
				$result = false;
			}
			
			$this->assertTrue($result);
			
		}
		
		public function testFetchListingDetails()
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

			
			$aptDetails = fetchListingDetails(1);
			
			$result = true;
			
			if(!isset($aptDetails) || count($aptDetails) != 13 || $aptDetails['name'] != "Lantern Park")
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
	}

?>