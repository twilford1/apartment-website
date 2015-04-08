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

?>