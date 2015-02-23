<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	require_once("models/header.php");
?>

<h1>Apartment Listings TEST</h1>
	<h3>Welcome!</h3>
	
<?php
	include 'models/footer.php';
?>