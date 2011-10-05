<?php

$con = mysql_connect("localhost", "root", "root");
$db = mysql_select_db("itsumo", $con);

//$replanner = 0;
$global = 1;
$communication = 0;
$odsize = 25000;
$network = "poa.notl";

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

$big_stack = array();

//foreach (array(1000) as $steps_point) {
foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
	$stack = array($steps_point);

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
					select avg(trvtime.trv_time) as media
						from trvtime, 
						(select driver_id, avg(trv_time) as mean, count(trv_time) as ct from trvtime where 
						replanner = 0 and global = 1 and communication = 0 and odsize = $odsize and network = \"$network\"
						group by driver_id
						having mean > $steps_point) base
					where 
						base.driver_id = trvtime.driver_id and 
						replanner = $replanner and global = $global and communication = $communication and 
						odsize = $odsize and network = \"$network\"";

				$res = mysql_query($sql);

				echo mysql_error();
				
				$position = $steps_point . "_" . $replanner . "_" . $global . "_" . $communication;

				$data[$position] = mysql_result($res, 0, "media");

				//echo $position . " = " . $data[$position] . "<br>";
				
				array_push($stack, $data[$position]);
				//echo $steps_point . " " . mysql_result($res, 0, "media") . "<br>"; // . " " . getConfigExperimento($replanner, $global, $communication) . "<br>";

			}

		}
	}
	
	
	array_push($big_stack, $stack);
}


//fwrite($f, "TEXT TO WRITE");
echo "write to file...<br>"; 
for ($j=1;$j<=4;$j++) {
	
	switch ($j) {
		case 1: $config = "gnr"; break;
		case 2: $config = "gr"; break;		
		case 3: $config = "l"; break;
		case 4: $config = "l+c"; break;
	}
	
  $fname = "/home/maicon/Desktop/xmgracedata/xmgrace.25k.".$config.".dat";
	$f = fopen($fname, "w");
	echo "generating " . $fname . "...<br>";
	
	foreach ($big_stack as $i => $value) {
	    //echo $i . " - " . $value . "<br>";
	    foreach ($value as $x => $newvalue) {
	    	if ($x == 0) {
	    		echo $newvalue . " ";
	    		fwrite($f, $newvalue . " ");
	    	} else 
	    	if ($x == $j) {
	    		echo $newvalue . "<br>";
	    		fwrite($f, $newvalue . "\n");
	    	}
	    } 
	}
	echo " done!<br><br>";

	fclose($f); 
}



?>
