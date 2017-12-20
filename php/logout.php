<?php
include('classes/DB.php');
include('classes/Login.php');

if (isset($_COOKIE['SNID'])) {
	DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
}
setcookie('SNID', '1', time()-3600);
setcookie('SNID_', '1', time()-3600);
header('Location: index.php');
?>