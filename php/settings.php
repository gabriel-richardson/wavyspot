<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAVY / SETTINGS</title>
	<link rel="icon" href="images/wavyspot.ico">
	<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<?php
include('header.php');
$email = DB::query('SELECT email FROM users WHERE id=:id', array(':id'=>Login::isLoggedIn()))[0]['email'];

if (isset($_POST['acct-submit'])) {
	try {
		$email = DB::query('SELECT email FROM users WHERE id=:id', array(':id'=>Login::isLoggedIn()))[0]['email'];
		$password = DB::query('SELECT password FROM users WHERE id=:id', array(':id'=>Login::isLoggedIn()))[0]['password'];
		$new_username = $_POST['username'];
		$new_email = $_POST['email'];
		$verify_password = $_POST['password'];

		if ($new_username == '' || $new_email == '' || $verify_password == '') {
			$error = 'Complete all fields';
		} else {
			if ($new_username != $username) {
				if (DB::query('SELECT username from users WHERE LOWER(username)=:username', array(':username'=>strtolower($new_username)))) {
					$error = 'Username taken';
				}
				if (!preg_match('/^[-a-zA-Z0-9_]+$/', $new_username)) {
					$error = 'Username can only contain numbers, letters, "-", and "_"';
				}
			}
			if ($new_email != $email) {
				if (DB::query('SELECT email from users WHERE LOWER(email)=:email', array(':email'=>strtolower($new_email)))) {
					$error = 'Email is already in use';
				}
				if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
					$error = 'Email invalid';
				}
			}
			if (!password_verify($verify_password, $password)) {
				$error = 'Password incorrect';
			}

		}

		if (!isset($error)) {
			DB::query('UPDATE users SET email=:email WHERE id=:id', array(':email'=>$new_email, ':id'=>Login::isLoggedIn()));
			DB::query('UPDATE users SET username=:username WHERE id=:id', array(':username'=>$new_username, ':id'=>Login::isLoggedIn()));
			header('Location: settings.php');
		} else {
			header('Location: settings.php?acct-error='.$error);
		}
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
}

if (isset($_POST['pass-submit'])) {
	try {
		$password = DB::query('SELECT password FROM users where id=:id', array(':id'=>Login::isLoggedIn()))[0]['password'];
		$old_pass = $_POST['old-pass'];
		$new_pass = $_POST['new-pass'];
		$verify_pass = $_POST['verify-pass'];

		if ($new_pass == '' || $verify_pass == '' || $password == '') {
			$error = 'Complete all fields';
		} else {
			if (!password_verify($old_pass, $password)) {
				$error = 'Old password is incorrect';
			} else if ($new_pass != $verify_pass) {
				$error = 'Passwords do not match';
			} else if (strlen($new_pass) <= 6){
				$error = 'Choose a password longer than 6 characters';
			}
		}

		if (!isset($error)) {
			DB::query('UPDATE users SET password=:password WHERE id=:id', array(':password'=>password_hash($new_pass, PASSWORD_BCRYPT), ':id'=>Login::isLoggedIn()));
			header('Location: settings.php');
		} else {
			header('Location: settings.php?pass-error='.$error);
		}
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
}
?>
<body>
<div class="settings">
	<div class="settings-header">
		<h3>Account</h3>
		<hr/>
	</div>
	<div class="settings-body">
		<form class="acct-form" action="settings.php" method="post" autocomplete="off">
			<p>Username</p><input type="text" name="username" value="<?php echo $username; ?>" maxlength="10"/><p/><p/>
			<p>Email</p><input type="text" name="email" value="<?php echo $email; ?>" maxlength="60"/><p/>
			<p>Password</p><input type="password" name="password"/><p/>
			<p><?php echo $_GET['acct-error']; ?></p>
			<input id="acct-submit" class="btn" type="submit" name="acct-submit" disabled="true">
		</form>
	</div>
	<div class="settings-header">
		<hr/>
		<h3>Privacy</h3>
		<hr/>
	</div>
	<div class="settings-body">
		<form class="pass-form" action="settings.php" method="post" autocomplete="off">
			<p>Old Password</p><input type="password" name="old-pass" maxlength="10"/><p/><p/>
			<p>New Password</p><input type="password" name="new-pass" maxlength="60"/><p/>
			<p>Verify Password</p><input type="password" name="verify-pass" maxlength="60"/><p/>
			<p><?php echo $_GET['pass-error']; ?></p>
			<input id="pass-submit" class="btn" type="submit" name="pass-submit" disabled="true">
		</form>
	</div>
	<div class="deactivate-footer">
		<hr>
		<a href="deactivate.php">DEACTIVATE ACCOUNT</a>
	</div>
</div>
<script>
	$(".acct-form").on('input',function() {
		$("#acct-submit").prop('disabled', false);
	});

	$(".pass-form").on('input',function() {
		$("#pass-submit").prop('disabled', false);
	});
</script>
</body>
</html>