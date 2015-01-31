<?php

require("simple_html_dom.php");

$ARCHIVO = "prueba123.html";
$N_JORNADAS = 20;

echo "Descargando datos de lfp.es ... \n\n";

$ch = curl_init("http://www.lfp.es/includes/ajax.php?action=cambiar_jornada_widget_liga");
	$fp = fopen($ARCHIVO, "w");
	curl_setopt($ch, CURLOPT_FILE, $fp);

for($i = 1; $i <= $N_JORNADAS; $i++){

	curl_setopt($ch, CURLOPT_POSTFIELDS, 'num_jornada=' . $i . '&competicion=1&temporada=2');

	$salida = curl_exec($ch);

}

echo "Datos descargados correctamente.\n\n";

curl_close($ch);
fclose($fp);

$datos = new simple_html_dom($ARCHIVO);

for($i = 1; $i <= $N_JORNADAS; $i++){
	
	$locales = $datos->find("div[id=div_jornada_" . $i . "_1_2] * a span[class=equipo left local] span[class=team]");
	$visitantes = $datos->find("div[id=div_jornada_" . $i . "_1_2] * a span[class=equipo left visitante] span[class=team]");
	$resultados = $datos->find("div[id=div_jornada_" . $i . "_1_2] * a span[class=hora_resultado left] span[class=horario_partido]");

echo "Jornada $i \n-------------------------------- \n"; 


	for($j = 0; $j < count($locales); $j++){

		echo($locales[$j]->innertext . " " . $resultados[$j]->innertext . " " . $visitantes[$j]->innertext . "\n");	

	}

echo "--------------------------------\n";


}

unlink($ARCHIVO);

?>
