<!--
Iowa City Google Maps php page
by Mattie Fickel 

Resources: 
http://bootsnipp.com/snippets/featured/panel-table-with-filters-per-column

-->

<?php
//include in order to connect to the database
require_once("models/config.php");
//require the page containing the db information, etc
require_once("models/header.php");
	
if (!securePage($_SERVER['PHP_SELF']))
{
	die();
}

if(!isUserLoggedIn())
{
	header("Location: admin_users.php");
	die();
}

if(!empty($_POST))
{
	//get values from POST
	$amount = $_POST["amount"];
	$payer_id = fetchUserID($_POST["payer_name"]);
	$due_date = $_POST["date_due"];
	$payee_id = $loggedInUser->user_id;
	$description = $_POST["description"];
	$delete = $POST['delete'];
	
	//add a new cost if appropriate
	if($amount && $payer_id && $payee_id && $description)
	{
		//****VALIDATE DATE*****//
		
		//if the payer and payee are not the same person
		if(userIdExists($payer_id) && $payee_id != $payer_id)
		{
			addCost($description, $payer_id, $payee_id, $due_date, false, false, $amount);
			//echo "<script type='text/javascript'>alert('Cost added successfully.');</script>";
			echo '<div class="alert alert-success">
					<a href="#" class="close" data-dismiss="alert">&times;</a>
						<strong>Success!</strong> Your cost has been added successfully.
				  </div>';

		}
		else
		{
			echo '<div class="alert alert-warning">
					<a href="#" class="close" data-dismiss="alert">&times;</a>
						<strong>Error!</strong> Make sure the user name entered is correct!
				  </div>';
		}
	}
	
	
	/*if(isset($delete))
	{
		
	}*/
}

//get roommate data
$payee_cost = fetchOwedCosts($loggedInUser->user_id);
$payer_cost = fetchDebtCosts($loggedInUser->user_id);
$complete_cost = fetchCompleteCosts($loggedInUser->user_id);
$roommates = fetchAllDebtors($loggedInUser->user_id);
$payees = fetchAllPayees($loggedInUser->user_id);

//consolidate debt data
for($i=0; $i<count($roommates); $i++)
{
	$roommates[$i][debts] = fetchDebtCosts($roommates[$i][cost_payer_id]);
	
	$roommates[$i][debt_total] = 0;
	
	foreach($roommates[$i][debts] as $debt)
	{	
		if($debt[cost_payee_id] == $loggedInUser->user_id)
		{
			$roommates[$i][debt_total] = $roommates[$i][debt_total] + $debt[cost_amount];
		}
	}
}

//add payee data to consolidated roommate data
for($j=0; $j<count($payees); $j++)
{
	$payees[$j][owed] = fetchOwedCosts($payees[$j][cost_payee_id]);
			
	$payees[$j][owed_total] = 0;
	
	foreach($payees[$j][owed] as $owed)
	{	
		if($owed[cost_payer_id] == $loggedInUser->user_id)
		{
			$payees[$j][owed_total] = $payees[$j][owed_total] + $owed[cost_amount];
		}
	}
	
	$payees[$j][recieved_total] = 0;
	$payees[$j][paid_total] = 0;
	
	foreach($complete_cost as $cost)
	{
		if($cost[cost_payee_id] == $payees[$j][cost_payee_id])
		{
			$payees[$j][recieved_total] = $payees[$j][recieved_total] + $cost[cost_amount];
		}
		else if($cost[cost_payer_id] == $payees[$j][cost_payee_id])
		{
			$payees[$j][paid_total] = $payees[$j][paid_total] + $cost[cost_amount];
		}
	}
	
	$i;
	
	for($i=0; $i<count($roommates); $i++)
	{
		if($roommates[$i][cost_payer_id] == $payees[$j][cost_payee_id])
		{
			$roommates[$i][owed] = $payees[$j][owed];		
			$roommates[$i][owed_total] = $payees[$j][owed_total];
			$roommates[$i][recieved_total] = $payees[$j][recieved_total];
			$roommates[$i][paid_total] = $payees[$j][paid_total];
			break;
		}
	}
	
	//if the roommate is not also a debtor
	if($i == count($roommates))
	{		
		array_push($roommates, $payees[$j]);
	}
}

