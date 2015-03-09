<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
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
			$folderActive = "class='active'";
			break;
		case "sent":
			$messages = fetchMessages($loggedInUser->user_id, "sent");
			$folderActive = "class='active'";
			break;
		case "drafts":
			$messages = fetchMessages($loggedInUser->user_id, "drafts");
			$folderActive = "class='active'";
			break;
		default:
			$_GET['m'] = "inbox";
			$messages = fetchMessages($loggedInUser->user_id, "inbox");
			$folderActive = "class='active'";
	}
	
	if(!empty($_POST))
	{
		//error_log("Forms posted");
		
		//TODO test
		if(isset($_POST['sendButton']))
		{
			newMessage("insert", $loggedInUser->user_id, $_POST['recipient'], $_POST['subject'], $_POST['message'], 0);
			header("Location: messages.php?m=".$_GET['m']);
			die();	
		}
		else if(isset($_POST['draftButton']))
		{
			newMessage("insert", $loggedInUser->user_id, $_POST['recipient'], $_POST['subject'], $_POST['message'], 1);
			header("Location: messages.php?m=".$_GET['m']);
			die();
		}
	}
	else
	{
		//error_log("Forms not posted");
	}
	
	require_once("models/header.php");
	
	echo "
	
	<div class='page-header'>
		<h1>Messages</h1>
	</div>

	<div class='container'>
		<div class='row'>
			<aside class='col-md-2 pad-right-0'>
				<ul class='nav nav-pills nav-stacked'>
					<li ".$folderActive."><a href='messages.php?m=inbox'><span class='badge pull-right'>".$unreadCount."</span> Inbox </a></li>
					<li><a href='messages.php?m=sent'> Sent </a></li>
					<li><a href='messages.php?m=drafts'> Drafts </a></li>
				</ul>
			</aside>
			
			<div class='col-md-10'>
				<!--inbox toolbar-->
				<div class='row'>
					<div class='col-xs-12'>
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
								<li><a href='#' data-toggle='modal' data-target='#modalCompose'>Compose new</a></li>
								<li><a href='user_settings.php' class='text-muted'>Settings</a></li>
							</ul>
						</div>
						
					</div>
					<!--/col-->
					<div class='col-xs-12 spacer5'></div>
				</div>
				<!--/row-->
				
				<!--/inbox toolbar-->
				<div class='panel panel-default inbox' id='inboxPanel'>
					<!--message list-->
					<div class='table-responsive'>
						<table class='table table-striped table-hover refresh-container pull-down'>
							<thead class='hidden-xs'>
								<tr>
									<td class='col-sm-3'><a href='#'><strong>Date / Time</strong></a></td>
									<td class='col-sm-2'><a href='#'><strong>Sender</strong></a></td>
									<td class='col-sm-4'><a href='#'><strong>Subject</strong></a></td>
									<td class='col-sm-1'></td>
								</tr>
							</thead>
							<tbody>";
								
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
									
									echo "
									<tr>
										<td class='col-sm-3 col-xs-4'>
											<span>".$b1.date("M d, Y - g:i:s A", $m['timestamp']).$b2."</span>
										</td>
										<td class='col-sm-2 col-xs-4'>
											<span>".$b1.fetchUsername($m['sender_id']).$b2."</span>
										</td>
										<td class='col-sm-4 col-xs-6'>
											<span>".$b1.$m['subject'].$b2."</span>
										</td>
										<td class='col-sm-1 col-sm-2'>
											<a href ='message.php?id=".$m['id']."' class='btn btn-xs btn-primary'>View</a>
										</td>
									</tr>";
								}
								if(!isset($messages))
								{
									echo "
									<tr>
										<td class='col-sm-3 col-xs-4'>
											-
										</td>
										<td class='col-sm-2 col-xs-4'>
											-
										</td>
										<td class='col-sm-4 col-xs-6'>
											-
										</td>
										<td class='col-sm-1 col-sm-2'></td>
									</tr>";
								}
							echo "	
							</tbody>
						</table>
					</div>
				</div>
				<!--/inbox panel-->
				<div class='well well-s text-right'>
					<em>Inbox last updated: <span>".date("M d, Y - g:i:s A", time())."</span></em>
				</div>
				
				<!--paging-->
				<div class='pull-right'>
					<span class='text-muted'>
						<b>1</b>-<b>1</b> of <b>".count($messages)."</b>
					</span>
					<div class='btn-group btn-group'>
						<button type='button' class='btn btn-default btn-lg'>
							<span class='glyphicon glyphicon-chevron-left'></span>
						</button>
						<button type='button' class='btn btn-default btn-lg'>
							<span class='glyphicon glyphicon-chevron-right'></span>
						</button>
					</div>
				</div>
				<hr>
				
			</div>
			<!--/col-9-->
			
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
									<label class='col-sm-2'>To</label>
									<div class='col-sm-10'>
										<input type='text' name='recipient' class='form-control' placeholder='recipient'>
									</div>
								</div>
								<div class='form-group'>
									<label class='col-sm-2'>Subject</label>
									<div class='col-sm-10'>
										<input type='text' name='subject' class='form-control' placeholder='subject'>
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
	
	
	
	include 'models/footer.php';
?>