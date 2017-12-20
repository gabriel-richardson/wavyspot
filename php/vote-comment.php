<?php
include('classes/DB.php');
include('classes/Login.php');
if ($_POST['dir'] == "true") {
	if (DB::query('SELECT user_id FROM comment_votes WHERE comment_id=:comment_id AND user_id=:user_id AND vote_value=1', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()))) {
		DB::query('DELETE FROM comment_votes WHERE comment_id=:comment_id AND user_id=:user_id AND vote_value=1', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE comments SET points=points-1 WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']));
		$points = DB::query('SELECT points FROM comments WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']))[0]['points'];
	} else if (DB::query('SELECT user_id FROM comment_votes WHERE comment_id=:comment_id AND user_id=:user_id AND vote_value=-1', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()))) {
		DB::query('DELETE FROM comment_votes WHERE comment_id=:comment_id AND user_id=:user_id AND vote_value=-1', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('INSERT INTO comment_votes VALUES (null, :comment_id, :user_id, 1)', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE comments SET points=points+2 WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']));
		$points = DB::query('SELECT points FROM comments WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']))[0]['points'];
	} else {
		DB::query('INSERT INTO comment_votes VALUES (null, :comment_id, :user_id, 1)', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE comments SET points=points+1 WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']));
		$points = DB::query('SELECT points FROM comments WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']))[0]['points'];
	}
	if ($points == 1) {
		echo $points, ' point';
	} else {
		echo $points, ' points';
	}
} else {
	if (DB::query('SELECT user_id FROM comment_votes WHERE comment_id=:comment_id AND user_id=:user_id AND vote_value=-1', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()))) {
		DB::query('DELETE FROM comment_votes WHERE comment_id=:comment_id AND user_id=:user_id AND vote_value=-1', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE comments SET points=points+1 WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']));
		$points = DB::query('SELECT points FROM comments WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']))[0]['points'];
	} else if (DB::query('SELECT user_id FROM comment_votes WHERE comment_id=:comment_id AND user_id=:user_id AND vote_value=1', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()))) {
		DB::query('DELETE FROM comment_votes WHERE comment_id=:comment_id AND user_id=:user_id AND vote_value=1', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('INSERT INTO comment_votes VALUES (null, :comment_id, :user_id, -1)', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE comments SET points=points-2 WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']));
		$points = DB::query('SELECT points FROM comments WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']))[0]['points'];
	} else {
		DB::query('INSERT INTO comment_votes VALUES (null, :comment_id, :user_id, -1)', array(':comment_id'=>$_POST['comment_id'], ':user_id'=>Login::isLoggedIn()));
		DB::query('UPDATE comments SET points=points-1 WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']));
		$points = DB::query('SELECT points FROM comments WHERE id=:comment_id', array(':comment_id'=>$_POST['comment_id']))[0]['points'];
	}
	if ($points == 1) {
		echo $points, ' point';
	} else {
		echo $points, ' points';
	}
}
?>