/*
Please consider that the JS part isn't production ready at all, I just code it to show the concept of merging filters and titles together !
*/
/*
echo "
<script>
$(document).ready(function(){
    $('.filterable .btn-filter').click(function(){
        var $panel = $(this).parents('.filterable'),
        $filters = $panel.find('.filters input'),
        $tbody = $panel.find('.table tbody');
        if ($filters.prop('disabled') == true) {
            $filters.prop('disabled', false);
            $filters.first().focus();
        } else {
            $filters.val('').prop('disabled', true);
            $tbody.find('.no-result').remove();
            $tbody.find('tr').show();
        }
    });

    $('.filterable .filters input').keyup(function(e){
        //Ignore tab key
        var code = e.keyCode || e.which;
        if (code == '9') return;
        //Useful DOM data and selectors
        var $input = $(this),
        inputContent = $input.val().toLowerCase(),
        $panel = $input.parents('.filterable'),
        column = $panel.find('.filters th').index($input.parents('th')),
        $table = $panel.find('.table'),
        $rows = $table.find('tbody tr');
        //Dirtiest filter function ever ;)
        var $filteredRows = $rows.filter(function(){
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });
        //Clean previous no-result if exist
        $table.find('tbody .no-result').remove();
        //Show all rows, hide filtered ones (never do that outside of a demo ! xD)
        $rows.show();
        $filteredRows.hide();
        //Prepend no-result row if all rows are filtered
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No result found</td></tr>'));
        }
    });
});
</script>";*/

//make the javascript for the page
echo '';

