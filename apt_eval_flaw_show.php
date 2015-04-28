<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}

	
	
	//$landlords = fetchLandlords($_SESSION['searchTerms']);
	$aptId = $_GET['apartment_id'];
	$flaw = getImageAptID($aptId);
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
						<th> </th>
						<th>Name</th>
						<th>Address</th>
						<th>Contact</th>
						<th></th>
					</tr>
				</thead>
				<tbody>";
					
					//Display list of pages
					foreach ($flaws as $ll)
					{
						//".$ll['fieldNameFromFunction']."
						echo "
						<tr>
							<td>
								".$ll['name']."
							</td>
							<td>
								<a href=''>".$ll['address']."</a>
							</td>
							<td>
								".$ll['email']."
							</td>
							<td>
								<a class='btn btn-primary' href='landlord_profile.php?id=".$ll['landlord_id']."'>View</a>
							</td>
						</tr>";
					}
					
					if(empty($flaws))
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
		</div>
	</center>";
	
	include 'models/footer.php';
?>