<?php

require("simple_html_dom.php");
require_once("connection.inc.php");
require_once("Connection.php");

$connection = new Connection(DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
$connection->connect();
$connection->selectDatabase();

$ACTUAL = date("Y");

for($temporada = 1928; $temporada <= $ACTUAL-2; $temporada++){
	if($temporada >= 2000){
		$temp = "1" . substr($temporada, 2, 2);
	}else{
		$temp = "0" . substr($temporada, 2, 2);
	}

	$temp2 = $temporada . "-" . substr($temporada+1, 2, 2);
	$temp3 = $temporada . "/" . ($temporada+1);

	echo "Temporada $temp3\n";

	$ch = curl_init("http://www.lfp.es/includes/ajax.php");
	curl_setopt($ch, CURLOPT_POSTFIELDS, "input_competicion=Primera&input_equipo=&input_jornada=&input_temporada=" . $temp . "&input_temporada_nombre=" . $temp2 . "&action=estadisticas_historicas&tipo=calendario");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$respuesta = curl_exec($ch);

	$datos = new simple_html_dom($respuesta);

	$jornadas1 = $datos->find("div[class=jornada_calendario_historico]");
	$jornadas2 = $datos->find("div[class=jornada_calendario_historico_last]");
	$jornadas = array_merge($jornadas1, $jornadas2);

	foreach($jornadas as $jornada){
		$jornada_numero = substr($jornada->find("span[class=cabecera_span_estadisticas_historicas]",0)->innertext,9,2);
		echo "Jornada $jornada_numero \n";
		$partidos = $jornada->find("table tbody tr td span");
		foreach($partidos as $partido){
			$s = explode("<br />", $partido->innertext);
			$l1 = explode(":",$s[0]);
			$equipo_local = $l1[0];
			$goles_local = substr($l1[1], 4, 1);

			$v1 = explode(":",$s[1]);
			$equipo_visitante = $v1[0];
			$goles_visitante = substr($v1[1], 4, 1);

			$db_query = sprintf("INSERT INTO partidos(temporada, jornada, equipo_local, equipo_visitante, goles_local, goles_visitante) VALUES ('%s', '%u','%s','%s','%u','%u')", $temp3, $jornada_numero, $equipo_local, $equipo_visitante, $goles_local, $goles_visitante);
			$resultado = $connection->query($db_query);

			echo "$equipo_local $goles_local - $goles_visitante $equipo_visitante \n";
		}
		echo "\n";
	}
}
curl_close($ch);

$connection->close();
?>
