<?php

include("db.php");

$sqlFeuchte = "SELECT `DATUM`, `FEUCHTE` FROM `2Tage` ORDER BY `ID` DESC LIMIT 3"; 
$feuchte = mysql_query($sqlFeuchte) or die(mysql_error());
echo "Temperaturverlauf der letzten 3 Stunden (Zeitintervall zwischen zwei Messungen ist 1 Stunde 8 Sekunden):";
echo "<br />";

while ($test = mysql_fetch_array($feuchte)) {
	echo $test['DATUM']." - ".$test['FEUCHTE']." %";
	echo "<br />";
}

?>
