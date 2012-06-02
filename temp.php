<?php

include("db.php");

$sqlTemp = "SELECT `DATUM`, `TEMP` FROM `2Tage` ORDER BY `ID` DESC LIMIT 3"; 
$temp = mysql_query($sqlTemp) or die(mysql_error());
echo "Temperaturverlauf der letzten 3 Stunden (Zeitintervall zwischen zwei Messungen ist 1 Stunde):";
echo "<br />";

while ($test = mysql_fetch_array($temp)) {
	echo $test['DATUM']." - ".$test['TEMP']." &#176C";
	echo "<br />";
}

?>
