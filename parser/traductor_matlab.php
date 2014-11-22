<?php

	$archivo = fopen("./datos/sa2011-2012.txt","r") or die();
	
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

