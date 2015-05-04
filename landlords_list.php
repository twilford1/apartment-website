<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}

	//TODO UPDATE
	
	$landlords = fetchLandlords($_SESSION['searchTerms']);
	
	require_once("models/header.php");
	
	echo "
	<center>
	<div style='width:800px;'>
		<h2>Landlords and Property Management Companies</h2>
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
					foreach ($landlords as $ll)
					{
						//".$ll['fieldNameFromFunction']."
						echo "
						<tr>
							<td>
								<img src='/models/site-templates/images/no-image.png' alt='No Image' width='50' height='50'>
							</td>
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
					
					if(empty($landlords))
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
			<a href='apartment_post.php' class='btn btn-primary'>Post Properties</a>
			
		</div>
	</center>";
	
	include 'models/footer.php';
?>