<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	$pages = getPageFiles(); //Retrieve list of pages in root usercake folder
	$dbpages = fetchAllPages(); //Retrieve list of pages in pages table
	$creations = array();
	$deletions = array();
	//Check if any pages exist which are not in DB
	foreach ($pages as $page)
	{
		if(!isset($dbpages[$page]))
		{
			$creations[] = $page;	
		}
	}
	//Enter new pages in DB if found
	if (count($creations) > 0)
	{
		createPages($creations)	;
	}
	if (count($dbpages) > 0)
	{
		//Check if DB contains pages that don't exist
		foreach ($dbpages as $page)
		{
			if(!isset($pages[$page['page']]))
			{
				$deletions[] = $page['id'];	
			}
		}
	}
	//Delete pages from DB if not found
	if (count($deletions) > 0)
	{
		deletePages($deletions);
	}
	//Update DB pages
	$dbpages = fetchAllPages();
	require_once("models/header.php");
	
	echo "	
	<div class='page-header'>
		<h1>Pages</h1>
	</div>
	
	<center>
		<div style='width:600px;'>
			<div class='table-responsive'>
				<form name='adminUsers' action='".$_SERVER['PHP_SELF']."' method='post'>
					<table id='grid-basic' class='table table-striped'>
						<thead>
							<tr>
								<th data-column-id='id' data-type='numeric' data-order='asc'>Id</th>
								<th data-column-id='page' data-formatter='link' data-sortable='false'>Page</th>
								<th data-column-id='access'>Access</th>
							</tr>
						</thead>
						<tbody>";
							//Display list of pages
							foreach ($dbpages as $page)
							{
								echo "
								<tr>
									<td>
										".$page['id']."
									</td>
									<td>
										<a href ='admin_page.php?id=".$page['id']."'>".$page['page']."</a>
									</td>
									
									<td>";
										//Show public/private setting of page
										if($page['private'] == 0)
										{
											echo "Public";
										}
										else
										{
											echo "Private";	
										}
										
										echo "
									</td>
								</tr>";
							}
						echo "
						</tbody>
					</table>
				</form>
			</div>
		</div>	
	</center>
	
	<script>
		$(document).ready(function(){
			$('#grid-basic').bootgrid();
		});
	</script>
	";
	
	include 'models/footer.php';
?>