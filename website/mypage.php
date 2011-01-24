<?php
session_start();

require_once 'lib/database.php';
require_once 'lib/weight.php';
require_once 'lib/config.php';

$headers = array(
	'<!--[if IE]><script language="javascript" type="text/javascript" src="js/excanvas.js"></script><![endif]-->',
	'<script type="text/javascript" src="js/jquery-1.4.2.min.js" ></script>',
	'<script type="text/javascript" src="js/jquery.jqplot.min.js"></script>',
	'<link rel="stylesheet" type="text/css" href="css/main.css" />',
	'<link rel="stylesheet" type="text/css" href="js/jquery.jqplot.css" />',
);

// Already Logged in
if(!isset($_SESSION['userID'])) {
	header('Location: /');
	exit;
}

# logout
$msg = '';
if( isset($_GET['logout']) && $_GET['logout'] == 1) {
	session_destroy();
	header('Location: /');
	exit;
}

$db = new Database('biggest');

// -------- Messages -------------
if(isset($_POST['message'])) {
	$db->query("insert into message (userID, message) values (%d, '%s')", 
		array($_SESSION['userID'], $_POST['message']), false);
	header('Location: /mypage.php');
	exit;
}
$messages = $db->query("select realname, message, date_format(date, '%%m-%%d-%%Y %%h:%%i%%p') datef from message m " . 
	'join user u on m.userID = u.ID order by date desc');


// ---------------

# record weight
$msg = '';
if( isset($_POST['weight'])) {
	if(!preg_match('/^\d+(\.\d+)?$/', $_POST['weight'])) {
		$msg = '<div class="error">Please enter you valid weight.</div>';
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
		
		$weight = floatval($_POST['weight']);
		if($sameDay) {
			$db->query( "update weighin set weight = %f, date = now() where " . 
				"userID = %d and date_format(date, '%%Y-%%m-%%d') = date_format(now(), '%%Y-%%m-%%d')",
				array($weight, $_SESSION['userID'], $weight), false);
			
		} else {
			$db->query("insert into weighin (userID, weight) values (%d, %f)", array($_SESSION['userID'], number_format($weight, 2)), false);
		}
		header('Location: /mypage.php');
		exit;
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

# chart data
$chartData = getChartData();

# days left
$daysLeft = ceil((strtotime(getConfig('endDate')) - time() ) / (60*60*24));

include('tmpl/header.php');
?>

<a style="float:right;" href="mypage.php?logout=1">Log Out</a>

<h1 class="title">Biggest Loser</h1>

<div class="bmi-key">
	<span class="days-left">Days Left: <?= $daysLeft ?></span>
	
	<h3>BMI Categories:</h3>
	<ul>
		<li>Under Weight = 18.5 or less</li>
		<li>Normal weight = 18.5 to 24.9</li>
		<li>Over weight = 25 to 29.9</li>
		<li>Obesity = 30 or greater</li>
	</ul>
</div>



<div class="weight-form">
	<form method="POST">
		<b>Enter Weight</b>&nbsp; 
		<input type="text" name="weight" /> <button type="submit">Submit</button>
	</form>
	<?= $msg ?>
</div>


<? if($tableInfo): ?>
<h2>My Log</h2>
<table class="log">
<tr><th>Date</th><th>Weight</th><th>BMI</th><th>Percent Loss</th></tr>
<?foreach($tableInfo as $row): ?>
	<tr>
		<td><?=$row['date'] ?></td>
		<td><?=$row['weight']?>lb</td>
		<td><?=$row['bmi']?></td>
		<td><?=$row['bodyChange'] ?>%</td>
	</tr>
<?endforeach?>
</table>
<? endif ?>
<br />
<hr style="clear: both;" />

<div>
	<div style="width: 500px; float: right; max-height: 230px; overflow-y: auto; ">
		<?foreach($messages as $message): ?>
			<?= '<div><b>' . htmlentities($message['realname']) . ': </b>' . htmlentities($message['message']) . ' - ' . $message['datef'] . '</div><br />'?>
		<?endForeach?>
	</div>

	<form style="width: 400px;" method="POST">

		<h2>Sent Message To The Group</h2>
		<textarea name="message" style="width: 100%; height: 150px;"></textarea><br />
		<button type="submit">Send Message To Group</button>
	</form>

</div>



<hr style="clear: both;" />

<div id="chartdiv" style="height:450px;"></div>

<script type="text/javascript">
	$.jqplot('chartdiv',  [
		<?= $chartData['data'] ?>
		], {
		title: "Percent Change Comparison",
		axes: {
			yaxis:{
				min: <?= $chartData['minY'] ?>, 
				max: <?= $chartData['maxY']?>
			}, 
			xaxis:{
				min: 0,
				max: 30,
				ticks: <?= $chartData['ticks']?>
			}
		},
		series: [ <?= $chartData['names'] ?>],
		legend: { show: true}
	});
</script>

<hr />





<?php
include 'tmpl/footer.php';
?>
