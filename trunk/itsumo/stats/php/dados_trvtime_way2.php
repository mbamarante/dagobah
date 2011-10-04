<?php

$con = mysql_connect("localhost", "root", "root");
$db = mysql_select_db("itsumo", $con);

function getConfigExperimento($replanner, $global, $communication) {

	$res = "not defined";

	if (!$replanner && $global && !$communication) {
		$res = "GNR";
	} else
	if ($replanner && $global && !$communication) {
		$res = "GR";
	} else
	if ($replanner && !$global && !$communication) {
		$res = "L";
	} else
	if ($replanner && !$global && $communication) {
		$res = "L+C";
	}

	return $res;
}

//$replanner = 0;
$global = 1;
$communication = 0;
$odsize = 25000;
$network = "poa.notl";

$big_stack = array();

//foreach (array(100, 1000) as $steps_point) {
foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
	$stack = array($steps_point);

	//echo "<br>" . $steps_point . "<br>";

	foreach (array(0, 1) as $communication) {

		foreach (array(1, 0) as $global) {

			if ($communication && $global) {
				continue;
			}

			foreach (array(0, 1) as $replanner) {

				if (!$replanner && !$global) {
					continue;
				}

				/* tempo de viagem */
				$sql = "
						select avg(media) as mean_rounds, STDDEV(media) as sd from (
							select trvtime.round, avg(trvtime.trv_time) as media
								from trvtime, 
								(select round, driver_id, avg(trv_time) as mean, count(trv_time) as ct from trvtime where 
								replanner = $replanner and global = $global and communication = $communication and odsize = $odsize and network = \"$network\"
								group by round, driver_id
								having mean > $steps_point) base
							where 
								base.driver_id = trvtime.driver_id and 
								base.round = trvtime.round and
								replanner = $replanner and global = $global and communication = $communication and 
								odsize = $odsize and network = \"$network\"
								group by trvtime.round
						) as sd";
					
				$res = mysql_query($sql);
				
				//$position = $steps_point . "_" . $replanner . "_" . $global . "_" . $communication;
				//$data[$position] = mysql_result($res, 0, "media");
				//echo $position . " = " . $data[$position] . "<br>";
					
				//echo $steps_point . "_" . $replanner . "_" . $global . "_" . $communication . " = ";
				#echo getConfigExperimento($replanner, $global, $communication) . " | " . $steps_point . " | ";
				#echo mysql_result($res, 0, "media") . " | ";
				$media_tempo[getConfigExperimento($replanner, $global, $communication)."_".$steps_point] = mysql_result($res, 0, "mean_rounds");
				$desvio_tempo[getConfigExperimento($replanner, $global, $communication)."_".$steps_point] = mysql_result($res, 0, "sd");

			}

		}
	}

	//array_push($big_stack, $stack);
}

foreach (array("GNR", "GR", "L", "L+C") as $config) {

	echo $config . "|STEPS|";
	foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
		echo $steps_point. "|";
	}

	echo "<br>";

	echo $config . "|MEAN|";
	foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
			
		echo round($media_tempo[$config."_".$steps_point],2) . "|";
	}

	echo "<br>";

	echo $config . "|SD|";
	foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
			
		echo round($desvio_tempo[$config."_".$steps_point],2) . "|";
	}

	echo "<br>";

}

echo "----------------------------------<br>";

foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
	echo $steps_point. " ";
}

echo "<br>";

foreach (array("GNR", "GR", "L", "L+C") as $config) {

	foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
			
		echo round($media_tempo[$config."_".$steps_point],2) . " ";
	}

	echo "<br>";

}

foreach (array("GNR", "GR", "L", "L+C") as $config) {

	foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
			
		echo round($desvio_tempo[$config."_".$steps_point],2) . " ";
	}

	echo "<br>";
}

?>
