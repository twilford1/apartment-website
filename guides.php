<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	require_once("models/header.php");	
	
	echo "
	<center>
		<h2>Guides</h2>
	</center>
	<br>
	
	<link rel='stylesheet' href='//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css'>
	<div class='container'>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<h3 class='panel-title'><b>Iowa City Guides</b></h3>
			</div>   
			<a href='utility_guide.php'>Utilities Guide</a>
		</div>
		<br>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<h3 class='panel-title'><b>Apartment Search Guides</b></h3>
			</div>   
			<a href='apt_eval_guide.php'>Walkthrough Checklist</a>
		</div>
	</div>";
	
	include 'models/footer.php';
?>