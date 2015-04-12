<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	$llId = $_GET['id'];
	
	$llDetails = fetchLandlordDetails($llId);
	
	if($llDetails == null)
	{
		header("Location: landlords_list.php");
		die();
	}
	
	require_once("models/header.php");	
	
	echo resultBlock($errors, $successes);
	echo "
	<style>
		 .animated {
			-webkit-transition: height 0.2s;
			-moz-transition: height 0.2s;
			transition: height 0.2s;
		}

		.stars
		{
			margin: 20px 0;
			font-size: 24px;
			color: #d17581;
		}
	</style>
	<center>
		<h2>Landlord/ Property Management Company Profile</h2>
	</center>
	<br>
	<div class='container'>
		<div class='row'>			
			<div class='col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad' >
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<center>
							<h3 class='panel-title'>".$llDetails['name']."</h3>
						</center>
					</div>
				
					<div class='panel-body'>
						<div class='row'>
							<div class='col-md-3 col-lg-3 ' align='center'>
								<img src='/models/site-templates/images/no-image.png' alt='No Image' width='125' height='125'>
							</div>
						
							<div class=' col-md-9 col-lg-9 '> 
								<table class='table table-user-information'>
									<tbody>
										<tr>
											<td>Address:</td>
											<td>".$llDetails['address']."</td>
										</tr>
										<tr>
											<td>Contact</td>
											<td>".$llDetails['email']."</td>
										</tr>							</tbody>
								</table>
								<center>
									<a href='#' class='btn btn-primary'>View Landlord's Listings</a>
									<a href='#' class='btn btn-primary'>View Listings on Map</a>
								</center>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<br>
	<br>
	<br>
	<br>
	
	<div class='container'>
		<div class='row' style='margin-top:40px;'>
			<div class='col-md-6'>
				<div class='well well-sm'>
					<div class='text-right'>
						<a class='btn btn-success btn-green' href='#reviews-anchor' id='open-review-box'>Leave a Review</a>
					</div>
				
					<div class='row' id='post-review-box' style='display:none;'>
						<div class='col-md-12'>
							<form accept-charset='UTF-8' action='' method='post'>
								<input id='ratings-hidden' name='rating' type='hidden'> 
								<textarea class='form-control animated' cols='50' id='new-review' name='comment' placeholder='Enter your review here...' rows='5'></textarea>
				
								<div class='text-right'>
									<div class='stars starrr' data-rating='0'></div>
									<a class='btn btn-danger btn-sm' href='#' id='close-review-box' style='display:none; margin-right: 10px;'>
									<span class='glyphicon glyphicon-remove'></span>Cancel</a>
									<button class='btn btn-success btn-lg' type='submit'>Save</button>
								</div>
							</form>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
	<script src='reviewJS.js' type='text/javascript'></script>

        <hr>";
	
	include 'models/footer.php';
	
?>