<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}

	
	
	//$landlords = fetchLandlords($_SESSION['searchTerms']);
	$aptId = $_SESSION['flaw_apt'];
	$aptImages = fetchImages($aptId);
	//$flaw = getImageAptID($aptId);
	//getLastImage();
	//echo '<img src="data:image/jpeg;base64,'.base64_encode($showimage).'"/>';
	//echo $aptId;
	
	require_once("models/header.php");
	
	echo "
	<center>
	<div style='width:800px;'>
		<h2>Apartment Flaws Reported</h2>
		<br>
		<div class='table-responsive'>
			<table class='table table-striped'>
				<thead>
					<tr>
						<div style='width:100px;'>
						<th>Image</th>
						</div>
						<th>Location</th>
						<th>Description</th>
						<th></th>
					</tr>
				</thead>
				<tbody>";
					
					//Display list of pages
					foreach ($aptImages as $ll)
					{
						//".$ll['fieldNameFromFunction']."
						$temp = $ll['location'];
						$loc = 'null';
						if($temp==1)
							$loc = 'General';
						else if($temp==2)
							$loc = 'Bathroom';
						else if($temp==3)
							$loc = 'Kitchen';
						else if($temp==4)
							$loc = 'Bedroom';
						
						$temp2 = $ll['image'];
						$temp = $ll['description'];
						if($temp==NULL)
							$temp = 'None';
							
						echo "
						<tr>
							<td>
								<img src='data:image/jpeg;base64,".base64_encode($temp2)."'/>
							</td>
							<td>
								".$loc."
							</td>
							<td>
								".$temp."
							</td>
							
						</tr>";
					}
					// <a class='btn btn-primary' href='landlord_profile.php?id=".$ll['landlord_id']."'>View</a>
					if(empty($aptImages))
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
						</tr>";
					}
				echo "
				</tbody>
			</table>
			<a class='btn btn-primary' href='apartment_listing.php?id=".$aptId."'>Back to Listing</a>
			<br>
			<br>
			<br>
		</div>
	</center>";
	
	include 'models/footer.php';
?>