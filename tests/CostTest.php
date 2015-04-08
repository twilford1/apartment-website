<?php

/*
 *Test class for functions having to do with favorites which are included
 *in funcs.php.  Because of issues with phpunit,
 *the functions from funcs.php MUST be copied from there
 *to this class to be tested.  Has to do with global variable
 *issues that I am unable to solve in any other way as of yet.
 *
 */
class CostTest extends PHPUnit_Framework_TestCase
{	
	//test that only the user's debts are retrieved
	public function testFetchDebtCosts()
	{
		fetchDebtCosts($user_id);
	}
	
	//test that only the user's owed costs are fetched
	public function testFetchOwedCosts()
	{
		fetchOwedCosts($user_id);
	}

	//test that only the user's completed transactions are fetched
	public function testFetchCompleteCosts()
	{
		fetchCompleteCosts($user_id);
	}
	
	//test that all of the user's debtors are fetched (distinct)
	public function testFetchAllDebtors()
	{
		fetchAllDebtors($user_id);
	}
	
	//test that all of the people the user owes are fetched (distinct)
	public function testFetchAllPayees()
	{
		fetchAllPayees($user_id);
	}
	
	//test that costs can be added to the db
	public function testAddCost()
	{
		addCost($description, $payer_id, $payee_id, $due_date, $delivered, $recieved, $amount);
	}
	
	//test that existing costs can be updated
	public function testUpdateCost()
	{
		updateCost($cost_id, $recieved, $delivered);
	}
	
	//test that existing costs can be deleted
	public function testDeleteCost()
	{
		deleteCost($cost_id);
	}
}

/************************COST FUNCTIONS***********************************/
//Get costs which the user owes
function fetchDebtCosts($user_id)
{
	// Return all debts for this user
	global $mysqli, $db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."costs
		WHERE cost_payer_id = ? AND (cost_recieved = false OR cost_delivered = false)");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($cost_id, $cost_description, $cost_payer_id, $cost_payee_id, $cost_due_date, $cost_delivered, $cost_recieved, $cost_amount);
	
	while ($stmt->fetch())
	{
		$row[] = array('cost_id' => $cost_id, 'cost_description' => $cost_description, 'cost_payer_id' => $cost_payer_id, 'cost_payee_id' => $cost_payee_id, 'cost_due_date' => $cost_due_date, 'cost_delivered' => $cost_delivered, 'cost_recieved' => $cost_recieved, 'cost_amount' => $cost_amount);
	}
	$stmt->close();
	return ($row);
}

//Get costs which the user is owed
function fetchOwedCosts($user_id)
{
	// Return all debts for this user
	global $mysqli, $db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."costs
		WHERE cost_payee_id = ? AND (cost_recieved = false OR cost_delivered = false)");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($cost_id, $cost_description, $cost_payer_id, $cost_payee_id, $cost_due_date, $cost_delivered, $cost_recieved, $cost_amount);
	
	while ($stmt->fetch())
	{
		$row[] = array('cost_id' => $cost_id, 'cost_description' => $cost_description, 'cost_payer_id' => $cost_payer_id, 'cost_payee_id' => $cost_payee_id, 'cost_due_date' => $cost_due_date, 'cost_delivered' => $cost_delivered, 'cost_recieved' => $cost_recieved, 'cost_amount' => $cost_amount);
	}
	$stmt->close();
	return ($row);
}

//Get complete costs for the user
function fetchCompleteCosts($user_id)
{
	// Return all debts for this user
	global $mysqli, $db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."costs
		WHERE (cost_payer_id = ? OR cost_payee_id = ?) AND (cost_recieved = true AND cost_delivered = true)");
	$stmt->bind_param("ii", $user_id, $user_id);
	$stmt->execute();
	$stmt->bind_result($cost_id, $cost_description, $cost_payer_id, $cost_payee_id, $cost_due_date, $cost_delivered, $cost_recieved, $cost_amount);
	
	while ($stmt->fetch())
	{
		$row[] = array('cost_id' => $cost_id, 'cost_description' => $cost_description, 'cost_payer_id' => $cost_payer_id, 'cost_payee_id' => $cost_payee_id, 'cost_due_date' => $cost_due_date, 'cost_delivered' => $cost_delivered, 'cost_recieved' => $cost_recieved, 'cost_amount' => $cost_amount);
	}
	$stmt->close();
	return ($row);
}

//Get all of the users who currently owe the specified user
function fetchAllDebtors($user_id)
{
	// Return all debtors for this user
	global $mysqli, $db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT DISTINCT cost_payer_id
		FROM (SELECT 
		cost_payer_id
		FROM ".$db_table_prefix."costs
		WHERE cost_payee_id = ? AND cost_recieved = false) AS table1");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($cost_payer_id);
	
	while ($stmt->fetch())
	{
		$row[] = array('cost_payer_id' => $cost_payer_id);
	}
	$stmt->close();
	return ($row);
}

//Get all of the users who are currently owed by the specified user
function fetchAllPayees($user_id)
{
	// Return all debts for this user
	global $mysqli, $db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT DISTINCT cost_payee_id
		FROM (SELECT 
		cost_payee_id
		FROM ".$db_table_prefix."costs
		WHERE cost_payer_id = ? AND cost_recieved = false) AS table1");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($cost_payee_id);
	
	while ($stmt->fetch())
	{
		$row[] = array('cost_payee_id' => $cost_payee_id);
	}
	$stmt->close();
	return ($row);
}

//Delete specified costs
function deleteCost($cost_id)
{
	global $mysqli, $db_table_prefix; 
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."costs 
		WHERE cost_id = ?");
	$stmt->bind_param("i", $cost_id);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//Add specified cost
function addCost($description, $payer_id, $payee_id, $due_date, $delivered, $recieved, $amount)
{
	global $mysqli, $db_table_prefix;
	//Insert the message into the database
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."costs (
		cost_description,
		cost_payer_id,
		cost_payee_id,
		cost_due_date,
		cost_delivered,
		cost_recieved,
		cost_amount
		)
		VALUES (
		?,
		?,
		?,
		?,
		?,
		?,
		?
		)");		
	$stmt->bind_param("siisiid", $description, $payer_id, $payee_id, $due_date, $delivered, $recieved, $amount);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Update the status of the transaction
function updateCost($cost_id, $recieved, $delivered)
{
	global $mysqli, $db_table_prefix;
	//Update the cost in the database
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."costs
		SET cost_recieved = ?,
		cost_delivered = ?
		WHERE cost_id = ?
		LIMIT 1");
	$stmt->bind_param("iii", $recieved, $delivered, $cost_id);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

?>