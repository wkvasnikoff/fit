<?php

function saveMinMax(&$state, $datapoint, $minMax)
{
	if($minMax == 'min') {
		if($datapoint < $state) {
			$state = $datapoint - 0.1;
		}
	} elseif($minMax == 'max') {
		if($datapoint > $datapoint) {
			$state = $datapoint + 0.1;
		}
	}
	
}

function getChartData()
{
	$output = array(
		'names' => array(),
		'data' => array(),
		'maxX' => 0,
		'minY' => 0,
		'maxY' => 0.1,
		'ticks' => '',
	);

	$db = new Database('biggest');
	$rows = $db->query("select ID, realname from user order by realname");

	foreach($rows as $row){
		$output['names'][]  = '{label: "' . $row['realname'] . '"}';
		$weighinRows = $db->query("select datediff(date, '2011-01-16') as day, weight from weighin where userID = %d", array($row['ID']));

		if($weighinRows) {
			$initialWeight = $weighinRows[0]['weight'];
			$points = array();
			foreach($weighinRows as $r) {
				$day = $r['day'];
				$change = number_format( (($r['weight'] - $initialWeight)*100.0)/ $initialWeight, 2);
				$points[] = "[$day, $change]";

				saveMinMax($output['minY'], $change, 'min');
				saveMinMax($output['maxY'], $change, 'max');
				saveMinMax($output['maxX'], $day, 'max');
			}

			$points = '[' . join(',', $points) . ']';
			$output['data'][] = $points;
		} else {
			$output['data'][] = '[[]]';
		}
	}

	$output['data'] = join(',', $output['data']);
	$output['names'] = join(',', $output['names']);

	$numTicks = ($output['maxX'] / 7) + 1;
	for($i=0;$i<=$numTicks; $i++) {
		$output['ticks'][] = $i*7;
	}

	$output['ticks'] = '[' . join(',', $output['ticks']) . ']';
	



	return $output;
}
