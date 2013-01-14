<?php

class Config
{
	public static function getConfig($configName)
	{
		$xml = simplexml_load_file('/var/www/html/biggest/etc/config.xml');
		return $xml->$configName;
	}	
}



