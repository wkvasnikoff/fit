<?php
session_start();

function __autoload($class)
{
	$parts = explode('\\', $class);
	require_once('lib/' . join('/', $parts) . '.php');
}


