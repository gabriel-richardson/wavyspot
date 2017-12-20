<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAVY</title>
	<link rel="icon" href="images/wavyspot.ico">
	<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/styles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://apis.google.com/js/api.js"></script>
	<script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
	<script src="javascript/post.js"></script>
</head>
<body>
<?php
include('classes/DB.php');
include('classes/Login.php');
if (!Login::isLoggedIn()) { ?>
	<ul class="navigation">
		<li><a <?php if($current == 'new') {echo 'class="active"';} ?> href="index.php?sort=new">new</a></li>
		<div class="header-divider">/</div>
		<li><a <?php if($current == 'hot') {echo 'class="active"';} ?> href="index.php?sort=hot">hot</a></li>
		<li class="right"><a href="#modal" data-target="#modal" data-toggle="modal">register</a></li>
		<div class="header-divider-right">/</div>
		<li class="right"><a href="#modal" data-target="#modal" data-toggle="modal">login</a></li>
		<li class="center"><img src="images/wavyspot.png"/></li>
	</ul>
<?php
} else {
	$username = DB::query('SELECT username FROM users WHERE id=:id', array(':id'=>Login::isLoggedIn()))[0]['username']; ?>
	<ul class="navigation">
		<li><a <?php if($current == 'new') {echo 'class="active"';} ?> href="index.php?sort=new">new</a></li>
		<div class="header-divider">/</div>
		<li><a <?php if($current == 'hot') {echo 'class="active"';} ?> href="index.php?sort=hot">hot</a></li>
		<li class="dropdown right">
			<div class="header-divider">/</div>
			<div class="btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?php echo $username ?> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right">
					<li><a href="profile.php?username=<?php echo $username ?>&sort=new">profile</a></li>
					<li><a href="notifications.php">notifications</a></li>
					<li><a href="settings.php">settings</a></li>
					<li><a href="logout.php">logout</a></li>
				</ul>
			</div>
		</li>
		<li class="right" id="post"><a href="" data-toggle="modal" data-target="#modal">post</a></li>
		<li class="center"><a href="index.php" id="img-icon"><img src="images/wavyspot.png"/></a></li>
	</ul>
	<div class="modal" tabindex="-1" role="dialog" id="modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Find your music</h4>
					<div class="row">
						<div class="col-sm-12">
							<div class="input-group">
								<input type="text" class="form-control" placeholder="Search" id="txt-search" autocomplete="off" tabindex="-1" />
								<div class="input-group-btn">
									<button type="button" class="btn btn-primary" id="search">
										<span id="icon" class="glyphicon glyphicon-search"></span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-body" tabindex="-1" id="modal-body">
					<ul id="music-container">
					</ul>
				</div>
				<div class="modal-footer">
					<button  id="btn-post" type="button" class="btn btn-primary" data-dismiss="modal" disabled="true">post</button>
				</div>
			</div>
		</div>
	</div>
<?php
}
?>
</body>
</html>