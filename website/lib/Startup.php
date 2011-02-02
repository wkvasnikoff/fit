<?php
session_start();

function __autoload($class)
{
	$parts = explode('\\', $class);
	$path = 'lib/' . join('/', $parts) . '.php';
	require_once($path);
}


