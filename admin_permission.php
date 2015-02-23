<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	$permissionId = $_GET['id'];
	//Check if selected permission level exists
	if(!permissionIdExists($permissionId))
	{
		header("Location: admin_permissions.php");
		die();	
	}
	$permissionDetails = fetchPermissionDetails($permissionId); //Fetch information specific to permission level
	//Forms posted
	if(!empty($_POST))
	{
		
		//Delete selected permission level
		if(!empty($_POST['delete']))
		{
			$deletions = $_POST['delete'];
			if ($deletion_count = deletePermission($deletions))
			{
				$successes[] = lang("PERMISSION_DELETIONS_SUCCESSFUL", array($deletion_count));
			}
			else
			{
				$errors[] = lang("SQL_ERROR");	
			}
		}
		else
		{
			//Update permission level name
			if($permissionDetails['name'] != $_POST['name'])
			{
				$permission = trim($_POST['name']);
				
				//Validate new name
				if (permissionNameExists($permission))
				{
					$errors[] = lang("ACCOUNT_PERMISSIONNAME_IN_USE", array($permission));
				}
				elseif (minMaxRange(1, 50, $permission))
				{
					$errors[] = lang("ACCOUNT_PERMISSION_CHAR_LIMIT", array(1, 50));	
				}
				else {
					if (updatePermissionName($permissionId, $permission))
					{
						$successes[] = lang("PERMISSION_NAME_UPDATE", array($permission));
					}
					else
					{
						$errors[] = lang("SQL_ERROR");
					}
				}
			}
			
			//Remove access to pages
			if(!empty($_POST['removePermission']))
			{
				$remove = $_POST['removePermission'];
				if ($deletion_count = removePermission($permissionId, $remove))
				{
					$successes[] = lang("PERMISSION_REMOVE_USERS", array($deletion_count));
				}
				else
				{
					$errors[] = lang("SQL_ERROR");
				}
			}
			
			//Add access to pages
			if(!empty($_POST['addPermission']))
			{
				$add = $_POST['addPermission'];
				if ($addition_count = addPermission($permissionId, $add))
				{
					$successes[] = lang("PERMISSION_ADD_USERS", array($addition_count));
				}
				else
				{
					$errors[] = lang("SQL_ERROR");
				}
			}
			
			//Remove access to pages
			if(!empty($_POST['removePage']))
			{
				$remove = $_POST['removePage'];
				if ($deletion_count = removePage($remove, $permissionId))
				{
					$successes[] = lang("PERMISSION_REMOVE_PAGES", array($deletion_count));
				}
				else
				{
					$errors[] = lang("SQL_ERROR");
				}
			}
			
			//Add access to pages
			if(!empty($_POST['addPage']))
			{
				$add = $_POST['addPage'];
				if ($addition_count = addPage($add, $permissionId))
				{
					$successes[] = lang("PERMISSION_ADD_PAGES", array($addition_count));
				}
				else
				{
					$errors[] = lang("SQL_ERROR");
				}
			}
			$permissionDetails = fetchPermissionDetails($permissionId);
		}
	}
	$pagePermissions = fetchPermissionPages($permissionId); //Retrieve list of accessible pages
	$permissionUsers = fetchPermissionUsers($permissionId); //Retrieve list of users with membership
	$userData = fetchAllUsers(); //Fetch all users
	$pageData = fetchAllPages(); //Fetch all pages
	require_once("models/header.php");
	
	echo "<center>";
	echo resultBlock($errors,$successes);
	echo "
	<div style='width:600px;'>
		<h2>Group: ".$permissionDetails['name']."</h2>
		
		<form name='adminPermission' action='".$_SERVER['PHP_SELF']."?id=".$permissionId."' method='post'>
			<table class='table table-striped'>
				<thead>
					<tr>
						<th>Permission Information</th>
						<th>Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<b>ID:</b>
						</td>
						<td>
							".$permissionDetails['id']."
						</td>
					</tr>
					<tr>
						<td>
							<b>Name:</b>
						</td>
						<td>
							<input type='text' class='form-control' name='name' value='".$permissionDetails['name']."' />
						</td>
					</tr>
					<tr>
						<td>
							<b>Delete:</b>
						</td>
						<td>
							<input type='checkbox' name='delete[".$permissionDetails['id']."]' id='delete[".$permissionDetails['id']."]' value='".$permissionDetails['id']."'>
						</td>
					</tr>
					
					<thead>
						<tr>
							<th>Permission Membership</th>
							<th>Users</th>
						</tr>
					</thead>
				
					<tr>
						<td>
							<b>Remove Members:</b>
						</td>
						<td>";
							//List users with permission level
							foreach ($userData as $v1)
							{
								if(isset($permissionUsers[$v1['id']]))
								{
									echo "<input type='checkbox' name='removePermission[".$v1['id']."]' id='removePermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['display_name'];
									echo "<br>";
								}
							}
							echo"
						</td>
					</tr>					
					<tr>
						<td>
							<b>Add Members:</b>
						</td>
						<td>";
							//List users without permission level
							foreach ($userData as $v1)
							{
								if(!isset($permissionUsers[$v1['id']]))
								{
									echo "<input type='checkbox' name='addPermission[".$v1['id']."]' id='addPermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['display_name'];
									echo "<br>";
								}
							}
							echo"
						</td>
					</tr>
					
					<thead>
						<tr>
							<th>Permission Access</th>
							<th>Pages</th>
						</tr>
					</thead>
				
					<tr>
						<td>
							<b>Public Access:</b>
						</td>
						<td>";
							//List public pages
							foreach ($pageData as $v1)
							{
								if($v1['private'] != 1)
								{
									echo $v1['page']."<br>";
								}
							}
							echo"
						</td>
					</tr>					
					<tr>
						<td>
							<b>Remove Access:</b>
						</td>
						<td>";
							//List pages accessible to permission level
							foreach ($pageData as $v1)
							{
								if(isset($pagePermissions[$v1['id']]) AND $v1['private'] == 1)
								{
									echo "<input type='checkbox' name='removePage[".$v1['id']."]' id='removePage[".$v1['id']."]' value='".$v1['id']."'> ".$v1['page'];
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
							//List pages inaccessible to permission level
							foreach ($pageData as $v1)
							{
								if(!isset($pagePermissions[$v1['id']]) AND $v1['private'] == 1)
								{
									echo "<input type='checkbox' name='addPage[".$v1['id']."]' id='addPage[".$v1['id']."]' value='".$v1['id']."'> ".$v1['page'];
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
	</div>";
	
	echo "</center>";
	
	include 'models/footer.php';
?>