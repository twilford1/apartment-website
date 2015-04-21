<?php

//require "models/config.php";
require_once "models/funcs.php";

/*
 *Test class for costs.php; Functions used for costs.php
 *are included in funcs.php.  These are the functions
 *that will be unit tested.  
 *
 ********Issues with PHPUnit require that
 *db setup is managed within this test file instead of using
 *config.php.
 *
 *Functions to test:
 *	-addCost
 *	-updateCost
 *	-deleteCost
 *	-fetchRoommates
 *	-fetchOwedCosts
 *	-fetchDebtCosts
 *	-fetchCompleteCosts
 */
class CostTest extends PHPUnit_Framework_TestCase
{	
	//set up each test
	public function setUp()
	{
		////////////Setup the test database///////////////////
		
		$db_host = "localhost"; //Host address (most likely localhost)
		$db_name = "website_test"; //Name of Database
		$db_user = "websiteUser"; //Name of database user
		$db_pass = "4WPXGzCUm2y2TeG7"; //Password for database user
		$db_table_prefix = "apt_";
		$GLOBALS['db_table_prefix'] = $db_table_prefix;

		$errors = array();
		$successes = array();
		$GLOBALS['errors'] = $errors;
	    $GLOBALS['successes'] = $successes;

		//Create a new mysqli object with database connection parameters
		$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
		$GLOBALS['mysqli'] = $mysqli;

		if(mysqli_connect_errno())
		{
			echo "Connection Failed: " . mysqli_connect_errno();
			exit();
		}
		
		/////////////Modify the test database for test cases///////////////
		
		//add test roommates
		insertTestRoommate(-1, -2);
		insertTestRoommate(-1, -3);
		insertTestRoommate(-2, -3);
		insertTestRoommate(-2, -1);
		insertTestRoommate(-3, -1);
		insertTestRoommate(-3, -2);
		
		//add test costs
		addTestCost("Delete Me", -1, -2, "", 0, 0, 10.00);
		addTestCost("Delete Me", -2, -1, "", 1, 0, 20.00);
		addTestCost("Delete Me", -1, -2, "", 0, 0, 30.00);
		addTestCost("Delete Me", -1, -2, "", 1, 1, 40.00);
		
		//save all of the current cost tuples
		$GLOBALS['all_costs'] = getAllCosts();
	}
	
