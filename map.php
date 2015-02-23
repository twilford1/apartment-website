<!--
Iowa City Google Maps php page
by Mattie Fickel 

Resources: 
http://www.w3schools.com/googleAPI/google_maps_basic.asp

https://developers.google.com/maps/articles/phpsqlajax_v3

https://developers.google.com/maps/documentation/javascript/examples/infowindow-simple

-->

<?php 
//include in order to connect to the database
require_once("models/config.php");
	
if (!securePage($_SERVER['PHP_SELF']))
{
	die();
}

//get the apartments from the database (eventually use a radius)
$apartments = fetchIowaCityApartments();

require_once("models/header.php");

echo"   
        <script>
		//use a geocoder object to convert addresses to latitude and longitude
		var geocoder = new google.maps.Geocoder();
		var map;
		
		//initialize the Google Map with Iowa City at the center
        function initialize() 
		{
		  //create the map
          var mapProp = 
		  {
            center:new google.maps.LatLng(41.6660136,-91.544685),
            zoom:12,
            mapTypeId:google.maps.MapTypeId.ROADMAP
          };
          map=new google.maps.Map(document.getElementById(\"googleMap\"),mapProp);
		  
		  //populate the map with markers
		  ";
		  
		//Cycle through apartments
		foreach ($apartments as $apartment)
		{
			echo "
		  //create marker for apartment
		  createMarker(\"". $apartment['address'] ."\", 
		  \"<html><body><h3>". $apartment['name'] ."</h3><h4>". $apartment['address'] ."</h4>\"
				+\"<ul><li>". $apartment['status'] ." (updated ". $apartment['last_updated'] .")</li><li>\"+
				\"". $apartment['num_bedrooms'] ." bedrooms, ". $apartment['num_bathrooms'] ." bathrooms</li><li>\"+
				\"$". $apartment['price'] ." per month</li><li>Landlord ID: ". $apartment['landlord_id'] ."\"+
				\"</li></ul><p>". $apartment['description'] ."</p>\"+
				\"<p><a href='/apartment_listing.php?id=".$apartment['apartment_id']."' target='_blank'>\"+
				\"More info about this location</a></p></body></html>\",
				\"". $apartment['address'] ."\");
		
		";
        }
		
		echo "
		}
        google.maps.event.addDomListener(window, 'load', initialize);

	    //change the address the map centers around using input from user
	    function changeCenter() 
	    {
			var address = document.getElementById('address').value;	
			geocoder.geocode( { 'address': address}, function(results, status)
			{
				if (status == google.maps.GeocoderStatus.OK) 
				{
					map.setCenter(results[0].geometry.location);
					
					//create a marker for the location
					createMarker(address, \"Hi I'm a placeholder for actual apartment info!<br><a href='apartment.krakenshell.com/apartment_listing.php' target='_blank'>More info about this location</a>\", address);
				} 
				else 
				{
					alert(\"Geocode was not successful for the following reason: \" + status);
				}
			});
		  
	   }
	   
	   //creates a marker with an info window that appears when the marker is clicked
	   function createMarker(address, contentString, titleString)
	   {
		   geocoder.geocode( { 'address': address}, function(results, status)
			{
				if (status == google.maps.GeocoderStatus.OK) 
				{
					var infowindow = new google.maps.InfoWindow({
						//contentString can be formatted in html!
						content: contentString
					 });

					 var marker = new google.maps.Marker({
						position: results[0].geometry.location,
						map: map,
						title: titleString
					 });
					 
					 google.maps.event.addListener(marker, 'click', function() {
						infowindow.open(map,marker);
					 });
				}
				else 
				{
					alert(\"Geocode was not successful for the following reason: \" + status);
					return NULL;
				}
			});
	   }
        </script>

		<h1>Apartments near Iowa City</h1>
		
		<h3>The map below shows apartment listings in the Iowa City area.  Click on a location to view more information.</h3>
		
    	<!--allow the user to enter a new address -->
        Enter an address to center the map there: <input type=\"text\" name=\"address\" id=\"address\">
        <button onclick=\"changeCenter()\">Enter</button>
        <br><br>
        
        <!--display the Google Map -->
        <div id=\"googleMap\" style=\"width:750px;height:500px;\"></div>
		
		<!-- display a list of the apartments shown on the page (in the future, this should be a scrolling textbox or something) -->
		<h3>Click the links below for more information about listings shown on the map:</h3>";
		
	//Cycle through apartments
	foreach ($apartments as $apartment)
	{
		echo "
		<div id=\"apartment_".$apartment['apartment_id']."\"><a href='/apartment_listing.php?id=".$apartment['apartment_id']."' target='_blank'>".$apartment['address']."</a></div>
		";
	}
	
	echo "<br><br>";
		
		include 'models/footer.php';

?>