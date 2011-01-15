<?php
session_start();

require_once 'lib/database.php';

$headers = array(
	'<link rel="stylesheet" type="text/css" href="css/main.css" />'
);

// Already Logged in
if(isset($_SESSION['userID'])) {
	header('Location: mypage.php');
	exit;
}

# login
$error = '';
if( isset($_POST['username']) && isset($_POST['password']) ) {
	$db = new Database('biggest');
	$rows = $db->query("select ID, realname from user where username = '%s' and password='%s'",
		array($_POST['username'], $_POST['password']));

	if(count($rows) > 0) {
		$row = $rows[0];
		$_SESSION['userID'] = intval($row['ID']);
		$_SESSION['realname'] = $row['realname'];
		header('Location: mypage.php');
		exit;
	} else {
		$error = '<div class="error">Your username or password is incorrect.</div>';
	}
}

include('tmpl/header.php');
?>

<h1>Biggest Loser</h1>

<form method="POST" class="login" >
	<div><span>Username</span><input name="username" id="username" type="text" /></div>
	<div><span>Password</span><input name="password" id="password" type="password" /></div>
	<br />
	<button type="submit">Login</button>
</form>

<?= $error ?>

<?php
include 'tmpl/footer.php';
?>
</html>
