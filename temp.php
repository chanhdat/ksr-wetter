<?php

include("db.php");

$sqlTemp = "SELECT `DATUM`, `TEMP` FROM `1Tag` ORDER BY `ID` DESC LIMIT 6"; 
$temp = mysql_query($sqlTemp) or die(mysql_error());
echo "Temperaturverlauf der letzten 3 Stunden (Zeitintervall zwischen zwei Messungen ist 30 Minuten):";
echo "<br />";

while ($test = mysql_fetch_array($temp)) {
	echo $test['DATUM']." - ".$test['TEMP']." &#176C";
	echo "<br />";
}

?>
