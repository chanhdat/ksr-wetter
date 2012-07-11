<?php

include("db.php");

$sqlQFE = "SELECT `DATUM`, `QFE` FROM `1Tag` ORDER BY `ID` DESC LIMIT 6"; 
$qfe = mysql_query($sqlQFE) or die(mysql_error());
$sqlQFF = "SELECT `DATUM`, `QFF` FROM `1Tag` ORDER BY `ID` DESC LIMIT 6"; 
$qff = mysql_query($sqlQFF) or die(mysql_error());

echo "Der &Aumlnderungsverlauf der Luftdruck auf Stationshöhe letzten 3 Stunden (Zeitintervall zwischen zwei Messungen ist 30 Minuten):";
echo "<br />";

while ($test = mysql_fetch_array($qfe)) {
	echo $test['DATUM']." - ".$test['QFE']." hPa";
	echo "<br />";
}

echo "Der &Aumlnderungsverlauf der Luftdruck auf Meereshöhe (480m.&uuml.M)letzten 3 Stunden (Zeitintervall zwischen zwei Messungen ist 30 Minuten):";
echo "<br />";

while ($test = mysql_fetch_array($qff)) {
	echo $test['DATUM']." - ".$test['QFF']." hPa";
	echo "<br />";
}
?>
