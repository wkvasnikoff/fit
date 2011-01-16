<?php

function getChartData()
{
	$output = array(
		'names' => array(),
		'data' => array(),
	);

	$db = new Database('biggest');
	$rows = $db->query("select ID, realname from user order by realname");

	foreach($rows as $row){
		$output['names'][]  = '{label: "' . $row['realname'] . '"}';
		$weighinRows = $db->query("select datediff(date, '2011-01-10') as day, weight from weighin where userID = %d", array($row['ID']));


		if($weighinRows) {
			$initialWeight = $weighinRows[0]['weight'];
			$points = array();
			foreach($weighinRows as $r) {
				$day = $r['day'];
				$change = number_format( (($r['weight'] - $initialWeight)*100.0)/ $initialWeight, 2);
				$points[] = "[$day, $change]";
			}

			$points = '[' . join(',', $points) . ']';
			$output['data'][] = $points;
		} else {
			$output['data'][] = '[[]]';
		}


	}

	$output['data'] = join(',', $output['data']);
	$output['names'] = join(',', $output['names']);

	return $output;
}


