<?php
require_once 'lib/Startup.php';

$headers = [
    '<link rel="stylesheet" type="text/css" href="css/main.css" />'
];

// Already Logged in
if (isset($_SESSION['userID'])) {
    header('Location: mypage.php');
    exit;
}

# login
$msg = '';
if (isset($_POST['username']) && isset($_POST['password'])) {
    $users = db\User::getByQuery("select * from user where username = '%s' and password='%s'",
        [$_POST['username'], $_POST['password']]);
    if ($users) {
        $user = $users[0];
        $_SESSION['userID'] = intval($user->ID);
        $_SESSION['realname'] = $user->realname;
        header('Location: mypage.php');
        exit;
    } else {
        $msg = '<div class="error">Your username or password is incorrect.</div>';    
    }
}

include('tmpl/header.php');
# ---------------------------------------------------------------------
?>

<h1>Biggest Loser</h1>

<form method="POST" class="login" >
    <div><span>Username</span><input name="username" id="username" type="text" /></div>
    <div><span>Password</span><input name="password" id="password" type="password" /></div>
    <br />
    <button type="submit">Login</button>
</form>

<?= $msg ?>
<br /><br />
<a href="newuser.php">Create User</a>

<?php
include 'tmpl/footer.php';
