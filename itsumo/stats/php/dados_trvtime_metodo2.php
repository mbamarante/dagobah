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
//$global = 1;
//$communication = 0;

$network = "poa.notl";

//foreach (array("u","nu") as $odtype) {
foreach (array("u") as $odtype) {

$home = "../poa".$odtype;

//	foreach (array(20000, 25000, 40000) as $odsize) {
	foreach (array(20000) as $odsize) {
//		$tabela="trvtime_".$odtype.$odsize;
		$tabela="travels_delay";

		echo $odtype."_".$odsize."\n";

		//$odsize = 25000;
		//$odtype = "u";

		$data_fname = "$home/trvtime.m2.".$odtype."_$odsize.dat";
		$error_fname = "$home/error.m2.".$odtype."_$odsize.dat";

		$big_stack = array();

		$intervalo = array(-1, 0, 300, 600, 900, 1200, 1500, 1800, 2100);


		//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
		foreach ($intervalo as $steps_point) {
			$stack = array($steps_point);

			echo $steps_point . "\n";
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
						select avg(media) as mean_rounds, STDDEV(media) as sd, avg(ds) dss from (
							select $tabela.round, avg(trvtime/num_trips) as media, base.ds
								from $tabela,
								(select round, driver_id, avg(trvtime/num_trips) as mean, count(trvtime) as ct, avg(distance/num_trips) ds from $tabela where
								replanner = 0 and global = 1 and communication = 0 and odsize = $odsize and network = \"$network\"
								and simulation like \"%.".$odtype."_%\"
								group by round, driver_id";
						if ($steps_point >= 0) {
							$sql .= "	having mean >= $steps_point and mean < $steps_point+300";
						}
						$sql .="			) base
							where
								base.driver_id = $tabela.driver_id and
								base.round = $tabela.round and
								replanner = $replanner and global = $global and communication = $communication and
								odsize = $odsize and network = \"$network\"
								and simulation like \"%.".$odtype."_%\"
								group by $tabela.round
						) as sd";

						#echo $sql; exit(0);
						$res = mysql_query($sql);
						echo mysql_error();

						//$position = $steps_point . "_" . $replanner . "_" . $global . "_" . $communication;
						//$data[$position] = mysql_result($res, 0, "media");
						//echo $position . " = " . $data[$position] . "<br>";
							
						//echo $steps_point . "_" . $replanner . "_" . $global . "_" . $communication . " = ";
						#echo getConfigExperimento($replanner, $global, $communication) . " | " . $steps_point . " | ";
						#echo mysql_result($res, 0, "media") . " | ";
						$media_tempo[getConfigExperimento($replanner, $global, $communication)."_".$steps_point] = mysql_result($res, 0, "mean_rounds");
						$desvio_tempo[getConfigExperimento($replanner, $global, $communication)."_".$steps_point] = mysql_result($res, 0, "sd");
						$distance[getConfigExperimento($replanner, $global, $communication)."_".$steps_point] = mysql_result($res, 0, "dss");
					}
				}
			}

			//array_push($big_stack, $stack);
		}

		//foreach (array("GNR", "GR", "L", "L+C") as $config) {
		//
		//	echo $config . "|STEPS|";
		//	//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
		//	foreach ($intervalo as $steps_point) {
		//		echo $steps_point. "|";
		//	}
		//
		//	echo "<br>";
		//
		//	echo $config . "|MEAN|";
		//	//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
		//	foreach ($intervalo as $steps_point) {
		//
		//		echo round($media_tempo[$config."_".$steps_point],2) . "|";
		//	}
		//
		//	echo "<br>";
		//
		//	echo $config . "|SD|";
		//	//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
		//	foreach ($intervalo as $steps_point) {
		//
		//		echo round($desvio_tempo[$config."_".$steps_point],2) . "|";
		//	}
		//
		//	echo "<br>";
		//
		//}

		echo "TRVTime<br>";
		$f = fopen($data_fname, "w");

		//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
		foreach ($intervalo as $steps_point) {
			echo $steps_point. " ";
			fwrite($f, $steps_point. " ");
		}

		echo "<br>";
		fwrite($f, "\n");

		foreach (array("GNR", "GR", "L", "L+C") as $config) {

			//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
			foreach ($intervalo as $steps_point) {
				echo round($media_tempo[$config."_".$steps_point],2) . " ";
				fwrite($f, round($media_tempo[$config."_".$steps_point],2) . " ");
			}

			echo "<br>";
			fwrite($f, "\n");

		}

		echo "<br>SD<br>";
		fclose($f);

		$f = fopen($error_fname, "w");

		//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
		foreach ($intervalo as $steps_point) {
			echo $steps_point. " ";
			fwrite($f, $steps_point. " ");
		}

		echo "<br>";
		fwrite($f, "\n");

		foreach (array("GNR", "GR", "L", "L+C") as $config) {

			//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
			foreach ($intervalo as $steps_point) {
				echo round($desvio_tempo[$config."_".$steps_point],2) . " ";
				fwrite($f, round($desvio_tempo[$config."_".$steps_point],2) . " ");

			}

			echo "<br>";
			fwrite($f, "\n");
		}
		fclose($f);
		
		echo "<br><br>distance<br>";
		foreach (array("GNR", "GR", "L", "L+C") as $config) {
	
			//foreach (array(0, 100, 400, 700, 1000, 1500, 2000) as $steps_point) {
			foreach ($intervalo as $steps_point) {
				echo round($distance[$config."_".$steps_point],2) . " ";
				//fwrite($f, round($distance[$config."_".$steps_point],2) . " ");
			}
		
			echo "<br>";
			//fwrite($f, "\n");

		}
	}
}

?>
