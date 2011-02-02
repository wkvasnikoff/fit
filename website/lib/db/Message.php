<?php
namespace db;

class Message extends \DB_Abstract
{
	protected static $keys = array('ID');
	protected static $tableName = 'message';

	protected $m_ID;
	protected $m_userID;
	protected $m_message;
	protected $m_date;

	public function getUserRealName()
	{
		$user =  User::getByKey($this->userID);	
		return $user->realname;
	}

	public function dateFormatted()
	{
		return date('F jS, Y g:i a', strtotime($this->date));
	}
}

