<?php

class Database
{
	private $connection;

	public function __construct($db)
	{
		$xml = simplexml_load_file(__DIR__ . '/../../etc/config.xml');
		$dbinfo = $xml->xpath("db/connection[@name='" . $db . "']");
		$dbinfo = $dbinfo[0];

		$this->connection = new mysqli(
			$dbinfo->host, 
			$dbinfo->username,
			$dbinfo->password,
			$dbinfo->dbname
		);
	}

	public function query($sql, $params = array(), $select=true)
	{
		foreach($params as &$param) {
			$param = $this->connection->real_escape_string($param);
		}

		$sql = vsprintf($sql, $params);
		$result = $this->connection->query($sql);

		if(!$select) {
			if($result === false) {
				echo $this->connection->error;
				exit;
			}
			return $result;
		}

		$rows = array();
		if(!$result) {
			return array();
		}

		while($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		
		return $rows;
	}
}

