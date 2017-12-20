<?php
include('classes/DB.php');
include('classes/Login.php');
if ($_POST['dir'] == "true") {
	if (DB::query('SELECT user_id FROM post_votes WHERE post_id=:post_id AND user_id=:user_id AND vote_value=1', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()))) {
		DB::query('DELETE FROM post_votes WHERE post_id=:post_id AND user_id=:user_id AND vote_value=1', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE posts SET points=points-1 WHERE id=:post_id', array(':post_id'=>$_POST['post_id']));
		$points = DB::query('SELECT points FROM posts WHERE id=:post_id', array(':post_id'=>$_POST['post_id']))[0]['points'];
	} else if (DB::query('SELECT user_id FROM post_votes WHERE post_id=:post_id AND user_id=:user_id AND vote_value=-1', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()))) {
		DB::query('DELETE FROM post_votes WHERE post_id=:post_id AND user_id=:user_id AND vote_value=-1', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('INSERT INTO post_votes VALUES (null, :post_id, :user_id, 1)', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE posts SET points=points+2 WHERE id=:post_id', array(':post_id'=>$_POST['post_id']));
		$points = DB::query('SELECT points FROM posts WHERE id=:post_id', array(':post_id'=>$_POST['post_id']))[0]['points'];
	} else {
		DB::query('INSERT INTO post_votes VALUES (null, :post_id, :user_id, 1)', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE posts SET points=points+1 WHERE id=:post_id', array(':post_id'=>$_POST['post_id']));
		$points = DB::query('SELECT points FROM posts WHERE id=:post_id', array(':post_id'=>$_POST['post_id']))[0]['points'];
	}

	echo $points;
} else {
	if (DB::query('SELECT user_id FROM post_votes WHERE post_id=:post_id AND user_id=:user_id AND vote_value=-1', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()))) {
		DB::query('DELETE FROM post_votes WHERE post_id=:post_id AND user_id=:user_id AND vote_value=-1', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE posts SET points=points+1 WHERE id=:post_id', array(':post_id'=>$_POST['post_id']));
		$points = DB::query('SELECT points FROM posts WHERE id=:post_id', array(':post_id'=>$_POST['post_id']))[0]['points'];
	} else if (DB::query('SELECT user_id FROM post_votes WHERE post_id=:post_id AND user_id=:user_id AND vote_value=1', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()))) {
		DB::query('DELETE FROM post_votes WHERE post_id=:post_id AND user_id=:user_id AND vote_value=1', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('INSERT INTO post_votes VALUES (null, :post_id, :user_id, -1)', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE posts SET points=points-2 WHERE id=:post_id', array(':post_id'=>$_POST['post_id']));
		$points = DB::query('SELECT points FROM posts WHERE id=:post_id', array(':post_id'=>$_POST['post_id']))[0]['points'];
	} else {
		DB::query('INSERT INTO post_votes VALUES (null, :post_id, :user_id, -1)', array(':post_id'=>$_POST['post_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE posts SET points=points-1 WHERE id=:post_id', array(':post_id'=>$_POST['post_id']));
		$points = DB::query('SELECT points FROM posts WHERE id=:post_id', array(':post_id'=>$_POST['post_id']))[0]['points'];
	}

	echo $points;
}
?>