<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	//Forms posted
	if(!empty($_POST) && $emailActivation)
	{
		$email = $_POST["email"];
		$username = $_POST["username"];
		
		//Perform some validation
		//Feel free to edit / change as required
		if(trim($email) == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_EMAIL");
		}
		//Check to ensure email is in the correct format / in the db
		else if(!isValidEmail($email) || !emailExists($email))
		{
			$errors[] = lang("ACCOUNT_INVALID_EMAIL");
		}
		
		if(trim($username) == "")
		{
			$errors[] =  lang("ACCOUNT_SPECIFY_USERNAME");
		}
		else if(!usernameExists($username))
		{
			$errors[] = lang("ACCOUNT_INVALID_USERNAME");
		}
		
		if(count($errors) == 0)
		{
			//Check that the username / email are associated to the same account
			if(!emailUsernameLinked($email,$username))
			{
				$errors[] = lang("ACCOUNT_USER_OR_EMAIL_INVALID");
			}
			else
			{
				$userdetails = fetchUserDetails($username);
				
				//See if the user's account is activation
				if($userdetails["active"]==1)
				{
					$errors[] = lang("ACCOUNT_ALREADY_ACTIVE");
				}
				else
				{
					if ($resend_activation_threshold == 0)
					{
						$hours_diff = 0;
					}
					else
					{
						$last_request = $userdetails["last_activation_request"];
						$hours_diff = round((time()-$last_request) / (3600*$resend_activation_threshold),0);
					}
					
					if($resend_activation_threshold!=0 && $hours_diff <= $resend_activation_threshold)
					{
						$errors[] = lang("ACCOUNT_LINK_ALREADY_SENT",array($resend_activation_threshold));
					}
					else
					{
						//For security create a new activation url;
						$new_activation_token = generateActivationToken();
						
						if(!updateLastActivationRequest($new_activation_token,$username,$email))
						{
							$errors[] = lang("SQL_ERROR");
						}
						else
						{
							$mail = new userCakeMail();
							
							$activation_url = $websiteUrl."activate-account.php?token=".$new_activation_token;
							
							//Setup our custom hooks
							$hooks = array(
								"searchStrs" => array("#ACTIVATION-URL","#USERNAME#"),
								"subjectStrs" => array($activation_url,$userdetails["display_name"])
								);
							
							if(!$mail->newTemplateMsg("resend-activation.txt",$hooks))
							{
								$errors[] = lang("MAIL_TEMPLATE_BUILD_ERROR");
							}
							else
							{
								if(!$mail->sendMail($userdetails["email"],"Activate your ".$websiteName." Account"))
								{
									$errors[] = lang("MAIL_ERROR");
								}
								else
								{
									//Success, user details have been updated in the db now mail this information out.
									$successes[] = lang("ACCOUNT_NEW_ACTIVATION_SENT");
								}
							}
						}
					}
				}
			}
		}
	}
	//Prevent the user visiting the logged in page if he/she is already logged in
	if(isUserLoggedIn())
	{
		header("Location: account.php");
		die();
	}
	require_once("models/header.php");
	
	echo "<center>";
	echo resultBlock($errors,$successes);
	//Show disabled if email activation not required
	if(!$emailActivation)
	{ 
		echo lang("FEATURE_DISABLED");
	}
	else
	{
		echo "
		<div style='width:400px;'>
			<form class='form-horizontal' name='resendActivation' action='".$_SERVER['PHP_SELF']."' method='post'>
				<div class='form-group'>
					<div class='col-sm-offset-3 col-sm-9'>
						<h2>Resend Email</h2>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-3 control-label'>Username</label>
					<div class='col-sm-9'>
						<input type='text' class='form-control' name='username' placeholder='Username'>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-3 control-label'>Email</label>
					<div class='col-sm-9'>
						<input type='text' class='form-control' name='email' placeholder='Email'>
					</div>
				</div>
				<div class='form-group'>
					<div class='col-sm-offset-3 col-sm-9'>
						<button type='submit' class='btn btn-primary' name='login'>Submit</button>
					</div>
				</div>
			</form>			
		</div>";
	}
	
	echo "</center>";
	
	include 'models/footer.php';
	
?>