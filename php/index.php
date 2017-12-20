<?php
$current;

if (isset($_GET['username']) && ($_GET['sort'] == 'new' || $_GET['sort'] == '')) {
	$current = 'profile-new';
} else if (isset($_GET['username']) && $_GET['sort'] == 'hot') {
	$current = 'profile-hot';
} else if ($_GET['sort'] == 'new') {
	$current = 'new';
} else if ($_GET['sort'] == 'hot') {
	$current = 'hot';
} else if ($_GET['sort'] == '') {
	$current = 'new';
}
include('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAVY</title>
	<link rel="icon" href="images/wavyspot.ico">
	<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="build/build.css"">
	<link rel="stylesheet" href="css/styles.css">
	<link rel="stylesheet" href="css/bootstrap-xlgrid.min.css">
	<script src="build/build.js"></script>
	<script src="javascript/delete-post.js"></script>
	<script src="javascript/vote.js"></script>
	<script src="javascript/login.js"></script>
	<script src="javascript/comment.js"></script>
</head>
<body>
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

function populate() {
	$user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
	if ($GLOBALS['current'] == 'new') {
		$dbposts = DB::query('SELECT * FROM posts ORDER BY id DESC');
	} else if ($GLOBALS['current'] == 'hot') {
		$dbposts = DB::query('SELECT * FROM posts ORDER BY points DESC');
	} else if ($GLOBALS['current'] == 'profile-new') {
		$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:user_id ORDER BY id DESC', array(':user_id'=>$user_id));
	} else if ($GLOBALS['current'] == 'profile-hot') {
		$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:user_id ORDER BY points DESC', array(':user_id'=>$user_id));
	}

	if (count($dbposts) == 0) { ?>
		<br>
		<center><p style="font-size: 18px">POST SOMETHING</p></center>
	<?php } ?>

	<div class="container-fluid">
	<div class="row">
	<?php
	foreach ($dbposts as $p) {
		$vote = DB::query('SELECT vote_value FROM post_votes WHERE user_id=:user_id AND post_id=:post_id', array(':user_id'=>Login::isLoggedIn(), ':post_id'=>$p['id']))[0]['vote_value'];
		$time = time_diff($p['posted_at']);
		$username = DB::query('SELECT username FROM users WHERE id=:user_id', array(':user_id'=>$p['user_id']))[0]['username']; 
		if ($username == '') {
			$username = '[deleted]';
		} ?>
		<div class="post-container col-xl-2 col-lg-5ths col-md-3 col-sm-4 col-xs-6 col-xxs-12">
			<div class="post center-block">
				<div class="post-header">
					<?php if ($username != '[deleted]') { ?>
						<a href="profile.php?username=<?php echo $username; echo "&sort=new" ?>"><?php echo $username ?></a>
					<?php } else { ?>
						<p>[deleted]</p>
					<?php } ?>
					<p><?php echo "/ ", $time; ?></p>
					<?php
					if ($p['user_id'] == Login::isLoggedIn()) { ?>
						<button type="button" data-toggle="modal" data-target="#del-modal" class="close" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					<?php } ?>
				</div>
				<div class="post-body">
					<div class="play-container">
						<img src="<?php echo $p['artwork']; ?>"/>
						<div class="overlay" id="overlay<?php echo $p['id']; ?>"></div>
						<audio class="audio" id="song<?php echo $p['id']; ?>" src="<?php echo $p['preview_url']; ?>"></audio>
						<script>
							var audio = require('audio');
							var el = document.querySelector('#song<?php echo $p['id']; ?>');
							$()
							audio(el);
							$("audio").on("play", function() {
								$("audio").not(this).each(function(index, audio) {
									audio.pause();
								});
							});
						</script>
					</div>
					<p id="song-title"><a href="<?php echo $p['buy_url']; ?>"><?php echo $p['song']; ?></a></p>
					<p id="caption-secondary"><?php echo $p['artist'], " - ", $p['album']; ?></p>
				</div>
				<div class="post-footer">
					<button id="btn-up<?php echo $p['id']; ?>" class="<?php if ($vote == 1) { echo "up-arrow-click vote"; } else { echo "vote"; }?>" onclick="<?php if (Login::isLoggedIn()) { echo "upvote({$p['id']})"; } ?>" <?php if (!Login::isLoggedIn()) { echo 'data-toggle="modal" data-target="#modal"'; } ?>>
						<span class="glyphicon glyphicon-arrow-up"></span>
					</button>
					<p id="points<?php echo $p['id']; ?>" class="<?php if ($vote == 1) { echo "up-arrow-click vote"; } else if ($vote == -1) { echo "down-arrow-click vote"; } else { echo "vote"; } ?>"><?php echo $p['points']; ?></p>
					<button id="btn-down<?php echo $p['id']; ?>" class="<?php if ($vote == -1) { echo "down-arrow-click vote"; } else { echo "vote"; }?>" onclick="<?php if (Login::isLoggedIn()) { echo "downvote({$p['id']})"; } ?>"<?php if (!Login::isLoggedIn()) { echo 'data-toggle="modal" data-target="#modal"'; } ?>>
						<span class="glyphicon glyphicon-arrow-down"></span>
					</button>
					<p class="right"><?php echo count(DB::query('SELECT * FROM comments WHERE post_id=:post_id', array(':post_id'=>$p['id'])))?></p>
					<button id="btn-comment" onclick='comment(<?php echo $p['id'] ?>)' class="right">
						<span class="glyphicon glyphicon-comment"></span>
					</button>
				</div>
			</div>
		</div>
<?php } ?>
	</div>
	</div>
	<div class="modal" tabindex="-1" role="dialog" id="del-modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Are you sure you want to delete this?</h4>
				</div>
				<div class="err-modal-body" tabindex="-1" id="modal-body">
					<button class="btn" style="margin-top: -16px; margin-right: -16px; background-color: #F78C58; color: white; float: right;" onclick="delete_post(<?php echo $p['id']; ?>)">Confirm</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" tabindex="-1" role="dialog" id="modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="error-title" style="color: red"></h4>
				</div>
				<div class="modal-body" tabindex="-1" id="modal-body">
					<div class="register">
						<h4>register</h4>
						<form action="register.php" method="post" autocomplete="off" id="register"><br>
							<input type="text" name="username" placeholder="username" maxlength="10"/><p/><p/>
							<input type="password" name="password" placeholder="password" maxlength="60"/><p/><p/>
							<input type="email" name="email" placeholder="email"/><p/>
							<input class="btn" id="modal-btn" type="submit" name="submit" value="create account"/>
						</form>
					</div>
					<div class="vr">&nbsp;</div>
					<div class="login">
						<h4>login</h4>
						<form action="login.php" method="post" autocomplete="off" id="login"><br>
							<input type="text" name="username" placeholder="username" maxlength="10"/><p/><p/>
							<input type="password" name="password" placeholder="password" maxlength="60"/><p/>
							<input class="btn" id="modal-btn" type="submit" name="login" value="login"/><p/>
						</form>
					</div>
					<script>
						$('form#login').submit(function(){
							$.ajax({
								url: 'login.php',
								type: 'POST',
								data: $(this).serialize(),
								success: function(data){
									if(data) {
										$("#error-title").html(data);
									} else{
										window.location.href = "index.php";
									}
								}
							});
						});
					</script>
					<script>
						$('form#register').submit(function(){
							$.ajax({
								url: 'register.php',
								type: 'POST',
								data: $(this).serialize(),
								success: function(data){
									if(data) {
										$("#error-title").html(data);
									} else{
										window.location.href = "index.php";
									}
								}
							});
						});
					</script>
				</div>
			</div>
		</div>
	</div>
<?php }

populate();

?>
</body>
</html>