<?php
namespace db;

class Weight extends \DB_Abstract
{
	protected static $keys = array('ID');
	protected static $tableName = 'weighin';

	protected $m_ID;
	protected $m_userID;
	protected $m_weight;
	protected $m_date;

}

