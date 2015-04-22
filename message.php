<?php
	require_once("models/config.php");
	
	if(!securePage($_SERVER['PHP_SELF']))
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
	
	//Check if the logged in user is either the sender or recipient
	if($message['sender_id'] != $loggedInUser->user_id && $message['recipient_id'] != $loggedInUser->user_id)
	{
		header("Location: messages.php?m=inbox");
		die();	
	}
	
	if(!empty($_POST))
	{
		// If the toggle read button was pressed then only update the read value
		if(isset($_POST['toggleRead']))
		{
			// If the message has not been read then read it, otherwise
			// unread the message
			if($message['wasRead'] == 0)
			{
				toggleMessageRead($message['id'], 1);
			}
			else
			{
				toggleMessageRead($message['id'], 0);
			}
			
			$message = fetchMessageDetails($messageId);
		}
		else
		{
			// Handling an accept or deny from a permission request
			if(isset($_POST['grantRequest']))
			{
				if(strpos($message['subject'], "Admin Request") !== false)
				{
					addPermission(2, $message['sender_id']);
					removePermission(1, $message['sender_id']);
				}
				elseif(strpos($message['subject'], "Landlord Request") !== false)
				{
					addPermission(3, $message['sender_id']);
					removePermission(1, $message['sender_id']);
				}
				
				emailAdmins($message['sender_id'], $message['subject']." granted", fetchUsername($loggedInUser->user_id)." has granted the following request:\r\n\r\n".$message['message']."\r\n\r\n This change will be applied the next time you login");
				deleteMessages($message['sender_id'], $message['subject'], $message['message']);
				$successes = [0 => "Permission Granted"];
				header("Refresh: 2;url=messages.php?m=inbox");
			}
			if(isset($_POST['denyRequest']))
			{
				emailAdmins($message['sender_id'], $message['subject']." denied", fetchUsername($loggedInUser->user_id)." has denied the following request:\r\n\r\n".$message['message']);
				deleteMessages($message['sender_id'], $message['subject'], $message['message']);
				$successes = [0 => "Permission Denied"];
				header("Refresh: 2;url=messages.php?m=inbox");
			}
			
						
			// If the message was sent to you then that means you are
			// replying, so the new recipient is the old sender
			if($loggedInUser->user_id == $message['recipient_id'])
			{
				$recipientID = fetchUserID($_POST['sender']);
			}
			else
			{
				$recipientID = fetchUserID($_POST['recipient']);
			}
			
			if(isset($_POST['sendButton']))
			{
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
							if($message['draft'] == 1)
							{
								if(updateMessage($message['id'], $recipientID, $_POST['subject'], $_POST['message'], 0))
								{
									$successes = [0 => "Message Sent"];
									$message = fetchMessageDetails($messageId);
									header("Refresh: 2;url=messages.php?m=inbox");
								}
								else
								{
									$errors[] = lang("SQL_ERROR");
								}
							}
							else
							{
								if(newMessage($loggedInUser->user_id, $recipientID, "RE: ".$message['subject'], $_POST['message'], 0))
								{
									$successes = [0 => "Message Sent"];
									header("Refresh: 2;url=messages.php?m=inbox");
								}
								else
								{
									$errors[] = lang("SQL_ERROR");
								}
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
				// If the current message is already a draft, just update
				// otherwise create a new message for the draft
				if($message['draft'] == 1)
				{
					if(updateMessage($message['id'], $recipientID, $_POST['subject'], $_POST['message'], 1))
					{
						$successes = [0 => "Draft Saved"];
						$message = fetchMessageDetails($messageId);
					}
					else
					{
						$errors[] = lang("SQL_ERROR");
					}
				}
				else
				{
					if(newMessage($loggedInUser->user_id, $recipientID, $_POST['subject'], $_POST['message'], 1))
					{
						$successes = [0 => "Draft Saved"];
						$message = fetchMessageDetails($messageId);
						header("Refresh: 2;url=messages.php?m=drafts");
					}
					else
					{
						$errors[] = lang("SQL_ERROR");
					}
				}
			}
			else if(isset($_POST['discardButton']))
			{
				if(deleteMessage($_GET['id']))
				{
					$successes = [0 => "Message Deleted"];
					header("Refresh: 2;url=messages.php?m=drafts");
				}
				else
				{
					$errors[] = lang("SQL_ERROR");
				}
			}
		}
	}
	
	// Mark message read by from viewing
	if($message['wasRead'] == 0 && !isset($_POST['toggleRead']))
	{
		toggleMessageRead($message['id'], 1);
		$message = fetchMessageDetails($messageId);
	}
	
	// 1 - default == read
	// 0 - primary == not read
	if($message['wasRead'] == 0)
	{
		$toggleReadLabel = "btn btn-primary";
	}
	else
	{
		$toggleReadLabel = "btn btn-default";
	}
	
	require_once("models/header.php");
	
	echo "<center>";
	echo resultBlock($errors, $successes);
	echo "</center>";
	
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
					<form class='form-horizontal' action='".$_SERVER['PHP_SELF']."?id=".$_GET['id']."' method='post' role='form' id='message-form'>";	
						
						if($message['draft'] == 1)
						{
							echo "
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>To:</label>
								<div class='col-sm-9'>
									<input type='text' name='recipient' class='form-control' value='".fetchUsername($message['recipient_id'])."'>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>From:</label>
								<div class='col-sm-9'>
									<input type='text' name='sender' class='form-control' value='".fetchUsername($message['sender_id'])."'>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>Subject:</label>
								<div class='col-sm-9'>
									<input type='text' name='subject' maxlength='60' class='form-control' value='".$message['subject']."'></input>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>Message:</label>
								<div class='col-sm-9'>
									<textarea name='message' class='form-control' rows='4'>".$message['message']."</textarea>
								</div>
							</div>
							
							<center>
								<button type='submit' name='discardButton' class='btn btn-default' onclick='confirmDelete(this.form);'>Discard</button>
								<button type='submit' name='draftButton' class='btn btn-default'>Save</button>
								<button type='submit' name='sendButton' class='btn btn-primary'>Send</button>
							</center>";
						}
						else
						{
							echo "
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>To:</label>
								<div class='col-sm-9'>
									<input type='text' name='recipient' class='form-control' value='".fetchUsername($message['recipient_id'])."'>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>From:</label>
								<div class='col-sm-9'>
									<input type='text' name='sender' class='form-control' value='".fetchUsername($message['sender_id'])."'>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>Subject:</label>
								<div class='col-sm-9'>
									<input type='text' name='subject' maxlength='60' class='form-control' value='".$message['subject']."'>
								</div>
							</div>
							<div class='form-group'>
								<label for='to' class='col-sm-2 control-label'>Message:</label>
								<div class='col-sm-9'>
									<textarea class='form-control' rows='4'>".$message['message']."</textarea>
								</div>
							</div>";
							
							if($loggedInUser->user_id != $message['sender_id'])
							{
								echo "<center>";
								
								if(isRequest($message['id']))
								{
									echo "
									<button type='submit' name='toggleRead' class='".$toggleReadLabel."'>
										<span class='glyphicon glyphicon-envelope'></span>
									</button>
									<button type='submit' name='grantRequest' class='btn btn-primary'>Grant Request</button>
									<button type='submit' name='denyRequest' class='btn btn-primary'>Deny Request</button>
									<br>";
								}
								else
								{									
									echo"
										<br>
										<button type='submit' name='toggleRead' class='".$toggleReadLabel."'>
											<span class='glyphicon glyphicon-envelope'></span>
										</button>
										<a class='btn btn-primary' data-toggle='collapse' href='#collapseExample' aria-expanded='false' aria-controls='collapseExample'>
											<span class='glyphicon glyphicon-pencil'></span>
										</a>
									</center>
									<br>
									<div class='collapse' id='collapseExample'>			
										<div class='col-sm-9 col-sm-offset-2'>	
											<div class='form-group'>
												<textarea name='message' class='form-control' id='message' name='body' rows='4' placeholder='Click here to reply'></textarea>
											</div>
											
											<center>
												<button type='reset' class='btn btn-default' data-toggle='collapse' data-target='#collapseExample' aria-expanded='false' aria-controls='collapseExample'>Discard</button>
												<button type='submit' name='draftButton' class='btn btn-default'>Draft</button>
												<button type='submit' name='sendButton' class='btn btn-primary'>Send</button>
											</center>
										</div>
									</div>";
								}
							}
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
?>

<script>
	function confirmDelete(form) {
		if (confirm("Are you sure you want to delete this draft?")) {
			form.submit();
		}
	}
</script>

<?php	
	include 'models/footer.php';
?>