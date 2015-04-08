<!--
Iowa City Google Maps php page
by Mattie Fickel 

Resources: 
http://www.w3schools.com/googleAPI/google_maps_basic.asp

https://developers.google.com/maps/articles/phpsqlajax_v3

https://developers.google.com/maps/documentation/javascript/examples/infowindow-simple

https://www.codeofaninja.com/2014/06/google-maps-geocoding-example-php.html

http://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit

-->

<?php 
//include in order to connect to the database
require_once("models/config.php");
//require the page containing the db information, etc
require_once("models/header.php");
	
if (!securePage($_SERVER['PHP_SELF']))
{
	die();
}

//get apartments within 20 miles of Iowa City
//limit to 50 results
$apartments = fetchIowaCityApartments(20, 50);

$loggedIn = false;

//Check if user is logged in
if(isUserLoggedIn())
{
	if(!empty($_POST))
	{
		$favorite_id = $_POST[favorite_id];
		$type = $_POST[type];
		
		if(isset($favorite_id) && isset($type))
		{
			switch($type)
			{
				case 'add':
					//add the favorite to the db
					addFavorite($loggedInUser->user_id, $favorite_id);
					break;
				case 'delete':
					deleteFavorite($loggedInUser->user_id, $favorite_id);
			}
		}
	}
	
	$loggedIn = true;
	
	//get the user's favorite apartments if logged in
    $favorites = fetchFavorites($loggedInUser->user_id);
	
	// mark/add favorites to apartments
	for ($i=0; $i < count($apartments); $i++)
	{
		$apartments[$i][fave] = false;
		
		//for each favorite
		for ($j=0; $j < count($favorites); $j++)
		{	
			if($favorites[$j][apartment_id] == $apartments[$i][apartment_id])
			{	
				$apartments[$i][fave] = true;
				array_splice($favorites, $j, 1);
				break;
			}
		}
	}
	
	foreach($favorites as $favorite)
	{
		$favorite[fave] = true;
		array_push($apartments, $favorite);
	}
}

//echo(json_encode($apartments));

