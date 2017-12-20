<?php
include('classes/DB.php');
$id = $_POST['id'];
DB::query('DELETE FROM comments WHERE id=:id', array(':id'=>$id));
?>