	//test that costs can be added to the db
	public function testAddCostToDB()
	{
		//inform the user
		echo("------------------------\n");
		echo("CostTest Case 1: testAddCostToDB\n");
		
		//get all costs
		global $all_costs;
		
		//add a cost
		addCost("Delete Me", -1, -1, "", 0, 0, 0);
		
		//get all costs again
		$somanycosts = getAllCosts();
		
		//delete test cost
		$cost = getCostByDescription("Delete Me");
		deleteTestCost($cost['cost_id']);
		
		//assert that the number of costs has gone up
		$this->assertTrue((count($all_costs)) == count($somanycosts)-1);
		
		echo("Cost is added to the DB:\n");
		echo(((count($all_costs)) == count($somanycosts)-1)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that existing costs can be updated
	public function testUpdateCostInDB()
	{
		//inform the user
		echo("------------------------\n");
		echo("CostTest Case 2: testUpdateCostInDB\n");
		
		//add a cost to test
		addTestCost("Delete Me", -1, -1, "", 0, 0, 0);
		
		//try to update the new cost
		$cost = getCostByDescription("Delete Me");	
		updateCost($cost['cost_id'], 1, 1);
		
		//get the updated tuple
		$cost2 = getCostById($cost['cost_id']);
		
		//delete new cost	
		if(isset($cost2['cost_id']))
		{
			deleteTestCost($cost2['cost_id']);
		}
		
		//check to see if changes went through
		$this->assertTrue($cost2['cost_recieved'] == 1);
		
		echo("Cost recieved field was updated:\n");
		echo(($cost2['cost_recieved'] == 1)?"PASS\n":"FAIL\n");
		
		$this->assertTrue($cost2['cost_delivered'] == 1);	
		
		echo("Cost delivered field was updated:\n");
		echo(($cost2['cost_delivered'] == 1)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that existing costs can be deleted
	public function testDeleteCostFromDB()
	{
		//inform the user
		echo("------------------------\n");
		echo("CostTest Case 3: testDeleteCostFromDB\n");
		
		//add a cost to test
		addTestCost("Delete Me", -1, -1, "", 0, 0, 0);
		
		//get all costs
		$somanycosts = getAllCosts();
		
		//try to delete the cost
		$cost = getCostByDescription("Delete Me");	
		deleteCost($cost['cost_id']);
		
		//get all costs again
		$somanycosts2 = getAllCosts();
		
		//make sure there is one less tuple
		$this->assertTrue((count($somanycosts) - 1) == count($somanycosts2));
		
		echo("Cost was deleted successfully:\n");
		echo(((count($somanycosts) - 1) == count($somanycosts2))?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that the correct roommates are found
	public function testFetchRoommatesFromDB()
	{
		//inform the user
		echo("------------------------\n");
		echo("CostTest Case 4: testFetchRoommatesFromDB\n");
		
		//set test user id
		$user_id = -1;
		
		//fetch test roommates of test user
		$roommates = fetchRoommates($user_id);
		$result = isset($roommates[0]);
		
		//make sure at least one roommate returned
		$this->assertTrue($result);
		
		echo("At least one roommate tuple returned:\n");
		echo(($result)?"PASS\n":"FAIL\n");
		
		if($result)
		{
			//make sure only two roommates are returned
			$this->assertTrue(count($roommates) == 2);
			
			echo("Exactly 2 tuples returned:\n");
			echo((count($roommates) == 2)?"PASS":"FAIL");
		}
		
		echo("\n\n");
	}
	
	//test that only the user's debts are retrieved
	public function testFetchDebtCostsForUser()
	{
		//inform the user
		echo("------------------------\n");
		echo("CostTest Case 5: testFetchDebtCostsForUser\n");
		
		//set user id
		$user_id = -1;
		
		//get debts for the test user
		$costs = fetchDebtCosts($user_id);
		$total = 0;
		
		//get the total debt they owe
		foreach($costs as $cost)
		{
			$total = $total + $cost['cost_amount'];
		}
		
		//assert that the total debt is what was expected
		$this->assertTrue($total == 40.00);
		
		echo("Exactly $40.00 worth of debt for test user:\n");
		echo(($total == 40.00)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//test that only the user's owed costs are fetched
	public function testFetchOwedCostsForUser()
	{
		//inform the user
		echo("------------------------\n");
		echo("CostTest Case 6: testFetchOwedCostsForUser\n");
		
		//set user id
		$user_id = -1;
		
		//get debts for the test user
		$costs = fetchOwedCosts($user_id);
		$total = 0;
		
		//get the total debt they owe
		foreach($costs as $cost)
		{
			$total = $total + $cost['cost_amount'];
		}
		
		//assert that the total debt is what was expected
		$this->assertTrue($total == 20.00);
		
		echo("Exactly $20.00 worth owed to the test user:\n");
		echo(($total == 20.00)?"PASS":"FAIL");
		echo("\n\n");
	}

	//test that only the user's completed transactions are fetched
	public function testFetchCompleteCostsForUser()
	{
		//inform the user
		echo("------------------------\n");
		echo("CostTest Case 7: testFetchCompleteCostsForUser\n");
		
		//set user id
		$user_id = -1;
		
		//get debts for the test user
		$costs = fetchCompleteCosts($user_id);
		$total = 0;
		
		//get the total debt they owe
		foreach($costs as $cost)
		{
			$total = $total + $cost['cost_amount'];
		}
		
		//assert that the total debt is what was expected
		$this->assertTrue($total == 40.00);
		
		echo("Exactly $40.00 worth of completed transactions for the test user:\n");
		echo(($total == 40.00)?"PASS":"FAIL");
		echo("\n\n");
	}
	
	//reset the database after each test
	public function teardown()
	{
		//remove test roommates
		deleteTestRoommate(-1, -2);
		deleteTestRoommate(-1, -3);
		deleteTestRoommate(-2, -3);
		deleteTestRoommate(-2, -1);
		deleteTestRoommate(-3, -1);
		deleteTestRoommate(-3, -2);
		
		//remove test costs
		for($i=0; $i<4; $i++)
		{
			$cost = getCostByDescription("Delete Me");
			deleteTestCost($cost['cost_id']);
		}
	}
	
	/*
	//THESE TWO ARE NOT BEING USED!!!
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
	*/
}

/*
 *insertTestRoommate: inserts one roommate tuple
 */
function insertTestRoommate($user_id, $roommate_id)
{
	global $mysqli, $db_table_prefix;
	//insert apartment
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."roommates (
		user_id,
		roommate_id)
		VALUES (
		?,
		?)");		
	$stmt->bind_param("ii", $user_id, $roommate_id);
	$result = $stmt->execute();
	$stmt->close();	
}

/*
 * getTestRoommate: returns all roommates of a test user
 */
function getTestRoommates($user_id)
{
	global $mysqli, $db_table_prefix;
	
	//return apartment tuple
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."roommates
		WHERE user_id = ?
		");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($id, $roommate_id);
	
	$row = NULL;
	
	while ($stmt->fetch())
	{
		$row[] = array('user_id' => $user_id, 'roommate_id' => $roommate_id);
	}
	$stmt->close();
	return $row;
}

/*
 *deleteTestRoommate: deletes a roommate tuple
 */
function deleteTestRoommate($user_id, $roommate_id)
{
	global $mysqli, $db_table_prefix;
	
	//delete apartment
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."roommates
		WHERE user_id = ? AND roommate_id = ?");		
	$stmt->bind_param("ii", $user_id, $roommate_id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

/*
 *getAllCosts: returns all of the cost tuples
 */
function getAllCosts()
{
	global $mysqli, $db_table_prefix;
	
	//save all apartments in the db to global variable
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."costs");
	$stmt->execute();
	$stmt->bind_result($cost_id, $cost_description, $cost_payer_id, $cost_payee_id, $cost_due_date, $cost_delivered, $cost_recieved, $cost_amount);
	
	while ($stmt->fetch())
	{
		$row[] = array('cost_id' => $cost_id, 'cost_description' => $cost_description, 'cost_payer_id' => $cost_payer_id, 'cost_payee_id' => $cost_payee_id, 'cost_due_date' => $cost_due_date, 'cost_delivered' => $cost_delivered, 'cost_recieved' => $cost_recieved, 'cost_amount' => $cost_amount);
	}
	$stmt->close();
	return $row;
}

/*
 *deleteTestCost: deletes a cost tuple
 */
function deleteTestCost($cost_id)
{
	global $mysqli, $db_table_prefix;
	
	//delete apartment
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."costs
		WHERE cost_id = ?");		
	$stmt->bind_param("i", $cost_id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

/*
 *getCostByDescription: returns one cost tuple
 */
function getCostByDescription($cost_description)
{
	global $mysqli, $db_table_prefix;
	
	//save all apartments in the db to global variable
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."costs 
		WHERE cost_description = ? LIMIT 1");
	$stmt->bind_param("s", $cost_description);
	$stmt->execute();
	$stmt->bind_result($cost_id, $cost_desc, $cost_payer_id, $cost_payee_id, $cost_due_date, $cost_delivered, $cost_recieved, $cost_amount);
	
	$row;
	
	while ($stmt->fetch())
	{
		$row = array('cost_id' => $cost_id, 'cost_description' => $cost_desc, 'cost_payer_id' => $cost_payer_id, 'cost_payee_id' => $cost_payee_id, 'cost_due_date' => $cost_due_date, 'cost_delivered' => $cost_delivered, 'cost_recieved' => $cost_recieved, 'cost_amount' => $cost_amount);
	}
	
	$stmt->close();
	return $row;
}

/*
 *getCostById: get cost tuple by its id
 */
function getCostById($cost_id)
{
	global $mysqli, $db_table_prefix;
	
	//save all apartments in the db to global variable
	$stmt = $mysqli->prepare("SELECT *
		FROM ".$db_table_prefix."costs 
		WHERE cost_id = ?");
	$stmt->bind_param("i", $cost_id);
	$stmt->execute();
	$stmt->bind_result($id, $cost_desc, $cost_payer_id, $cost_payee_id, $cost_due_date, $cost_delivered, $cost_recieved, $cost_amount);
	
	$row;
	
	while ($stmt->fetch())
	{
		$row = array('cost_id' => $id, 'cost_description' => $cost_desc, 'cost_payer_id' => $cost_payer_id, 'cost_payee_id' => $cost_payee_id, 'cost_due_date' => $cost_due_date, 'cost_delivered' => $cost_delivered, 'cost_recieved' => $cost_recieved, 'cost_amount' => $cost_amount);
	}
	
	$stmt->close();
	return $row;
}

/*
 *addTestCost: adds a cost to the db for testing
 */
function addTestCost($description, $payer_id, $payee_id, $due_date, $delivered, $recieved, $amount)
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

?>