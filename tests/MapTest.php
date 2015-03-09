<?php

/*
 *Test class for map.php; Functions used for map.php
 *are included in funcs.php.  These are the functions
 *that will be unit tested.  Because of issues with phpunit,
 *the functions from funcs.php MUST be copied from there
 *to this class to be tested.  Has to do with global variable
 *issues that I am unable to solve in any other way as of yet.
 *
 *Functions to test:
 *	-fetchIowaCityApartments
 *	-mathGeoProximity
 *	-mathGeoDistance
 *	-geocode
 */
class MapTest extends PHPUnit_Framework_TestCase
{	
	//make sure function works for a set radius
	public function testFetchIowaCityApartmentsSetRadius()
	{
		//fetch the apartments with a radius chosen
		$apartments = fetchIowaCityApartments(20);
		
		//result of the function
		$result = true;
		
		foreach($apartments as $apartment)
		{
			if($apartment['address'] == "Wisconsin" && count($apartments) != 5)
			{
				$result = false;
			}
		}
		
		//only tuple which should not be returned is the address "Wisconsin"
		$this->assertTrue($result);
	}
	
	//fetch all possible apartments when radius isn't set
	public function testFetchIowaCityApartmentsNoRadius()
	{
		//fetch the apartments with no radius (default)
		$apartments = fetchIowaCityApartments();
		
		//only tuple which should not be returned is the address "Wisconsin"
		$this->assertTrue(count($apartments) == 6);
	}
	
	//test that all expected attributes are received
	public function testFetchIowaCityApartmentsAllAttributes()
	{
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
	}
	
	//test that proximity in miles is returned correctly
	public function testMathGeoProximityMiles()
	{
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
	}
	
	//test that proximity in km is returned correctly
	public function testMathGeoProximityKm()
	{
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
	}
	
	//test that all fields are returned as expected
	public function testMathGeoProximityAllFields()
	{
		$proximity = mathGeoProximity( 30, 30, 20, true);
		
		//result of test
		$result = false;
		
		//check that all of the expected attributes are set for the first tuple
		if(isset($proximity['latitudeMin']) && isset($proximity['latitudeMax']) && isset($proximity['longitudeMin']) && isset($proximity['longitudeMax']))
		{
			$result = true;
		}
		
		$this->assertTrue($result);
	}
	
	//test that the distance is correct (miles)
	public function testMathGeoDistanceMiles()
	{
		//get dist in miles
		$dist1 = mathGeoDistance( 30, 30, 20, 20, true);
		
		$this->assertTrue(number_format($dist1,2) == 931.76);
	}
	
	//test that the distance is correct (km)
	public function testMathGeoDistanceKm()
	{
		//get dist in miles
		$dist1 = mathGeoDistance( 30, 30, 20, 20, true);
		
		//get distance in km
		$dist2 = mathGeoDistance( 30, 30, 20, 20);
		
		//assert that km converted to miles is the same result as
		//getting miles from the function
		$this->assertTrue(($dist2 * 0.621371192) == $dist1);
	}
	
	//test to see if geocode returns the right coordinates
	public function testGeocodeIowaCityCoordinates()
	{
		//get the coordinates for Iowa City
		$location = geocode("Iowa City");
		
		//make sure they match the known coordinates
		$this->assertTrue($location[0] == 41.6611277 && $location[1] == -91.5301683);
	}
	
	//test to see if no result is returned for nonsense query
	public function testGeocodeNoResult()
	{
		//get the coordinates for Iowa City
		$location = geocode("bsjfbjsbjbfkjqwbefkbkjsbadbf");
		
		//make sure they match the known coordinates
		$this->assertTrue($location == NULL);
	}
}

/************************MAP FUNCTIONS***********************************/
	
