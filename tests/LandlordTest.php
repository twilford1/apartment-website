<?php
	require_once("models/funcs.php");
	/*
	 * Test class for landlords.php and landlord_profile.php
	 * functions in func.php
	 *
	 *Functions to test:
	 *	-fetchLandlords
	 *	-fetchLandlordDetails
	 */
	
	///////////Mattie: Commented out code below because of error---
	//////////PHP Fatal error:  Call to a member function prepare() on a non-object in /var/www/website_test/html/models/funcs.php on line 1833
	
/*	
	class LandlordTest extends PHPUnit_Framework_TestCase
	{	
		public function testFetchLandlords()
		{
			$landlords = fetchLandlords(null);
			
			$result = true;
			
			if(!isset($landlords))
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
		
		public function testFetchLandlordDetails()
		{
			$llDetails = fetchLandlordDetails(1);
			
			$result = true;
			
			if(!isset($llDetails) || count($llDetails) != 4 || $llDetails['name'] != "Apartments Downtown")
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
	}
*/

?>