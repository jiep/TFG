<?php

require("simple_html_dom.php");
require_once("conexion.php");

$ch = curl_init("http://www.lfp.es/includes/ajax.php?action=estadisticas_historicas");

curl_setopt($ch, CURLOPT_POSTFIELDS, 'tipo=clasificacion_historica');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$respuesta = curl_exec($ch);

$datos = new simple_html_dom($respuesta);

$equipos = $datos->find("span[class=nombre_equipo_clasificacion]");

foreach($equipos as $equipo){
  $nombre_equipo  = $equipo->innertext;
  $db_query = sprintf("INSERT INTO equipos(nombre) VALUES ('%s')", $nombre_equipo);
  $resultado = mysql_query($db_query);
  if(!$resultado){
    echo mysql_error() . "\n";
  }
}



curl_close($ch);

require_once("cerrar-conexion.php");
?>
