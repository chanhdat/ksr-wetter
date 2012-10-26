<?php 

// JPGraph Library einbinden 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_line.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_date.php"); 

// Datenbank-Zugriff
include("db.php");

$sqlFeuchte = "SELECT `DATUM`, `FEUCHTE` FROM `1Tag` WHERE `DATUM` >= SYSDATE( ) - INTERVAL 1 DAY ORDER BY `ID` DESC"; 

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
$graph->title->Set("Die Luftfeuchtigkeit letzte 24 Stunden");

//XY-Achse: datint: Datum - Integer
$graph->SetScale("datint");

//Datumsformat
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->SetLabelFormatString('d, M, H:i', true);
//$graph->xaxis->scale->SetTimeAlign(HOURADJ_2);

$graph -> xgrid -> Show(true, false);
$graph -> ygrid -> Show(true, false);
$graph->ygrid->SetColor("lightblue");

//Graphen generieren
$feuchtelinie = new LinePlot($FeuchteWert, $datum);
$graph->Add($feuchtelinie); 
$feuchtelinie->SetColor("red");

//legende
$feuchtelinie->SetLegend('Luftfeuchtigkeit');
$graph->legend->SetLineWeight(2);
$graph->legend->Pos(0.05, 0.01, 'right', 'top');
$graph->legend->SetColor("darkblue");
$graph->legend->SetFont(FF_FONT1, FS_NORMAL);
$graph->legend->SetFillColor('gray');

//Grafik anzeigen
$graph->Stroke();

?>