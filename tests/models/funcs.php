<?php

	//Functions that do not interact with DB
	//------------------------------------------------------------------------------

	//Retrieve a list of all .php files in models/languages
	function getLanguageFiles()
	{
		$directory = "models/languages/";
		$languages = glob($directory . "*.php");
		//print each file name
		return $languages;
	}

	//Retrieve a list of all .css files in models/site-templates 
	function getTemplateFiles()
	{
		$directory = "models/site-templates/";
		$languages = glob($directory . "*.css");
		//print each file name
		return $languages;
	}

	//Retrieve a list of all .php files in root files folder
	function getPageFiles()
	{
		$directory = "";
		$pages = glob($directory . "*.php");
		//print each file name
		foreach ($pages as $page){
			$row[$page] = $page;
		}
		return $row;
	}

	//Destroys a session as part of logout
	function destroySession($name)
	{
		if(isset($_SESSION[$name]))
		{
			$_SESSION[$name] = NULL;
			unset($_SESSION[$name]);
		}
	}

	//Generate a unique code
	function getUniqueCode($length = "")
	{	
		$code = md5(uniqid(rand(), true));
		if ($length != "") return substr($code, 0, $length);
		else return $code;
	}

	//Generate an activation key
	function generateActivationToken($gen = null)
	{
		do
		{
			$gen = md5(uniqid(mt_rand(), false));
		}
		while(validateActivationToken($gen));
		return $gen;
	}

	//@ Thanks to - http://phpsec.org
	function generateHash($plainText, $salt = null)
	{
		if ($salt === null)
		{
			$salt = substr(md5(uniqid(rand(), true)), 0, 25);
		}
		else
		{
			$salt = substr($salt, 0, 25);
		}
		
		return $salt . sha1($salt . $plainText);
	}

	//Checks if an email is valid
	function isValidEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		else {
			return false;
		}
	}

	//Inputs language strings from selected language.
	function lang($key,$markers = NULL)
	{
		global $lang;
		if($markers == NULL)
		{
			$str = $lang[$key];
		}
		else
		{
			//Replace any dyamic markers
			$str = $lang[$key];
			$iteration = 1;
			foreach($markers as $marker)
			{
				$str = str_replace("%m".$iteration."%",$marker,$str);
				$iteration++;
			}
		}
		//Ensure we have something to return
		if($str == "")
		{
			return ("No language key found");
		}
		else
		{
			return $str;
		}
	}

	//Checks if a string is within a min and max length
	function minMaxRange($min, $max, $what)
	{
		if(strlen(trim($what)) < $min)
			return true;
		else if(strlen(trim($what)) > $max)
			return true;
		else
		return false;
	}

	//Replaces hooks with specified text
	function replaceDefaultHook($str)
	{
		global $default_hooks,$default_replace;	
		return (str_replace($default_hooks,$default_replace,$str));
	}

	//Displays error and success messages
	function resultBlock($errors, $successes)
	{
		//Error block
		if(count($errors) > 0)
		{
			echo "
			<div class='alert alert-danger' id='error' style='width:50%;'>
				
				<button style='float:left;' type='button' class='btn btn-danger' onclick=\"showHide('error');\">Dismiss</button>
				<ul>";
				foreach($errors as $error)
				{
					echo "<li>".$error."</li>";
				}
			echo "</ul>
			</div>";
		}
		//Success block
		if(count($successes) > 0)
		{
			echo "
			<div class='alert alert-success' id='success' style='width:50%;'>
				
				<button style='float:left;' type='button' class='btn btn-success' onclick=\"showHide('success');\">Dismiss</button>
				<ul>";
				foreach($successes as $success)
				{
					echo "<li>".$success."</li>";
				}
			echo "</ul>
			</div>";
		}
	}

	//Completely sanitizes text
	function sanitize($str)
	{
		return strtolower(strip_tags(trim(($str))));
	}

	//Functions that interact mainly with .users table
	//------------------------------------------------------------------------------

	//Delete a defined array of users
	function deleteUsers($users) {
		global $mysqli,$db_table_prefix; 
		$i = 0;
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."users 
			WHERE id = ?");
		$stmt2 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_permission_matches 
			WHERE user_id = ?");
		foreach($users as $id){
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt2->bind_param("i", $id);
			$stmt2->execute();
			$i++;
		}
		$stmt->close();
		$stmt2->close();
		return $i;
	}

	//Check if a display name exists in the DB
	function displayNameExists($displayname)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE
			display_name = ?
			LIMIT 1");
		$stmt->bind_param("s", $displayname);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Check if an email exists in the DB
	function emailExists($email)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE
			email = ?
			LIMIT 1");
		$stmt->bind_param("s", $email);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Check if a user name and email belong to the same user
	function emailUsernameLinked($email,$username)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE user_name = ?
			AND
			email = ?
			LIMIT 1
			");
		$stmt->bind_param("ss", $username, $email);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Retrieve information for all users
	function fetchAllUsers()
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			user_name,
			display_name,
			password,
			email,
			activation_token,
			last_activation_request,
			lost_password_request,
			active,
			title,
			sign_up_stamp,
			last_sign_in_stamp
			FROM ".$db_table_prefix."users");
		$stmt->execute();
		$stmt->bind_result($id, $user, $display, $password, $email, $token, $activationRequest, $passwordRequest, $active, $title, $signUp, $signIn);
		
		while ($stmt->fetch()){
			$row[] = array('id' => $id, 'user_name' => $user, 'display_name' => $display, 'password' => $password, 'email' => $email, 'activation_token' => $token, 'last_activation_request' => $activationRequest, 'lost_password_request' => $passwordRequest, 'active' => $active, 'title' => $title, 'sign_up_stamp' => $signUp, 'last_sign_in_stamp' => $signIn);
		}
		$stmt->close();
		return ($row);
	}

	//Retrieve complete user information by username, token or ID
	function fetchUserDetails($username=NULL, $token=NULL, $id=NULL)
	{
		if($username!=NULL) {
			$column = "user_name";
			$data = $username;
		}
		elseif($token!=NULL) {
			$column = "activation_token";
			$data = $token;
		}
		elseif($id!=NULL) {
			$column = "id";
			$data = $id;
		}
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			user_name,
			display_name,
			password,
			email,
			activation_token,
			last_activation_request,
			lost_password_request,
			active,
			title,
			sign_up_stamp,
			last_sign_in_stamp
			FROM ".$db_table_prefix."users
			WHERE
			$column = ?
			LIMIT 1");
			$stmt->bind_param("s", $data);
		
		$stmt->execute();
		$stmt->bind_result($id, $user, $display, $password, $email, $token, $activationRequest, $passwordRequest, $active, $title, $signUp, $signIn);
		while ($stmt->fetch()){
			$row = array('id' => $id, 'user_name' => $user, 'display_name' => $display, 'password' => $password, 'email' => $email, 'activation_token' => $token, 'last_activation_request' => $activationRequest, 'lost_password_request' => $passwordRequest, 'active' => $active, 'title' => $title, 'sign_up_stamp' => $signUp, 'last_sign_in_stamp' => $signIn);
		}
		$stmt->close();
		return ($row);
	}

	//Toggle if lost password request flag on or off
	function flagLostPasswordRequest($username,$value)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
			SET lost_password_request = ?
			WHERE
			user_name = ?
			LIMIT 1
			");
		$stmt->bind_param("ss", $value, $username);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}

	//Check if a user is logged in
	function isUserLoggedIn()
	{
		global $loggedInUser,$mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT 
			id,
			password
			FROM ".$db_table_prefix."users
			WHERE
			id = ?
			AND 
			password = ? 
			AND
			active = 1
			LIMIT 1");
		$stmt->bind_param("is", $loggedInUser->user_id, $loggedInUser->hash_pw);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if($loggedInUser == NULL)
		{
			return false;
		}
		else
		{
			if ($num_returns > 0)
			{
				return true;
			}
			else
			{
				destroySession("userCakeUser");
				return false;	
			}
		}
	}

	//Change a user from inactive to active
	function setUserActive($token)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
			SET active = 1
			WHERE
			activation_token = ?
			LIMIT 1");
		$stmt->bind_param("s", $token);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}

	//Change a user's display name
	function updateDisplayName($id, $display)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
			SET display_name = ?
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("si", $display, $id);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}

	//Update a user's email
	function updateEmail($id, $email)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
			SET 
			email = ?
			WHERE
			id = ?");
		$stmt->bind_param("si", $email, $id);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}

	//Input new activation token, and update the time of the most recent activation request
	function updateLastActivationRequest($new_activation_token,$username,$email)
	{
		global $mysqli,$db_table_prefix; 	
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
			SET activation_token = ?,
			last_activation_request = ?
			WHERE email = ?
			AND
			user_name = ?");
		$stmt->bind_param("ssss", $new_activation_token, time(), $email, $username);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}

	//Generate a random password, and new token
	function updatePasswordFromToken($pass,$token)
	{
		global $mysqli,$db_table_prefix;
		$new_activation_token = generateActivationToken();
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
			SET password = ?,
			activation_token = ?
			WHERE
			activation_token = ?");
		$stmt->bind_param("sss", $pass, $new_activation_token, $token);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}

	//Update a user's title
	function updateTitle($id, $title)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
			SET 
			title = ?
			WHERE
			id = ?");
		$stmt->bind_param("si", $title, $id);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;	
	}

	//Check if a user ID exists in the DB
	function userIdExists($id)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("i", $id);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Checks if a username exists in the DB
	function usernameExists($username)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE
			user_name = ?
			LIMIT 1");
		$stmt->bind_param("s", $username);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Check if activation token exists in DB
	function validateActivationToken($token,$lostpass=NULL)
	{
		global $mysqli,$db_table_prefix;
		if($lostpass == NULL) 
		{	
			$stmt = $mysqli->prepare("SELECT active
				FROM ".$db_table_prefix."users
				WHERE active = 0
				AND
				activation_token = ?
				LIMIT 1");
		}
		else 
		{
			$stmt = $mysqli->prepare("SELECT active
				FROM ".$db_table_prefix."users
				WHERE active = 1
				AND
				activation_token = ?
				AND
				lost_password_request = 1 
				LIMIT 1");
		}
		$stmt->bind_param("s", $token);
		$stmt->execute();
		$stmt->store_result();
			$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Functions that interact mainly with .permissions table
	//------------------------------------------------------------------------------

	//Create a permission level in DB
	function createPermission($permission) {
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."permissions (
			name
			)
			VALUES (
			?
			)");
		$stmt->bind_param("s", $permission);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}

	//Delete a permission level from the DB
	function deletePermission($permission) {
		global $mysqli,$db_table_prefix,$errors; 
		$i = 0;
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."permissions 
			WHERE id = ?");
		$stmt2 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_permission_matches 
			WHERE permission_id = ?");
		$stmt3 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."permission_page_matches 
			WHERE permission_id = ?");
		foreach($permission as $id){
			if ($id == 1){
				$errors[] = lang("CANNOT_DELETE_NEWUSERS");
			}
			elseif ($id == 2){
				$errors[] = lang("CANNOT_DELETE_ADMIN");
			}
			else{
				$stmt->bind_param("i", $id);
				$stmt->execute();
				$stmt2->bind_param("i", $id);
				$stmt2->execute();
				$stmt3->bind_param("i", $id);
				$stmt3->execute();
				$i++;
			}
		}
		$stmt->close();
		$stmt2->close();
		$stmt3->close();
		return $i;
	}

	//Retrieve information for all permission levels
	function fetchAllPermissions()
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			name
			FROM ".$db_table_prefix."permissions");
		$stmt->execute();
		$stmt->bind_result($id, $name);
		while ($stmt->fetch()){
			$row[] = array('id' => $id, 'name' => $name);
		}
		$stmt->close();
		return ($row);
	}

	//Retrieve information for a single permission level
	function fetchPermissionDetails($id)
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			name
			FROM ".$db_table_prefix."permissions
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($id, $name);
		while ($stmt->fetch()){
			$row = array('id' => $id, 'name' => $name);
		}
		$stmt->close();
		return ($row);
	}

	//Check if a permission level ID exists in the DB
	function permissionIdExists($id)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT id
			FROM ".$db_table_prefix."permissions
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("i", $id);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Check if a permission level name exists in the DB
	function permissionNameExists($permission)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT id
			FROM ".$db_table_prefix."permissions
			WHERE
			name = ?
			LIMIT 1");
		$stmt->bind_param("s", $permission);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Change a permission level's name
	function updatePermissionName($id, $name)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."permissions
			SET name = ?
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("si", $name, $id);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;	
	}

	//Functions that interact mainly with .user_permission_matches table
	//------------------------------------------------------------------------------

	//Match permission level(s) with user(s)
	function addPermission($permission, $user) {
		global $mysqli,$db_table_prefix; 
		$i = 0;
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."user_permission_matches (
			permission_id,
			user_id
			)
			VALUES (
			?,
			?
			)");
		if (is_array($permission)){
			foreach($permission as $id){
				$stmt->bind_param("ii", $id, $user);
				$stmt->execute();
				$i++;
			}
		}
		elseif (is_array($user)){
			foreach($user as $id){
				$stmt->bind_param("ii", $permission, $id);
				$stmt->execute();
				$i++;
			}
		}
		else {
			$stmt->bind_param("ii", $permission, $user);
			$stmt->execute();
			$i++;
		}
		$stmt->close();
		return $i;
	}

	//Retrieve information for all user/permission level matches
	function fetchAllMatches()
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			user_id,
			permission_id
			FROM ".$db_table_prefix."user_permission_matches");
		$stmt->execute();
		$stmt->bind_result($id, $user, $permission);
		while ($stmt->fetch()){
			$row[] = array('id' => $id, 'user_id' => $user, 'permission_id' => $permission);
		}
		$stmt->close();
		return ($row);	
	}

	//Retrieve list of permission levels a user has
	function fetchUserPermissions($user_id)
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT
			id,
			permission_id
			FROM ".$db_table_prefix."user_permission_matches
			WHERE user_id = ?
			");
		$stmt->bind_param("i", $user_id);	
		$stmt->execute();
		$stmt->bind_result($id, $permission);
		while ($stmt->fetch()){
			$row[$permission] = array('id' => $id, 'permission_id' => $permission);
		}
		$stmt->close();
		if (isset($row)){
			return ($row);
		}
	}

	//Retrieve list of users who have a permission level
	function fetchPermissionUsers($permission_id)
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT id, user_id
			FROM ".$db_table_prefix."user_permission_matches
			WHERE permission_id = ?
			");
		$stmt->bind_param("i", $permission_id);	
		$stmt->execute();
		$stmt->bind_result($id, $user);
		while ($stmt->fetch()){
			$row[$user] = array('id' => $id, 'user_id' => $user);
		}
		$stmt->close();
		if (isset($row)){
			return ($row);
		}
	}

	//Unmatch permission level(s) from user(s)
	function removePermission($permission, $user) {
		global $mysqli,$db_table_prefix; 
		$i = 0;
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_permission_matches 
			WHERE permission_id = ?
			AND user_id =?");
		if (is_array($permission)){
			foreach($permission as $id){
				$stmt->bind_param("ii", $id, $user);
				$stmt->execute();
				$i++;
			}
		}
		elseif (is_array($user)){
			foreach($user as $id){
				$stmt->bind_param("ii", $permission, $id);
				$stmt->execute();
				$i++;
			}
		}
		else {
			$stmt->bind_param("ii", $permission, $user);
			$stmt->execute();
			$i++;
		}
		$stmt->close();
		return $i;
	}

	//Functions that interact mainly with .configuration table
	//------------------------------------------------------------------------------

	//Update configuration table
	function updateConfig($id, $value)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."configuration
			SET 
			value = ?
			WHERE
			id = ?");
		foreach ($id as $cfg){
			$stmt->bind_param("si", $value[$cfg], $cfg);
			$stmt->execute();
		}
		$stmt->close();	
	}

	//Functions that interact mainly with .pages table
	//------------------------------------------------------------------------------

	//Add a page to the DB
	function createPages($pages) {
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."pages (
			page
			)
			VALUES (
			?
			)");
		foreach($pages as $page){
			$stmt->bind_param("s", $page);
			$stmt->execute();
		}
		$stmt->close();
	}

	//Delete a page from the DB
	function deletePages($pages) {
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."pages 
			WHERE id = ?");
		$stmt2 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."permission_page_matches 
			WHERE page_id = ?");
		foreach($pages as $id){
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt2->bind_param("i", $id);
			$stmt2->execute();
		}
		$stmt->close();
		$stmt2->close();
	}

	//Fetch information on all pages
	function fetchAllPages()
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			page,
			private
			FROM ".$db_table_prefix."pages");
		$stmt->execute();
		$stmt->bind_result($id, $page, $private);
		while ($stmt->fetch()){
			$row[$page] = array('id' => $id, 'page' => $page, 'private' => $private);
		}
		$stmt->close();
		if (isset($row)){
			return ($row);
		}
	}

	//Fetch information for a specific page
	function fetchPageDetails($id)
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			page,
			private
			FROM ".$db_table_prefix."pages
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($id, $page, $private);
		while ($stmt->fetch()){
			$row = array('id' => $id, 'page' => $page, 'private' => $private);
		}
		$stmt->close();
		return ($row);
	}

	//Check if a page ID exists
	function pageIdExists($id)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("SELECT private
			FROM ".$db_table_prefix."pages
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("i", $id);	
		$stmt->execute();
		$stmt->store_result();	
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	//Toggle private/public setting of a page
	function updatePrivate($id, $private)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."pages
			SET 
			private = ?
			WHERE
			id = ?");
		$stmt->bind_param("ii", $private, $id);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;	
	}

	//Functions that interact mainly with .permission_page_matches table
	//------------------------------------------------------------------------------

	//Match permission level(s) with page(s)
	function addPage($page, $permission) {
		global $mysqli,$db_table_prefix; 
		$i = 0;
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."permission_page_matches (
			permission_id,
			page_id
			)
			VALUES (
			?,
			?
			)");
		if (is_array($permission)){
			foreach($permission as $id){
				$stmt->bind_param("ii", $id, $page);
				$stmt->execute();
				$i++;
			}
		}
		elseif (is_array($page)){
			foreach($page as $id){
				$stmt->bind_param("ii", $permission, $id);
				$stmt->execute();
				$i++;
			}
		}
		else {
			$stmt->bind_param("ii", $permission, $page);
			$stmt->execute();
			$i++;
		}
		$stmt->close();
		return $i;
	}

	//Retrieve list of permission levels that can access a page
	function fetchPagePermissions($page_id)
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT
			id,
			permission_id
			FROM ".$db_table_prefix."permission_page_matches
			WHERE page_id = ?
			");
		$stmt->bind_param("i", $page_id);	
		$stmt->execute();
		$stmt->bind_result($id, $permission);
		while ($stmt->fetch()){
			$row[$permission] = array('id' => $id, 'permission_id' => $permission);
		}
		$stmt->close();
		if (isset($row)){
			return ($row);
		}
	}

	//Retrieve list of pages that a permission level can access
	function fetchPermissionPages($permission_id)
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT
			id,
			page_id
			FROM ".$db_table_prefix."permission_page_matches
			WHERE permission_id = ?
			");
		$stmt->bind_param("i", $permission_id);	
		$stmt->execute();
		$stmt->bind_result($id, $page);
		while ($stmt->fetch()){
			$row[$page] = array('id' => $id, 'permission_id' => $page);
		}
		$stmt->close();
		if (isset($row)){
			return ($row);
		}
	}

	//Unmatched permission and page
	function removePage($page, $permission) {
		global $mysqli,$db_table_prefix; 
		$i = 0;
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."permission_page_matches 
			WHERE page_id = ?
			AND permission_id =?");
		if (is_array($page)){
			foreach($page as $id){
				$stmt->bind_param("ii", $id, $permission);
				$stmt->execute();
				$i++;
			}
		}
		elseif (is_array($permission)){
			foreach($permission as $id){
				$stmt->bind_param("ii", $page, $id);
				$stmt->execute();
				$i++;
			}
		}
		else {
			$stmt->bind_param("ii", $permission, $user);
			$stmt->execute();
			$i++;
		}
		$stmt->close();
		return $i;
	}

	//Check if a user has access to a page
	function securePage($uri){
		
		//Separate document name from uri
		$tokens = explode('/', $uri);
		$page = $tokens[sizeof($tokens)-1];
		global $mysqli,$db_table_prefix,$loggedInUser;
		//retrieve page details
		$stmt = $mysqli->prepare("SELECT 
			id,
			page,
			private
			FROM ".$db_table_prefix."pages
			WHERE
			page = ?
			LIMIT 1");
		$stmt->bind_param("s", $page);
		$stmt->execute();
		$stmt->bind_result($id, $page, $private);
		while ($stmt->fetch()){
			$pageDetails = array('id' => $id, 'page' => $page, 'private' => $private);
		}
		$stmt->close();
		//If page does not exist in DB, allow access
		if (empty($pageDetails)){
			return true;
		}
		//If page is public, allow access
		elseif ($pageDetails['private'] == 0) {
			return true;	
		}
		//If user is not logged in, deny access
		elseif(!isUserLoggedIn()) 
		{
			header("Location: login.php");
			return false;
		}
		else {
			//Retrieve list of permission levels with access to page
			$stmt = $mysqli->prepare("SELECT
				permission_id
				FROM ".$db_table_prefix."permission_page_matches
				WHERE page_id = ?
				");
			$stmt->bind_param("i", $pageDetails['id']);	
			$stmt->execute();
			$stmt->bind_result($permission);
			while ($stmt->fetch()){
				$pagePermissions[] = $permission;
			}
			$stmt->close();
			//Check if user's permission levels allow access to page
			if ($loggedInUser->checkPermission($pagePermissions)){ 
				return true;
			}
			//Grant access if master user
			elseif ($loggedInUser->user_id == $master_account){
				return true;
			}
			else {
				header("Location: account.php");
				return false;	
			}
		}
	}
	
	//Retrieve the apartment listings for the given search term
	//*************NEEDED FOR MAP.PHP!!!****************
	function fetchListingsWithTerms($terms = NULL)
	{
		if($terms != NULL)
		{
			// TODO custom search results
			
			// Return all listings with limit
			if(isset($terms['limit']))
			{
				global $mysqli, $db_table_prefix; 
				$stmt = $mysqli->prepare("SELECT 
					apartment_id,
					name,
					address,
					latitude,
					longitude,
					num_bedrooms,
					num_bathrooms,
					landlord_id,
					price,
					deposit,
					description,
					status,
					last_updated
					FROM ".$db_table_prefix."apartments 
					LIMIT ".$terms['limit']);
				$stmt->execute();
				$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
				
				while ($stmt->fetch())
				{
					$row[] = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
				}
				$stmt->close();
				return ($row);
			}
		}
		else
		{
			// Return all listings
			global $mysqli, $db_table_prefix; 
			$stmt = $mysqli->prepare("SELECT 
				apartment_id,
				name,
				address,
				latitude,
				longitude,
				num_bedrooms,
				num_bathrooms,
				landlord_id,
				price,
				deposit,
				description,
				status,
				last_updated
				FROM ".$db_table_prefix."apartments");
			$stmt->execute();
			$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
			
			while ($stmt->fetch())
			{
				$row[] = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
			}
			$stmt->close();
			return ($row);
		}
	}

	//Retrieve the apartment listings for the given search term
	function fetchListings($search = null)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			apartment_id,
			name,
			address,
			latitude,
			longitude,
			num_bedrooms,
			num_bathrooms,
			landlord_id,
			price,
			deposit,
			description,
			status,
			last_updated
			FROM ".$db_table_prefix."apartments");
		$stmt->execute();
		$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
		
		while ($stmt->fetch())
		{
			$row[] = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
		}
		$stmt->close();
			
		if($search != null)
		{
			if(isset($row))
			{
				$terms = explode(" ", $search);
				$rowLength = count($row);
				
				for($i = 0; $i < $rowLength; $i++)
				{
					$matchFound = false;
					foreach($terms as $t)
					{
						if(contains($row[$i]['name'], $t) || contains($row[$i]['address'], $t) || contains($row[$i]['description'], $t))
						{
							$matchFound = true;
						}
					}
					
					if($matchFound == false)
					{
						unset($row[$i]);
					}
				}
			}
		}
		
		return ($row);
	}
	
	function contains($statement, $term)
	{
		if(strpos(strtolower($statement), strtolower($term)) != false)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//Retrieve the details for the apartment listing
	function fetchListingDetails($id)
	{
		// Return specified listing
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			apartment_id,
			name,
			address,
			latitude,
			longitude,
			num_bedrooms,
			num_bathrooms,
			landlord_id,
			price,
			deposit,
			description,
			status,
			last_updated
			FROM ".$db_table_prefix."apartments
			WHERE apartment_id = ?
			");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
		
		while ($stmt->fetch())
		{
			$row = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
		}
		$stmt->close();
		return ($row);
	}
	
    //Fetches apartments in a certain radius of Iowa City for the map.php page
	function fetchIowaCityApartments($radius = NULL, $limit = 20)
	{
		$terms = array("limit"=>$limit);
		$result = fetchListingsWithTerms($terms);
		
		//check if apartments have latitude and longitude values yet
		foreach($result as $apartment)
		{	
			if($apartment['latitude'] == NULL || $apartment['longitude'] == NULL)
			{
				$results = geocode($apartment['address']);
				$apartment['latitude'] = $results[0];
				$apartment['longitude'] = $results[1];
				
				//if a result was found
				if(($apartment['latitude'] != NULL) && ($apartment['longitude'] != NULL))
				{
					//update each coordinates
					updateLatLng($apartment['apartment_id'], $apartment['latitude'], $apartment['longitude']);
				}
			}
		}
		
		//fetch all listings
		if($radius == NULL)
		{
			return $result;
		}
		//return only the locations within the given radius of Iowa City
		else
		{	
			global $mysqli,$db_table_prefix;
			
			//get proximity variable in miles for IowaCity coordinates
			$proximity = mathGeoProximity(41.6660136,-91.544685, $radius, true);
			
			//Below code not working; always returns true for some reason...
			/*
			//find matches in db
			$stmt = $mysqli->prepare("SELECT * 
				FROM   ".$db_table_prefix."apartments
				WHERE  (latitude BETWEEN ?
						AND ?)
				  AND (longitude BETWEEN ?
						AND ?)
			");
			$stmt->bind_param('dddd', number_format($proximity['latitudeMin'], 6), number_format($proximity['latitudeMax'], 6),
								 number_format($proximity['longitudeMin'], 6), number_format($proximity['longitudeMax'], 6));
			$result = $stmt->execute();
			$stmt->close();
			*/

			// fetch all record and check whether they are really within the radius
			$recordsWithinRadius = array();
			
			//check each result
			foreach($result as $apartment)
			{
				if($apartment['latitude'] != NULL && $apartment['longitude'] != NULL)
				{
					$distance = mathGeoDistance(41.6660136,-91.544685, $apartment['latitude'], $apartment['longitude'], true);
					
					if ($distance <= $radius) 
					{
						array_push($recordsWithinRadius, $apartment);
					}
				}
			}
			
			return $recordsWithinRadius;
		}
	}
	
	// calculate geographical proximity
	function mathGeoProximity( $latitude, $longitude, $radius, $miles = false )
	{
		$radius = $miles ? $radius : ($radius * 0.621371192);

		$lng_min = $longitude - $radius / abs(cos(deg2rad($latitude)) * 69);
		$lng_max = $longitude + $radius / abs(cos(deg2rad($latitude)) * 69);
		$lat_min = $latitude - ($radius / 69);
		$lat_max = $latitude + ($radius / 69);

		return array(
			'latitudeMin'  => $lat_min,
			'latitudeMax'  => $lat_max,
			'longitudeMin' => $lng_min,
			'longitudeMax' => $lng_max
		);
	}

	// calculate geographical distance between 2 points
	function mathGeoDistance( $lat1, $lng1, $lat2, $lng2, $miles = false )
	{
		$pi80 = M_PI / 180;
		$lat1 *= $pi80;
		$lng1 *= $pi80;
		$lat2 *= $pi80;
		$lng2 *= $pi80;

		$r = 6372.797; // mean radius of Earth in km
		$dlat = $lat2 - $lat1;
		$dlng = $lng2 - $lng1;
		$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$km = $r * $c;

		return ($miles ? ($km * 0.621371192) : $km);
	}
	
	//Update the coordinates of an apartment's location
	function updateLatLng($id, $lat, $lng)
	{
		global $mysqli,$db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."apartments
			SET latitude = ?, longitude = ?
			WHERE
			apartment_id = ?
			LIMIT 1");
		$stmt->bind_param('ddi', $lat, $lng, $id);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}
	
	// function to geocode address, it will return false if unable to geocode address
	function geocode($address)
	{
		// url encode the address
		$address = urlencode($address);
		 
		// google map geocode api url
		$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}";
	 
		// get the json response
		$resp_json = file_get_contents($url);
		 
		// decode the json
		$resp = json_decode($resp_json, true);
		// response status will be 'OK', if able to geocode given address
		if($resp['status']=='OK'){
	 
			// get the important data
			$lat = $resp['results'][0]['geometry']['location']['lat'];
			$lng = $resp['results'][0]['geometry']['location']['lng'];
			
			return array($lat, $lng);
		}
		else
		{
			return NULL;
		}
	}
	
	//Fetches a user's favorite listings using their user id
	function fetchFavorites($user_id)
	{
		// Return all favorites for this user
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
            apartment_id,
			name,
			address,
			latitude,
			longitude,
			num_bedrooms,
			num_bathrooms,
			landlord_id,
			price,
			deposit,
			description,
			status,
			last_updated
			FROM (SELECT 
			apartment_id
			FROM ".$db_table_prefix."favorites
			WHERE user_id = ?) as table1 NATURAL JOIN ".$db_table_prefix."apartments");
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
		
		while ($stmt->fetch())
		{
			$row[] = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
		}
		$stmt->close();
		return ($row);
	}
	
	//Add a new favorite for the user and apartment specified
	function addFavorite($user_id, $apartment_id)
	{
		global $mysqli, $db_table_prefix;
		//Insert the message into the database
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."favorites (
			user_id,
			apartment_id
			)
			VALUES (
			?,
			?
			)");		
		$stmt->bind_param("ii", $user_id, $apartment_id);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}
	
	//Delete the specified favorite
	function deleteFavorite($user_id, $apartment_id)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."favorites 
			WHERE user_id = ? AND apartment_id = ?");
		$stmt->bind_param("ii", $user_id, $apartment_id);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}
	
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
	
	function fetchCostById($cost_id)
	{
		// Return all debts for this user
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT *
			FROM ".$db_table_prefix."costs
			WHERE cost_id = ?");
		$stmt->bind_param("i", $cost_id);
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
	
	//Retrieve the landlords for the given search term
	function fetchLandlords($terms = NULL)
	{
		if($terms != NULL)
		{
			// TODO custom search results
		}
		else
		{
			// Return all listings
			global $mysqli, $db_table_prefix; 
			$stmt = $mysqli->prepare("SELECT 
				landlord_id,
				name,
				address,
				email
				FROM ".$db_table_prefix."landlords");
			$stmt->execute();
			$stmt->bind_result($landlord_id, $name, $address, $email);
			
			while ($stmt->fetch())
			{
				$row[] = array('landlord_id' => $landlord_id, 'name' => $name, 'address' => $address, 'email' => $email);
			}
			$stmt->close();
			return ($row);
		}
	}
	
	//Retrieve the details for the apartment listing
	function fetchLandlordDetails($id)
	{
		// Return specified listing
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			landlord_id,
			name,
			address,
			email
			FROM ".$db_table_prefix."landlords
			WHERE landlord_id = ?
			");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($landlord_id, $name, $address, $email);
		
		while ($stmt->fetch())
		{
			$row = array('landlord_id' => $landlord_id, 'name' => $name, 'address' => $address, 'email' => $email);
		}
		$stmt->close();
		return ($row);
	}
	
	//Return the username given the specified user ID
	function fetchUsername($id = NULL)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			user_name
			FROM ".$db_table_prefix."users
			WHERE id = ?
			LIMIT 1");
			$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($user);
		$row = null;
		while ($stmt->fetch())
		{
			$row = $user;
		}
		$stmt->close();
		return ($row);
	}
	
	//Return the user's id given the specified username
	function fetchUserID($username = NULL)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id
			FROM ".$db_table_prefix."users
			WHERE user_name = ?
			LIMIT 1");
			$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($user);
		$row = null;
		while ($stmt->fetch())
		{
			$row = $user;
		}
		$stmt->close();
		return ($row);
	}
	
	//Retrieve all the user's sent messages
	function fetchMessages($user_id, $mailbox)
	{
		switch ($mailbox)
		{
			case "inbox":
				$column = "recipient_id";
				break;
			case "sent":
				$column = "sender_id";
				break;
			case "drafts":
				$column = "sender_id";
				break;
			default:
				return null;
		}
		
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			*
			FROM ".$db_table_prefix."messages
			WHERE
			$column = ?
			");
			$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->bind_result($id, $sender_id, $recipient_id, $subject, $message, $timestamp, $wasRead, $draft);
		while ($stmt->fetch())
		{
			if($mailbox == "drafts")
			{
				if($draft == 1)
				{
					$row[] = array('id' => $id, 'sender_id' => $sender_id, 'recipient_id' => $recipient_id, 'subject' => $subject, 'message' => $message, 'timestamp' => $timestamp, 'wasRead' => $wasRead, 'draft' => $draft);
				}
			}
			else if($mailbox == "sent")
			{
				if($draft != 1)
				{
					$row[] = array('id' => $id, 'sender_id' => $sender_id, 'recipient_id' => $recipient_id, 'subject' => $subject, 'message' => $message, 'timestamp' => $timestamp, 'wasRead' => $wasRead, 'draft' => $draft);
				}
			}
			else
			{
				$row[] = array('id' => $id, 'sender_id' => $sender_id, 'recipient_id' => $recipient_id, 'subject' => $subject, 'message' => $message, 'timestamp' => $timestamp, 'wasRead' => $wasRead, 'draft' => $draft);
			}
		}
		$stmt->close();
		return ($row);
	}
	
	//Retrieve the message details
	function fetchMessageDetails($id)
	{
		global $mysqli, $db_table_prefix; 
		// Select statement acting weird
		$stmt = $mysqli->prepare("SELECT 
			*
			FROM ".$db_table_prefix."messages
			WHERE
			id = ?
			LIMIT 1");
			$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($id, $sender_id, $recipient_id, $subject, $message, $timestamp, $wasRead, $draft);
		while ($stmt->fetch())
		{
			$row = array('id' => $id, 'sender_id' => $sender_id, 'recipient_id' => $recipient_id, 'subject' => $subject, 'message' => $message, 'timestamp' => $timestamp, 'wasRead' => $wasRead, 'draft' => $draft);
		}
		$stmt->close();
		return ($row);
	}
	
	//Retrieve the message details
	function newMessage($sender_id, $recipient_id, $subject, $message, $draft)
	{
		global $mysqli, $db_table_prefix;
		//Insert the message into the database
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."messages (
			sender_id,
			recipient_id,
			subject,
			message,
			timestamp,
			wasRead,
			draft
			)
			VALUES (
			?,
			?,
			?,
			?,
			'".time()."',
			'0',
			?
			)");		
		$stmt->bind_param("iissi", $sender_id, $recipient_id, $subject, $message, $draft);
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}
	
	function updateMessage($message_id, $recipient_id, $subject, $message, $draft)
	{
		global $mysqli, $db_table_prefix;
		//Update the message in the database
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."messages
			SET recipient_id = ?,
			subject = ?,
			message = ?,
			timestamp = ?,
			draft = ?
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("issiii", $recipient_id, $subject, $message, time(), $draft, $message_id);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}
	
	//Delete a message from the DB
	function deleteMessage($id)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."messages 
			WHERE id = ?");
		$stmt->bind_param("i", $id);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}
	
	//Read a message
	function toggleMessageRead($id, $toggle)
	{
		global $mysqli, $db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."messages
			SET wasRead = ?
			WHERE
			id = ?
			LIMIT 1
			");
		$stmt->bind_param("ii", $toggle, $id);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}
	
	//Read all message for a user
	function readAllMessages($userID)
	{
		global $mysqli, $db_table_prefix;
		$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."messages
			SET wasRead = 1
			WHERE
			recipient_id = ?
			");
		$stmt->bind_param("i", $userID);
		$result = $stmt->execute();
		$stmt->close();
		return $result;
	}
	
	//Check if a message ID exists in the DB
	function messageIdExists($id)
	{
		global $mysqli, $db_table_prefix;
		$stmt = $mysqli->prepare("SELECT id
			FROM ".$db_table_prefix."messages
			WHERE
			id = ?
			LIMIT 1");
		$stmt->bind_param("i", $id);	
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();
		
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			return false;	
		}
	}
	
	//Retrieve the unread count
	function unreadCount($user_id)
	{		
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			*
			FROM ".$db_table_prefix."messages
			WHERE recipient_id = ?
			");
			$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->bind_result($id, $sender_id, $recipient_id, $subject, $message, $timestamp, $wasRead, $draft);
		$total = 0;
		while ($stmt->fetch())
		{
			if($wasRead == 0)
			{
				$total++;
			}			
		}
		$stmt->close();
		return ($total);
	}
	
	//Fetch js limited information on all pages
	function jsFetchAllPages()
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			page
			FROM ".$db_table_prefix."pages");
		$stmt->execute();
		$stmt->bind_result($id, $page);
		$i = 0;
		while ($stmt->fetch())
		{
			$row[$i++] = array('id' => $id, 'page' => $page);
		}
		$stmt->close();
		if(isset($row))
		{
			return ($row);
		}
	}
	
	//Retrieve js limited information for all users
	function jsFetchAllUsers()
	{
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			user_name
			FROM ".$db_table_prefix."users");
		$stmt->execute();
		$stmt->bind_result($id, $user);
		$i = 0;
		while ($stmt->fetch())
		{
			$row[$i++] = array('id' => $id, 'user_name' => $user);
		}
		$stmt->close();
		return ($row);
	}
	
	//Retrieve the apartment listings for the given search term
	function jsFetchListings($search = null)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			apartment_id,
			name,
			address,
			latitude,
			longitude,
			num_bedrooms,
			num_bathrooms,
			landlord_id,
			price,
			deposit,
			description,
			status,
			last_updated
			FROM ".$db_table_prefix."apartments");
		$stmt->execute();
		$stmt->bind_result($apartment_id, $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status, $last_updated);
		
		while ($stmt->fetch())
		{
			$row[] = array('apartment_id' => $apartment_id, 'name' => $name, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'num_bedrooms' => $num_bedrooms, 'num_bathrooms' => $num_bathrooms, 'landlord_id' => $landlord_id, 'price' => $price, 'deposit' => $deposit, 'description' => $description, 'status' => $status, 'last_updated' => $last_updated);
		}
		$stmt->close();
			
		if($search != null)
		{
			if(isset($row))
			{
				$terms = explode(" ", $search);
				$rowLength = count($row);
				
				for($i = 0; $i < $rowLength; $i++)
				{
					$matchFound = false;
					foreach($terms as $t)
					{
						if(contains($row[$i]['name'], $t) || contains($row[$i]['address'], $t) || contains($row[$i]['description'], $t))
						{
							$matchFound = true;
						}
					}
					
					if($matchFound == false)
					{
						unset($row[$i]);
					}
				}
			}
		}
		
		// Strip off all fields but the apartment id field
		if(isset($row))
		{
			foreach ($row as $r)
			{
				$modifiedRow[] = array('apartment_id' => $r['apartment_id'], 'address' => $r['address']);
			}
		}
		else
		{
			$modifiedRow = null;
		}
		
		return ($modifiedRow);
	}
	
	//Retrieve all the user's sent messages with limited info
	function jsFetchMessages($user_id, $mailbox)
	{
		switch ($mailbox)
		{
			case "inbox":
				$column = "recipient_id";
				break;
			case "sent":
				$column = "sender_id";
				break;
			case "drafts":
				$column = "sender_id";
				break;
			default:
				return null;
		}
		
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id,
			draft
			FROM ".$db_table_prefix."messages
			WHERE
			$column = ?
			");
			$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->bind_result($id, $draft);
		while ($stmt->fetch())
		{
			if($mailbox == "drafts")
			{
				if($draft == 1)
				{
					$row[] = array('id' => $id);
				}
			}
			else if($mailbox == "sent")
			{
				if($draft != 1)
				{
					$row[] = array('id' => $id);
				}
			}
			else
			{
				$row[] = array('id' => $id);
			}
		}
		$stmt->close();
		return ($row);
	}
	
	// Functions for the Apartment table
	// ----------------------------------------------------------------
	
	// Add an apartment to the apartment table
	function createApartment($name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status)
	{		
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."apartments (
			name,
			address,
			latitude,
			longitude,
			num_bedrooms,
			num_bathrooms,
			landlord_id,
			price,
			deposit,
			description,
			status,
			last_updated
			)
			VALUES (
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			?,
			'".time()."'
			)");


		$stmt->bind_param("ssddiiiddss", $name, $address, $latitude, $longitude, $num_bedrooms, $num_bathrooms, $landlord_id, $price, $deposit, $description, $status); // s for string i for integer 
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}
	
	//Delete an apartment from the DB
	function deleteApartment($id){
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."apartments 
			WHERE apartment_id = ?");

		$stmt->bind_param("i", $id);
		$stmt->execute();

		$stmt->close();

	}
	
	//Return the apartment's id given the specified apartment name
	function fetchApartmentID($apartment_name = NULL)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			apartment_id
			FROM ".$db_table_prefix."apartments
			WHERE name = ?
			LIMIT 1");
			$stmt->bind_param("s", $apartment_name);
		$stmt->execute();
		$stmt->bind_result($apt);
		$row = null;
		while ($stmt->fetch())
		{
			$row = $apt;
		}
		$stmt->close();
		return ($row);
	}
			
	// Functions for the Image table
	// -----------------------------------------------------
	
	// upload image
	function uploadImage($name, $image, $apartment_id, $location)
	{
		
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."images (
			name,
			image,
			apartment_id,
			location
			)
			VALUES (
			?,
			?,
			?,
			?
			)");
			
		$stmt->bind_param("sbii", $name, $image, $apartment_id, $location);
		$stmt->send_long_data(1, file_get_contents($image));
		$result = $stmt->execute();
		$stmt->close();	
		return $result;
	}
	
	//Return the image's id given the specified image name
	function fetchImageID($image_name = NULL)
	{
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT 
			id
			FROM ".$db_table_prefix."images
			WHERE name = ?
			LIMIT 1");
			$stmt->bind_param("s", $image_name);
		$stmt->execute();
		$stmt->bind_result($im);
		$row = null;
		while ($stmt->fetch())
		{
			$row = $im;
		}
		$stmt->close();
		return ($row);
	}
	
	//Echo the image given selected image id
	function getImage($id)
	{
		
		global $mysqli, $db_table_prefix; 
		$stmt = $mysqli->prepare("SELECT image FROM ".$db_table_prefix."images WHERE id=?");
			
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($showimage);
		$stmt->fetch();
		if(!isset($showimage))
		{
			echo "Please select an image.";
		}
		else{
			echo '<img src="data:image/jpeg;base64,'.base64_encode($showimage).'"/>';	
		}
		//header("Content-Type: image/jpeg");
	
		$stmt->close();	
		
	}
	
	//Delete an image from the DB
	function deleteImage($id){
		global $mysqli,$db_table_prefix; 
		$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."images 
			WHERE id = ?");

		$stmt->bind_param("i", $id);
		$stmt->execute();

		$stmt->close();

	}
?>
