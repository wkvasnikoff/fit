<?php

require_once 'lib/user.php';
require_once 'lib/test.php';



#$test = Test::getByKey(array(1));


$test = new Test();
$test->ID = 1;
$test->msg = 'al';
$test->save();
