<?php


include("autoload.php");
include("../rankings/Ranking.php");
include("../rankings/RankingCollection.php");
include("../rankings/Graph.php");
include("../rankings/EvolutiveCompetitivityGraph.php");
require_once("connection.inc.php");

$temporada = $_POST["season"] ? $_POST["season"] : "2013/2014";

$con = new Connection (DB_HOST,DB_PORT,DB_USERNAME,DB_PASSWORD,DB_NAME);
$con->connect();
$con->selectDatabase();
$equipos = $con->query("SELECT DISTINCT equipo from rankings where temporada = \"$temporada\"");
$con->close();

$img = array();
$style = array();

$i = 0;
foreach($equipos as $equipo){
  if (array_key_exists('equipo', $equipo)){
    $style[$i] = array("selector" => '#' . $equipo["equipo"], "css" => array("background-image" => "img/escudos/" . $equipo['equipo'] .".png"));
    $i++;
  }
}

echo(json_encode($style));

?>
