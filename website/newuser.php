<?php
require_once 'lib/Startup.php';

$headers = array(
    '<link rel="stylesheet" type="text/css" href="css/main.css" />'
);

// Already Logged in
if (isset($_SESSION['userID'])) {
    header('Location: mypage.php');
    exit;
}

# login
$values = array('secret' => '', 'realname' => '', 'username' => '', 'password' => '', 'feet' => '', 'inches' => '');
$msg = '';
$secret = Config::getConfig('secret');
if (isset($_POST['secret']) && $_POST['secret'] === $secret) {
    $user = new db\User();
    $user->realname = $_POST['realname'];
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    $user->height = $_POST['feet']*12 + $_POST['inches'];
    $user->save();
    $msg = '<div>User Created, click <a href="/">here</a> to login</div>';
} elseif (isset($_POST['secret'])) {
    $msg = '<div class="error">Secret is incorrect</div>';
    foreach ($_POST as $key => $value) {
        if (array_key_exists($key, $value)) {
            $values[$key] = $value;
        }
    }
}

include('tmpl/header.php');
# ----------------------------------------------------------
?>

<h1>Biggest Loser</h1>

<h2>Create User</h2>

<form method="POST" class="new-user" >
    <div><span>Secret Code</span><input name="secret" type="text" value="<?= $values['secret'] ?>" /></div>
    <div><span>Real Name</span><input name="realname" id="realname" type="text" value="<?= $values['realname'] ?>" /></div>
    <div><span>Your Username</span><input name="username" id="username" type="text" value="<?= $values['username'] ?>" /></div>
    <div><span>Your Password</span><input name="password" id="password" type="password" value="<?= $values['password'] ?>" /></div>
    <div>
        <span>Your Height</span>
            <input style="width: 30px;" name="feet" type="text" value="<?= $values['feet'] ?>" /> feet 
            <input style="width: 30px;" name="inches" type="text" value="<?= $values['inches'] ?>" /> inches
    </div><br />

    <button type="submit">Create User</button>
</form>

<?= $msg ?>

<?php
include 'tmpl/footer.php';
