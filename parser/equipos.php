<?php

require("simple_html_dom.php");
require_once("connection.inc.php");
require_once("Connection.php");

$connection = new Connection(DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
$connection->connect();
$connection->selectDatabase();

$ch = curl_init("http://www.lfp.es/includes/ajax.php?action=estadisticas_historicas");

curl_setopt($ch, CURLOPT_POSTFIELDS, 'tipo=clasificacion_historica');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$respuesta = curl_exec($ch);

$datos = new simple_html_dom($respuesta);

$equipos = $datos->find("span[class=nombre_equipo_clasificacion]");

foreach($equipos as $equipo){
  $nombre_equipo  = $equipo->innertext;
  $db_query = sprintf("INSERT INTO equipos(nombre) VALUES ('%s')", $nombre_equipo);
  $resultado = $connection->query($db_query);
}



curl_close($ch);

$connection->close();
?>
