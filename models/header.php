<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html" />
		<meta name="description" content="A short description." />
		<link rel="icon" type="image/png" href="/models/site-templates/images/favicon.ico">
		
		<title><?= $websiteName ?></title>
		
		<link href="<?= $template ?>" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="models/funcs.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		
		<link rel="stylesheet" href="models/jquery.bootgrid.min.css"/>
		<script type="text/javascript" src="models/jquery.bootgrid.min.js"></script>

		<script type="text/javascript" src="models/bootstrap.js"></script>
		
		<!-- Added by Mattie for Google Maps -->
		<script src="http://maps.googleapis.com/maps/api/js"></script>
		
		<!-- Added by Mattie for Costs.php 
			 MUST STAY BELOW THE OTHER JQUERY INCLUDE AND IN THIS ORDER-->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="https://raw.githubusercontent.com/digitalBush/jquery.maskedinput/1.4.0/dist/jquery.maskedinput.js"></script>
	</head>
	
	<body>
		
		<?php
			include 'left-nav.php';
		?>
		
		<!-- Page Content -->
		<div class="container">
			<br>
			<br>
			<br>
			<br>