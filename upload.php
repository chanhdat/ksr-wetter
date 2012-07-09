<?php
//Datenbank-Verbindung herstellen
include("db.php");

if ((($_GET['key']) == "root")) {	//wenn key stimmt
	//TEMPeratur, LufDRUCK, FEUCHT-Messwerten aufrufen
	$TEMP = ($_GET['TEMP']);
	$TAU = ($_GET['TAU']);
	$QFE = ($_GET['QFE']);
	$GFF = ($_GET['QFF']);
	$FEUCHTE = ($_GET['FEUCHTE']);
	
	//Eingetragene Messwerten anzeigen - Debug!
	echo "<p>Temperatur ist ".$TEMP ."</p>";
	echo "<p>Taupunkttemperatur ist ".$TEMP ."</p>";
	echo "<p>Luftdruck auf Stationshoehe ist ".$QFE ."</p>";
	echo "<p>Luftdruck auf Meereshöhe ist ".$QFF ."</p>";
	echo "Die Feuchtigkeit liegt bei ".$FEUCHTE." %";
	
	//Daten in Tabelle eintragen
	mysql_query("INSERT INTO 1Tag (DATUM, TEMP, TAU, QFE, QFF, FEUCHTE) 
				VALUES (NOW(), $TEMP, $TAU, $QFE, $QFF, $FEUCHTE)"); 		
	//Datum und Zeit, TEMPeratur, TAUpunkttemperatur, QFE, QFF, FEUCHTigkeit
	
} else {

	$ergebnis = mysql_query("SELECT * FROM 1Tag ORDER BY ID DESC LIMIT 1");	 //nur der letzte Eintrag
	while($row = mysql_fetch_object($ergebnis))
	{
		echo "Aktuellste Werten in der Datenbank: <br><br>";
		echo "<font color = 'black'>","Datum / Uhrzeit \t","<b>","<font color = 'red'>",$row->DATUM,"</b><br>";
		echo "<font color = 'black'>","Temp \t\t","<b>","<font color = 'red'>",$row->TEMP," &#176C","</b><br>";
		echo "<font color = 'black'>","Taupunkt \t\t","<b>","<font color = 'red'>",$row->TAU," &#176C","</b><br>";		
		echo "<font color = 'black'>","QFE \t\t","<b>","<font color = 'red'>",$row->QFE," hPA","</b><br>";
		echo "<font color = 'black'>","QFF \t\t","<b>","<font color = 'red'>",$row->QFF," hPA","</b><br>";
		echo "<font color = 'black'>","Feuchtigkeit \t\t","<b>","<font color = 'red'>",$row->FEUCHTE," %","</b><br>";
	}
}

?>