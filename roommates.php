<!--
Iowa City Apartment App: roommates php page
by Mattie Fickel 

Resources:

-->

<?php
//include in order to connect to the database
require_once("models/config.php");
//require the page containing the db information, etc
require_once("models/header.php");

//echo '<link rel="stylesheet" href="models/fancy_checkbox.css">';
	
if (!securePage($_SERVER['PHP_SELF']))
{
	die();
}

if(!isUserLoggedIn())
{
	header("Location: admin_users.php");
	die();
}

//save user's id
$user_id = $loggedInUser->user_id;
//save user's current roommates
$roommates = fetchRoommates($user_id);

if(!empty($_POST))
{
	//if the user is adding a roommate
	if (isset($_POST['Add'])) 
	{
        //get values from POST
		$roommate_name = $_POST['roommate_name'];
		//get the roommate's user_id
		$roommate_id = fetchUserID($roommate_name);
		//whether the roommate is already a roommate
		$isRoommate = false;
		
		foreach($roommates as $roommate)
		{
			if($roommate['roommate_id'] == $roommate_id)
			{
				$isRoommate = true;
			}
		}
		
		//if the roommate already exists inform user
		if($isRoommate)
		{
			echo '<div class="alert alert-warning">
					<a href="#" class="close" data-dismiss="alert">&times;</a>
						<strong>Error!</strong> '.$roommate_name.' already exists!
				  </div>';
		}
		else
		{
			//if the roommate exists as a user
			if(userIdExists($roommate_id))
			{
				//add each user to the other's roommates
				addRoommate($user_id, $roommate_id);
				addRoommate($roommate_id, $user_id);
				
				//add the roommate for the user's other roommates (assuming they all live together)
				foreach($roommates as $roommate)
				{
					addRoommate($roommate['roommate_id'], $roommate_id);
					addRoommate($roommate_id, $roommate['roommate_id']);
				}
				
				echo '<div class="alert alert-success">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
							<strong>Success!</strong> Your roommate has been added successfully.
					  </div>';
			}
			//else the roommate does not have an account
			else
			{
				echo '<div class="alert alert-warning">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
							<strong>Error!</strong> '.$roommate_name.' is not a user on this site!
							Please make sure the user\'s username is correct.
					  </div>';
			}
		}
    } 
	//else the user is removing a roommate
	else 
	{
		//get roommate info from POST
        $roommate = $_POST['remove'];
		$roommate_id = $roommate[roommate_id];
		$roommate_name = fetchUsername($roommate_id);
		
		//whether the roommate is already a roommate
		$isRoommate = false;
		
		foreach($roommates as $roommate)
		{
			if($roommate['roommate_id'] == $roommate_id)
			{
				$isRoommate = true;
			}
		}
		
		//if the roommate exists
		if($isRoommate)
		{
				//remove each user from the other's roommates
				removeRoommate($user_id, $roommate_id);
				removeRoommate($roommate_id, $user_id);
				
				//remove the roommate for the user's other roommates (assuming they all live together)
				foreach($roommates as $roommate)
				{
					removeRoommate($roommate['roommate_id'], $roommate_id);
					removeRoommate($roommate_id, $roommate['roommate_id']);
				}
				
				echo '<div class="alert alert-success">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
							<strong>Success!</strong> Your roommate has been removed successfully.
					  </div>';
		}
		//else the roommate does not exist in the first place
		else
		{
			echo '<div class="alert alert-warning">
					<a href="#" class="close" data-dismiss="alert">&times;</a>
						<strong>Error!</strong> '.$roommate_name.' is not a roommate of yours!
				  </div>';
		}
    }
}

$roommates = fetchRoommates($user_id);

echo '<center><h2>Manage Roommates</h2>Use the table below to manage the roommates associated with your profile.<br><hr><br>
<!--table-->
	<div class="row" style="width:600px">
		<div class="panel panel-primary filterable">
			<div class="panel-heading">
				<h3 class="panel-title">Roommates</h3>
			</div>
			<table class="table">
				<thead>
					<tr class="filters">
						<th>Roommate Username</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<form name=\'updateRoommates\' class=\'form-horizontal\' action="'.$_SERVER['PHP_SELF'].'" method=\'post\'>';
				
				foreach($roommates as $roommate)
				{
					echo '</td><td>'. fetchUsername($roommate['roommate_id']) .
						 '</td><td><button type=\'submit\' class=\'btn btn-primary\' value ='.$roommate['roommate_id'].' name="remove[roommate_id]">Remove</button>
						 </td><td></td></tr>';
				}
				
echo '		</form>
				<form name=\'addRoommate\' class=\'form-horizontal\' action="'.$_SERVER['PHP_SELF'].'" method=\'post\'>
					<th><input type="text" class="form-control" placeholder="i.e. john_smith" name="roommate_name"></th>
					<th><button style="width:78px" type=\'submit\' class=\'btn btn-primary\' name=\'Add\'>Add</button></th>
					<th></th>
				</form>
			</tbody>
		</table>
	</div>
</div></center>';
	
	include 'models/footer.php';

?>