<?php
include('classes/DB.php');
$id = $_POST['id'];
DB::query('DELETE FROM posts WHERE id=:id', array(':id'=>$id));
DB::query('DELETE FROM comments WHERE post_id=:id', array(':id'=>$id));
?>