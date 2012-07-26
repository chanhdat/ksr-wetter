<?php

include("db.php");

$sqlTemp = "SELECT `DATUM`, `TEMP` FROM `1Tag` ORDER BY `ID` DESC LIMIT 6"; 
$temp = mysql_query($sqlTemp) or die(mysql_error());
$sqlTemp2 = "SELECT `DATUM`, `TEMP2` FROM `1Tag` ORDER BY `ID` DESC LIMIT 6"; 
$temp2 = mysql_query($sqlTemp2) or die(mysql_error());
echo "Temperaturverlauf der letzten 3 Stunden (Zeitintervall zwischen zwei Messungen ist 30 Minuten):";
echo "<br />";
echo "Sensor 1:";
echo "<br />";

while ($test = mysql_fetch_array($temp)) {
	echo $test['DATUM']." - ".$test['TEMP']." &#176C";
	echo "<br />";
}

echo "Sensor 2:";
echo "<br />";

while ($test2 = mysql_fetch_array($temp2)) {
	echo $test['DATUM']." - ".$test['TEMP2']." &#176C";
	echo "<br />";
?>
