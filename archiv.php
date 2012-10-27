<?php
include("db.php");
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_line.php"); 
include ("/srv/www/vhosts/chanhdat.us/httpdocs/jpgraph/jpgraph_date.php"); 

$w = ($_GET['wahl']);
$d = ($_GET['tag']);
$m = ($_GET['monat']);
$y = ($_GET['jahr']);
$r = ($_GET['resultat']);

//Einheit für jede Variable
if ($w=='TEMP') {
	$einheit = "&deg; C";
} elseif ($w == 'FEUCHTE') {
	$einheit = "%";
} elseif ($w == 'QFE') {
	$einheit = "hPa";
} else {
	$einheit = "hPa";
}

//Daten abrufen
if ($d != 0 & $m != 0) {
//	echo "<p>Festhalten und nicht loslassen, baby, wir gehen zur&uuml;ck zum Datum : ".$d.".".$m.".".$y.", um die Ver&auml;nderung von ".$w." damals zu beachten.</p>";
	$date = $y.'-'.$m.'-'.$d;
	$archiv = mysql_query("SELECT `DATUM`, $w FROM `1Tag` WHERE DATE(`DATUM`)='$date' ORDER BY 'ID' DESC") or die(mysql_error());
	
} elseif ($d == 0 & $m != 0) {
//	echo "<p>Festhalten und nicht loslassen, baby, wir gehen zur&uuml;ck zum ".$m."ten Monat (".$y."), um die Ver&auml;nderung von ".$w." damals zu beachten.</p>";
	$archiv = mysql_query("SELECT `DATUM`, $w FROM `1Tag` WHERE MONTH(`DATUM`)='$m' ORDER BY 'ID' DESC") or die(mysql_error());	
				  
} else {
//	echo "<p>Festhalten und nicht loslassen, baby, wir gehen zur&uuml;ck zum Jahr ".$y.", um die Ver&auml;nderung von ".$w." damals zu beachten.</p>";
	$archiv = mysql_query("SELECT `DATUM`, $w FROM `1Tag` WHERE YEAR(`DATUM`)='$y' ORDER BY 'ID' DESC") or die(mysql_error());
}

if ($r == 'list') {
while ($test = mysql_fetch_array($archiv)) {
	echo $test['DATUM']." - ".$test[$w].$einheit."<br />";
}
} elseif ($r == 'graph') {
	

//In Array eintragen
$i=0;
while ($array = mysql_fetch_row($archiv)) {
	$zeit[$i] = strtotime($array[0]);
	$wert[$i] = $array[1];
	$i++;
}

//Grafik generieren
$graph = new Graph(1000,600,"auto");
$graph->SetMargin(40,40,20,100); 			//Rahmen
$graph->title->Set("Verlauf der Wetter&aumlnderung im gew&auml;hlten Zeitdauer");

//XY-Achse: datint: Datum - Integer
$graph->SetScale("datint");

//Datumsformat
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->SetLabelFormatString('d, M H:i', true);
//$graph->xaxis->scale->SetTimeAlign(DAYADJ_2);

//$graph -> xgrid -> Show(true, true);

//Graphen generieren
$wlinie = new LinePlot($wert, $zeit);
$graph->Add($wlinie); 
$wlinie->SetColor("red");

//legende
$wlinie->SetLegend($w);
$graph->legend->SetLineWeight(2);
$graph->legend->Pos(0.05, 0.01, 'right', 'top');
$graph->legend->SetColor("darkblue");
$graph->legend->SetFont(FF_FONT1, FS_NORMAL);
$graph->legend->SetFillColor('gray');

//Grafik anzeigen
$graph->Stroke();
}

?>

