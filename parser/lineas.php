<?php

include("autoload.php");
include("../rankings/Ranking.php");
include("../rankings/RankingCollection.php");
include("../rankings/Graph.php");
include("../rankings/EvolutiveCompetitivityGraph.php");
require_once("connection.inc.php");

//$temporada = "1928/1929";
$temporada = $_POST["season"] ? $_POST["season"] : "2013/2014";


$con = new Connection(DB_HOST,DB_PORT,DB_USERNAME,DB_PASSWORD,DB_NAME);
$con->connect();
$con->selectDatabase();
$num_equipos = $con->query("SELECT DISTINCT equipo from rankings where temporada = \"$temporada\"");
$num_jorn = $con->query("SELECT MAX(jornada) as MAX from rankings where temporada = \"$temporada\" ")[1]["MAX"];

$equipos = array();

foreach($num_equipos as $equipo){
  if (array_key_exists('equipo', $equipo)){
    $equipos[] = $equipo["equipo"];
  }
}

$jornadas = array();

for($i = 0; $i < $num_jorn; $i++){
  $jornadas[$i] = $i + 1;
}

$data = array();

foreach($equipos as $equipo){
  $ranking = $con->query("SELECT posicion from rankings where temporada = \"$temporada\" and equipo = \"$equipo\" order by jornada");
  $datos = array();
  foreach($ranking as $rank){
    if (array_key_exists('posicion', $rank)){
      $datos[] = $rank["posicion"];
    }
  }
  $data[] = $datos;
}

$con->close();

$json = json_encode(array("labels" => $jornadas, "datasets" => $data));

echo($json . "\n");

?>
