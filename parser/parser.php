<?php

require_once("simplehtmldom1.11/simple_html_dom.php");

function parser($url){

	$html = file_get_html($url);

	$filas = $html->find("div div div table.wikitable tbody tr");
	$equipos = array();
	$_re = array();

	$filas = $html->find("table.wikitable tbody tr");

	foreach($filas as $fila){
		if (isset($fila->find("td a",0)->title)) {
				$nombre_equipo = $fila->find("td a",0)->title;
			if($nombre_equipo != null){
				$equipos[] = $nombre_equipo;
			}else{
				continue;
			}
		}
	
	}

	foreach($filas as $fila){
	if (isset($fila->find("td a",0)->title)) {
			$nombre_equipo = $fila->find("td a",0)->title;
	    }else{
			continue;
		}

		foreach($fila->find("span span.nowrap") as $resultados){
			$resultado = $resultados->innertext;
			$_resultado = explode("–", "$resultado");
			if(!empty($_resultado)){
				$_resultado_local = $_resultado[0];
				$_resultado_visitante = $_resultado[1];
				$_re[] = $nombre_equipo . "," . $_resultado_local . "," . $_resultado_visitante . ",";
			}
		} 

	}


	$output = array_slice($_re,10);
	$_equipos = array_slice($equipos,0,20);

	$k = 0;
	for($i=0; $i<count($_equipos); $i++){
		for($j=0; $j<count($_equipos); $j++){
			if($j != $i){
				echo $output[$k] . $equipos[$j] . "\n";
				$k++;		
			}
		}
	}
}
?>
