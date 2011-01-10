<?php

$error = '';
# login
if(
	array_key_exists('username', $_POST) && 
	array_key_exists('password', $_POST)
) {
	echo 'test';


}



?><!DOCTYPE HTML>
<html>
	<head>
		<title>Biggest Loser</title>
		<link rel="stylesheet" type="text/css" href="css/main.css" />

	</head>
	<body>
		<h1>Biggest Loser</h1>

		<?= $error ?>

		<form method="POST" class="login" >
			<div><span>Username</span><input name="username" id="username" type="text" /></div>
			<div><span>Password</span><input name="password" id="password" type="password" /></div>
			<br />
			<button type="submit">Login</button>

		</form>

	</body>
</html>
