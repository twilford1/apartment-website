<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	$aptId = $_GET['id'];
	
	$aptDetails = fetchListingDetails($aptId);
	
	if($aptDetails == null)
	{
		header("Location: apartment_listings.php");
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
		<h2>Apartment Listing</h2>
	</center>
	<br>
	<div class='srtgs'  id='rt_".$aptId."'></div>
	<div class='container'>
		<div class='row'>			
			<div class='col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad' >
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<center>
							<h3 class='panel-title'>".$aptDetails['name']."</h3>
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
											<td>Landlord:</td>
											<td>id ".$aptDetails['landlord_id']."</td>
										</tr>
										<tr>
											<td>Address:</td>
											<td>".$aptDetails['address']."</td>
										</tr>
										<tr>
											<td>Rent:</td>
											<td>$".$aptDetails['price']."</td>
										</tr>
										<tr>
											<td>Status:</td>
											<td>".$aptDetails['status']."</td>
										</tr>
										<tr>
											<td>Bed/Bath</td>
											<td>".$aptDetails['num_bedrooms']."/".$aptDetails['num_bathrooms']."</td>
										</tr>
										<tr>
											<td>Description</td>
											<td>".$aptDetails['description']."</td>
										</tr>
										<tr>
											<td>Contact</td>
											<td><a href='mailto:info@support.com'>info@support.com</a></td>
										</tr>
										<tr>
											<td>Phone Number</td>
											<td>
												123-4567-890(Landline)<br><br>555-4567-890(Mobile)
											</td>
										</tr>
									</tbody>
								</table>
								<center>
									<a href='#' class='btn btn-primary'>View Landlord</a>
									<a href='#' class='btn btn-primary'>View on Map</a>
								</center>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
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
	<script src='reviewJS.js' type='text/javascript'></script>";
	
	include 'models/footer.php';
?>