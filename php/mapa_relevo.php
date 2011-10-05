<?php

$con = mysql_connect("localhost", "root", "root");
$db = mysql_select_db("itsumo", $con);

$sql1 = "SELECT x, y, avg(z) FROM net_ocp n where global = 1 and replan = 0 and comm = 0 group by x, y"; // GNR
$sql2 = "SELECT x, y, avg(z) FROM net_ocp n where global = 1 and replan = 1 and comm = 0 group by x, y"; // GR
$sql3 = "SELECT x, y, avg(z) FROM net_ocp n where global = 0 and replan = 1 and comm = 0 group by x, y"; // L
$sql4 = "SELECT x, y, avg(z) FROM net_ocp n where global = 0 and replan = 1 and comm = 1 group by x, y"; // L+C

$result = mysql_query($sql1);

$fname = "/home/maicon/projects/simulations-fluxocontinuo/plots/relevo-matlab-php/relevo.25k.GNR.dat";
$f = fopen($fname, "w");

while ($row = mysql_fetch_array($result)) {
    fwrite($f, $row[0] . " " . $row[1] . " " . $row[2] . "\n");  
}

fclose($f); 


$result = mysql_query($sql2);

$fname = "/home/maicon/projects/simulations-fluxocontinuo/plots/relevo-matlab-php/relevo.25k.GR.dat";
$f = fopen($fname, "w");

while ($row = mysql_fetch_array($result)) {
    fwrite($f, $row[0] . " " . $row[1] . " " . $row[2] . "\n");  
}

fclose($f); 


$result = mysql_query($sql3);

$fname = "/home/maicon/projects/simulations-fluxocontinuo/plots/relevo-matlab-php/relevo.25k.L.dat";
$f = fopen($fname, "w");

while ($row = mysql_fetch_array($result)) {
    fwrite($f, $row[0] . " " . $row[1] . " " . $row[2] . "\n");  
}

fclose($f); 


$result = mysql_query($sql4);

$fname = "/home/maicon/projects/simulations-fluxocontinuo/plots/relevo-matlab-php/relevo.25k.LC.dat";
$f = fopen($fname, "w");

while ($row = mysql_fetch_array($result)) {
    fwrite($f, $row[0] . " " . $row[1] . " " . $row[2] . "\n");  
}

fclose($f); 


?>
