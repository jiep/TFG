<?php

	include("datos_equipos.php");
	include("datos_goles.php");

	$archivo = fopen("./datos/2011-2012.txt","r") or die();
	
	while(!feof($archivo)){
		$linea = fgets($archivo);
		if($linea != null){
			$_linea = explode(",",$linea);
			$_equipo_local = $_linea[0];
			$_equipo_visitante = str_replace("\n","", $_linea[3]);
			$_goles_local = $_linea[1];
			$_goles_visitante = $_linea[2];
			echo $equipos["$_equipo_local"] . $equipos["$_equipo_visitante"] . "," . $goles["$_goles_local"] . $goles["$_goles_visitante"] . "\n";
		}
	}

	fclose($archivo);

?>
