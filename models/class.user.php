<?php

	class loggedInUser
	{
		public $email = NULL;
		public $hash_pw = NULL;
		public $user_id = NULL;
		
		//Simple function to update the last sign in of a user
		public function updateLastSignIn()
		{
			global $mysqli,$db_table_prefix;
			$time = time();
			$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
				SET
				last_sign_in_stamp = ?
				WHERE
				id = ?");
			$stmt->bind_param("ii", $time, $this->user_id);
			$stmt->execute();
			$stmt->close();	
		}
		
		//Return the timestamp when the user registered
		public function signupTimeStamp()
		{
			global $mysqli,$db_table_prefix;
			
			$stmt = $mysqli->prepare("SELECT sign_up_stamp
				FROM ".$db_table_prefix."users
				WHERE id = ?");
			$stmt->bind_param("i", $this->user_id);
			$stmt->execute();
			$stmt->bind_result($timestamp);
			$stmt->fetch();
			$stmt->close();
			return ($timestamp);
		}
		
		//Update a users password
		public function updatePassword($pass)
		{
			global $mysqli,$db_table_prefix;
			$secure_pass = generateHash($pass);
			$this->hash_pw = $secure_pass;
			$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
				SET
				password = ? 
				WHERE
				id = ?");
			$stmt->bind_param("si", $secure_pass, $this->user_id);
			$stmt->execute();
			$stmt->close();	
		}
		
		//Update a users email
		public function updateEmail($email)
		{
			global $mysqli, $db_table_prefix;
			$this->email = $email;
			$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
				SET 
				email = ?
				WHERE
				id = ?");
			$stmt->bind_param("si", $email, $this->user_id);
			$stmt->execute();
			$stmt->close();	
		}
		
		//Update a users gender
		public function updateGender($gender)
		{
			global $mysqli, $db_table_prefix;
			$this->gender = $gender;
			$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
				SET 
				gender = ?
				WHERE
				id = ?");
			$stmt->bind_param("si", $gender, $this->user_id);
			$stmt->execute();
			$stmt->close();	
		}
		
		//Update a users private profile status
		public function updatePrivateProfile($privateProfile)
		{
			global $mysqli, $db_table_prefix;
			$this->private_profile = $privateProfile;
			$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
				SET 
				private_profile = ?
				WHERE
				id = ?");
			$stmt->bind_param("ii", $privateProfile, $this->user_id);
			$stmt->execute();
			$stmt->close();
		}
		
		//Update a users description
		public function updateDescription($description)
		{
			global $mysqli, $db_table_prefix;
			$this->description = $description;
			$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
				SET 
				description = ?
				WHERE
				id = ?");
			$stmt->bind_param("si", $description, $this->user_id);
			$stmt->execute();
			$stmt->close();
		}
		
		//Is a user has a permission
		public function checkPermission($permission)
		{
			global $mysqli,$db_table_prefix,$master_account;
			
			//Grant access if master user
			
			$stmt = $mysqli->prepare("SELECT id 
				FROM ".$db_table_prefix."user_permission_matches
				WHERE user_id = ?
				AND permission_id = ?
				LIMIT 1
				");
			$access = 0;
			foreach($permission as $check){
				if ($access == 0){
					$stmt->bind_param("ii", $this->user_id, $check);
					$stmt->execute();
					$stmt->store_result();
					if ($stmt->num_rows > 0){
						$access = 1;
					}
				}
			}
			if ($access == 1)
			{
				return true;
			}
			if ($this->user_id == $master_account){
				return true;	
			}
			else
			{
				return false;	
			}
			$stmt->close();
		}
		
		//Logout
		public function userLogOut()
		{
			destroySession("userCakeUser");
		}	
	}

?>