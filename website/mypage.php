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

# logout
$error = '';
if( isset($_GET['logout']) && $_GET['logout'] == 1) {
	session_destroy();
	header('Location: /');
	exit;
}

$db = new Database('biggest');

# record weight
$error = '';
if( isset($_POST['weight'])) {
	if(!preg_match('/^\d+$/', $_POST['weight'])) {
		$error = '<div class="error">Please enter you valid weight.</div>';
	} else {
		$rows = $db->query('select date from weighin where userID = %d order by date desc limit 1', array($_SESSION['userID']));
		$sameDay = false;
		if($rows) {
			$prevDate = date('Y-m-d', strtotime($rows[0]['date']));
			$now = date('Y-m-d', time());
			if($prevDate === $now) {
				$sameDay = true;
			}
		}
		
		$weight = intval($_POST['weight']);
		if($sameDay) {
			$db->query( "update weighin set weight = %d, date = now() where " . 
				"userID = %d and date_format(date, '%%Y-%%m-%%d') = date_format(now(), '%%Y-%%m-%%d')",
				array($weight, $_SESSION['userID'], $weight), false);
			
		} else {
			$db->query("insert into weighin (userID, weight) values (%d, %d)", array($_SESSION['userID'], $weight), false);
		}
	}
}

# get weights
$rows = $db->query("select realname, height from user where ID = %d", array($_SESSION['userID']));
$userInfo = $rows[0];
$height = $userInfo['height'];
$rows = $db->query("select weight, date from weighin where userID = %d order by date", array($_SESSION['userID']));

if($rows) {
	$initialWeight = $rows[0]['weight'];
}

$tableInfo = array();
foreach($rows as $row) {
	$weight = $row['weight'];
	$bmi = ($weight * 703) / ($height * $height);
	$tableInfo[] = array(
		'date' => date('F d, Y', strtotime($row['date'])) ,
		'weight' => $weight,
		'bmi' => number_format($bmi, 2),
		'bodyChange' => number_format((($initialWeight - $row['weight'])*100.0) / $initialWeight, 2),
	);
}


include('tmpl/header.php');
?>

<a style="float:right;" href="mypage.php?logout=1">Log Out</a>

<h1>Biggest Loser</h1>

<form method="POST">
	<h2>Enter Weight</h2>
	<input type="text" name="weight" /> <br /><br /> 
	<button type="submit">Submit</button>
</form>

<?= $error ?>

<hr />

<h2>My Log</h2>
<table class="log">
<tr><th>Date</th><th>Weight</th><th>BMI</th><th>Body Weight Change</th></tr>
<?foreach($tableInfo as $row): ?>
	<tr>
		<td><?=$row['date'] ?></td>
		<td><?=$row['weight']?>lb</td>
		<td><?=$row['bmi']?></td>
		<td><?=$row['bodyChange'] ?>%</td>
	</tr>
<?endforeach?>
</table>



<?php
include 'tmpl/footer.php';
?>
