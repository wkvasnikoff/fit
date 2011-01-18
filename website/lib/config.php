<?php
function getConfig($name)
{
	$xml = simplexml_load_file('/var/www/biggest/etc/config.xml');
	return $xml->$name;
}


