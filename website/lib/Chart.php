<?php

class Chart
{
	public static function getChartPercent()
	{
		$output = array(
			'names' => array(),
			'data' => array(),
			'maxX' => 14,
			'minY' => -10,
			'maxY' => 1,
			'ticks' => array(),
		);

		$db = new Database('biggest');
		$rows = $db->query("select ID, realname from user order by realname");

		foreach($rows as $row){
			$output['names'][]  = '{label: "' . $row['realname'] . '"}';
			$weighinRows = $db->query(
				"select datediff(date, '2011-01-16') as day, weight from weighin where userID = %d",
				array($row['ID']));

			if($weighinRows) {
				$initialWeight = $weighinRows[0]['weight'];
				$points = array();
				foreach($weighinRows as $r) {
					$day = $r['day'];
					$change = number_format( (($r['weight'] - $initialWeight)*100.0)/ $initialWeight, 2);
					$points[] = "[$day, $change]";

					self::saveMinMax($output['minY'], $change, 'min');
					self::saveMinMax($output['maxY'], $change, 'max');
					self::saveMinMax($output['maxX'], $day, 'max');
				}

				$points = '[' . join(',', $points) . ']';
				$output['data'][] = $points;
			} else {
				$output['data'][] = '[[]]';
			}
		}

		$output['data'] = join(',', $output['data']);
		$output['names'] = join(',', $output['names']);

		# weekly
		for($i=0; $i <= $output['maxX']+1+(floor($output['maxX']*0.20)+7 ); $i+=7) {
			$output['ticks'][] = $i;
		}

		$output['ticks'] = '[' . join(',', $output['ticks']) . ']';
	
		$js = '	
		<script type="text/javascript">
		$.jqplot("chartdiv",  [
			' . $output['data'] . '
			], {
			title: "Percent Change Comparison",
			axes: {
				yaxis:{
					min: ' . $output['minY'] . ', 
					max: ' . $output['maxY'] . '
				}, 
				xaxis:{
					min: 0,
					max: 30,
					ticks: ' . $output['ticks'] . '
				}
			},
			series: [ ' . $output['names'] . '],
			legend: { show: true}
		});
		</script>';
	
		
		return $js;
	}

	private static function saveMinMax(&$state, $value, $minMax)
	{
		if($minMax == 'min') {
			if($value < $state) {
				$state = $value;
			}
		} elseif($minMax == 'max') {
			if($value > $state) {
				$state = $value;
			}
		}
	}
}
	

