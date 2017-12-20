<?php
include('classes/DB.php');
include('classes/Login.php');

if (isset($_POST['comment-submit'])) {
	$body = $_POST['comment'];
	$user_id = Login::isLoggedIn();
	$post_id = $_GET['post_id'];
	if (isset($_GET['parent_id'])) {
		$parent_id = $_GET['parent_id'];
		$receiver_id = DB::query('SELECT user_id FROM comments WHERE id=:id', array(':id'=>$parent_id))[0]['user_id'];
		$receiver_username = DB::query('SELECT username FROM users WHERE id=:id', array(':id'=>$receiver_id))[0]['username'];
		DB::query('INSERT INTO comments VALUES(null, :body, NOW(), :user_id, :post_id, :parent_id, 1)', array(':body'=>$body, ':user_id'=>$user_id, ':post_id'=>$post_id, ':parent_id'=>$parent_id));
		if ($receiver_id != $user_id && $receiver_username != '') {
			DB::query('INSERT INTO notifications VALUES(null, :receiver_id, :sender_id, :post_id, 1, :body, NOW())', array(':receiver_id'=>$receiver_id, ':sender_id'=>$user_id, ':post_id'=>$post_id, ':body'=>$body));
		}
	} else {
		$receiver_id = DB::query('SELECT user_id FROM posts WHERE id=:id', array(':id'=>$post_id))[0]['user_id'];
		$receiver_username = DB::query('SELECT username FROM users WHERE id=:id', array(':id'=>$receiver_id))[0]['username'];
		DB::query('INSERT INTO comments VALUES(null, :body, NOW(), :user_id, :post_id, null, 1)', array(':body'=>$body, ':user_id'=>$user_id, ':post_id'=>$post_id));
		if ($receiver_id != $user_id && $receiver_username != '') {
			DB::query('INSERT INTO notifications VALUES(null, :receiver_id, :sender_id, :post_id, 2, :body, NOW())', array(':receiver_id'=>$receiver_id, ':sender_id'=>$user_id, ':post_id'=>$post_id, ':body'=>$body));
		}
	}
	$comment_id = DB::query('SELECT id FROM comments WHERE id=(SELECT MAX(id) FROM comments)')[0]['id'];
	DB::query('INSERT INTO comment_votes VALUES (null, :comment_id, :user_id, 1)', array(':comment_id'=>$comment_id, ':user_id'=>$user_id));
	header('Location: comment.php?post_id='.$post_id.'&sort=hot');
}
?>