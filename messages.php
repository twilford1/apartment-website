<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	if(!empty($_POST))
	{
		if(isset($_POST['sendButton']))
		{
			$recipientID = fetchUserID($_POST['recipient']);
			
			if(isset($recipientID))
			{
				if($_POST['subject'] == "")
				{
					$_POST['subject'] = "(no subject)";
				}
				
				if(strlen($_POST['subject']) <= 60)
				{
					if($_POST['message'] != "")
					{
						if(newMessage($loggedInUser->user_id, $recipientID, $_POST['subject'], $_POST['message'], 0))
						{
							$successes = [0 => "Message Sent"];
						}
						else
						{
							$errors[] = lang("SQL_ERROR");
						}
					}
					else
					{
						$errors = [0 => "Message empty"];
					}
				}
				else
				{
					$errors = [0 => "Subject too long"];
				}				
			}
			else
			{
				$errors = [0 => "Invalid Recipient"];
			}
		}
		else if(isset($_POST['draftButton']))
		{
			$recipientID = fetchUserID($_POST['recipient']);
			
			if(!isset($recipientID))
			{
				$recipientID = null;
			}
			
			if($_POST['subject'] == "")
			{
				$_POST['subject'] = "(no subject)";
			}
			
			if(strlen($_POST['subject']) <= 60)
			{
				if(newMessage($loggedInUser->user_id, $recipientID, $_POST['subject'], $_POST['message'], 1))
				{
					$successes = [0 => "Draft Saved"];
					$_GET['m'] = "drafts";
				}
				else
				{
					$errors[] = lang("SQL_ERROR");
				}
			}
			else
			{
				$errors = [0 => "Subject too long"];
			}
		}
	}
	
	$unreadCount = unreadCount($loggedInUser->user_id);
	
	if(!isset($_GET['m']))
	{
		$_GET['m'] = "inbox";
	}	
	switch($_GET['m'])
	{
		case "inbox":
			$messages = fetchMessages($loggedInUser->user_id, "inbox");
			$inboxActive = "class='active'";
			$sentActive = "";
			$draftsActive = "";
			$tableHeader = "Sender";
			break;
		case "sent":
			$messages = fetchMessages($loggedInUser->user_id, "sent");
			$sentActive = "class='active'";
			$inboxActive = "";
			$draftsActive = "";
			$tableHeader = "Recipient";
			break;
		case "drafts":
			$messages = fetchMessages($loggedInUser->user_id, "drafts");
			$draftsActive = "class='active'";
			$inboxActive = "";
			$sentActive = "";
			$tableHeader = "Recipient";
			break;
		default:
			$_GET['m'] = "inbox";
			$messages = fetchMessages($loggedInUser->user_id, "inbox");
			$inboxActive = "class='active'";
			$sentActive = "";
			$draftsActive = "";
			$tableHeader = "Sender";
	}
	
	require_once("models/header.php");
	
	echo "<center>";
	echo resultBlock($errors, $successes);
	echo "</center>";
			
	echo "
	
	<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/angularjs/1.2.3/angular.min.js'></script>
			
	<div class='page-header'>
		<div class='row'>
			<div class='col-md-2'>
				<h1>Messages</h1>
			</div>
			<div class='col-md-4'>
				<br>
				<a href='messages.php?m=".$_GET['m']."' class='btn btn-default btn-lg'>
					<span class='glyphicon glyphicon-refresh'></span>
				</a>
				<button class='btn btn-default btn-lg' title='Compose New' data-toggle='modal' data-target='#modalCompose'>
					<span class='glyphicon glyphicon-edit'></span>
				</button>
				
				<div class='btn-group btn-group-lg'>
					<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>
						More <span class='caret'></span>
					</button>
					<ul class='dropdown-menu' role='menu'>
						<li><a data-toggle='modal' data-target='#modalCompose'>Compose new</a></li>
						<li><a href='mark_messages.php?id=".$loggedInUser->user_id."' class='text-muted'>Mark All Read</a></li>
						<li><a href='user_settings.php' class='text-muted'>Settings</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class='container'>
		
		<div class='row'>
			
			
			<aside class='col-md-2 pad-right-0'>
				<br>
				<ul class='nav nav-pills nav-stacked'>
					<li ".$inboxActive."><a href='messages.php?m=inbox'><span class='badge pull-right'>".$unreadCount."</span> Inbox </a></li>
					<li ".$sentActive."><a href='messages.php?m=sent'> Sent </a></li>
					<li ".$draftsActive."><a href='messages.php?m=drafts'> Drafts </a></li>
				</ul>
			</aside>
			
			<div class='col-md-10'>
				
				
				<!--message list-->
				<div class='table-responsive'>
					<table id='grid-basic' class='table table-striped table-hover'>
						<thead class='hidden-xs'>
							<tr>
								<th data-column-id='id' data-type='numeric' data-identifier='true'>ID</th>
								<td data-column-id='date_time' data-order='desc'>Date / Time</td>
								<td data-column-id='sender_recipient'>".$tableHeader."</td>";
								
								if($_GET['m'] == "inbox")
								{
									echo "
									<td data-column-id='read'>Read</td>";
								}
								
								echo "
								<td data-column-id='subject'>Subject</td>
								<td data-column-id='view' data-formatter='link'>View</td>
							</tr>
						</thead>
						<tbody>";
							$i = 1;
							foreach ($messages as $m)
							{
								// Print the unread messages in bold
								if($_GET['m'] == "inbox" && $m['wasRead'] == 0)
								{
									$b1 = "<b>";
									$b2 = "</b>";
								}
								else
								{
									$b1 = "";
									$b2 = "";
								}
								
								if($_GET['m'] == "inbox")
								{
									$tableField = $m['sender_id'];
								}
								else
								{
									$tableField = $m['recipient_id'];
								}
								
								echo "
								<tr>
									<td>
										".$i++."
									</td>
									<td>
										<span>".$b1.date("Y-m-d  g:i:s A", $m['timestamp']).$b2."</span>
									</td>
									<td>
										<span>".$b1.fetchUsername($tableField).$b2."</span>
									</td>";
									
									if($_GET['m'] == "inbox")
									{
										echo "
										<td>";
										if($m['wasRead'] == 0)
										{
											echo "unread";
										}
										echo "
										</td>";
									}
									
									echo "
									<td>
										<span>".$b1;
										if(strlen($m['subject']) > 30)
										{
											echo substr($m['subject'], 0, 30)."...";
										}
										else
										{
											echo $m['subject'];
										}
										echo $b2."</span>
									</td>
									<td>
										<a href ='message.php?id=".$m['id']."' class='btn btn-xs btn-primary'>View</a>
									</td>
								</tr>";
							}							
						echo "	
						</tbody>
					</table>
				</div>
			</div>
			
			<!-- /.modal compose message -->
			<div class='modal fade' id='modalCompose'>
				<div class='modal-dialog'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button type='button' class='close' data-dismiss='modal' aria-hidden='true'><span class='glyphicon glyphicon-remove'></span></button>
							<h4 class='modal-title'>Compose Message</h4>
						</div>
						
						<form name='composeNew' action='".$_SERVER['PHP_SELF']."?m=".$_GET['m']."' method='post' class='form-horizontal'>
							<div class='modal-body'>
								
								<div class='form-group'>
									<div class='col-sm-2'>
										<div class='dropdown dropdown-scroll' ng-app='app'>
											<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown' id='dropdownMenu1' type='button'>
													To
											</button>
											
											<ul class='dropdown-menu' role='menu' aria-labelledby='dropdownMenu1' ng-controller='ListCtrl'>
												<li role='presentation'>
													<div class='input-group input-group-sm search-control'>
														<span class='input-group-addon'>
															<span class='glyphicon glyphicon-user'></span>
														</span>
														<input type='text' class='form-control' placeholder='Username' ng-model='query'></input>
													</div>
												</li>
												<li role='presentation' ng-repeat='item in items | filter:query'>
													<a href='#' id='usernameLink' onclick='insertUsername(this.innerHTML)'>{{item.name}}</a>
												</li>
												
											</ul>
										</div>			
									</div>	
									<div class='col-sm-10'>
										<input type='text' class='form-control' id='recipientField' name='recipient' placeholder='recipient' value=''>
									</div>
								</div>
								
								
								<div class='form-group'>
									<label class='col-sm-2'>Subject</label>
									<div class='col-sm-10'>
										<input type='text' name='subject' maxlength='60' class='form-control' placeholder='subject'>
									</div>
								</div>
								<div class='form-group'>
									<label class='col-sm-12'>Message</label>
									<div class='col-sm-12'>
										<textarea class='form-control' name='message' rows='12'></textarea>
									</div>
								</div>
							</div>
							<div class='modal-footer'>
								<button type='button' class='btn btn-default pull-left' data-dismiss='modal'>Cancel</button> 
								<input type='submit' name='draftButton' value='Save Draft' class='btn btn-warning pull-left'></input>
								<input type='submit' name='sendButton' value='Send' class='btn btn-primary'></input>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>";
?>

<script>
	<?php
		$php_array = jsFetchMessages($loggedInUser->user_id, $_GET['m']);
		echo "var messages = ".json_encode($php_array).";\n";
	?>
	$("#grid-basic").bootgrid({
		url: "/api/data/basic",
		formatters: {
			"link": function(column, row)
			{
				return "<a href='message.php?id=" + messages[row.id - 1]['id'] + "' class='btn btn-xs btn-primary'>View</a>";
			}
		}
	});
	
	function insertUsername(user) {
		//alert( user );
		$("#recipientField").val(user);
	}
	
	// Angular
	var searchUserApp = angular.module('app', []);
	searchUserApp.controller('ListCtrl', function ($scope) {
		<?php
			$php_array = jsFetchAllUsernames();
			echo "$"."scope.items = ".json_encode($php_array).";\n";
		?>
	});
	// jQuery
	$('.dropdown-menu').find('input').click(function (e) {
		e.stopPropagation();
	});
</script>

<?php
	include 'models/footer.php';
?>