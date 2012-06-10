<?php
//Datenbank-Verbindung herstellen
include("db.php");

if ((($_GET['key']) == "root")) {	//wenn key stimmt
	//TEMPeratur, LufDRUCK, FEUCHT-Messwerten aufrufen
	$TEMP = ($_GET['TEMP']);
	$DRUCK = ($_GET['DRUCK']);
	$FEUCHTE = ($_GET['FEUCHTE']);
	
	//Eingetragene Messwerten anzeigen - Debug!
	echo "<p>Temperatur ist ".$TEMP ."</p>";
	echo "<p>Luftdruck ist ".$DRUCK ."</p>";
	echo "Die Feuchtigkeit liegt bei ".$FEUCHTE." %";
	
	//Daten in Tabelle eintragen
	mysql_query("INSERT INTO 2Tage (DATUM, TEMP, DRUCK, FEUCHTE) 
				VALUES (NOW(), $TEMP, $DRUCK, $FEUCHTE)"); 		
	//Datum und Zeit, TEMPeratur, TEMPeratur, LuftDRUCK, FEUCHTigkeit
	
} else {

	$ergebnis = mysql_query("SELECT * FROM 2Tage ORDER BY ID DESC LIMIT 1");	 //nur letzten Datensatz
	while($row = mysql_fetch_object($ergebnis))
	{
		echo "Aktuellste Werten in der Datenbank: <br><br>";
		echo "<font color = 'black'>","Datum / Uhrzeit \t","<b>","<font color = 'red'>",$row->DATUM,"</b><br>";
		echo "<font color = 'black'>","Temp \t\t","<b>","<font color = 'red'>",$row->TEMP," &#176C","</b><br>";
		echo "<font color = 'black'>","Druck \t\t","<b>","<font color = 'red'>",$row->DRUCK," hPA","</b><br>";
		echo "<font color = 'black'>","Feuchtigkeit \t\t","<b>","<font color = 'red'>",$row->FEUCHTE," %","</b><br>";
	}
}

?>