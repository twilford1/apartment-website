<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
		
	//Forms posted
	if(!empty($_POST))
	{
		$errors = array();
		$name = trim($_POST['apartment_name']);
		$address = trim($_POST["address"]);
		$num_bedrooms = (int)$_POST["num_bedrooms"];
		$num_bathrooms = (int)$_POST["num_bathrooms"];
		$price = (double)$_POST["price"];
		$description = trim($_POST["description"]);
		
		if(minMaxRange(5, 25, $name))
		{
			$errors[] = lang("PROPERTY_CHAR_LIMIT", array(5, 25));
		}
		if(!ctype_alnum($name))
		{
			$errors[] = lang("PROPERTY_INVALID_CHARACTERS");
		}
		//End data validation

		//$apartment = createApartment($name, $address, NULL, NULL, $num_bedrooms, $num_bathrooms, 1,$price, NULL, $description, "available");
		
		if(count($errors) == 0)
		{	
			//Construct an apartment object
			$apartment = createApartment($name, $address, NULL, NULL, $num_bedrooms, $num_bathrooms, 1,$price, NULL, $description, "available");
			
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
		if(!empty($apartment))
		{
			$successes[] = lang("PROPERTY_ADDED");
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
					<h2>Post Properties</h2>
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
					<input type='number' class='form-control' name='price' min='0' max='10000' placeholder='$0.00'>
				</div>
			</div>


			<div class='form-group'>
				<label class='col-sm-4 control-label'>Description</label>
				<div class='col-sm-8'>
					<textarea class='form-control' name='description' rows='5' maxlength='500' placeholder='Description about your property' ></textarea>
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