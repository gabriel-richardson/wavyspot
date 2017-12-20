<?php
include('classes/DB.php');
include('classes/Login.php');
	
function time_diff($posted_at) {
	$start_date = new DateTime($posted_at);
	$end_date = new DateTime();
	$interval = $start_date->diff($end_date);
	if ($interval->y > 0) {
		return "y";
	} else if ($interval->m > 0) {
		return "mo";
	} else if ($interval->d > 0) {
		return "d";
	} else if ($interval->h > 0) {
		return "h";
	} else if ($interval->i > 0) {
		return "m";
	} else if ($interval->s > 0) {
		return "s";
	} else {
		return "1s";
	}
}

if (isset($_POST['artist'])) {
	$artist = $_POST['artist'];
	$song = $_POST['song'];
	$album = $_POST['album'];
	$artwork = $_POST['artwork'];
	$preview = $_POST['preview'];
	$buy = $_POST['buy'];
	$user_id = Login::isLoggedIn();

	$posted_at = DB::query('SELECT posted_at FROM posts WHERE preview_url=:preview_url AND id=:id ORDER BY id desc', array(':preview_url'=>$preview, ':id'=>$user_id))[0]['posted_at'];
	if ($posted_at == "") {
		DB::query('INSERT INTO posts VALUES (null, :artist, :song, :album, :artwork, :preview_url, :buy_url, NOW(), :user_id, 1)', array(':artist'=>$artist, ':song'=>$song, ':album'=>$album, ':artwork'=>$artwork, ':preview_url'=>$preview, ':buy_url'=>$buy, ':user_id'=>$user_id));
			$post_id = DB::query('SELECT id FROM posts WHERE id=(SELECT MAX(id) FROM posts)')[0]['id'];
			DB::query('INSERT INTO post_votes VALUES (null, :post_id, :user_id, 1)', array(':post_id'=>$post_id, ':user_id'=>$user_id));
	} else if (time_diff($posted_at) == "y" || time_diff($posted_at) == "mo" || time_diff($posted_at) == "d") {
			DB::query('INSERT INTO posts VALUES (null, :artist, :song, :album, :artwork, :preview_url, :buy_url, NOW(), :user_id, 1)', array(':artist'=>$artist, ':song'=>$song, ':album'=>$album, ':artwork'=>$artwork, ':preview_url'=>$preview, ':buy_url'=>$buy, ':user_id'=>$user_id));
			$post_id = DB::query('SELECT id FROM posts WHERE id=(SELECT MAX(id) FROM posts)')[0]['id'];
			DB::query('INSERT INTO post_votes VALUES (null, :post_id, :user_id, 1)', array(':post_id'=>$post_id, ':user_id'=>$user_id));
	} else {
		echo "YOU CAN'T POST THE SAME SONG MORE THAN ONCE A DAY";
	}
}
?>