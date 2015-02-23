<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	//Forms posted
	if(!empty($_POST))
	{
		//Delete permission levels
		if(!empty($_POST['delete']))
		{
			$deletions = $_POST['delete'];
			if ($deletion_count = deletePermission($deletions))
			{
				$successes[] = lang("PERMISSION_DELETIONS_SUCCESSFUL", array($deletion_count));
			}
		}
		
		//Create new permission level
		if(!empty($_POST['newPermission']))
		{
			$permission = trim($_POST['newPermission']);
			
			//Validate request
			if (permissionNameExists($permission))
			{
				$errors[] = lang("PERMISSION_NAME_IN_USE", array($permission));
			}
			elseif (minMaxRange(1, 50, $permission))
			{
				$errors[] = lang("PERMISSION_CHAR_LIMIT", array(1, 50));	
			}
			else{
				if (createPermission($permission))
				{
				$successes[] = lang("PERMISSION_CREATION_SUCCESSFUL", array($permission));
				}
				else
				{
					$errors[] = lang("SQL_ERROR");
				}
			}
		}
	}
	$permissionData = fetchAllPermissions(); //Retrieve list of all permission levels
	
	require_once("models/header.php");
	echo "<center>";
	echo resultBlock($errors,$successes);
	echo "
	<div style='width:400px;'>		
		<form class='form-horizontal' name='adminPermissions' action='".$_SERVER['PHP_SELF']."' method='post'>
			<div class='form-group'>
				<div col-sm-12'>
					<h2>Permission Groups</h2>
				</div>
			</div>
			
			<table class='table table-striped'>
				<thead>
					<tr>
						<th>Delete</th>
						<th>Permission Name</th>
					</tr>
				</thead>
				<tbody>";
					//List each permission level
					foreach ($permissionData as $v1)
					{
						echo "
						<tr>
							<td>
								<input type='checkbox' name='delete[".$v1['id']."]' id='delete[".$v1['id']."]' value='".$v1['id']."'>
							</td>
							<td>
								<a href='admin_permission.php?id=".$v1['id']."'>".$v1['name']."</a>
							</td>
						</tr>";
					}
				echo "
				</tbody>
			</table>
			
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Permission Name</label>
				<div class='col-sm-7'>
					<input type='text' class='form-control' name='newPermission' />
				</div>
			</div>
			<div class='form-group'>
				<div class='col-sm-offset-5 col-sm-7'>
					<button type='submit' class='btn btn-primary' name='Submit'>Submit</button>
				</div>
			</div>
		</form>
	</div>";
	
	echo "</center>";
	
	include 'models/footer.php';
?>