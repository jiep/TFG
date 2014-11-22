<?php

	include("datos_goles.php");

	$archivo = fopen("./datos/2011-2012.txt","r") or die();
	
	while(!feof($archivo)){
		$linea = fgets($archivo);
		if($linea != null){
			$_linea = explode(",",$linea);
			$_goles_local = $_linea[1];
			$_goles_visitante = $_linea[2];
			echo $goles["$_goles_local"] . $goles["$_goles_visitante"] . "\n";
		}
	}

	fclose($archivo);

?>
