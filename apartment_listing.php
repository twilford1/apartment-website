<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	$aptId = $_GET['id'];
	
	$aptDetails = fetchListingDetails($aptId);
	$reviews = getAptReviews($aptId);
	$revCount = 0;
	$alreadyReviewed = false;
	foreach ($reviews as $rev)
	{
		$revCount++;
		if($rev['user_id'] == $loggedInUser->user_id)
		{
			$alreadyReviewed = true;
		}
	}
	
	if(isUserLoggedIn())
	{
		if(!empty($_POST))
		{
			$apartment_id = $aptId;
			$user_id = $loggedInUser->user_id;
			$review = trim($_POST["review"]);
			$rating = (int)$_POST["rating"];
			
			//Construct an apartment review object
			$review = createApartmentReview($apartment_id, $user_id, $review, $rating);

			if(!empty($review))
			{
				$successes[] = lang("REVIEW_ADDED");
				$reviews = getAptReviews($aptId);
				$revCount = 0;
				foreach ($reviews as $rev)
				{
					$revCount++;
					if($rev['user_id'] == $loggedInUser->user_id)
					{
						$alreadyReviewed = true;
					}
				}
			}
		}
	}
	
	if($aptDetails == null)
	{
		header("Location: apartment_listings.php");
		die();
	}
	
	require_once("models/header.php");	
	
	echo "<center>";
	echo resultBlock($errors, $successes);
	echo "</center>";
	
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
		
		.user_name{
			font-size:14px;
			font-weight: bold;
		}
		
		.comments-list .media{
			border-bottom: 1px dotted #ccc;
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
									<br>
									<center>									
										<a href='utility_guide.php' class='btn btn-primary'>View Utility Guide</a>
										<a href='apt_eval_flaw_show.php' class='btn btn-primary'>View Flaws</a>
										<a href='apt_eval_guide.php' class='btn btn-primary'>Document Flaws</a>
									</center>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
	
	if(isUserLoggedIn() && !$alreadyReviewed)
	{
		echo "
		<div class='container'>
			<div class='row' style='margin-top:0px;'>
				<div class='col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad'>
					<div class='well well-sm'>
						<div class='text-center'>
							<a class='btn btn-success btn-green' href='#reviews-anchor' id='open-review-box'>Leave a Review</a>
						</div>
					
						<div class='row' id='post-review-box' style='display:none;'>
							<div class='col-md-12'>
								<form accept-charset='UTF-8' action='' method='post'>
									<input id='ratings-hidden' name='rating' type='hidden'> 
									<textarea class='form-control animated' cols='50' id='new-review' name='review' placeholder='Enter your review here...' rows='5'></textarea>
					
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
		</div>";
	}
	
	echo "
	<div class='container'>
		<div class='row'>
			<div class='col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad' >
			  <div class='page-header'>
				<h1><small class='pull-right'>".$revCount." reviews</small> Reviews </h1>
			  </div> 
			   <div class='comments-list'>";
					
					if(empty($reviews))
					{
						echo "
							<div class='media'>
								<div class='media-body'>
									
								  <h4 class='media-heading user_name'>No reviews yet.</h4>
								  Add a review above and tell us what you think about this apartment!
								</div>
							</div>";
					}
					else
					{
						//Display list of reviews
						foreach ($reviews as $rev)
						{
							$revCount++;
							$user = fetchUserDetails(NULL, NULL, $rev['user_id']);
							echo "
							<div class='media'>
								<!--<p class='pull-right'><small>5 days ago</small></p>-->
								<a class='media-left' href='#'>
								  <img alt='User Pic' src=".get_gravatar( $user['email'], 80, 'mm','x', false ).">								  
								</a>
								<div class='media-body'>
									
								  <h4 class='media-heading user_name'>".$user['user_name']."</h4>
								  <p>".$rev['review']."</p>
								  <div class='pull-right'>
									<div class='stars'>";
									switch($rev['rating'])
									{
										case '0':
											echo "<img src='/models/site-templates/images/star0.png'>";
											break;
										case '1':
											echo "<img src='/models/site-templates/images/star1.png'>";
											break;
										case '2':
											echo "<img src='/models/site-templates/images/star2.png'>";
											break;
										case '3':
											echo "<img src='/models/site-templates/images/star3.png'>";
											break;
										case '4':
											echo "<img src='/models/site-templates/images/star4.png'>";
											break;
										case '5':
											echo "<img src='/models/site-templates/images/star5.png'>";
											break;
									}
									echo "
									</div>
								  </div>
								  <br><br>
								  <p><small>Was this review helpful? <a href=''>Yes</a> - <a href=''>No</a> - <a href=''>Flag</a></small></p>
								</div>
							</div>";
						}
					}
					echo "
			   </div>
			</div>
		</div>
	</div>
	
	<script src='models/reviewJS.js' type='text/javascript'></script>";
	
	include 'models/footer.php';
?>