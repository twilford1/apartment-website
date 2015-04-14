<?php
	require_once("models/funcs.php");
	/*
	 * Test class for apartment_listings.php and apartment_listing.php
	 * functions in func.php
	 *
	 *Functions to test:
	 *	-fetchListings
	 *	-fetchListingDetails
	 */
	 
	class SearchTest extends PHPUnit_Framework_TestCase
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

			$listings1 = fetchListings("nonMatchingWords");
			$listings2 = fetchListings("dodge");
			
			$result = true;
			
			if(count($listings1) > 0)
			{
				$result = false;
			}
			
			if(count($listings2) < 2)
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
	}

?>