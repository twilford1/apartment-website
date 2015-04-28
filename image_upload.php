<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}

	
	//Forms posted
	if(!empty($_POST['upload_image_submit']))
	{
		$errors = array();
		$file = $_FILES['uploaded_image']['tmp_name'];
		if(!isset($file))
			echo "Please select an image.";
		else
		{
			$image = $_FILES['uploaded_image']['tmp_name'];
			$image_name = $_FILES['uploaded_image']['name'];
			$image_size = getimagesize($_FILES['uploaded_image']['tmp_name']);
			
		}
		
		if($image_size==FALSE)
			$errors[] = lang("IMAGE_INVALID_TYPE");
		else
		{
			$new_image = uploadImage($image_name, $image, 1,1);
			if(!empty($new_image))
				$successes[] = lang("IMAGE_UPLOADED");
			else
			{
				$errors[] = lang("IMAGE_UPLOADED_FAILED");
			}
		}
		//End data validation

	}
	
	if(!empty($_POST['choose_flaw_submit']))
	{
		$errors = array();
		
		/*if()
			echo "Please select an image.";
		else
		{
			
			
		}
		
		if()
			$errors[] = lang("IMAGE_INVALID_TYPE");
		else
		{
			
		}
		//End data validation*/

	}
	
	require_once("models/header.php");
	
	echo "<center>";
	
	echo resultBlock($errors,$successes);
	echo "
	<div style='width:400px;'>
		<form class='form-horizontal' name='newImage' action='' method='post' enctype='multipart/form-data'>
			<div class='form-group'>
				<div class='col-sm-offset-3 col-sm-9'>
					<h2>Upload A Image</h2>
				</div>
			</div>
			<div class='form-group'>
				<label class='col-sm-3 control-label'>File</label>
				<div class='col-sm-9'>
					<input type='file' class='form-control' name='uploaded_image'>
				</div>
			</div>

			<div class='form-group'>
				<div class='col-sm-offset-3 col-sm-9'>
					<button type='submit' class='btn btn-primary' name='upload_image_submit'>Upload</button>
					<br><br>
					<a href='apt_eval_guide.php' class='btn btn-primary' name='Walkthrough Checklist'>Back to checklist</a>
				</div>
			</div>
			
		</form>			
	</div>
	
			
			
			
			
			
			
		</form>
	</div>";
	
	
	echo "</center>";
	echo "<center>";
	//echo getImage(7);
	getLastImage();
	///***************************************
	//  TODO
	// 	* getLastImage  X
	//  * Post property page adding deposits row	X
	//  * Flaws documenting page (Document flaws button)
	//  * Flaws showing page  (View flaws button)
	///***************************************
	
	
	
	
	echo "</center>";
	include 'models/footer.php';
?>