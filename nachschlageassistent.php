<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Referenzbereich waehlen</title>
</head>

<body>
<form action="archiv.php" method="get">
	
    Datum w&auml;hlen:
    <select name="tag">
		<option value="0">Tag</option>
		<!Tage im Monat!>
        <option value="01">1</option>
        <option value="02">2</option>
        <option value="03">3</option>
        <option value="04">4</option>
        <option value="05">5</option>
        <option value="06">6</option>
        <option value="07">7</option>
        <option value="08">8</option>
        <option value="09">9</option>
        <?php
		$range = range(10, 31);
		foreach ($range as $d) {
		echo "<option value='$d'>$d</option>";
		}
		?>
	</select>
	
    <select name="monat">
		<option value="0">Monat</option>
		<!Monate im Jahr!>
        <option value="01">Januar</option>
        <option value="02">Februar</option>
        <option value="03">Maerz</option>
        <option value="04">April</option>
        <option value="05">Mai</option>
        <option value="06">Juni</option>
        <option value="07">Juli</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">Oktober</option>
        <option value="11">November</option>
        <option value="12">Dezember</option>
    </select> 
    
    <select name="jahr">
    	<?php
			$range = range(2012, 2020);
			foreach ($range as $y) {
			echo "<option value='$y'>$y</option>";
			}
		?>
    </select>   
    <br />
    <select name="resultat">
    	<option value='graph'>Diagram</option>
        <option value='list'>Messdaten</option>
    </select> 
    von:         
    <select name="wahl">
    	<option value='TEMP'>Temperatur</option>
        <option value='QFE'>QFE</option>
        <option value='QFF'>QFF</option>
        <option value='FEUCHTE'>Feuchtigkeit</option>
    </select>  
    
    <input type="submit" value="Show me, baby!" />   
</form>

</body>
</html>