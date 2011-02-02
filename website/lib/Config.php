<?php

class Config
{
	public static function getConfig($configName)
	{
		$xml = simplexml_load_file('/var/www/biggest/etc/config.xml');
		return $xml->$configName;
	}	
}



