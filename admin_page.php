<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	$pageId = $_GET['id'];
	
	//Check if selected pages exist
	if(!pageIdExists($pageId))
	{
		header("Location: admin_pages.php");
		die();	
	}
	$pageDetails = fetchPageDetails($pageId); //Fetch information specific to page
	//Forms posted
	if(!empty($_POST))
	{
		$update = 0;
		
		if(!empty($_POST['private']))
		{
			$private = $_POST['private'];
		}
		
		//Toggle private page setting
		if (isset($private) AND $private == 'Yes')
		{
			if ($pageDetails['private'] == 0)
			{
				if (updatePrivate($pageId, 1))
				{
					$successes[] = lang("PAGE_PRIVATE_TOGGLED", array("private"));
				}
				else
				{
					$errors[] = lang("SQL_ERROR");
				}
			}
		}
		else if ($pageDetails['private'] == 1){
			if (updatePrivate($pageId, 0))
			{
				$successes[] = lang("PAGE_PRIVATE_TOGGLED", array("public"));
			}
			else
			{
				$errors[] = lang("SQL_ERROR");	
			}
		}
		
		//Remove permission level(s) access to page
		if(!empty($_POST['removePermission']))
		{
			$remove = $_POST['removePermission'];
			if ($deletion_count = removePage($pageId, $remove))
			{
				$successes[] = lang("PAGE_ACCESS_REMOVED", array($deletion_count));
			}
			else
			{
				$errors[] = lang("SQL_ERROR");	
			}
			
		}
		
		//Add permission level(s) access to page
		if(!empty($_POST['addPermission']))
		{
			$add = $_POST['addPermission'];
			if ($addition_count = addPage($pageId, $add))
			{
				$successes[] = lang("PAGE_ACCESS_ADDED", array($addition_count));
			}
			else
			{
				$errors[] = lang("SQL_ERROR");	
			}
		}
		
		$pageDetails = fetchPageDetails($pageId);
	}
	$pagePermissions = fetchPagePermissions($pageId);
	$permissionData = fetchAllPermissions();
	require_once("models/header.php");	
	
	echo "<center>";
	echo resultBlock($errors, $successes);
	echo "</center>";
	
	echo "
	
	<div class='page-header'>
		<h1>
			<a href ='admin_pages.php' class='btn btn-default'>
				<span class='glyphicon glyphicon-circle-arrow-left'></span>
			</a>
			Page Information: ".$pageDetails['page']."
		</h1>
	</div>
	
	<center>
		<div style='width:600px;'>
			
			<form name='adminPage' action='".$_SERVER['PHP_SELF']."?id=".$pageId."' method='post'>
				<table class='table table-striped'>
					<thead>
						<tr>
							<th>Page Information</th>
							<th>Page Access</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<b>ID:</b>
							</td>
							<td>
								".$pageDetails['id']."
							</td>
						</tr>
						<tr>
							<td>
								<b>Name:</b>
							</td>
							<td>
								".$pageDetails['page']."
							</td>
						</tr>
						<tr>
							<td>
								<b>Private:</b>
							</td>
							<td>";
								//Display private checkbox
								if ($pageDetails['private'] == 1)
								{
									echo "<input type='checkbox' name='private' id='private' value='Yes' checked>";
								}
								else
								{
									echo "<input type='checkbox' name='private' id='private' value='Yes'>";	
								}
							echo "
							</td>
						</tr>
						
						<thead>
							<tr>
								<th>Permission Setting</th>
								<th>Groups</th>
							</tr>
						</thead>
						
						<tr>
							<td>
								<b>Remove Access:</b>
							</td>
							<td>";
								//Display list of permission levels with access
								foreach ($permissionData as $v1)
								{
									if(isset($pagePermissions[$v1['id']]))
									{
										echo "<input type='checkbox' name='removePermission[".$v1['id']."]' id='removePermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['name'];
										echo "<br>";
									}
								}
								echo"
							</td>
						</tr>
						<tr>
							<td>
								<b>Add Access:</b>
							</td>
							<td>";
								//Display list of permission levels without access
								foreach ($permissionData as $v1)
								{
									if(!isset($pagePermissions[$v1['id']]))
									{
										echo "<input type='checkbox' name='addPermission[".$v1['id']."]' id='addPermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['name'];
										echo "<br>";
									}
								}
								echo"
							</td>
						</tr>
					</tbody>
				</table>
				<input type='submit' value='Update' class='btn btn-primary' style='max-width:200px;' />
			</form>
		</div>
	</center>";
	
	include 'models/footer.php';
?>