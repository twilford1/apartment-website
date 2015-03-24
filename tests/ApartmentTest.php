<?php

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