<?php

require("simple_html_dom.php");

$N_JORNADAS = 20;

echo "Descargando datos de lfp.es ... \n\n";

$ch = curl_init("http://www.lfp.es/includes/ajax.php?action=cambiar_jornada_widget_liga");

for($i = 1; $i <= $N_JORNADAS; $i++){

	curl_setopt($ch, CURLOPT_POSTFIELDS, 'num_jornada=' . $i . '&competicion=1&temporada=2');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$respuesta = curl_exec($ch);

	$datos = new simple_html_dom($respuesta);

	$locales = $datos->find("div[id=div_jornada_" . $i . "_1_2] * a span[class=equipo left local] span[class=team]");
	$visitantes = $datos->find("div[id=div_jornada_" . $i . "_1_2] * a span[class=equipo left visitante] span[class=team]");
	$resultados = $datos->find("div[id=div_jornada_" . $i . "_1_2] * a span[class=hora_resultado left] span[class=horario_partido]");

echo "Jornada $i \n-------------------------------- \n"; 


	for($j = 0; $j < count($locales); $j++){

		echo($locales[$j]->innertext . " " . $resultados[$j]->innertext . " " . $visitantes[$j]->innertext . "\n");	

	}

echo "--------------------------------\n";
}


curl_close($ch);

?>