//Fetches apartments in a certain radius of Iowa City for the map.php page
function fetchIowaCityApartments($radius = NULL, $limit = 20)
{
	$mockListings = array(["apartment_id" => 1, "latitude" => 41.684727, "longitude" => -91.591194, "address" => "20th Ave Place Coralville, IA 52241"],
						  ["apartment_id" => 8, "latitude" => 43, "longitude" => -90, "address" => "Wisconsin"],
						  ["apartment_id" => 2, "latitude" => 41.657234, "longitude" => -91.527702, "address" => "317 South Johnson Iowa City, IA 52240"],
						  ["apartment_id" => 3, "latitude" => 41.663624, "longitude" => -91.535896, "address" => "14 East Market Iowa City, IA 52245"],
						  ["apartment_id" => 4, "latitude" => 41.653446, "longitude" => -91.526306, "address" => "621 S Dodge Street Iowa City, IA 52240"],
						  ["apartment_id" => 5, "latitude" => 41.658039, "longitude" => -91.530991, "address" => "318 E Burlington St Iowa City, IA 52240"]);
	
	$result = $mockListings;
	
	//check if apartments have latitude and longitude values yet
	foreach($result as $apartment)
	{	
		if($apartment['latitude'] == NULL || $apartment['longitude'] == NULL)
		{
			$results = geocode($apartment['address']);
			$apartment['latitude'] = $results[0];
			$apartment['longitude'] = $results[1];
			
			//if a result was found
			if(($apartment['latitude'] != NULL) && ($apartment['longitude'] != NULL))
			{
				//update each coordinates
				updateLatLng($apartment['apartment_id'], $apartment['latitude'], $apartment['longitude']);
			}
		}
	}
	
	//fetch all listings
	if($radius == NULL)
	{
		return $result;
	}
	//return only the locations within the given radius of Iowa City
	else
	{	
		//get proximity variable in miles for IowaCity coordinates
		$proximity = mathGeoProximity(41.6660136,-91.544685, $radius, true);

		// fetch all record and check whether they are really within the radius
		$recordsWithinRadius = array();
		
		//check each result
		foreach($result as $apartment)
		{
			if($apartment['latitude'] != NULL && $apartment['longitude'] != NULL)
			{
				$distance = mathGeoDistance(41.6660136,-91.544685, $apartment['latitude'], $apartment['longitude'], true);
				
				if ($distance <= $radius) 
				{
					array_push($recordsWithinRadius, $apartment);
				}
			}
		}
		
		return $recordsWithinRadius;
	}
}

// calculate geographical proximity
function mathGeoProximity( $latitude, $longitude, $radius, $miles = false )
{
	$radius = $miles ? $radius : ($radius * 0.621371192);

	$lng_min = $longitude - $radius / abs(cos(deg2rad($latitude)) * 69);
	$lng_max = $longitude + $radius / abs(cos(deg2rad($latitude)) * 69);
	$lat_min = $latitude - ($radius / 69);
	$lat_max = $latitude + ($radius / 69);

	return array(
		'latitudeMin'  => $lat_min,
		'latitudeMax'  => $lat_max,
		'longitudeMin' => $lng_min,
		'longitudeMax' => $lng_max
	);
}

// calculate geographical distance between 2 points
function mathGeoDistance( $lat1, $lng1, $lat2, $lng2, $miles = false )
{
	$pi80 = M_PI / 180;
	$lat1 *= $pi80;
	$lng1 *= $pi80;
	$lat2 *= $pi80;
	$lng2 *= $pi80;

	$r = 6372.797; // mean radius of Earth in km
	$dlat = $lat2 - $lat1;
	$dlng = $lng2 - $lng1;
	$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	$km = $r * $c;

	return ($miles ? ($km * 0.621371192) : $km);
}

// function to geocode address, it will return false if unable to geocode address
function geocode($address)
{
	// url encode the address
	$address = urlencode($address);
	 
	// google map geocode api url
	$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}";
 
	// get the json response
	$resp_json = file_get_contents($url);
	 
	// decode the json
	$resp = json_decode($resp_json, true);
	// response status will be 'OK', if able to geocode given address
	if($resp['status']=='OK'){
 
		// get the important data
		$lat = $resp['results'][0]['geometry']['location']['lat'];
		$lng = $resp['results'][0]['geometry']['location']['lng'];
		
		return array($lat, $lng);
	}
	else
	{
		return NULL;
	}
}

?>