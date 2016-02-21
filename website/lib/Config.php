<?php

class Config
{
	public static function getConfig($configName)
	{
		$xml = simplexml_load_file(__DIR__ . '/../../etc/config.xml');
		return $xml->$configName;
	}
}



