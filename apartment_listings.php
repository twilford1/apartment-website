<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}

	//TODO UPDATE
	$_SESSION['searchTerms'] = null;
	$listings = fetchListings($_SESSION['searchTerms']);
	
	require_once("models/header.php");
	
	echo "
	<center>
	<div style='width:800px;'>
		<h2>Apartment Listings</h2>
		<br>
		<div class='table-responsive'>
			<table class='table table-striped'>
				<thead>
					<tr>
						<th> </th>
						<th>Name</th>
						<th>Address</th>
						<th>Rent</th>
						<th>Bed/Bath</th>
						<th></th>
					</tr>
				</thead>
				<tbody>";
				//Display list of pages
				foreach ($listings as $apt)
				{
					//".$apt['fieldNameFromFunction']."
					echo "
					<tr>
						<td>
							<img src='/models/site-templates/images/no-image.png' alt='No Image' width='50' height='50'>
						</td>
						<td>
							".$apt['name']."
						</td>
						<td>
							<a href='map.php'>".$apt['address']."</a>
						</td>
						<td>
							$".$apt['price']."
						</td>
						<td>
							".$apt['num_bedrooms']."/".$apt['num_bathrooms']."
						</td>
						<td>
							<a class='btn btn-primary' href='apartment_listing.php?id=".$apt['apartment_id']."'>View</a>
						</td>
					</tr>";
				}
				
				if(empty($listings))
				{
					echo "
					<tr>
						<td>
							-
						</td>
						<td>
							-
						</td>
						<td>
							-
						</td>
						<td>
							-
						</td>
						<td>
							-
						</td>
						<td>
							-
						</td>
					</tr>";
				}
				echo "
				</tbody>
			</table>
		</div>
	</center>";
	
	include 'models/footer.php';
?>