//echo the javascript and html code below
echo "   
	<script>
		//use a geocoder object to convert addresses to latitude and longitude
		var geocoder = new google.maps.Geocoder();
		var apartments = ".json_encode($apartments).";
		var map;
		var coordinates = new Array();
		var markers = new Array();
		/*var stars = [
			'/models/site-templates/images/yellow_star.png',
			'/models/site-templates/images/blue_star.png'
		];*/
		
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
		  
		  //populate the Iowa City apartments map with markers
		  var description;		//to hold each apartment's description string
		  var address;			//to hold the current apartment's address string
		  var bounds = new google.maps.LatLngBounds();
		  
		  //cycle through apartments
		  for(var i=0; i < apartments.length; i++)
		  {
			  description = \"<html><body><h3>\"+apartments[i].name+\"</h3><h4>\"+apartments[i].address+\"</h4>\"
					  +\"<ul><li>\"+apartments[i].status+\" (updated \"+apartments[i].last_updated+\")</li><li>\"+
					  apartments[i].num_bedrooms+\" bedrooms, \"+apartments[i].num_bathrooms+\" bathrooms</li><li>\"+
					  \"$\"+apartments[i].price+\" per month</li><li>Landlord ID: \"+apartments[i].landlord_id+
					  \"</li></ul><p>\"+apartments[i].description+\"</p>\"+
					  \"<p><a href='/apartment_listing.php?id=\"+apartments[i].apartment_id+\"' target='_blank'>\"+
					  \"More info about this location</a></p></body></html>\";
					  
			  address = apartments[i].address;
			  
			  //if there is a longitude and latitude
			  if(apartments[i].longitude != null && apartments[i].latitude != null)
			  {
				  //create marker for apartment
				  createMarkerLatLng(apartments[i].latitude, apartments[i].longitude, description, address, apartments[i].fave, apartments[i].apartment_id);
			  }
			  //otherwise use the address
			  else
			  {
				  createMarkerAddr(address, description, address, apartments[i].fave, apartments[i].apartment_id);
			  }
			  
			  //extend the bounds of the map for each new marker
			  bounds.extend(new google.maps.LatLng(apartments[i].latitude, apartments[i].longitude));
		  }
		  
		  //change the zoom-level of the map to fit all of the markers
		  map.fitBounds(bounds);
		}

		//initialize the map
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
					createMarkerAddr(address, address, address, null, false, 'red');
				} 
				else 
				{
					alert(\"Geocode was not successful for the following reason: \" + status);
				}
			});	  
	   }
	   
	   //creates a marker with an info window that appears when the marker is clicked
	   function createMarkerLatLng(latitude, longitude, contentString, titleString, fave, id)
	   {
		     var latlng = new google.maps.LatLng(latitude, longitude);
		   
		     var infowindow = new google.maps.InfoWindow({
				//contentString can be formatted in html!
				content: contentString
			 });

			 var marker = new google.maps.Marker({
				icon: (!fave) ? 'http://maps.google.com/mapfiles/ms/icons/ltblue-dot.png':'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
				position: latlng,
				map: map,
				title: titleString
			 });
			 
			 markers.push({marker: marker, id: id});
			 
			 google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			 });
	   }
	   
	   //creates a marker with an info window that appears when the marker is clicked
	   function createMarkerAddr(address, contentString, titleString, id, fave, color)
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
						icon: 'http://maps.google.com/mapfiles/ms/icons/'+ color +'-dot.png',
						position: results[0].geometry.location,
						map: map,
						title: titleString
					 });
					 
					 if(id != null)
					 {
						markers.push({marker: marker, id: id});
					 }
					 
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
	   
	   /*
	   //adds or removes a favorite and does proper book-keeping
	   function changeStar(id) 
	   {
		   //if the user is logged in
		   if(".json_encode($loggedIn).")
		   {
			   //get the image to be changed
			   var temp = document.getElementById(id).src;
			   
			   //remove the excess from the string
			   temp = temp.substring(temp.indexOf('/models'));
			   
			   //change the image
			   document.getElementById(id).src = stars[(stars.indexOf(temp)+1)%2];
			   
			   //get the apartment_id
			   id = id.replace('fave_', '');
			   
			   //if the apartment is being favorited
			   if(temp.indexOf('blue') > -1)
			   {
				   //change the marker icon
				   for(var i=0; i<markers.length; i++)
				   {
					   if(markers[i].id == id)
					   {
						   markers[i].marker.setIcon('http://maps.google.com/mapfiles/ms/icons/yellow-dot.png');
						   break;
					   }
				   }
				   
				   //post favorite information
				   post('".$_SERVER['PHP_SELF']."', {favorite_id: id, type: 'add'});
			   }
			   //otherwise the apartment favorite is being deleted
			   else
			   {   
				   //change the marker icon
				   for(var i=0; i<markers.length; i++)
				   {
					   if(markers[i].id == id)
					   {
						   markers[i].marker.setIcon('http://maps.google.com/mapfiles/ms/icons/ltblue-dot.png');
						   break;
					   }
				   }
				   
				   //post favorite information
				   post('".$_SERVER['PHP_SELF']."', {favorite_id: id, type: 'delete'});
			   }
		   }
	   }   
	   */
	   
	   function post(path, params, method) 
	   {
			method = method || \"post\"; // Set method to post by default if not specified.

			// The rest of this code assumes you are not using a library.
			// It can be made less wordy if you use one.
			var form = document.createElement(\"form\");
			form.setAttribute(\"method\", method);
			form.setAttribute(\"action\", path);

			for(var key in params) {
				if(params.hasOwnProperty(key)) {
					var hiddenField = document.createElement(\"input\");
					hiddenField.setAttribute(\"type\", \"hidden\");
					hiddenField.setAttribute(\"name\", key);
					hiddenField.setAttribute(\"value\", params[key]);

					form.appendChild(hiddenField);
				 }
			}

			document.body.appendChild(form);
			form.submit();
		}
		</script>
	<div class=\"container\">
		<div class=\"row clearfix\">
			<div class=\"col-md-12 column\">
			    <div class=\"page-header\">
					<h1>
						Map <br><small>The map below shows Iowa City apartment listings in blue"
						.(!isUserLoggedIn() ? ".<br>Log in to view your favorite listings in yellow." : ", and your favorites in yellow.").
								"</small>
					</h1>
				</div>
				<div class=\"tabbable\" id=\"tabs-549238\">
					<ul class=\"nav nav-tabs\">
						<li class=\"active\">
							<a href=\"#panel-185212\" data-toggle=\"tab\">Apartments in Iowa City</a>
						</li>
					</ul>
					<div class=\"tab-content\">
						<div class=\"tab-pane active\" id=\"panel-185212\">
							<p>
								<div class=\"col-md-8 column\">
									<div class=\"panel panel-default\">
										<div class=\"panel-heading\">
											<h3 class=\"panel-title\">
												<!--allow the user to enter a new address -->
												Enter an address to center the map there: <input type=\"text\" name=\"address\" id=\"address\">
												<button onclick=\"changeCenter()\">Enter</button>
												<br><br>
											</h3>
										</div>
										<div class=\"panel-body\">
											<!--display the Google Map -->
											<div id=\"googleMap\" style=\"width:600px;height:500px;\"></div>
										</div>
										<div class=\"panel-footer\">
											The map above shows apartment listings in the Iowa City area.  Click on a location to view more information.
										</div>
									</div>
								</div>
								<div class=\"col-md-4 column\">
									<div class=\"panel-group\" id=\"panel-812954\">";
									
				$i=0;
				
				//Cycle through apartments
				foreach ($apartments as $apartment)
				{
					$i++;
					
					echo"
										<div class=\"panel panel-default\">
											<div class=\"panel-heading\">
												 <a class=\"panel-title collapsed\" data-toggle=\"collapse\" data-parent=\"#panel-812954\" href=\"#panel-element-1".$apartment['apartment_id']."\">".$i.". ".$apartment['address']."</a>
												 <img id='fave_".$apartment['apartment_id']."' style=\"width: 60px; height: 34px;\" src='/models/site-templates/images/".($apartment[fave] ? "yellow_star.png" : "blue_star.png")."' onclick=".
														($apartment[fave] ? "\"post('".$_SERVER['PHP_SELF']."', {favorite_id: ".$apartment['apartment_id'].", type: 'delete'});\"" : "\"post('".$_SERVER['PHP_SELF']."', {favorite_id: ".$apartment['apartment_id'].", type: 'add'});\"").">
										    </div>
											<div id=\"panel-element-1".$apartment['apartment_id']."\" class=\"panel-collapse collapse\">
												<div class=\"panel-body\">
													<div id=\"apartment_".$apartment['apartment_id']."\"><a href='/apartment_listing.php?id=".$apartment['apartment_id']."' target='_blank'>".$apartment['address']."</a>
													</div>
												</div>
											</div>
										</div>
					";
				}
				echo "<br><br>
									</div>
								</div>
							</p>
						</div>
					</div>
				</div>
				<div class=\"row clearfix\">
					
				</div>
			</div>
		</div>
	</div>";
	
	//require the page containing the db information, etc
    require_once("models/footer.php");
	?>