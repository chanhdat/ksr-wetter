<?php 

// JPGraph Library einbinden 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_line.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_date.php"); 

// Datenbank-Zugriff
include("db.php");

//Die 48 letze Messwerten aufrufen (= 24 Stunden)

$sqlDruck = "SELECT `DATUM`, `QFE` FROM `1Tag` ORDER BY `ID` DESC LIMIT 48"; 

$druck = mysql_query($sqlDruck) or die(mysql_error());

//In Array eintragen
$i=0;
while ($array = mysql_fetch_row($druck)) {
	$datum[$i] = strtotime($array[0]);
	$DruckWert[$i] = $array[1];
	$i++;
}

//Grafik generieren
$graph = new Graph(1000,600,"auto");
$graph->SetMargin(40,40,20,100); 			//Rahmen
$graph->title->Set("Verlauf des Luftdruck (auf Stationshöhe) Heute");

//XY-Achse: datint: Datum - Integer
$graph->SetScale("datint");

//Datumsformat
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->SetLabelFormatString('d, M, H:i', true);
$graph->xaxis->scale->SetTimeAlign(HOURADJ_2);

$graph -> xgrid -> Show(true, true);

//Graphen generieren
$drucklinie = new LinePlot($DruckWert, $datum);
$graph->Add($drucklinie); 
$drucklinie->SetColor('red','darked');

//legende
$drucklinie->SetLegend('luftdruck auf Meeresh&oumlhe');
$graph->legend->SetLineWeight(2);
$graph->legend->Pos(0.05, 0.01, 'right', 'top');
$graph->legend->SetColor("darkblue");
$graph->legend->SetFont(FF_FONT1, FS_NORMAL);
$graph->legend->SetFillColor('gray');

//Grafik anzeigen
$graph->Stroke();

?>