<?php
#If submit button clicked
include('classes/Login.php');
include('classes/DB.php');
try {
		#Get form info
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];

		#Check for empty fields
	if (empty($username) || empty($password) || empty($email)){
		$error = 'Complete all fields';
	} else {
			#Check password length
		if (strlen($password) <= 6){
			$error = 'Choose a password longer than 6 characters';
		}

			#Check if username is taken
		if (DB::query('SELECT username FROM users WHERE LOWER(username) = :username', array(':username'=>strtolower($username)))) {
			$error = 'Username taken';
		}


			#Check if username is valid
		if (!preg_match('/^[-a-zA-Z0-9_]+$/', $username)) {
			$error = 'Username can only contain numbers, letters, "-", and "_"';
		}

			#Check if email is valid
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error = 'Email invalid';
		}

			#Check if email is taken
		if (DB::query('SELECT email FROM users WHERE LOWER(email) = :email', array(':email'=>strtolower($email)))) {
			$error = 'Email is already registered';
		}
	}

		#If no error
	if (!isset($error)) {
		DB::query('INSERT INTO users VALUES (null, :username, :password, :email)', array(':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email));
		$cstrong = True;
			#Create token
		$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
			#Get user id
		$user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
			#Insert token and id
		DB::query('INSERT INTO login_tokens VALUES(null, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
			#Set cookie
		setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
			#Second cookie
		setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
		exit();
	} else {
		echo $error;
	}
} catch(PDOException $e) {
	echo 'Error: ' . $e->getMessage();
}
?>
