<?php
session_start();

require_once 'lib/database.php';

$headers = array(
	'<link rel="stylesheet" type="text/css" href="css/main.css" />'
);

// Already Logged in
if(!isset($_SESSION['userID'])) {
	header('Location: /');
	exit;
}

# login
$error = '';
if( isset($_POST['username']) && isset($_POST['password']) ) {
	$db = new Database('biggest');

}

include('tmpl/header.php');
?>

<h1>Biggest Loser</h1>

<form method="POST">
	<h2>Enter Weight</h2>
	<input type="text" name="weight" /> <br /><br /> 
	<button type="submit">Submit</button>
</form>

<hr />

<h2>My Log</h2>
<table class="log">
<tr><th>Date</th><th>Weight</th><th>BMI</th><th>Body Weight Change</th></tr>
<tr><td>Date</td><td>Weight</td><td>BMI</td><td>Body Weight Change</td></tr>
<tr><td>Date</td><td>Weight</td><td>BMI</td><td>Body Weight Change</td></tr>
<tr><td>Date</td><td>Weight</td><td>BMI</td><td>Body Weight Change</td></tr>
<tr><td>Date</td><td>Weight</td><td>BMI</td><td>Body Weight Change</td></tr>
<tr><td>Date</td><td>Weight</td><td>BMI</td><td>Body Weight Change</td></tr>
</tr>

</table>



<?php
include 'tmpl/footer.php';
?>
</html>
