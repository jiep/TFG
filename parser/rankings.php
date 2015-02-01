<?php

require("simple_html_dom.php");
require_once("conexion.php");

$N_JORNADAS = 20;
$TEMPORADA = "2014/2015";

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

		$resultado = $resultados[$j]->innertext;
		if(strpos($resultado,'-') !== false){
			$goles = explode("-", $resultado);
			$equipo_local = $locales[$j]->innertext;
			$equipo_visitante = $visitantes[$j]->innertext;
			echo($equipo_local . " " . $goles[0] . "-" . $goles[1] . " " . $equipo_visitante . "\n");

			$db_query = sprintf("INSERT INTO partidos(temporada, jornada, equipo_local, equipo_visitante, goles_local, goles_visitante) VALUES ('%s', '%u','%s','%s','%u','%u')", $TEMPORADA, $i, $equipo_local, $equipo_visitante, $goles[0], $goles[1]);
			$resultado = mysql_query($db_query);

			if(!$resultado){
				echo mysql_error() . "\n";
			}

		}	

	}

echo "--------------------------------\n";
}


curl_close($ch);

require_once("cerrar-conexion.php");

?>
