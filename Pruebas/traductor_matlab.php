<?php
// traductor_matlab.txt

	$archivo = fopen("./datos/salidas.txt","r") or die();
	
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

