<?php

include("db.php");

$sqlDruck = "SELECT `DATUM`, `DRUCK` FROM `2Tage` ORDER BY `ID` DESC LIMIT 3"; 
$druck = mysql_query($sqlDruck) or die(mysql_error());
echo "Temperaturverlauf der letzten 3 Stunden (Zeitintervall zwischen zwei Messungen ist 1 Stunde 8 Sekunden):";
echo "<br />";

while ($test = mysql_fetch_array($druck)) {
	echo $test['DATUM']." - ".$test['DRUCK']." hPa";
	echo "<br />";
}

?>
