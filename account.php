<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}

	if(!empty($_GET))
	{
		$userId = $_GET['id'];
		if(!userIdExists($userId))
		{
			header("Location: account.php");
			die();
		}
	}
	else
	{
		$userId = $loggedInUser->user_id;
	}
	
	$userDetails = fetchUserDetails(NULL, NULL, $userId);
	
	require_once("models/header.php");
	
	echo "
	<div class='page-header'>
		<h1>
			".$userDetails['user_name']."
		</h1>
	</div>	
	
	<div class='container target'>
        
		<div class='row'>
            <div class='col-sm-3'>
                <!--left col-->
				
				<center>
					<a href='#'>
						<img class='img-circle img-responsive' src='".get_gravatar($userDetails['email'], 120, 'mm','x', false )."' title='profile image'>
					</a>
					<br>";
					
					if($userId == $loggedInUser->user_id)
					{
						echo "
						<a href ='messages.php?m=inbox' class='btn btn-primary'>Messages</a>
						<a href ='user_settings.php' class='btn btn-primary'>Settings</a>";
					}
					else
					{
						echo "
						<a href ='messages.php?m=inbox' class='btn btn-primary'>Send Message</a>";
					}
					
					echo "
					<br>
					<br>
				</center>
				
                <ul class='list-group'>
                    <li class='list-group-item text-muted' contenteditable='false'>Profile</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Joined</strong>
						</span>
						".date("M d, Y", $userDetails['sign_up_stamp'])."
					</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Last seen</strong>
						</span>
						".date("M d, Y", $userDetails['last_sign_in_stamp'])."
					</li>";
					
					if($userId == $loggedInUser->user_id || $userDetails['private_profile'] == 0)
					{
						echo "
						<li class='list-group-item text-right'>
							<span class='pull-left'>
								<strong class=''>Real name</strong>
							</span>
							".$userDetails['display_name']."
						</li>
						
						<li class='list-group-item text-right'>
							<span class='pull-left'>
								<strong class=''>Title</strong>
							</span>
							".$userDetails['title']."
						</li>
						
						<li class='list-group-item text-right'>
							<span class='pull-left'>
								<strong class=''>Gender</strong>
							</span>
							".$userDetails['gender']."
						</li>";
					}	
						
					if($userId == $loggedInUser->user_id)
					{
						echo "
						<li class='list-group-item text-right'>
							<span class='pull-left'>
								<strong class=''>Email</strong>
							</span>
							".$userDetails['email']."
						</li>";
					}
				
				echo "
                </ul>";
				
				if($userId == $loggedInUser->user_id)
				{
					echo "
					<div class='panel panel-default'>
						<div class='panel-heading'>
							Unread Messages
						</div>

						<div class='panel-body'>";
							$uCount = unreadCount($userDetails['id']);
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
				}
				
				$aStats = aptReviewStats($userDetails['id']);
				$aRatingCount = 0;
				$aRatingAverage = 0;
				foreach($aStats as $r)
				{
					if(isset($r['rating']) && $r['rating'] != 0)
					{
						$aRatingCount++;
						$aRatingAverage += $r['rating'];
					}
				}
				if($aRatingCount > 0)
				{
					$aRatingAverage = $aRatingAverage / $aRatingCount;
				}
				
				////////////////////////////////////////////
				
				$lStats = llReviewStats($userDetails['id']);
				$lRatingCount = 0;
				$lRatingAverage = 0;
				foreach($lStats as $r)
				{
					if(isset($r['rating']) && $r['rating'] != 0)
					{
						$lRatingCount++;
						$lRatingAverage += $r['rating'];
					}
				}
				if($lRatingCount > 0)
				{
					$lRatingAverage = $lRatingAverage / $lRatingCount;
				}
				
				echo "
                <ul class='list-group'>
                    <li class='list-group-item text-muted'>
						Profile Stats
					</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Apartment Reviews</strong>
						</span>
						".count($aStats)."
					</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Apartment Ratings</strong>
						</span>
						".$aRatingCount."
					</li>
					
					<li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Apartment Average Rating</strong>
						</span>
						".number_format($aRatingAverage, 1, '.', '')."
					</li>
					
					<li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Landlord Reviews</strong>
						</span>
						".count($lStats)."
					</li>

                    <li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Landlord Ratings</strong>
						</span>
						".$lRatingCount."
					</li>
					
					<li class='list-group-item text-right'>
						<span class='pull-left'>
							<strong class=''>Landlord Average Rating</strong>
						</span>
						".number_format($lRatingAverage, 1, '.', '')."
					</li>";
					
					if($userId == $loggedInUser->user_id)
					{
						echo "
						<li class='list-group-item text-right'>
							<span class='pull-left'>
								<strong class=''>Sent Messages</strong>
							</span>
							".messageCount($userDetails['id'], "Sent")."
						</li>
						
						<li class='list-group-item text-right'>
							<span class='pull-left'>
								<strong class=''>Sent Recieved</strong>
							</span>
							".messageCount($userDetails['id'], "Received")."
						</li>";
					}
				echo "
                </ul>
				
            </div><!--/col-3-->

            <div class='col-sm-9' contenteditable='false' style=''>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        ".$userDetails['user_name']."'s Bio
                    </div>

                    <div class='panel-body'>";
						if($userId == $loggedInUser->user_id || $userDetails['private_profile'] == 0)
						{
							if(isset($userDetails['description']))
							{
								echo $userDetails['description'];
							}
							else
							{
								echo "Your profile description goes here. This can be updated from the <a href='user_settings.php'>user settings</a> page";
							}
						}
						else
						{
							echo $userDetails['user_name']."'s profile is private";
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