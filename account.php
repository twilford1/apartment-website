<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
		
	require_once("models/header.php");	
	
	/*
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
	*/
	
	
	echo "
	<div class='page-header'>
		<h1>
			".$loggedInUser->username."
		</h1>
	</div>	
	
	<div class='container target'>
        
		<div class='row'>
            <div class='col-sm-3'>
                <!--left col-->
				
				<center>
					<a href='#'>
						<img class='img-circle img-responsive' src='".get_gravatar( $loggedInUser->email, 120, 'mm','x', false )."' title='profile image'>
					</a>
					<br>
					<a href ='messages.php?m=inbox' class='btn btn-primary'>Messages</a>
					<a href ='user_settings.php' class='btn btn-primary'>Settings</a>
					<br>
					<br>
				</center>
				
                <ul class='list-group'>
                    <li class='list-group-item text-muted' contenteditable='false'>Profile</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Joined</strong>
						</span>
						".date("M d, Y", $loggedInUser->signupTimeStamp())."
					</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Last seen</strong>
						</span>
						".date("M d, Y", $loggedInUser->last_sign_in)."
					</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Real name</strong>
						</span>
						".$loggedInUser->displayname."
					</li>

					<li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Title</strong>
						</span>
						".$loggedInUser->title."
					</li>
					
					<li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Gender</strong>
						</span>
						".$loggedInUser->gender."
					</li>
					
                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Email</strong>
						</span>
						".$loggedInUser->email."
					</li>
                </ul>
				
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        Unread Messages
                    </div>

                    <div class='panel-body'>";
						$uCount = unreadCount($loggedInUser->user_id);
						if($uCount > 0)
						{
							if($uCount == 1)
							{
								echo "<a href='messages.php?m=inbox'>".$uCount." unread message</a>";
							}
							else
							{
								echo "<a href='messages.php?m=inbox'>".$uCount." unread messages</a>";
							}
						}
						else
						{
							echo "You have no unread messages";
						}
					echo "    
                    </div>
                </div>";
				
				$rStats = reviewStats($loggedInUser->user_id);
				$ratingCount = 0;
				$ratingAverage = 0;
				foreach($rStats as $r)
				{
					if(isset($r['rating']))
					{
						$ratingCount++;
						$ratingAverage += $r['rating'];
					}
				}
				if($ratingCount > 0)
				{
					$ratingAverage = $ratingAverage / $ratingCount;
				}
				
				echo "
                <ul class='list-group'>
                    <li class='list-group-item text-muted'>
						Profile Stats
					</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Reviews</strong>
						</span>
						".count($rStats)."
					</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Ratings Count</strong>
						</span>
						".$ratingCount."
					</li>
					
					<li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Average Rating</strong>
						</span>
						".number_format($ratingAverage, 1, '.', '')."
					</li>
					
					<li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Sent Messages</strong>
						</span>
						".messageCount($loggedInUser->user_id, "Sent")."
					</li>
					
					<li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Sent Recieved</strong>
						</span>
						".messageCount($loggedInUser->user_id, "Received")."
					</li>
                </ul>
				
            </div><!--/col-3-->

            <div class='col-sm-9' contenteditable='false' style=''>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        ".$loggedInUser->username." Bio
                    </div>

                    <div class='panel-body'>";
						if(isset($loggedInUser->description))
						{
							echo $loggedInUser->description;
						}
						else
						{
							echo "Your profile description goes here. This can be updated from the <a href='user_settings.php'>user settings</a> page";
						}
                    echo "
                    </div>
                </div>

                <div class='panel panel-default target'>
                    <div class='panel-heading' contenteditable='false'>
                        Panel Title
                    </div>

                    <div class='panel-body'>
                        <div class='row'>
                            <div class='col-md-4'>
                                <div class='thumbnail'>
                                    <img alt='300x200' src=
                                    'http://lorempixel.com/600/200/people'>

                                    <div class='caption'>
                                        <h3>Stuff</h3>

                                        <p>Cocker Spaniel who loves treats.</p>

                                        <p></p>
                                    </div>
                                </div>
                            </div>

                            <div class='col-md-4'>
                                <div class='thumbnail'>
                                    <img alt='300x200' src=
                                    'http://lorempixel.com/600/200/city'>

                                    <div class='caption'>
                                        <h3>Stuffs</h3>

                                        <p>Is just another friendly dog.</p>

                                        <p></p>
                                    </div>
                                </div>
                            </div>

                            <div class='col-md-4'>
                                <div class='thumbnail'>
                                    <img alt='300x200' src=
                                    'http://lorempixel.com/600/200/sports'>

                                    <div class='caption'>
                                        <h3>Stuffss</h3>

                                        <p>Loves catnip and naps. Not fond of
                                        children.</p>

                                        <p></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        More Stuff
                    </div>

                    <div class='panel-body'>
                        Stuff about stuff and stuff
                    </div>
                </div>
            </div>

            <div id='push'></div>
        </div>
    </div>";
	
	
	
	include 'models/footer.php';
?>