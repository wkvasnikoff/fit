<?php

require_once 'lib/user.php';
require_once 'lib/test.php';



$test = Test::getByKey(array(1));


$test = new Test();
$test->msg = 'bob';
$test->save();
