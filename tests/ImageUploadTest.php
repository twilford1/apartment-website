<?php
	require_once("models/funcs.php");
	/*
	 * Test class for image_upload.php
	 * functions in func.php
	 *
	 *Functions to test:
	 *	-uploadImage
	 *  -fetchImageID
	 *  -deleteImage
	 */
	 
	class ImageUploadTest extends PHPUnit_Framework_TestCase
	{	
		public function testUploadImage()
		{
			$local_image = opendir("/models/site-templates/images/no-image.png");
			$test_image = uploadImage("TESTIMAGE", $local_image, 1,1);
			
			$result = true;
			
			if(!isset($test_image))
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
		
		public function test_FetchImageID_DeleteImage()
		{
			$imID = fetchImageID("TESTIMAGE");
			
			$result = true;
			
			if(!isset($imID))
			{
				$result = false;
			}
			
			$im_delete = deleteImage($imID);
			if(!isset($im_delete))
			{
				$result = false;
			}
			
			$this->assertTrue($result);
		}
	}

?>