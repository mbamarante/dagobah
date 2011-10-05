<?php

$con = mysql_connect("localhost", "root", "root");
$db = mysql_select_db("itsumo", $con);

//$replanner = 0;
$global = 1;
$communication = 0;
$odsize = 25000;
$network = "poa.notl";

$big_stack = array();

//foreach (array(1000) as $steps_point) {
foreach (array(0, 100, 200, 300, 400, 500, 1000) as $steps_point) {
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

				$position = $steps_point . "_" . $replanner . "_" . $global . "_" . $communication;

				$data[$position] = mysql_result($res, 0, "media");

				//echo $position . " = " . $data[$position] . "<br>";
				
				array_push($stack, $data[$position]);

			}

		}
	}
	
	
	array_push($big_stack, $stack);
}

//echo "agora vai...";

//print_r($big_stack);

//Include the code
include('phplot/phplot.php');

//Define the object
$graph =& new PHPlot(800,600);

//Set titles
//$graph->SetTitle("Tempos de Viagem\n\rSubtitle");
$graph->SetTitle("Tempos de Viagem");
$graph->SetXTitle('tempo minimo de viagem');
$graph->SetYTitle('tempo medio de viagem');


//Define some data
//$example_data = array(
//     array('a',3,4,2),
//     array('b',5,'',1),  // here we have a missing data point, that's ok
//     array('c',7,2,6),
//     array('d',8,1,4),
//     array('e',2,4,6),
//     array('f',6,4,5),
//     array('g',7,2,3)
//);

$graph->SetLegend(array("GNR","GR","L","L+C"));
$graph->SetDataValues($big_stack);


//Draw it
$graph->DrawGraph();

?>