<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}

	if(empty($_POST))
	{
		$_POST['searchTerms'] = null;
		$searchTitle = "";
	}
	else
	{
		if($_POST['searchTerms'] != "")
		{
			$searchTitle = ": ".$_POST['searchTerms'];
		}
		else
		{
			$searchTitle = "";
		}
	}
	
	$listings = fetchListings($_POST['searchTerms']);
	
	require_once("models/header.php");
		
	echo "
	<center>
	<div style='width:900px;'>
		<h2>Apartment Listings".$searchTitle."</h2>
		<br>
		<div class='table-responsive'>
			<table id='grid-basic' class='table table-hover table-striped'>
				<thead>
					<tr>
						<th data-column-id='id' data-type='numeric' data-order='asc' data-identifier='true'>ID</th>
						<th data-column-id='img' data-sortable='false' data-formatter='img'>Pic</th>
						<th data-column-id='name'>Name</th>
						<th data-column-id='address' data-formatter='link2'>Address</th>
						<th data-column-id='rent'>Rent</th>
						<th data-column-id='bed_bath'>Bed/Bath</th>
						<th data-column-id='view' data-sortable='false' data-formatter='link'>View</th>
					</tr>
				</thead>
				<tbody>";
				
				if(!empty($listings))
				{
					$i = 1;
					//Display list of pages
					foreach ($listings as $apt)
					{
						//".$apt['fieldNameFromFunction']."
						echo "
						<tr>
							<td>
								".$i++."
							</td>
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
				}
				echo "
				</tbody>
			</table>
		</div>
	</center>";
?>

<script>
	<?php
		$php_array = jsFetchListings($_POST['searchTerms']);
		echo "var listings = ".json_encode($php_array).";\n";
	?>
	$("#grid-basic").bootgrid({
		url: "/api/data/basic",
		formatters: {
			"link": function(column, row)
			{
				return "<a class='btn btn-primary' href='apartment_listing.php?id=" + listings[row.id - 1]['apartment_id'] + "'>View</a>";
			},
			"link2": function(column, row)
			{
				return "<a href='map.php'>" + listings[row.id - 1]['address'] + "</a>";
			},
			"img": function(column, row)
			{
				return "<img src='/models/site-templates/images/no-image.png' alt='No Image' width='50' height='50'>";
			}
		}
	});
</script>

<?php
	
	
	include 'models/footer.php';
?>