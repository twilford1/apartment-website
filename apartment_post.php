<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
		
	// create apartment 
	function createApartment($name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."landlords (
			name,
			address,
			latitude,
			longitude,
			num_bedrooms,
			num_bathrooms,
			landlord_id,
			price,
			deposit,
			description,
			status,
			last_updated,
			)
			VALUES (
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			)");
		$stmt->bind_param("ssddiiiddsss", $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated); // s for string i for integer 
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}
	
	//Forms posted
	if(!empty($_POST))
	{
		$errors = array();
		$apartmentname = trim($_POST["apartment_name"]);
		$address = trim($_POST["address"]);
		$num_bedrooms = trim($_POST["num_bedrooms"]);
		$num_bathrooms = trim($_POST["num_bathrooms"]);
		$price = trim($_POST["price"]);
		$description = trim($_POST["description"]);
		
		$apartment = createApartment($name, $address, null, null, $num_bedrooms, $num_bathrooms, null, $price, $deposit, $description, null, null);
		/*
		if(minMaxRange(5, 25, $username))
		{
			$errors[] = lang("ACCOUNT_USER_CHAR_LIMIT", array(5, 25));
		}
		if(!ctype_alnum($username))
		{
			$errors[] = lang("ACCOUNT_USER_INVALID_CHARACTERS");
		}
		if(minMaxRange(5, 25, $displayname))
		{
			$errors[] = lang("ACCOUNT_DISPLAY_CHAR_LIMIT", array(5, 25));
		}
		if(!ctype_alnum($displayname))
		{
			$errors[] = lang("ACCOUNT_DISPLAY_INVALID_CHARACTERS");
		}
		if(minMaxRange(8, 50, $password) && minMaxRange(8, 50, $confirm_pass))
		{
			$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT", array(8, 50));
		}
		else if($password != $confirm_pass)
		{
			$errors[] = lang("ACCOUNT_PASS_MISMATCH");
		}
		if(!isValidEmail($email))
		{
			$errors[] = lang("ACCOUNT_INVALID_EMAIL");
		}
		//End data validation
		if(count($errors) == 0)
		{	
			//Construct a user object
			//***************$user = new User($username, $displayname, $password, $email);
			$apartment = createApartment($name, $address, null, null, $num_bedrooms, $num_bathrooms, null, $price, $deposit, $description, null, null);
			
			//Checking this flag tells us whether there were any errors such as possible data duplication occured
			/*if(!$user->status)
			{
				if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE", array($username));
				if($user->displayname_taken) $errors[] = lang("ACCOUNT_DISPLAYNAME_IN_USE", array($displayname));
				if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE", array($email));		
			}
			else
			{
				//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
				if(!$user->userCakeAddUser())
				{
					if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
					if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
				}
			} */
		}
		if(count($errors) == 0)
		{
			//$successes[] = $user->success;
		}
		
		

	}
	
	require_once("models/header.php");
	
	echo "<center>";
	
	echo resultBlock($errors,$successes);
	echo "
	<div style='width:400px;'>
		<form class='form-horizontal' name='newApartment' action='' method='post'>
			<div class='form-group'>
				<div class='col-sm-offset-3 col-sm-9'>
					<h2>Post Property</h2>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-3 control-label'>Name</label>
				<div class='col-sm-9'>
					<input type='text' class='form-control' name='apartment_name' placeholder='Name'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-3 control-label'>Address</label>
				<div class='col-sm-9'>
					<input type='text' class='form-control' name='address' placeholder='Address'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-3 control-label'>Number of Bedrooms</label>
				<div class='col-sm-9'>
					<input type='number' class='form-control' name='num_bedrooms' min='1' max='5' placeholder='Number of Bedrooms'>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-3 control-label'>Number of Bathrooms</label>
				<div class='col-sm-9'>
					<input type='number' class='form-control' name='num_bathrooms' min='1' max='5' placeholder='Number of Bathrooms'>
				</div>
			</div>

			<div class='form-group'>
				<label class='col-sm-3 control-label'>Price</label>
				<div class='col-sm-9'>
					<input type='number' class='form-control' name='price' min='0' placeholder='$0.00'>
				</div>
			</div>


			<div class='form-group'>
				<label class='col-sm-4 control-label'>Description</label>
				<div class='col-sm-8'>
					<textarea class='form-control' name='description' rows='2' placeholder='Description about your property' ></textarea>
				</div>
			</div>

			<div class='form-group'>
				<div class='col-sm-offset-3 col-sm-9'>
					<button type='submit' class='btn btn-primary' name='post_apartment'>Post</button>
				</div>
			</div>
		</form>			
	</div>
	
			
			
			
			
			
			
		</form>
	</div>";
	
	echo "</center>";
	
	include 'models/footer.php';
?>