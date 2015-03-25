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
			$listings1 = fetchListings("nonMatchingWords");
			$listings2 = fetchListings("dodge");
			
			$result = true;
			
			if(isset($listings1))
			{
				$result = false;
			}
			
			if(!isset($listings2) || count($listings2) != 2)
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
	}

?>