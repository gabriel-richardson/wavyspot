<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAVY / BYE</title>
	<link rel="icon" href="images/wavyspot.ico">
	<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<script src="https://apis.google.com/js/api.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<?php
include('header.php');
if(isset($_POST['deactivate'])) {
	$password = DB::query('SELECT password FROM users WHERE id=:id', array(':id'=>Login::isLoggedIn()))[0]['password'];
	$verify_password = $_POST['password'];

	if (!password_verify($verify_password, $password)) {
		$error = 'Password is incorrect';
	}

	if (!isset($error)) {
		DB::query('DELETE FROM users where id=:id', array(':id'=>Login::isLoggedIn()));
		header('Location: logout.php');
	} else {
		header('Location: deactivate.php?error='.$error);
	}
}
?>
<body>
<div class="settings">
	<div class="settings-header">
		<h3>So this is it?</h3>
	</div>
	<div class="settings-body">
		<p class="deactivate">Letting go of someone special is hard...</p>
		<p class="deactivate" id="heartbreak">...but holding on to someone who doesn't even feel the same is <b>much</b> harder</p>
	</div>
	<div class="settings-footer">
		<form action="deactivate.php" method="post">
			<p>Password:</p><input type="password" name="password"/>
			<input type="submit" class="btn" name="deactivate" value="Deactivate"/>
			<p style="color: red"><?php echo $_GET['error']; ?></p>
		</form>
	</div>
</div>
</body>
</html>