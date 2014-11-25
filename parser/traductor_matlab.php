<?php

	$archivo = fopen("./datos/entradas.txt","r") or die();
	
	while(!feof($archivo)){
		$caracter = fgetc($archivo);
		if($caracter != null){
			if($caracter != "\n"){
				echo $caracter . ",";
			}else{
				echo "\n";
			}
		}
	}

	fclose($archivo);

?>

