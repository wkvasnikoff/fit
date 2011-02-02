<?php
namespace db;

require_once 'lib/db_abstract.php';

class Test extends \DB_Abstract
{
	protected static $keys = array('ID');
	protected static $tableName = 'test';

	protected $m_ID;
	protected $m_msg;	
}


