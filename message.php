<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	$messageId = $_GET['id'];
	
	//Check if the message exists
	if(!messageIdExists($messageId))
	{
		header("Location: messages.php?m=inbox");
		die();	
	}
	
	$message = fetchMessageDetails($messageId);
	
	if(!empty($_POST))
	{
		if(isset($_POST['sendButton']))
		{
			newMessage("insert", $message['sender_id'], $message['recipient_id'], "RE: ".$message['subject'], $_POST['body'], 0);
			header("Location: messages.php?m=inbox");
			die();	
		}
		else if(isset($_POST['draftButton']))
		{
			newMessage("update", $message['sender_id'], $message['recipient_id'], $message['subject'], $_POST['body'], 1);
			header("Location: messages.php?m=inbox");
			die();
		}
		else if(isset($_POST['discardButton']))
		{
			deleteMessage($_GET['id']);
			header("Location: messages.php?m=inbox");
			die();
		}
	}
		
	//Check if the logged in user is either the sender or recipient
	if($message['sender_id'] != $loggedInUser->user_id && $message['recipient_id'] != $loggedInUser->user_id)
	{
		header("Location: messages.php?m=inbox");
		die();	
	}
	
	if($message['wasRead'] == 0)
	{
		readMessage($message['id']);
	}
	
	require_once("models/header.php");
	
	echo "
	<div class='page-header'>
		<h1>
			<a href ='messages.php?m=inbox' class='btn btn-default'>
				<span class='glyphicon glyphicon-circle-arrow-left'></span>
			</a>
			Message Details
		</h1>
	</div>
	
	<div class='row'>
		<div class='col-md-3'>
			<!-- filler -->
		</div>
		
		<div class='col-md-6'>
			<div class='panel panel-default'>
				<div class='panel-body message'>
					<h4><p class='text-center'>";
					
					if($message['draft'] == 1)
					{
						echo "Draft";
					}
					else
					{
						echo "Sent";
					}
					
					echo ": ".date("M d, Y - g:i:s A", $message['timestamp'])."</p></h4>
					<br>
					<form class='form-horizontal' action='".$_SERVER['PHP_SELF']."?id=".$_GET['id']."' method='post' role='form'>";	
						
						if($message['draft'] == 1)
						{
							echo "
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>To:</label>
								<div class='col-sm-9'>
									<input type='email' class='form-control' value='".fetchUsername($message['recipient_id'])."'>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>From:</label>
								<div class='col-sm-9'>
									<input type='email' class='form-control' placeholder='".fetchUsername($message['sender_id'])."' disabled>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>Subject:</label>
								<div class='col-sm-9'>
									<input type='text' class='form-control' value='".$message['subject']."'></input>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>Message:</label>
								<div class='col-sm-9'>
									<textarea class='form-control' rows='4'>".$message['message']."</textarea>
								</div>
							</div>
							
							<center>
								<button type='reset' name='discardButton' class='btn btn-default'>Discard</button>
								<button type='submit' name='draftButton' class='btn btn-default'>Draft</button>
								<button type='submit' name='submitButton' class='btn btn-primary'>Send</button>
							</center>";
						}
						else
						{
							echo "
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>To:</label>
								<div class='col-sm-9'>
									<input type='email' class='form-control' placeholder='".fetchUsername($message['recipient_id'])."' disabled>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>From:</label>
								<div class='col-sm-9'>
									<input type='email' class='form-control' placeholder='".fetchUsername($message['sender_id'])."' disabled>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>Subject:</label>
								<div class='col-sm-9'>
									<input type='text' class='form-control' placeholder='".$message['subject']."' disabled>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>Message:</label>
								<div class='col-sm-9'>
									<textarea class='form-control' rows='4' placeholder='".$message['message']."' disabled></textarea>
								</div>
							</div>
							
							<br>
							<center>
								<a class='btn btn-default'>
									Read
								</a>
								<a class='btn btn-primary' data-toggle='collapse' href='#collapseExample' aria-expanded='false' aria-controls='collapseExample'>
									Reply
								</a>
							</center>
							<br>
							<div class='collapse' id='collapseExample'>			
								<div class='col-sm-9 col-sm-offset-2'>	
									<div class='form-group'>
										<textarea class='form-control' id='message' name='body' rows='4' placeholder='Click here to reply'></textarea>
									</div>
									
									<center>";
										
										if($message['draft'] == 1)
										{
											echo "<button type='submit' name='discardButton' class='btn btn-default'>Draft</button>";
										}
										else
										{
											echo "<button type='reset' class='btn btn-default' data-toggle='collapse' data-target='#collapseExample' aria-expanded='false' aria-controls='collapseExample'>Discard</button>";
										}
										
										echo "
										<button type='submit' name='draftButton' class='btn btn-default'>Draft</button>
										<button type='submit' name='sendButton' class='btn btn-primary'>Send</button>
									</center>
								</div>
							</div>";
						}
						
					echo "
					</form>
				</div>
			</div>
		</div>
		
		<div class='col-md-3'>
			<!-- filler -->
		</div>
				
	</div>";
	
	
	
	include 'models/footer.php';
?>