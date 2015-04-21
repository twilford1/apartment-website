<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
		
	require_once("models/header.php");	
	
	
	echo "
	<center>
		<div style='width:700px;'>
			<div class='jumbotron'>
				<h2>Hello $loggedInUser->displayname</h2>
				<h2>Welcome to Apartment Finder!</h2>
				<br>
				<h4>
					User title: $loggedInUser->title
					<br>
					<br>
					Registered on: ".date("M d, Y", $loggedInUser->signupTimeStamp())."
					<br>
					<br>
					Private Profile: ".$loggedInUser->private_profile."
					<br>
					<br>
					Description: ".$loggedInUser->description."
				</h4>
			</div>
		</div>
		
		<br>
		<br>
		
		<div class='container'>
      <div class='row'>
      
        <div class='col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad' >
   
   
          <div class='panel panel-info'>
            <div class='panel-heading'>
              <h3 class='panel-title'>$loggedInUser->displayname</h3>
            </div>
            <div class='panel-body'>
              <div class='row'>
                <div class='col-md-3 col-lg-3 ' align='center'> <img alt='User Pic' src=".get_gravatar( $loggedInUser->email, 80, 'mm','x', false )." class='img-circle'> </div>
                

                <div class=' col-md-9 col-lg-9 '> 
                  <table class='table table-user-information'>
                    <tbody>
                      <tr>
                        <td>Been a member since:</td>
                        <td>".date("M d, Y", $loggedInUser->signupTimeStamp())."</td>
                      </tr>
                      <tr>
                        <td>Age</td>
                        <td>22</td>
                      </tr>
					  <tr>
                        <td>Gender</td>
                        <td>$loggedInUser->gender</td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td><a href='mailto:$loggedInUser->email'>$loggedInUser->email</a></td>
                      </tr>

                           
                      </tr>
                     
                    </tbody>
                  </table>
                  
                  <a href='user_settings.php' class='btn btn-primary'>Edit Profile</a>

                </div>
              </div>
            </div>
                 <!--
				 <div class='panel-footer'>
							<a data-original-title='Broadcast Message' data-toggle='tooltip' type='button' class='btn btn-sm btn-primary'><i class='glyphicon glyphicon-envelope'></i></a>
                        
						<span class='pull-right'>
                            <a href='edit.html' data-original-title='Edit this user' data-toggle='tooltip' type='button' class='btn btn-sm btn-warning'><i class='glyphicon glyphicon-edit'></i></a>
                            <a data-original-title='Remove this user' data-toggle='tooltip' type='button' class='btn btn-sm btn-danger'><i class='glyphicon glyphicon-remove'></i></a>
                        </span>
                    </div> -->
            
          </div>
        </div>
      </div>
    </div>
	</center>";
	
	include 'models/footer.php';
?>