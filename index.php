<?php
	require_once('models/config.php');
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	require_once('models/header.php');

	echo "
	<div id='carousel-example-generic' class='carousel slide' data-ride='carousel'>
		<ol class='carousel-indicators'>
			<li data-target='#carousel-example-generic' data-slide-to='0' class=''></li>
			<li data-target='#carousel-example-generic' data-slide-to='1' class=''></li>
			<li data-target='#carousel-example-generic' data-slide-to='2' class='active'></li>
		</ol>
		<div class='carousel-inner' role='listbox'>
			<div class='item'>
				<img data-src='holder.js/1140x500/auto/#777:#555/text:First slide' alt='First slide [1140x500]' src='/models/site-templates/images/apt1.jpg' data-holder-rendered='true'>
			</div>
			<div class='item'>
				<img data-src='holder.js/1140x500/auto/#666:#444/text:Second slide' alt='Second slide [1140x500]' src='/models/site-templates/images/apt2.jpg' data-holder-rendered='true'>
			</div>
			<div class='item active'>
				<img data-src='holder.js/1140x500/auto/#555:#333/text:Third slide' alt='Third slide [1140x500]' src='/models/site-templates/images/apt3.jpg' data-holder-rendered='true'>
			</div>
		</div>
		<a class='left carousel-control' href='#carousel-example-generic' role='button' data-slide='prev'>
			<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>
			<span class='sr-only'>Previous</span>
		</a>
			<a class='right carousel-control' href='#carousel-example-generic' role='button' data-slide='next'>
			<span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>
			<span class='sr-only'>Next</span>
		</a>
	</div>
	
	<br>
	
	<div class='container'>
		
		<div class='jumbotron' style='background-color: #fff;'>
			<center>
				<h1>Hawk Nest</h1>
				<p class='lead'>Where birds of a feather flock together</p>
								
				<p><a class='btn btn-lg btn-success' href='apartment_listings.php'>View All Listings</a></p>
				
			</center>
		</div><!-- Example row of columns -->

		<div class='row'>
			<div class='col-lg-4'>
				<h2>Update 5/03: New features</h2>

				<p>We've updated some of the features on our site. Feel free to check them out.</p>
				
				<p><a class='btn btn-primary' href='account.php'>View details</a></p>
			</div>

			<div class='col-lg-4'>
				<h2>Update 4/16: New logo!</h2>

				<img src='/models/site-templates/images/nearOldCapitol.png' height='200' width='250' data-holder-rendered='true'>
				
			</div>

			<div class='col-lg-4'>
				<h2>Join Us</h2>

				<p>Join our online apartment community! Read our reviews, message other users, and even find a roommate!</p>

				<p><a class='btn btn-primary' href='#'>View details</a></p>
			</div>
		</div><!-- Site footer -->
	</div>";
	
	include 'models/footer.php';
?>