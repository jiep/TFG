<?php

	include("datos_equipos.php");

	$archivo = fopen("./datos/2011-2012.txt","r") or die();
	
	while(!feof($archivo)){
		$linea = fgets($archivo);
		if($linea != null){
			$_linea = explode(",",$linea);
			$_equipo_local = $_linea[0];
			$_equipo_visitante = str_replace("\n","", $_linea[3]);
			echo $equipos["$_equipo_local"] . $equipos["$_equipo_visitante"] . "\n";
		}
	}

	fclose($archivo);

?>
