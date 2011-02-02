<?php
namespace db;

class User extends \DB_Abstract
{
	protected static $keys = array('ID');
	protected static $tableName = 'user';

	protected $m_ID;
	protected $m_realname;
	protected $m_username;
	protected $m_password;
	protected $m_height;	

	public function getUserTableData()
	{
		$height = $this->height;
		$weights = Weight::getByQuery("select * from weighin where userID = '%s'", array($this->ID));

		if($weights) {
			$initialWeight = $weights[0]->weight;
		}
		
		$output = array();
		foreach($weights as $weight) {

			$lb = $weight->weight;
			$bmi = ($lb * 703) / ($height * $height);
			$date = date('F d, Y', strtotime($weight->date));
			
			$output[] = array(
				'date' => date('F d, Y', strtotime($weight->date)),
				'weight' => $lb,
				'bmi' => number_format($bmi, 2),
				'bodyChange' => number_format((($initialWeight - $lb)*100.0) / $initialWeight, 2),
			);
		}
		return $output;
	}
}