//make the html for the page
echo '
<div class="container" width="100%">
		<div class="row clearfix">
			<div class="col-md-12 column">
				<div class="col-md-8 column">
					<div class="tabbable" id="tabs-549238">
						<ul class="nav nav-tabs">
							<li class="active">
								<a href="#panel-1" data-toggle="tab">Money I\'m Owed</a>
							</li>
							<li>
								<a href="#panel-2" data-toggle="tab">Money I Owe</a>
							</li>
							<li>
								<a href="#panel-3" data-toggle="tab">Completed Transactions</a>
							</li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="panel-1">
								<p>
									<div class="panel panel-default">
										<div class="panel-body">
											<!--table-->
											<div class="row">
												<div class="panel panel-primary filterable">
													<div class="panel-heading">
														<h3 class="panel-title">Money I\'m Owed</h3>
														<div class="pull-right">
															<!--<button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filter</button>-->
														</div>
													</div>
													<table class="table">
														<thead>
															<tr class="filters">
																<form name=\'addCost\' class=\'form-horizontal\' action="'.$_SERVER['PHP_SELF'].'" method=\'post\'>
																	<th>Amount</th>
																	<th>Description</th>
																	<th>Paid By</th>
																	<th>Date Due</th>
																	<th>Delivered?</th>
																	<th>Recieved?</th>
																	<th></th>
																</form>
															</tr>
														</thead>
														<tbody>';
														
														foreach($payee_cost as $cost)
														{
															echo '<tr><td>$'. $cost[cost_amount] . 
																 '</td><td>'. $cost[cost_description] .
																 '</td><td>'. fetchUsername($cost[cost_payer_id]) .
																 '</td><td>'. (($cost[cost_due_date] != NULL) ? $cost[cost_due_date] : 'None') .
																 '</td><td>'. (($cost[cost_delivered] == 1) ? 'yes' : 'no') .
																 '</td><td>'. (($cost[cost_recieved] == 1) ? 'yes' : 'no') .
																 '<td></td></td></tr>';
														}
													
													$today=getdate(date("U"));
											
													echo'
															<form name=\'addCost\' class=\'form-horizontal\' action="'.$_SERVER['PHP_SELF'].'" method=\'post\'>
																<th><input type="text" class="form-control" placeholder="0.00" name="amount"></th>
																<th><input type="text" class="form-control" placeholder="Description" name="description"></th>
																<th><input type="text" class="form-control" placeholder="Username" name="payer_name"></th>
																<th><input type="date" class="form-control" placeholder="None" name="due_date"></th>
																<!--<th><input type="date" class="form-control" placeholder="'.$today[month]." ".$today[mday].",".$today[year].'" name="due_date"></th>-->
																<th><button type=\'submit\' class=\'btn btn-primary\' name=\'Update\'>Add Cost</button></th>
																<th></th>
															</form>
													    </tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</p>
							</div>
							<div class="tab-pane" id="panel-2">
								<p>
									<div class="panel panel-default">
										<div class="panel-body">
											<!--table-->
											<div class="row">
												<div class="panel panel-primary filterable">
													<div class="panel-heading">
														<h3 class="panel-title">Money I Owe</h3>
														<div class="pull-right">
															<!--<button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filter</button>-->
														</div>
													</div>
													<table class="table">
														<thead>
															<tr class="filters">
																<th>Amount</th>
																<th>Description</th>
																<th>Pay To</th>
																<th>Date Due</th>
																<th>Delivered?</th>
																<th>Recieved?</th>
															</tr>
														</thead>
														<tbody>';
														foreach($payer_cost as $cost)
														{
															echo '<tr><td>$'. $cost[cost_amount] . 
																 '</td><td>'. $cost[cost_description] .
																 '</td><td>'. fetchUsername($cost[cost_payee_id]) .
																 '</td><td>'. (($cost[cost_due_date] != NULL) ? $cost[cost_due_date] : 'None') .
																 '</td><td>'. (($cost[cost_delivered] == 1) ? 'yes' : 'no') .
																 '</td><td>'. (($cost[cost_recieved] == 1) ? 'yes' : 'no') .
																 '<td></td></td></tr>';
														}
														
													echo'</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</p>
							</div>
							<div class="tab-pane" id="panel-3">
								<p>
									<div class="panel panel-default">
										<div class="panel-body">
											<!--table-->
											<div class="row">
												<div class="panel panel-primary filterable">
													<div class="panel-heading">
														<h3 class="panel-title">Completed Transactions</h3>
														<div class="pull-right">
															<!--<button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filter</button>-->
														</div>
													</div>
													<table class="table">
														<thead>
															<tr class="filters">
																<th>Amount</th>
																<th>Description</th>
																<th>Paid To</th>
																<th>Paid By</th>
																<th>Date Due</th>
																<th></th>
															</tr>
														</thead>
														<tbody>';
														foreach($complete_cost as $cost)
														{
															echo '<tr><td>$'. $cost[cost_amount] . 
																 '</td><td>'. $cost[cost_description] .
																 '</td><td>'. (($loggedInUser->user_id == $cost[cost_payee_id]) ? 'you' : fetchUsername($cost[cost_payee_id])) .
																 '</td><td>'. (($loggedInUser->user_id == $cost[cost_payer_id]) ? 'you' : fetchUsername($cost[cost_payer_id])) .
																 '</td><td>'. (($cost[cost_due_date] != NULL) ? $cost[cost_due_date] : 'None') .
																 '<td></td></td></tr>';
														}
															
													echo'</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 column">
					<div class="panel-group" id="panel-1">
					<div class=\"panel-heading\">
						<h3>Roommate Summary</h3>
					</div>';
					
					//Cycle through debtors
					foreach ($roommates as $roommate)
					{	
						echo"
							<div class=\"panel panel-default\">
								<div class=\"panel-heading\">
									 <a class=\"panel-title collapsed\" data-toggle=\"collapse\" data-parent=\"#panel-812954\" href=\"#panel-element-1".$roommate[cost_payer_id]."\">".fetchUsername($roommate[cost_payer_id])."</a>
								</div>
								<div id=\"panel-element-1".$roommate[cost_payer_id]."\" class=\"panel-collapse collapse\">
									<div class=\"panel-body\">
										<div id=\"debtor_".$roommate[cost_payer_id]."\">
										     <ul class=\"list-group\">
											  <li class=\"list-group-item list-group-item-success\">Total amount owed to you:</li>
											  <li class=\"list-group-item\">$".$roommate[debt_total]."</li>
											  <li class=\"list-group-item list-group-item-info\">Total amount you owe:</li>
											  <li class=\"list-group-item\">$".$roommate[owed_total]."</li>
											  <li class=\"list-group-item list-group-item-warning\">Total amount paid:</li>
											  <li class=\"list-group-item\">$".$roommate[recieved_total]."</li>
											  <li class=\"list-group-item list-group-item-danger\">Total amount recieved:</li>
											  <li class=\"list-group-item\">$".$roommate[paid_total]."</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						";
					}
					
			  echo '</div>
				</div>
				<div class="row clearfix">
				</div>
			</div>
		</div>
	</div>';

?>