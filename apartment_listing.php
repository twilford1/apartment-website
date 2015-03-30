<html>
	<head>
		<link href="ratingfiles/ratings.css" rel="stylesheet" type="text/css" />
	</head>
</html>

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
	</div>";
	
	/*
	echo "
	<br>
	<br>
	<br>
	<br>
	<div class='container'>

        <div class='row'>

            <!-- Blog Post Content Column -->
            <div class='col-lg-8'>
			
                <!-- Comments Form -->
                <div class='well'>
                    <h4>Leave a Comment:</h4>
                    <form role='form'>
                        <div class='form-group'>
                            <textarea class='form-control' rows='3'></textarea>
                        </div>
                        <button type='submit' class='btn btn-primary'>Submit</button>
                    </form>
                </div>

                <hr>

                <!-- Posted Comments -->

                <!-- Comment -->
                <div class='media'>
                    <a class='pull-left' href='#'>
                        <img class='media-object' src='http://placehold.it/64x64' alt=''>
                    </a>
                    <div class='media-body'>
                        <h4 class='media-heading'>Start Bootstrap
                            <small>August 25, 2014 at 9:30 PM</small>
                        </h4>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
                    </div>
                </div>

                <!-- Comment -->
                <div class='media'>
                    <a class='pull-left' href='#'>
                        <img class='media-object' src='http://placehold.it/64x64' alt=''>
                    </a>
                    <div class='media-body'>
                        <h4 class='media-heading'>Start Bootstrap
                            <small>August 25, 2014 at 9:30 PM</small>
                        </h4>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
                        <!-- Nested Comment -->
                        <div class='media'>
                            <a class='pull-left' href='#'>
                                <img class='media-object' src='http://placehold.it/64x64' alt=''>
                            </a>
                            <div class='media-body'>
                                <h4 class='media-heading'>Nested Start Bootstrap
                                    <small>August 25, 2014 at 9:30 PM</small>
                                </h4>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
                            </div>
                        </div>
                        <!-- End Nested Comment -->
                    </div>
                </div>

            </div>
			
        </div>
        <!-- /.row -->

        <hr>

        <!-- Footer -->
        <footer>
            <div class='row'>
                <div class='col-lg-12'>
                    <p>Software Project Group 6 - 2015</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div>
	";
	*/
	
	echo "
	<script src='ratingfiles/ratings.js' type='text/javascript'></script>";
	
	include 'models/footer.php';
?>