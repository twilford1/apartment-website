<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	//Prevent the user visiting the logged in page if he/she is already logged in
	if(isUserLoggedIn())
	{
		header("Location: account.php");
		die();
	}
	//Forms posted
	if(!empty($_POST))
	{
		$errors = array();
		$username = sanitize(trim($_POST["username"]));
		$password = trim($_POST["password"]);
		
		//Perform some validation
		//Feel free to edit / change as required
		if($username == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
		}
		if($password == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
		}
		if(count($errors) == 0)
		{
			//A security note here, never tell the user which credential was incorrect
			if(!usernameExists($username))
			{
				$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
			}
			else
			{
				$userdetails = fetchUserDetails($username);
				//See if the user's account is activated
				if($userdetails["active"]==0)
				{
					$errors[] = lang("ACCOUNT_INACTIVE");
				}
				else
				{
					//Hash the password and use the salt from the database to compare the password.
					$entered_pass = generateHash($password,$userdetails["password"]);
					
					if($entered_pass != $userdetails["password"])
					{
						//Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
						$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
					}
					else
					{
						//Passwords match! we're good to go'
						
						//Construct a new logged in user object
						//Transfer some db data to the session object
						$loggedInUser = new loggedInUser();
						$loggedInUser->email = $userdetails["email"];
						$loggedInUser->user_id = $userdetails["id"];
						$loggedInUser->hash_pw = $userdetails["password"];
						$loggedInUser->title = $userdetails["title"];
						$loggedInUser->displayname = $userdetails["display_name"];
						$loggedInUser->username = $userdetails["user_name"];
						$loggedInUser->gender = $userdetails["gender"];
						$loggedInUser->private_profile = $userdetails["private_profile"];
						$loggedInUser->description = $userdetails["description"];
						
						//Update last sign in
						$loggedInUser->updateLastSignIn();
						$_SESSION["userCakeUser"] = $loggedInUser;
						
						//Redirect to user account page
						header("Location: account.php");
						die();
					}
				}
			}
		}
	}
	require_once("models/header.php");
	
	echo "<center>";
	echo resultBlock($errors, $successes);
	echo "
		<div style='width:400px;'>
			<form class='form-horizontal' name='login' action='".$_SERVER['PHP_SELF']."' method='post'>
				<div class='form-group'>
					<div class='col-sm-offset-3 col-sm-9'>
						<h2>Log-in</h2>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-3 control-label'>Username</label>
					<div class='col-sm-9'>
						<input type='text' class='form-control' name='username' placeholder='Username'>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-3 control-label'>Password </label>
					<div class='col-sm-9'>
						<input type='password' class='form-control' name='password' placeholder='Password'>
					</div>
				</div>
				<div class='form-group'>
					<div class='col-sm-offset-3 col-sm-9'>
						<button type='submit' class='btn btn-primary' name='login'>Login</button>
					</div>
				</div>
				<div class='form-group'>
					<div class='col-sm-offset-3 col-sm-9'>
						<a href='register.php'>Register</a> - <a href='forgot-password.php'>Forgot Password</a>
					</div>
				</div>
			</form>			
		</div>";
	
	echo "</center>";
	
	include 'models/footer.php';
?>