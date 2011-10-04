<?php

$con = mysql_connect("localhost", "root", "root");
//$con = mysql_connect("192.168.1.103", "root", "root");
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
$odsize = 20000;
$network = "poa.notl";

$big_stack = array();

$intervalo = array(0, 300, 600, 900, 1200, 1500, 1800, 2100);

//foreach (array(100, 1000) as $steps_point) {
foreach ($intervalo as $steps_point) {
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

				$sql = "
						select avg(ctdrivers) as mean, STDDEV(ctdrivers) as sd from (
							select count(*) as ctdrivers from (
								select trvtime.round, count(*) as numdrivers from trvtime, (
									select round, driver_id from trvtime where 
											replanner = 0 and global = 1 and communication = 0 and odsize = $odsize and network = \"$network\"
											group by round, driver_id
											having avg(trv_time) >= $steps_point and avg(trv_time) < $steps_point+300
									) as base 
									where
											base.round = trvtime.round and 
											base.driver_id = trvtime.driver_id and 
											replanner = $replanner and global = $global and communication = $communication and 
											odsize = $odsize and network = \"$network\"
									group by trvtime.round, trvtime.driver_id
								) as t 
							group by round 
						) as t2";
								
				$res = mysql_query($sql);
				
				//$position = $steps_point . "_" . $replanner . "_" . $global . "_" . $communication;
				//$data[$position] = mysql_result($res, 0, "media");
				//echo $position . " = " . $data[$position] . "<br>";
					
				//echo $steps_point . "_" . $replanner . "_" . $global . "_" . $communication . " = ";
				#echo getConfigExperimento($replanner, $global, $communication) . " | " . $steps_point . " | ";
				#echo mysql_result($res, 0, "media") . " | ";
				$media_tempo[getConfigExperimento($replanner, $global, $communication)."_".$steps_point] = mysql_result($res, 0, "mean");
				$desvio_tempo[getConfigExperimento($replanner, $global, $communication)."_".$steps_point] = mysql_result($res, 0, "sd");

			}

		}
	}

	//array_push($big_stack, $stack);
}

foreach (array("GNR", "GR", "L", "L+C") as $config) {

	echo $config . "|STEPS|";
	foreach ($intervalo as $steps_point) {
		echo $steps_point. "|";
	}

	echo "<br>";

	echo $config . "|MEAN|";
	foreach ($intervalo as $steps_point) {
			
		echo round($media_tempo[$config."_".$steps_point],2) . "|";
	}

	echo "<br>";

	echo $config . "|SD|";
	foreach ($intervalo as $steps_point) {
			
		echo round($desvio_tempo[$config."_".$steps_point],2) . "|";
	}

	echo "<br>";

}

echo "----------------------------------<br>TRVTime<br>";

foreach ($intervalo as $steps_point) {
	echo $steps_point. " ";
}

echo "<br>";

foreach (array("GNR", "GR", "L", "L+C") as $config) {

	foreach ($intervalo as $steps_point) {
			
		echo round($media_tempo[$config."_".$steps_point],2) . " ";
	}

	echo "<br>";

}

echo "<br>Percentual<br>";

foreach (array("GNR", "GR", "L", "L+C") as $config) {

	foreach ($intervalo as $steps_point) {
			
		echo round($media_tempo[$config."_".$steps_point],2)/($odsize/100) . " ";
	}

	echo "<br>";

}

echo "<br>SD<br>";

foreach ($intervalo as $steps_point) {
	echo $steps_point. " ";
}

echo "<br>";

foreach (array("GNR", "GR", "L", "L+C") as $config) {

	foreach ($intervalo as $steps_point) {
			
		echo round($desvio_tempo[$config."_".$steps_point],2) . " ";
	}

	echo "<br>";
}

?>
