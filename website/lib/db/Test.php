<?php
namespace db;

class Test extends \DB_Abstract
{
	protected static $keys = array('ID');
	protected static $tableName = 'test';

	protected $m_ID;
	protected $m_msg;	
}


