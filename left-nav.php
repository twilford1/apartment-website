<?php
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
?>

<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">Home</a>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<?php
					//Links for logged in user
					if(isUserLoggedIn())
					{
						echo "
						<li><a href='account.php'>Account Home</a></li>
						<li><a href='user_settings.php'>User Settings</a></li>
						<li><a href='apartment_listings.php'>Listings</a></li>
						<li><a href='landlords_list.php'>Landlords/Property Managers</a></li>
						<li><a href='map.php'>Map</a></li>";
						
						//Links for permission level 2 (default admin)
						if ($loggedInUser->checkPermission(array(2)))
						{
							echo "
							<li class='dropdown'>
								<a href='#' class='dropdown-toggle' data-toggle='dropdown'>Admin Menu<span class='caret'></span></a>
								<ul class='dropdown-menu' role='menu'>
									<li><a href='admin_configuration.php'>Admin Configuration</a></li>
									<li><a href='admin_users.php'>Admin Users</a></li>
									<li><a href='admin_permissions.php'>Admin Permissions</a></li>
									<li><a href='admin_pages.php'>Admin Pages</a></li>
								</ul>
							</li>";
						}
						
						echo "
						<li><a href='logout.php'>Logout</a></li>";
					} 
					//Links for users not logged in
					else
					{
						echo "
						<li><a href='login.php'>Login</a></li>
						<li><a href='register.php'>Register</a></li>
						<li><a href='apartment_listings.php'>Listings</a></li>
						<li><a href='map.php'>Map</a></li>";
						
						if ($emailActivation)
						{
							echo "<li><a href='resend-activation.php'>Resend Activation Email</a></li>";
						}
					}
				?>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container -->
</nav>