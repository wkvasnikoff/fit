<?php
require_once 'lib/db_abstract.php';

class User extends DB_Abstract
{
	protected static $keys = array('ID');
	protected static $tableName = 'user';

	protected $m_ID;
	protected $m_realname;
	protected $m_username;
	protected $m_password;
	protected $m_height;	
	
	
}


