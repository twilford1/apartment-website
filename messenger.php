<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	
	
	$messages = fetchInbox($loggedInUser->user_id);
	
	require_once("models/header.php");

	
	//date("H:i:s", time())
	
	echo "
	<div class='page-header'>
		<h1>Messages</h1>
	</div>

	<div class='container'>
		<div class='row'>
			<aside class='col-md-2 pad-right-0'>
				<ul class='nav nav-pills nav-stacked'>
					<li class='active'><a href='#'><span class='badge pull-right'>27</span> Inbox </a></li>
					<li><a href='#'> Sent </a></li>
					<li><a href='#'><span class='badge pull-right'>1</span> Drafts </a></li>
					<li><a href='#'> Deleted </a></li>
				</ul>
			</aside>
			
			<div class='col-md-10'>
				<!--inbox toolbar-->
				<div class='row'>
					<div class='col-xs-12'>
						<a href='#' class='btn btn-default btn-lg'>
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
								<li><a href='#'>Mark all as read</a></li>
								<li class='divider'></li>
								<li><a href='#' data-toggle='modal'>Compose new</a></li>
								<li><a href='#' class='text-muted'>Settings</a></li>
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
									echo "
									<tr>
										<td class='col-sm-3 col-xs-4'>
											<span>".date("Y-d-M H:m:s", $m['timestamp'])."</span>
										</td>
										<td class='col-sm-2 col-xs-4'>
											<span>".fetchUsername($m['sender_id'])."</span>
										</td>
										<td class='col-sm-4 col-xs-6'>
											<span>".$m['subject']."</span>
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
					<em>Inbox last updated: <span>2014-09-02 09:16</span></em>
				</div>
				
				<!--paging-->
				<div class='pull-right'>
					<span class='text-muted'>
						<b>1</b>-<b>1</b> of <b>1</b>
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
						<div class='modal-body'>
							<form role='form' class='form-horizontal'>
								<div class='form-group'>
									<label class='col-sm-2'>To</label>
									<div class='col-sm-10'>
										<input type='email' class='form-control' placeholder='recipient'>
									</div>
								</div>
								<div class='form-group'>
									<label class='col-sm-2'>Subject</label>
									<div class='col-sm-10'><input type='text' class='form-control' placeholder='subject'></div>
								</div>
								<div class='form-group'>
									<label class='col-sm-12'>Message</label>
									<div class='col-sm-12'>
										<textarea class='form-control' rows='12'></textarea>
									</div>
								</div>
							</form>
						</div>
						<div class='modal-footer'>
							<button type='button' class='btn btn-default pull-left' data-dismiss='modal'>Cancel</button> 
							<button type='button' class='btn btn-warning pull-left'>Save Draft</button>
							<button type='button' class='btn btn-primary '>Send <i class='fa fa-arrow-circle-right fa-lg'></i></button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal compose message -->
			<div>
				<!--/row ng-controller-->
			</div>
			<!--/container-->
		</div>
	</div>";
	
	
	
	
	
	
	
	
	
	
	include 'models/footer.php';
?>