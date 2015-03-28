<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
		
	// upload image
	function uploadImage($name, $image, $apartment_id, $location)
	{
		
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."images (
			name,
			image,
			apartment_id,
			location
			)
			VALUES (
			?,
			?,
			?,
			?
			)");
			
		$stmt->bind_param("sbii", $name, $image, $apartment_id, $location);
		$stmt->send_long_data(1, file_get_contents($image));
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}
	
	function getImage($id)
	{
		
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT image FROM ".$db_table_prefix."images WHERE id=?");
			
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($showimage);
		$stmt->fetch();
		echo '<img src="data:image/jpeg;base64,'.base64_encode($showimage).'"/>';
		//header("Content-Type: image/jpeg");
	
		$stmt->close();	
		
	}
	
	//Forms posted
	if(!empty($_POST))
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
	
	require_once("models/header.php");
	
	echo "<center>";
	
	echo resultBlock($errors,$successes);
	echo "
	<div style='width:400px;'>
		<form class='form-horizontal' name='newImage' action='' method='post' enctype='multipart/form-data'>
			<div class='form-group'>
				<div class='col-sm-offset-3 col-sm-9'>
					<h2>Upload Images</h2>
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
					<button type='submit' class='btn btn-primary' name='upload_image'>Upload</button>
				</div>
			</div>
			
		</form>			
	</div>
	
			
			
			
			
			
			
		</form>
	</div>";
	
	echo "</center>";
	
	getImage(8);
	
	include 'models/footer.php';
?>