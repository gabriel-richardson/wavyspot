<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAVY / NOTIFICATIONS</title>
	<link rel="icon" href="images/wavyspot.ico">
	<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/styles.css">
</head>
<?php

function time_diff($posted_at) {
	$start_date = new DateTime($posted_at);
	$end_date = new DateTime();
	$interval = $start_date->diff($end_date);
	if ($interval->y > 0) {
		return $interval->y."y";
	} else if ($interval->m > 0) {
		return $interval->m."mo";
	} else if ($interval->d > 0) {
		return $interval->d."d";
	} else if ($interval->h > 0) {
		return $interval->h."h";
	} else if ($interval->i > 0) {
		return $interval->i."m";
	} else if ($interval->s > 0) {
		return $interval->s."s";
	} else {
		return "1s";
	}
}

$current="notifications";
include('header.php');
?>
<body>
	<div class="settings">
	<div class="settings-header">
		<h3>Notifications</h3>
		<hr/>
	</div>
	<div class="settings-body">
		<?php
		$dbnotifications = DB::query('SELECT * FROM notifications WHERE receiver_id=:receiver_id ORDER BY id DESC', array(':receiver_id'=>Login::isLoggedIn()));
		if (count($dbnotifications) == 0) { ?>
			<center><p style="font-size: 18px; margin-top: 10px;">NO NOTIFICATIONS</p></center>
		<?php }
		foreach ($dbnotifications as $n) { 
			$sender = DB::query('SELECT username FROM users WHERE id=:user_id', array(':user_id'=>$n['sender_id']))[0]['username'];
			if ($n['type'] == 1) {
				$message = " replied to your comment on ";
			} else {
				$message = " commented on your ";
			} ?>
		<div class="notifications">
			<p><a href="profile.php?username=<?php echo $sender ?>&sort=hot"><?php echo $sender ?></a><?php echo $message ?><a href="comment.php?post_id=<?php echo $n['post_id'] ?>&sort=hot">post </a><span style="color: gray; font-weight: normal;"> / <?php echo time_diff($n['posted_at']) ?></span></p>
			<p style="color: gray; font-weight: normal;"><?php echo "> ", $n['body'] ?></p>
		</div>
		<hr/>
		<?php } ?>
	</div>
</div>
</body>
</html>