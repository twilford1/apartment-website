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

$payee_cost = fetchOwedCosts($loggedInUser->user_id);
$payer_cost = fetchDebtCosts($loggedInUser->user_id);
$complete_cost = fetchCompleteCosts($loggedInUser->user_id);

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

//make the html for the page
echo '
<div class="container">
		<div class="row clearfix">
			<div class="col-md-12 column">
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
								<div class="col-md-8 column">
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
																<th><input type="text" class="form-control" placeholder="Amount" disabled></th>
																<th><input type="text" class="form-control" placeholder="Description" disabled></th>
																<th><input type="text" class="form-control" placeholder="Paid By" disabled></th>
																<th><input type="text" class="form-control" placeholder="Date Due" disabled></th>
																<th><input type="text" class="form-control" placeholder="Delivered?" disabled></th>
																<th><input type="text" class="form-control" placeholder="Recieved?" disabled></th>
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
															     '</td></tr>';
														}
											
													echo'</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-4 column">
									<div class="panel-group" id="panel-812954">
										

									</div>
								</div>
							</p>
						</div>
						<div class="tab-pane" id="panel-2">
							<p>
								<div class="col-md-8 column">
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
																<th><input type="text" class="form-control" placeholder="Amount" disabled></th>
																<th><input type="text" class="form-control" placeholder="Description" disabled></th>
																<th><input type="text" class="form-control" placeholder="Pay To" disabled></th>
																<th><input type="text" class="form-control" placeholder="Date Due" disabled></th>
																<th><input type="text" class="form-control" placeholder="Delivered?" disabled></th>
																<th><input type="text" class="form-control" placeholder="Recieved?" disabled></th>
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
															     '</td></tr>';
														}
														
													echo'</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-4 column">
									<div class="panel-group" id="panel-812953">
										
									</div>
								</div>
							</p>
						</div>
						<div class="tab-pane" id="panel-3">
							<p>
								<div class="col-md-8 column">
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
																<th><input type="text" class="form-control" placeholder="Amount" disabled></th>
																<th><input type="text" class="form-control" placeholder="Description" disabled></th>
																<th><input type="text" class="form-control" placeholder="Paid To" disabled></th>
																<th><input type="text" class="form-control" placeholder="Paid By" disabled></th>
																<th><input type="text" class="form-control" placeholder="Date Due" disabled></th>
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
															     '</td></tr>';
														}
															
													echo'</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-4 column">
									<div class="panel-group" id="panel-812953">
										
									</div>
								</div>
							</p>
						</div>
					</div>
				</div>
				<div class="row clearfix">
					
				</div>
			</div>
		</div>
	</div>';

?>