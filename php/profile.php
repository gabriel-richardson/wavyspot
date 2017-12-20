<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAVY / <?php echo strtoupper($_GET['username']) ?></title>
	<link rel="icon" href="images/wavyspot.ico">
	<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<script src="https://apis.google.com/js/api.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="javascript/select.js"></script>
</head>
<body>
	<div class="sorted-by">
		<p>Sorted by:</p>
		<select onchange="onChange(this)">
			<option><?php if ($_GET['sort'] == 'new' || $_['sort'] == '') { echo 'new'; } else { echo 'hot'; } ?></option>
			<option><?php if ($_GET['sort'] == 'new' || $_['sort'] == '') { echo 'hot'; } else { echo 'new'; } ?></option>
		</select>
		<hr/>
	</div>
<?php
include('index.php');
?>
</body>
</html>