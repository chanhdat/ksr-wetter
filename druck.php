<?php

include("db.php");

$sqlDruck = "SELECT `DATUM`, `QFE` FROM `1Tag` ORDER BY `ID` DESC LIMIT 6"; 
$druck = mysql_query($sqlDruck) or die(mysql_error());
echo "Temperaturverlauf der letzten 3 Stunden (Zeitintervall zwischen zwei Messungen ist 30 Minuten):";
echo "<br />";

while ($test = mysql_fetch_array($druck)) {
	echo $test['DATUM']." - ".$test['QFE']." hPa";
	echo "<br />";
}

?>
