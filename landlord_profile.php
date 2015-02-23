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

    </div>";
	
	include 'models/footer.php';
?>