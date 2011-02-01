<?php
require_once 'lib/database.php';

abstract class DB_Abstract
{
	protected static $tableName;
	protected static $keys;
	
	public function __construct()
	{}	
	
	public function __get($name)
	{
		$name = "m_$name";
		if(isset($this->$name)) {
			return $this->$name;
		}
		return;
	}
	
	public function __set($name, $value)
	{
		$name = "m_$name";
		if(property_exists($this, "$name")) {
			$this->$name = $value;
		} else {
			echo "property $name does not exist on class";	
		}
	}
	
	public static function getByKey($keys)
	{
		if(!is_array($keys)) {
			$keys = array($keys);	
		}

		$sql = sprintf('select * from %s where ', static::$tableName);
		foreach(static::$keys as $key) {
			$sql .= " $key = '%s'";	
		}

		$objs = self::getByQuery($sql, $keys);
		if(count($objs) === 1) {
			return $objs[0];
		}

		return false;
	}

	public static function getByQuery($sql, array  $params=array())
	{
		$db = new Database('biggest');
		$rows = $db->query($sql, $params);
	
		$objs = array();

		foreach($rows as $row) {
			$class = get_called_class();
			$obj = new $class();
			foreach($row as $key => $value) {
				$obj->$key = $value;
			}
			$objs[] = $obj;
		}

		return $objs;
	}

	public function save()
	{
		$db = new Database('biggest');

		# get relevant properties
		$reflect = new ReflectionClass($this);
		$props = $reflect->getProperties(ReflectionProperty::IS_PROTECTED);
		$nameValue = array();
		foreach($props as $name => $value) {
			if(preg_match('/^m_/', $name)) {
				$nameValue[substr($name, 2)] = $value;
			}
		}

		# figure out if insert or update
		$op = 'update';
		foreach(static::$keys as $key) {
			if(!$this->$key) {
				$op = 'insert';
			}
		}

		if($op === 'insert') {
			$sql = "insert into {$this->tableName} ";
		
			

		} else {
			$sql = "update {$this->tableName} set ";
			
			# where clause
		}
	}

}


