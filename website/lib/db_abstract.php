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
		return $this->$name;
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

	public static function save()
	{
		# determine if insert or update
		
	}

}


