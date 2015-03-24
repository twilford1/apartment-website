<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	//Forms posted
	if(!empty($_POST))
	{
		$deletions = $_POST['delete'];
		if ($deletion_count = deleteUsers($deletions))
		{
			$successes[] = lang("ACCOUNT_DELETIONS_SUCCESSFUL", array($deletion_count));
		}
		else
		{
			$errors[] = lang("SQL_ERROR");
		}
	}
	$userData = fetchAllUsers(); //Fetch information for all users
	
	require_once("models/header.php");
	
	echo "
	<div class='page-header'>
		<h1>Admin Users</h1>
	</div>
	<center>";
	
	echo resultBlock($errors, $successes);
	echo "	
		<div style='width:800px;'>
			<form name='adminUsers' action='".$_SERVER['PHP_SELF']."' method='post'>
				<table id='grid-selection' class='table table-striped'>
					<thead>
						<tr>
							<th data-column-id='id' data-type='numeric' data-identifier='true'>Delete</th>
							<th data-column-id='username'>Username</th>
							<th data-column-id='display-name'>Display Name</th>
							<th data-column-id='title'>Title</th>
							<th data-column-id='last-sign-in'>Last Sign In</th>
						</tr>
					</thead>
					<tbody>";
						//Cycle through users
						foreach ($userData as $v1)
						{
							echo "
							<tr>
								<td>
									".$v1['id']."
									<!--
									<input type='checkbox' name='delete[".$v1['id']."]' id='delete[".$v1['id']."]' value='".$v1['id']."'>
									-->
								</td>
								<td>
									<a href='admin_user.php?id=".$v1['id']."'>".$v1['user_name']."</a>
								</td>
								<td>
									".$v1['display_name']."
								</td>
								<td>
									".$v1['title']."
								</td>
								<td>";
									//Interprety last login
									if ($v1['last_sign_in_stamp'] == '0')
									{
										echo "Never";	
									}
									else
									{
										echo date("j M, Y", $v1['last_sign_in_stamp']);
									}
								echo "
								</td>
							</tr>";
						}
					
					echo "
					</tbody>
				</table>
						
				<input type='submit' name='Submit' value='Delete' class='btn btn-primary' style='max-width:200px;'/>
			</form>
		</div>
	
	</center>
	
	<script>
		$(document).ready(function(){
			//$('#grid-selection').bootgrid();
		});
	</script>";
	
	include 'models/footer.php';
?>