<?php

$dbhost = "localhost";
$dbport = 3306;
$dbusuario = "rankings";
$dbpassword = "1234";
$db = "rankings";

$conexion = mysql_connect($dbhost.":".$dbport, $dbusuario, $dbpassword);
mysql_select_db($db, $conexion);

mysql_query("SET NAMES 'utf8'"); 

if($conexion == TRUE){
	echo "Conexión establecida con éxito con la base de datos\n";
}else{
	echo "No se pudo estblacer conexión con la base de datos\n";
	die();
}

?>
