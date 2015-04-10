<?php

include("autoload.php");
require_once("connection.inc.php");

//$temporada = $_POST["season"] ? $_POST["season"] : "2013/2014";

$temporada="1928/1929";

$con = new Connection (DB_HOST,DB_PORT,DB_USERNAME,DB_PASSWORD,DB_NAME);
$con->connect();
$con->selectDatabase();
$equipos = $con->query("SELECT DISTINCT equipo from rankings where temporada = \"$temporada\" order by equipo");

$i = 0;
foreach($equipos as $equipo){
  if (array_key_exists('equipo', $equipo)){
    $eq = $equipo["equipo"];
    $res = $con->query("SELECT id from equipos where nombre = \"$eq\"")[1]["id"];
    $style[$i] = array("selector" => "#$i", "css" => array("background-image" => "img/escudos/$res.png"));
    $i++;
  }
}

$selector_n = array("selector" => "node", "css" => array('height' => 40, 'width' => 40, 'background-fit' => 'cover', 'border-width' => 2, 'border-opacity' => 0.1, 'background-color' => '#dfdfdf', 'background-opacity' => 0.1, 'content' => 'data(name)', 'text-valign' => 'center', 'text-halign' => 'center'));
$selector_e = array("selector" => "edge", "css" => array('width' => 2, 'line-color' => '#ff0000'));


$con->close();

array_unshift($style, $selector_n, $selector_e);
echo(json_encode($style, JSON_UNESCAPED_SLASHES));

?>
