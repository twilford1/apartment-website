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
	}

?>