<?php
#If submit button clicked
include('classes/Login.php');
include('classes/DB.php');
try {
		#Get form info
	$username = strtolower($_POST['username']);
	$password = $_POST['password'];

		#Check for empty fields
	if (empty($username) || empty($password)){
		$error = 'Complete all fields';
	} else {
			#Check to see if username is registered
		if (!DB::query('SELECT username FROM users WHERE LOWER(username)=:username', array(':username'=>$username))) {
			$error = 'User not registered';
		}
			#Check to see if password matches username
		if (!password_verify($password, DB::query('SELECT password FROM users where LOWER(username)=:username', array(':username'=>strtolower($username)))[0]['password'])) {
			$error = 'Username or password is incorrect';
		}
	}

		#If no error
	if (!isset($error)) {
		$cstrong = True;
			#Create token
		$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
			#Get user id
		$user_id = DB::query('SELECT id FROM users WHERE LOWER(username)=:username', array(':username'=>$username))[0]['id'];
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