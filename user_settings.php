<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	//Prevent the user visiting the page if they are not logged in
	if(!isUserLoggedIn())
	{
		header("Location: login.php");
		die();
	}
	
	if(!empty($_POST))
	{
		if(isset($_POST['requestLandlord']))
		{
			$messageContent = fetchUsername($loggedInUser->user_id)." has requested to become a landlord. Their ID is: ".$loggedInUser->user_id;
			if(sendAdminsMessage($loggedInUser->user_id, "Landlord Request", $messageContent))
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
			$messageContent = fetchUsername($loggedInUser->user_id)." has requested to become an admin. Their ID is: ".$loggedInUser->user_id;
			if(sendAdminsMessage($loggedInUser->user_id, "Admin Request", $messageContent))
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
			$email = $_POST["email"];
			$gender = $_POST["gender"];
			$privateProfile = $_POST["privateProfile"];
			$description = $_POST["description"];
			
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
			
			if($gender != $loggedInUser->gender && count($errors) == 0)
			{
				$loggedInUser->updateGender($gender);
				$successes[] = lang("ACCOUNT_GENDER_UPDATED");
			}
			
			if($privateProfile != $loggedInUser->private_profile && count($errors) == 0)
			{
				$loggedInUser->updatePrivateProfile((int) $privateProfile);
				$successes[] = lang("ACCOUNT_PRIVATE_PROFILE_UPDATED");
			}
			
			if($description != $loggedInUser->description && count($errors) == 0)
			{
				$loggedInUser->updateDescription($description);
				$successes[] = lang("ACCOUNT_DESCRIPTION_UPDATED");
			}
			
			if(count($errors) == 0 AND count($successes) == 0)
			{
				$errors[] = lang("NOTHING_TO_UPDATE");
			}
		}
	}
	
	require_once("models/header.php");
	
	echo "<center>";
	echo resultBlock($errors, $successes);
	echo "</center>";
	
	echo "
	<link rel='stylesheet' href='http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/css/bootstrapValidator.min.css'/>
	<script type='text/javascript' src='http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js'></script>
	
	<div class='page-header'>
		<h1>
			User Settings
		</h1>
	</div>	
	
	<center>
	
	<div style='width:500px;'>
		<form name='updateAccount' class='form-horizontal' id='updateUsrAccount' action='".$_SERVER['PHP_SELF']."' method='post'>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Profile Picture</label>
				<div class='col-sm-7'>
					<img alt='User Pic' src=".get_gravatar($loggedInUser->email, 80, 'mm','x', false )." class='img-circle'>
					<br>
					<br>
					<a href='https://en.gravatar.com/emails/' class='btn btn-primary' title='Use $loggedInUser->email for gravatar'>Change Picture</a>
					<br><br>
					<a href='http://www.apartment.duckdns.org/roommates.php' class='btn btn-primary' title='Manage roommates'>Manage roommates</a>
			   </div>
			</div>
			<br>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Current Password</label>
				<div class='col-sm-7'>
					<input type='password' class='form-control' name='password' id='password' placeholder='Current Password (Required)'>
				</div>
			</div>
			<br>
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
			<br>			
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Account Type</label>
				<div class='col-sm-7'>";
					if($loggedInUser->checkPermission(array(2)))
					{						
						echo "<p class='form-control-static' style='float:left;'>$loggedInUser->title</p>";
					}
					else
					{
						echo "<p class='form-control-static' style='float:left;'><a href'#' data-toggle='collapse' data-target='#requestPermissions'>$loggedInUser->title</a></p>";
					}
				echo "
				</div>
			</div>
			<div class='collapse form-group' id='requestPermissions'>
				<label class='col-sm-5 control-label'></label>
				<div class='col-sm-7'>";
					$requestTimestamp = permissionRequestSent($loggedInUser->user_id);
					if(isset($requestTimestamp))
					{
						echo "<p class='form-control-static' style='float:left;'>Permissions requested on <b>".date("M d, Y", $requestTimestamp['timestamp'])."</b></p>";
					}
					else
					{
						echo "<button type='submit' name='requestLandlord' class='btn btn-primary' name='Update'>Request Landlord</button> <button type='submit' name='requestAdmin' class='btn btn-primary' name='Update'>Request Admin</button>";
					}
				echo "
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Email</label>
				<div class='col-sm-7'>
					<input type='text' class='form-control' name='email' value='".$loggedInUser->email."' placeholder='Email'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Gender</label>
				<div class='col-sm-7'>
					<select class='form-control' name='gender'>";
						$genderArray = array("male", "female", "unspecified");
						foreach ($genderArray as $g)
						{
							if ($loggedInUser->gender == $g)
							{
								echo "<option value='".$g."' selected>$g</option>";
							}
							else
							{
								echo "<option value='".$g."'>$g</option>";
							}
						}
					echo "
					</select>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Private Profile</label>
				<div class='col-sm-7'>
					<select class='form-control' name='privateProfile'>";
						$privateProfileArray = array("No" => 0, "Yes" => 1);
						foreach ($privateProfileArray as $p)
						{
							if ($loggedInUser->private_profile == $p)
							{
								echo "<option value='".$p."' selected>".array_search($p, $privateProfileArray)."</option>";
							}
							else
							{
								echo "<option value='".$p."'>".array_search($p, $privateProfileArray)."</option>";
							}
						}
					echo "
					</select>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-5 control-label'>Description</label>
				<div class='col-sm-7'>
					<textarea name='description' class='form-control' rows='3'>".$loggedInUser->description."</textarea>
				</div>
			</div>
				
			<br>
			<div class='form-group'>
				<div class='col-sm-offset-5 col-sm-7'>
					<button type='submit' class='btn btn-primary' name='Update'>Update Info</button>
				</div>
			</div>
		</form>			
		
	</div>";
	
	echo "</center>";
	
?>

<script>
	$('#updateUsrAccount').bootstrapValidator({
		live: 'disabled',
        message: 'This value is not valid',
        feedbackIcons: {
            //valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            password: {
                validators: {
                    notEmpty: {
                        message: 'Your password is required and cannot be empty'
                    }
                }
            }
        }
    });
</script>

<?php
	include 'models/footer.php';
?>