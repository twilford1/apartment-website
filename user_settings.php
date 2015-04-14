<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	//Prevent the user visiting the logged in page if he is not logged in
	if(!isUserLoggedIn())
	{
		header("Location: login.php");
		die();
	}
	
	if(!empty($_POST))
	{
		if(isset($_POST['requestLandlord']))
		{
			// TODO update
			$messageContent = fetchUsername($loggedInUser->user_id)." has requested to become a landlord. Their ID is: ".$loggedInUser->user_id;
			if(newMessage($loggedInUser->user_id, 1, "Landlord Request", $messageContent, 0))
			{
				$successes = [0 => "Landlord Request Sent"];
			}
			else
			{
				$errors[] = lang("SQL_ERROR");
			}
		}
		else if(isset($_POST['requestAdmin']))
		{			
			// TODO update
			$messageContent = fetchUsername($loggedInUser->user_id)." has requested to become an admin. Their ID is: ".$loggedInUser->user_id;
			if(newMessage($loggedInUser->user_id, 1, "Admin Request", $messageContent, 0))
			{
				$successes = [0 => "Admin Request Sent"];
			}
			else
			{
				$errors[] = lang("SQL_ERROR");
			}
		}
		else
		{
			$errors = array();
			$successes = array();
			$password = $_POST["password"];
			$password_new = $_POST["passwordc"];
			$password_confirm = $_POST["passwordcheck"];
			
			$errors = array();
			$email = $_POST["email"];
			
			//Perform some validation
			//Feel free to edit / change as required
			
			//Confirm the hashes match before updating a users password
			$entered_pass = generateHash($password, $loggedInUser->hash_pw);
			
			if (trim($password) == "")
			{
				$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
			}
			else if($entered_pass != $loggedInUser->hash_pw)
			{
				//No match
				$errors[] = lang("ACCOUNT_PASSWORD_INVALID");
			}	
			
			if($email != $loggedInUser->email)
			{
				if(trim($email) == "")
				{
					$errors[] = lang("ACCOUNT_SPECIFY_EMAIL");
				}
				else if(!isValidEmail($email))
				{
					$errors[] = lang("ACCOUNT_INVALID_EMAIL");
				}
				else if(emailExists($email))
				{
					$errors[] = lang("ACCOUNT_EMAIL_IN_USE", array($email));	
				}
				
				//End data validation
				if(count($errors) == 0)
				{
					$loggedInUser->updateEmail($email);
					$successes[] = lang("ACCOUNT_EMAIL_UPDATED");
				}
			}
			
			if ($password_new != "" OR $password_confirm != "")
			{
				if(trim($password_new) == "")
				{
					$errors[] = lang("ACCOUNT_SPECIFY_NEW_PASSWORD");
				}
				else if(trim($password_confirm) == "")
				{
					$errors[] = lang("ACCOUNT_SPECIFY_CONFIRM_PASSWORD");
				}
				else if(minMaxRange(8,50,$password_new))
				{	
					$errors[] = lang("ACCOUNT_NEW_PASSWORD_LENGTH", array(8, 50));
				}
				else if($password_new != $password_confirm)
				{
					$errors[] = lang("ACCOUNT_PASS_MISMATCH");
				}
				
				//End data validation
				if(count($errors) == 0)
				{
					//Also prevent updating if someone attempts to update with the same password
					$entered_pass_new = generateHash($password_new, $loggedInUser->hash_pw);
					
					if($entered_pass_new == $loggedInUser->hash_pw)
					{
						//Don't update, this fool is trying to update with the same password Â¬Â¬
						$errors[] = lang("ACCOUNT_PASSWORD_NOTHING_TO_UPDATE");
					}
					else
					{
						//This function will create the new hash and update the hash_pw property.
						$loggedInUser->updatePassword($password_new);
						$successes[] = lang("ACCOUNT_PASSWORD_UPDATED");
					}
				}
			}
			if(count($errors) == 0 AND count($successes) == 0)
			{
				$errors[] = lang("NOTHING_TO_UPDATE");
			}
		}
	}
	
	
	if($loggedInUser->checkPermission(array(1)))
	{
		$requestButtons = "<button type='submit' name='requestLandlord' class='btn btn-primary' name='Update'>Request Landlord</button> <button type='submit' name='requestAdmin' class='btn btn-primary' name='Update'>Request Admin</button>";
	}
	else if($loggedInUser->checkPermission(array(3)))
	{
		$requestButtons = "<button type='submit' name='requestAdmin' class='btn btn-primary' name='Update'>Request Admin</button>";
	}
	else
	{
		$requestButtons = "";
	}
	
	require_once("models/header.php");
	
	echo "<center>";
	echo resultBlock($errors, $successes);
	echo "</center>";
	
	echo "
	<div class='page-header'>
		<h1>
			User Settings
		</h1>
	</div>	
	
	<center>
	
	<div style='width:500px;'>
		<form name='updateAccount' class='form-horizontal' action='".$_SERVER['PHP_SELF']."' method='post'>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Current Password</label>
				<div class='col-sm-7'>
					<input type='password' class='form-control' name='password' placeholder='Current Password'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Email</label>
				<div class='col-sm-7'>
					<input type='text' class='form-control' name='email' value='".$loggedInUser->email."' placeholder='Email'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>New Password</label>
				<div class='col-sm-7'>
					<input type='password' class='form-control' name='passwordc' placeholder='New Password'>					
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Confirm Password</label>
				<div class='col-sm-7'>
					<input type='password' class='form-control' name='passwordcheck' placeholder='Confirm Password'>					
				</div>
			</div>
			<div class='form-group'>
				<div class='col-sm-offset-5 col-sm-7'>
					<button type='submit' class='btn btn-primary' name='Update'>Update</button>
					<br>
					<br>
					<a href='https://signup.wordpress.com/signup/?ref=oauth2&oauth2_redirect=eafa52c08af7306830929a914678c9ea%40https%3A%2F%2Fpublic-api.wordpress.com%2Foauth2%2Fauthorize%2F%3Fclient_id%3D1854%26response_type%3Dcode%26blog_id%3D0%26state%3D081f7f317b7890c132e23c865cf97ea4c7dabe03596fada55c3d1638a9603ea7%26redirect_uri%3Dhttps%253A%252F%252Fen.gravatar.com%252Fconnect%252F%253Faction%253Drequest_access_token%26jetpack-code%26jetpack-user-id%3D0%26action%3Doauth2-login&wpcom_connect=1' class='btn btn-primary'>Edit Profile Picture</a>
					<br>
					<label class='col-sm-60 control-label'>Note: Please sign-up/login with your ApartmentFinder Email</label>
					<br>
					<br>
					<br>						
					".$requestButtons."
				</div>
			</div>
		</form>			
		
	</div>";
	
	echo "</center>";
	
	include 'models/footer.php';
?>