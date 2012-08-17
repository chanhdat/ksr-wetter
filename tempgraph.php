<?php 

// JPGraph Library einbinden 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_line.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_date.php"); 

// Datenbank-Zugriff
include("db.php");

//Die 48 letze Messwerten aufrufen (= 24 Stunden)

$sqlTemp = "SELECT `DATUM`, `TEMP` FROM `1Tag` ORDER BY `ID` DESC LIMIT 48"; 
$sqlTau = "SELECT `DATUM`, `TAU` FROM `1Tag` ORDER BY `ID` DESC LIMIT 48"; 

$temp = mysql_query($sqlTemp) or die(mysql_error());
$tau = mysql_query($sqlTau) or die(mysql_error());

//In Array eintragen
$i=0;
while ($array1 = mysql_fetch_row($temp)) {
	$datum[$i] = strtotime($array1[0]);
	$TempWert[$i] = $array1[1];
	$i++;
}

$i=0;
while ($array3 = mysql_fetch_row($tau)) {
	$datum[$i] = strtotime($array3[0]);
	$TauWert[$i] = $array3[1];
	$i++;
}
//Grafik generieren
$graph = new Graph(1000,600,"auto");
$graph->SetMargin(40,40,20,100); 			//Rahmen
$graph->title->Set("Verlauf Temperatur Heute");

//XY-Achse: datint: Datum - Integer
$graph->SetScale("datint");

//Datumsformat
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->SetLabelFormatString('d, M, H:i', true);
$graph->xaxis->scale->SetTimeAlign(HOURADJ_2);

$graph -> xgrid -> Show(true, true);

//Graphen generieren
$templinie = new LinePlot($TempWert, $datum);
$graph->Add($templinie); 
$templinie->SetColor("red");

$taulinie = new LinePlot($TauWert, $datum);
$graph->Add($taulinie);
$taulinie->SetColor("#28A122");

//Legende
$templinie->SetLegend('Temperatur (Sensor &#35;1)');
$taulinie->SetLegend('Taupunkt');
$graph->legend->SetLineWeight(2);
$graph->legend->Pos( 0.05, 0.01, 'right', 'top'); 
$graph->legend->SetColor("darkblue"); 
$graph->legend->SetFont(FF_FONT1, FS_NORMAL); 
$graph->legend->SetFillColor('gray'); 

//Grafik anzeigen
$graph->Stroke();

?>