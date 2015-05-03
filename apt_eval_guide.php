<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
/*
	if(isset($_POST['choose_flaw_submit'])) 
	{
		//$_SESSION['post'] = $_POST;
		getLastImage();
		//if($_POST['choose_flaw_submit'])
		//header('Location: image_upload.php');
	}*/
	/****************************
	TODO
	* get landlord_id, apartment_id
	*
	
	
	
	*****************************/
	//$flaw_description = 'test';
	
	//Forms posted
	if(isset($_POST['choose_flaw_submit']) && !isset($_POST['upload_image_submit'])) 
	{
		//$_SESSION['post'] = $_POST;
		//getLastImage();
		//if($_POST['choose_flaw_submit'])
		//header('Location: image_upload.php');
		$_SESSION['flaw_description'] = $_POST['flaw_sel'];
		
	}
	
	if(isset($_POST['upload_image_submit']))
	{
		$errors = array();
		$temp = array();
		$temp = explode("_", $_SESSION['flaw_description']);
		$loc_id = $temp[0];
		$des = $temp[1];
		
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
			$new_image = uploadImage($image_name, $image, $_SESSION['flaw_apt'],$loc_id,$des);
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
	
	echo "
	<center>
		<h2>Apartment Evaluation Guide</h2>
	</center>
	<br>

	<link rel='stylesheet' href='//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css'>
	<div class='container'>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<h3 class='panel-title'><b>Select a section from the drop-down list to begin.</b></h3>
			</div>   
			<form class='form-horizontal radio' name='newFlaw' action='apt_eval_guide.php' method='post'>
			<ul class='list-group'>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-1' data-toggle='detail-1'>
						<div class='col-xs-10'>
							<b>General</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-1'>
						<hr></hr>
						<div class='container'>
							<div class='col-md-4'>
								<b>Apartment:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Air-Conditioning'>Air-Conditioning</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Ample electrical outlets'>Ample electrical outlets</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Privacy'>Privacy</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Blinds/curtains'>Blinds/curtains</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Cable TV connection'>Cable TV connection</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Floors (carpet/hardwood)'>Floors (carpet/hardwood)</label>
								</div>							
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Fireplace'>Fireplace</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Lighting/Natural light'>Lighting/Natural light</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Paint/wall markings/stains'>Paint/wall markings/stains</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Patio/balcony'>Patio/balcony</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Phone jack(s)/Ethernet jack(s)'>Phone jack(s)/Ethernet jack(s)</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_View'>View</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='1_Water pressure'>Water pressure</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Lease:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Pets allowed</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Physical changes allowed</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Penalty for breaking lease</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Late payment fee</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Lease length</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Date available</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Rent amount</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Unit Address</label>
								</div>							
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Utilities included in rent</label>
								</div>							
								<b>Neighhborhood:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Proximity to public transit</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Distance to school/work</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Proximity to bank and shops</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Community:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Laundry/Facilities nearby</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Garbage chute/trash removal nearby</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Noise level</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Parking (free or otherwise)</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Bike racks</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Elevator/stairs</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Roof access</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Soundproof walls</label>
								</div>							
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Mailbox</label>
								</div>							
								<b>Safety:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Emergency exits</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Fire extinguishers</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Functioning windows</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Locks on all doors</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Outside lighting</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Screens</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Smoke detectors</label>
								</div>
							</div>
						</div>
					</div>
				</li>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-2' data-toggle='detail-2'>
						<div class='col-xs-10'>
							<b>Bathroom</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-2'>
						<hr></hr>
						<div class='container'>
							<div class='col-md-4'>
								<b>Shower:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Broken/leaky shower head'>Broken/leaky shower head</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Stains'>Stains</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Damaged shower curtains or shower rod'>Damaged shower curtains or shower rod</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Broken or chipped tiles'>Broken or chipped tiles</label>
								</div>
								<b>Bathtub:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Broken/leaky faucet or knobs'>Broken/leaky faucet or knobs</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Stains'>Stains</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Damaged caulk'>Damaged caulk</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Mold (could be at outer base of tub or around inside edges)'>Mold (could be at outer base of tub or around inside edges)</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Broken or chipped porcelain or other tub material'>Broken or chipped porcelain or other tub material</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Toilet:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Difficult/damaged flushing mechanism'>Difficult/damaged flushing mechanism</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Stains in toilet bowl or on seat'>Stains in toilet bowl or on seat</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Mold around base or inner rim'>Mold around base or inner rim</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Broken lid, toilet seat, etc.'>Broken lid, toilet seat, etc.</label>
								</div>
								<b>Sink:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Faucet works (both hot and cold)'>Faucet works (both hot and cold)</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Stains/chips/rust/other damage'>Stains/chips/rust/other damage</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Mould around drain/faucet/outside of basin'>Mould around drain/faucet/outside of basin</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='2_Caulking in tact'>Caulking in tact</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Countertops/Drawers/Cabinets</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Vinyl peeling</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Dirty/mouldy</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Water damage on wood</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Drawers/cabinets open properly</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Knobs/handles fastened securely</label>
								</div>							
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Enough space</label>
								</div>				
							</div>
						</div>
					</div>
				</li>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-3' data-toggle='detail-3'>
						<div class='col-xs-10'>
							<b>Kitchen</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-3'>
						<hr></hr>
						<div class='container'>
							<div class='col-md-4'>
								<b>Refrigerator:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='3_Old/outdated'>Old/outdated</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Working freezer</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Enough space inside</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Broken or chipped shelving</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Leaking seals (can you feel cold air coming out?)</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Scratches/stains/other damage</label>
								</div>
								<b>Sink/Garbage Disposal:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Faucets work (both hot and cold water)</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Garbage disposal present/works</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Stains/chips/rust/other damage</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Caulk in tact</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Dishwasher:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Space within</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Special operating instructions</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Knobs/timer/other controls working</label>
								</div>
								<b>Countertops/Drawers/Cabinets</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Vinyl peeling</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Dirty/mouldy</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Water damage on wood</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Drawers/cabinets open properly</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Knobs/handles fastened securely</label>
								</div>							
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Enough space</label>
								</div>	
							</div>
							<div class='col-md-4'>
								<b>Oven/Stove/Microwave:</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Gas or Electric</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Dirty/damaged</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Stove pans/ microwave plate present/clean/undamaged</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Oven drawer</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Knobs/timer/other controls working</label>
								</div>							
							</div>
						</div>
					</div>
				</li>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-4' data-toggle='detail-4'>
						<div class='col-xs-10'>
							<b>Bedroom</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-4'>
						<hr></hr>
						<div class='container'>
							<div class='col-md-4'>
								<b>Closet</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='4_Lighting'>Lighting</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Walk-in</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Space/height</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Storage/shelving</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Doors/curtains</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Knobs/handles fastened securely</label>
								</div>							
							</div>
							<div class='col-md-4'>
								<b>Other</b>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value='TESTTESTTESTTESETEST'>Lighting</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Space/height</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Large window(s)</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Storage/shelving</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Doors/curtains</label>
								</div>
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Knobs/handles fastened securely</label>
								</div>							
								<div class='radio'>
								  <label><input type='radio' name='flaw_sel' value=''>Stains on floor/walls or other markings</label>
								</div>							
							</div>
						</div>
					</div>
				</li>
			</ul>
			<br>
			<center> <button type='submit' class='btn btn-primary' name='choose_flaw_submit' id='choose_flaw_submit'>Select</button> </center>
			<br>
			</form>
		</div>";
	echo "<center>";
	echo resultBlock($errors,$successes);
	echo "</center>";
	echo "
	<div class='container'>
<center>
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
					<button type='submit' class='btn btn-primary' name='upload_image_submit' id='upload_image_submit'>Upload</button>
					
				</div>
			</div>
			
			</form>			
		</div>
</center>
	</div>";
	
	//getLastImage();

	include 'models/footer.php';
?>

<script>
$(document).ready(function() {
    $('[id^=detail-]').hide();
    $('.toggle').click(function() {
        $input = $( this );
        $target = $('#'+$input.attr('data-toggle'));
        $target.slideToggle();
    });
});
</script>