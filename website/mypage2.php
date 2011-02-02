<?php
require_once 'lib/Startup.php';

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

// ----------------------- Form Posts --------------------------------
// -------- Messages -------------
if(isset($_POST['message'])) {
	$message = new db\Message();
	$message->userID = $_SESSION['userID'];
	$message->message = $_POST['message'];
	$message->date = date('Y-m-d G:i:s', time());
	$message->save();
	header('Location: /mypage.php');
	exit;
}

# ------ record weight ------------
$msg = '';
if( isset($_POST['weight'])) {
	if(!preg_match('/^\d+(\.\d+)?$/', $_POST['weight'])) {
		$msg = '<div class="error">Please enter you valid weight.</div>';
	} else {
		$newWeight = number_format(floatval($_POST['weight']), 2);

		$weights = db\Weight::getByQuery('select * from weighin where userID = %d order by date desc limit 1', array($_SESSION['userID']));
		$sameDay = false;
		
		if($weights) {
			$prevDate = date('Y-m-d', strtotime($weights[0]->date));
			$now = date('Y-m-d', time());
			if($prevDate === $now) {
				$sameDay = true;
			}
		}
	
		$weight = new db\Weight();
		$weight->userID = $_SESSION['userID'];
		$weight->weight = $newWeight;
		$weight->date = date('Y-m-d G:i:s', time());
	
		if($sameDay) {
			$weight->ID = $weights[0]->ID;
		}
		$weight->save();
		
		header('Location: /mypage.php');
		exit;
	}
}
# -------------- End Forms Posts -------------------


# get weights
$user = db\User::getByKey($_SESSION['userID']);
$weightInfo = $user->getUserTableData();

# days left
$daysLeft = ceil((strtotime(Config::getConfig('endDate')) - time() ) / (60*60*24));

# messages
$messages = db\Message::getByQuery('select * from message order by date desc');

include('tmpl/header.php');
#------------------------------------------------------------------------------------------------
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

<? if($weightInfo): ?>
<h2>My Log</h2>
<table class="log">
<tr><th>Date</th><th>Weight</th><th>BMI</th><th>Percent Loss</th></tr>
<?foreach($weightInfo as $row): ?>
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
			<div>
				<b><?= htmlentities($message->getUserRealName()); ?>: </b>
				<?= htmlentities($message->message) . ' - ' . $message->dateFormatted() ?>
			</div><br />
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
<?= Chart::getChartPercent() ?>
<hr />

<h1>Rules</h1>
<ol>
	<li>You must enter you're weight at minimum once a week on Sundays.</li>
	<li>Prizes: 1st $160, 2nd $80, and 3rd $40.</li>
</ol>

<?php
include 'tmpl/footer.php';
