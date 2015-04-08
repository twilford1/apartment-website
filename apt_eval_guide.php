<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
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
				<h3 class='panel-title'><b>Select a room type from the drop-down list to begin.</b></h3>
			</div>   
			<ul class='list-group'>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-1' data-toggle='detail-1'>
						<div class='col-xs-10'>
							<b>Bathroom Details</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-1'>
						<hr></hr>
						<div class='container'>
							<b>Shower:</b>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Broken/leaky shower head</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Stains</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Damaged shower curtains or shower rod</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Broken or chipped tiles</label>
							</div>
							<b>Bathtub:</b>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Broken/leaky faucet or knobs</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Stains</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Damaged caulk</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Mold (could be at outer base of tub or around inside edges)</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Broken or chipped porcelain or other tub material</label>
							</div>
							<b>Toilet</b>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Difficult/damaged flushing mechanism</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Stains in toilet bowl or on seat</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Mold around base or inner rim</label>
							</div>
							<div class='checkbox'>
							  <label><input type='checkbox' value=''>Broken lid, toilet seat, etc.</label>
							</div>
						</div>
					</div>
				</li>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-2' data-toggle='detail-2'>
						<div class='col-xs-10'>
							<b>Kitchen</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-2'>
						<hr></hr>
						<div class='container'>
						empty
						</div>
					</div>
				</li>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-3' data-toggle='detail-3'>
						<div class='col-xs-10'>
							<b>Bedroom</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-3'>
						<hr></hr>
						<div class='container'>
						empty
						</div>
					</div>
				</li>
			</ul>
		</div>

	<div class='container'>

	</div>";
	
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