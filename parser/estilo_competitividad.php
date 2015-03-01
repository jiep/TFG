<?php


include("autoload.php");
include("../rankings/Ranking.php");
include("../rankings/RankingCollection.php");
include("../rankings/Graph.php");
include("../rankings/EvolutiveCompetitivityGraph.php");
require_once("connection.inc.php");

$temporada = $_POST["season"] ? $_POST["season"] : "2013/2014";


//function datos_competitividad($temporada){

  $con = new Connection (DB_HOST,DB_PORT,DB_USERNAME,DB_PASSWORD,DB_NAME);
  $con->connect();
  $con->selectDatabase();
  $equipos = $con->query("SELECT DISTINCT equipo from rankings where temporada = \"$temporada\"");

  foreach($equipos as $equipo){
      $img[$equipo][$i] = $posicion;
    }
  }
  $con->close();
  //return($rankings->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON());
//}

print_r($equipos);

?>
