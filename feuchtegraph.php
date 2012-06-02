<?php 

// JPGraph Library einbinden 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_line.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_date.php"); 

// Datenbank-Zugriff
include("db.php");

//Die 24 letze Messwerten aufrufen (= 24 Stunden)

$sqlFeuchte = "SELECT `DATUM`, `FEUCHTE` FROM `2Tage` ORDER BY `ID` DESC LIMIT 48"; 

$feuchte = mysql_query($sqlFeuchte) or die(mysql_error());

//In Array eintragen
$i=0;
while ($array = mysql_fetch_row($feuchte)) {
	$datum[$i] = strtotime($array[0]);
	$FeuchteWert[$i] = $array[1];
	$i++;
}

//Grafik generieren
$graph = new Graph(1000,600,"auto");
$graph->SetMargin(40,40,20,100); 			//Rahmen

//XY-Achse: datint: Datum - Integer
$graph->SetScale("datint");

//Datumsformat
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->SetLabelFormatString('d, M, H:i', true);
$graph->xaxis->scale->SetTimeAlign(HOURADJ_2);

$graph -> xgrid -> Show(true, true);

//Graphen generieren
$feuchtelinie = new LinePlot($FeuchteWert, $datum);
$graph->Add($feuchtelinie);
$feuchtelinie->SetColor('red','darked');
$graph->Add($feuchtelinie); 


//Grafik anzeigen
$graph->Stroke();

?>