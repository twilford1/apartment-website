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
 *	-updateLatLng
 *	-geocode
 */
class MapTest extends PHPUnit_Framework_TestCase
{
	public function testTrueIsTrue()
	{
		$foo = true;
		$this->assertTrue($foo);
	}
	
	public function testFetchIowaCityApartmentsSmallRadius()
	{
		$apartments = fetchIowaCityApartments(5, 1);
	}
	
	public function testFetchIowaCityApartmentsLargeRadius()
	{
		$apartments = fetchIowaCityApartments(5, 1);
	}
	
	public function testFetchIowaCityApartmentsNoRadius()
	{
		$apartments = fetchIowaCityApartments(5, 1);
	}
}

/************************MAP FUNCTIONS***********************************/
	
//Fetches apartments in a certain radius of Iowa City for the map.php page
function fetchIowaCityApartments($radius = NULL, $limit = 20)
{
	$mockListings = array(["apartment_id" => 1, "latitude" => 41.6660136, "longitude" => -91.544685, "address" => "Iowa City"],
						  ["apartment_id" => 2, "latitude" => 43, "longitude" => -90, "address" => "Wisconsin"],
						  ["apartment_id" => 3, "latitude" => 41.6660136, "longitude" => -91.544685, "address" => ""],
						  ["apartment_id" => 4, "latitude" => 41.6660136, "longitude" => -91.544685, "address" => ""],
						  ["apartment_id" => 5, "latitude" => 41.6660136, "longitude" => -91.544685, "address" => ""],
						  ["apartment_id" => 6, "latitude" => 41.6660136, "longitude" => -91.544685, "address" => ""]);
	
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
		
		//Below code not working; always returns true for some reason...
		/*
		//find matches in db
		$stmt = $mysqli->prepare("SELECT * 
			FROM   ".$db_table_prefix."apartments
			WHERE  (latitude BETWEEN ?
					AND ?)
			  AND (longitude BETWEEN ?
					AND ?)
		");
		$stmt->bind_param('dddd', number_format($proximity['latitudeMin'], 6), number_format($proximity['latitudeMax'], 6),
							 number_format($proximity['longitudeMin'], 6), number_format($proximity['longitudeMax'], 6));
		$result = $stmt->execute();
		$stmt->close();
		*/

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

//Update the coordinates of an apartment's location
function updateLatLng($id, $lat, $lng)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."apartments
		SET latitude = ?, longitude = ?
		WHERE
		apartment_id = ?
		LIMIT 1");
	$stmt->bind_param('ddi', $lat, $lng, $id);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
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
	if($resp['status']='OK'){
 
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