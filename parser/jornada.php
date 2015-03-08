<?php

require("simple_html_dom.php");
require_once("connection.inc.php");
require_once("Connection.php");

$connection = new Connection(DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
print_r($connection);
$connection->connect();
$connection->selectDatabase();

$N_JORNADA = $argv[1];
$TEMPORADA = "2014/2015";

$ch = curl_init("http://www.lfp.es/includes/ajax.php?action=cambiar_jornada_widget_liga");


curl_setopt($ch, CURLOPT_POSTFIELDS, 'num_jornada=' . $N_JORNADA . '&competicion=1&temporada=2');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$respuesta = curl_exec($ch);

$datos = new simple_html_dom($respuesta);

$locales = $datos->find("div[id=div_jornada_" . $N_JORNADA . "_1_2] * a span[class=equipo left local] span[class=team]");
$visitantes = $datos->find("div[id=div_jornada_" . $N_JORNADA . "_1_2] * a span[class=equipo left visitante] span[class=team]");
$resultados = $datos->find("div[id=div_jornada_" . $N_JORNADA . "_1_2] * a span[class=hora_resultado left] span[class=horario_partido]");

for($j = 0; $j < count($locales); $j++){

	$resultado = $resultados[$j]->innertext;
	if(strpos($resultado,'-') !== false){
		$goles = explode("-", $resultado);
		$equipo_local = $locales[$j]->innertext;
		$equipo_visitante = $visitantes[$j]->innertext;
		echo($equipo_local . " " . $goles[0] . "-" . $goles[1] . " " . $equipo_visitante . "\n");

		$db_query = sprintf("INSERT INTO partidos(temporada, jornada, equipo_local, equipo_visitante, goles_local, goles_visitante) VALUES ('%s', '%u','%s','%s','%u','%u')", $TEMPORADA, $N_JORNADA, $equipo_local, $equipo_visitante, $goles[0], $goles[1]);
		$resultado = $connection->query($db_query);
	}

}

curl_close($ch);

$connection->close();
?>
