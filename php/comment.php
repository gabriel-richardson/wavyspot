<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAVY / COMMENT</title>
	<link rel="icon" href="images/wavyspot.ico">
	<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="build/build.css"">
	<link rel="stylesheet" href="css/styles.css">
	<link rel="stylesheet" href="css/bootstrap-xlgrid.min.css">
	<script src="build/build.js"></script>
	<script src="javascript/delete-post.js"></script>
	<script src="javascript/delete-comment.js"></script>
	<script src="javascript/vote.js"></script>
	<script src="javascript/vote-comment.js"></script>
	<script src="javascript/login.js"></script>
	<script src="javascript/comment.js"></script>
	<script src="javascript/select.js"></script>
</head>
<body>
<?php
include('header.php');
if (DB::query('SELECT * FROM posts WHERE id=:id', array(':id'=>$_GET['post_id']))) {
	$p = DB::query('SELECT * FROM posts WHERE id=:id', array(':id'=>$_GET['post_id']));
} else {
	header('Location: index.php');
}
$post_vote = DB::query('SELECT vote_value FROM post_votes WHERE user_id=:user_id AND post_id=:post_id', array(':user_id'=>Login::isLoggedIn(), ':post_id'=>$p[0]['id']))[0]['vote_value'];
$post_time = time_diff($p[0]['posted_at']);
$post_username = DB::query('SELECT username FROM users WHERE id=:user_id', array(':user_id'=>$p[0]['user_id']))[0]['username'];
if ($post_username == '') {
	$post_username = '[deleted]';
}
if ($_GET['sort'] == 'new' || $_GET['sort'] == '') {
	$dbcomments = DB::query('SELECT * FROM comments WHERE parent_id IS NULL AND post_id=:post_id ORDER BY id DESC', array(':post_id'=>$p[0]['id']));
} else {
	$dbcomments = DB::query('SELECT * FROM comments WHERE parent_id IS NULL AND post_id=:post_id ORDER BY points DESC', array(':post_id'=>$p[0]['id']));
}
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
?>
<div class="comment-container">
	<div class="comment-post">
		<div class="comment-post-header">
			<?php if ($post_username != '[deleted]') { ?>
				<a href="profile.php?username=<?php echo $post_username; echo "&sort=new" ?>"><?php echo $post_username ?></a>
			<?php } else { ?>
				<p>[deleted]</p>
			<?php } ?>
			<p><?php echo "/ ", $post_time; ?></p>
			<?php
			if ($p[0]['user_id'] == Login::isLoggedIn()) { ?>
				<button type="button" data-toggle="modal" data-target="#del-modal" class="close" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			<?php } ?>
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
						<button class="btn" style="margin-top: -16px; margin-right: -16px; background-color: #F78C58; color: white; float: right;" onclick="delete_post(<?php echo $p[0]['id']; ?>)">Confirm</button>
					</div>
				</div>
			</div>
		</div>
		<div class="comment-post-body">
			<div class="comment-play-container">
				<img src="<?php echo $p[0]['artwork']; ?>"/>
				<div class="comment-overlay" id="overlay<?php echo $p[0]['id']; ?>"></div>
				<audio class="audio" id="song<?php echo $p[0]['id']; ?>" src="<?php echo $p[0]['preview_url']; ?>"></audio>
				<script>
					var audio = require('audio');
					var el = document.querySelector('#song<?php echo $p[0]['id']; ?>');
					$()
					audio(el);
					$("audio").on("play", function() {
						$("audio").not(this).each(function(index, audio) {
							audio.pause();
						});
					});
				</script>
			</div>
			<p id="song-title"><a href="<?php echo $p[0]['buy_url']; ?>"><?php echo $p[0]['song']; ?></a></p>
			<p id="caption-secondary"><?php echo $p[0]['artist'], " - ", $p[0]['album']; ?></p>
		</div>
		<div class="post-footer">
			<button id="btn-up<?php echo $p[0]['id']; ?>" class="<?php if ($post_vote == 1) { echo "up-arrow-click vote"; } else { echo "vote"; }?>" onclick="<?php if (Login::isLoggedIn()) { echo "upvote({$p[0]['id']})"; } ?>" <?php if (!Login::isLoggedIn()) { echo 'data-toggle="modal" data-target="#modal"'; } ?>>
				<span class="glyphicon glyphicon-arrow-up"></span>
			</button>
			<p id="points<?php echo $p[0]['id']; ?>" class="<?php if ($post_vote == 1) { echo "up-arrow-click vote"; } else if ($post_vote == -1) { echo "down-arrow-click vote"; } else { echo "vote"; } ?>"><?php echo $p[0]['points']; ?></p>
			<button id="btn-down<?php echo $p[0]['id']; ?>" class="<?php if ($post_vote == -1) { echo "down-arrow-click vote"; } else { echo "vote"; }?>" onclick="<?php if (Login::isLoggedIn()) { echo "downvote({$p[0]['id']})"; } else { echo "login()"; } ?>">
				<span class="glyphicon glyphicon-arrow-down"></span>
			</button>
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
							return false;
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
							return false;
						});
					</script>
				</div>
			</div>
		</div>
	</div>
	</div>
	<div class="comment-section">
		<div class="comment-section-header">
			<?php if (Login::isLoggedIn()) { ?>
				<form action="submit-comment.php?post_id=<?php echo $_GET['post_id'] ?>" method="post">
					<textarea name="comment" rows="4" cols="73" placeholder="comment"></textarea>
					<input name="comment-submit" class="btn" type="submit" value="save"/>
				</form>
				<div class="comment-sorted-by">
					<p>Sorted by:</p>
					<select onchange="onChange(this)">
						<option><?php if ($_GET['sort'] == 'new' || $_GET['sort'] == '') { echo 'new'; } else { echo 'hot'; } ?></option>
						<option><?php if ($_GET['sort'] == 'new' || $_GET['sort'] == '') { echo 'hot'; } else { echo 'new'; } ?></option>
					</select>
				</div>
				<hr>
			<?php } ?>
		</div>
		<div class="comment-section-body" <?php if (!Login::isLoggedIn()) { ?> style="height: 96.5%;" <?php } ?>>
			<?php if (count($dbcomments) == 0) { ?>
					<br>
					<center><p style="font-size: 18px">NOBODY'S COMMENTED ON THIS</p></center>
				<?php }
				function build_comment($c) {
					$comment_username = DB::query('SELECT username FROM users WHERE id=:user_id', array(':user_id'=>$c['user_id']))[0]['username'];
					if ($comment_username == '') {
						$comment_username = '[deleted]';
					}
					$comment_vote = DB::query('SELECT vote_value FROM comment_votes WHERE user_id=:user_id AND comment_id=:comment_id', array(':user_id'=>Login::isLoggedIn(), ':comment_id'=>$c['id']))[0]['vote_value'];
					$comment_time = time_diff($c['posted_at']); ?>
					<div class="user-comment">
						<div class="vote-comment">
							<button id="btn-up<?php echo $c['id']; ?>" class="<?php if ($comment_vote == 1) { echo "up-arrow-click vote"; } else { echo "vote"; }?>" onclick="<?php if (Login::isLoggedIn()) { echo "comment_upvote({$c['id']})"; } ?>" <?php if (!Login::isLoggedIn()) { echo 'data-toggle="modal" data-target="#modal"'; } ?>>
								<span class="glyphicon glyphicon-arrow-up"></span>
							</button>
							<button id="btn-down<?php echo $c['id']; ?>" class="<?php if ($comment_vote == -1) { echo "down-arrow-click vote"; } else { echo "vote"; }?>" onclick="<?php if (Login::isLoggedIn()) { echo "comment_downvote({$c['id']})"; } else { echo "login()"; } ?>">
								<span class="glyphicon glyphicon-arrow-down"></span>
							</button>
						</div>
						<div class="comment-header">
							<?php if ($comment_username != '[deleted]') { ?>
								<a href="profile.php?username=<?php echo $comment_username; echo "&sort=new" ?>"><?php echo $comment_username?></a>
							<?php } else { ?>
								<p>[deleted]</p>
							<?php } ?>
								<p><?php echo '/ ', $comment_time, ' / '; ?></p>
									<p id="points<?php echo $c['id']; ?>"><?php if ($c['points'] == 1) { echo $c['points'], ' point'; } else { echo $c['points'], ' points'; } ?></p>
							<?php if ($c['user_id'] == Login::isLoggedIn()) { ?>
								<button type="button" data-toggle="modal" data-target="#del-com-modal" class="close" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							<?php } ?>
						</div>
						<div class="modal" tabindex="-1" role="dialog" id="del-com-modal">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
										<h4 class="modal-title">Are you sure you want to delete this?</h4>
									</div>
									<div class="err-modal-body" tabindex="-1" id="modal-body">
										<button class="btn" style="margin-top: -16px; margin-right: -16px; background-color: #F78C58; color: white; float: right;" onclick="delete_comment(<?php echo $c['id']; ?>)">Confirm</button>
									</div>
								</div>
							</div>
						</div>
						<div class="comment-body">
							<p><?php echo $c['body']; ?></p>
						</div>
						<div class="comment-footer">
							<a href="#" class="reply">reply</a>
							<form class="reply-form" style="display: none;" action="submit-comment.php?post_id=<?php echo $_GET['post_id']; ?>&parent_id=<?php echo $c['id']; ?>" method="post">
								<textarea name="comment" rows="3" cols="50" /></textarea>
								<input class="btn" name="comment-submit" type="submit" value="save"></input>
							</form>
							<script>
								$('.reply').on('click', function(e){ 
									e.preventDefault();
									var $parent = $(this).closest('div');
									$parent.find('form.reply-form').show();
								});
						</script>
						</div>
						<?php
						if ($_GET['sort'] == 'new' || $_GET['sort'] == '') {
							$dbreply = DB::query('SELECT * FROM comments WHERE parent_id=:parent_id ORDER BY id DESC', array(':parent_id'=>$c['id']));
						} else {
							$dbreply = DB::query('SELECT * FROM comments WHERE parent_id=:parent_id ORDER BY points DESC', array(':parent_id'=>$c['id']));
						}
						foreach ($dbreply as $r) {
							build_comment($r);
						} ?>
					</div>
				<?php }
				foreach ($dbcomments as $c) { 
					build_comment($c);
				} ?>
		</div>
	</div>
</div>
</body>
</html>