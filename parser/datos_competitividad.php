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
  $num_jorn = $con->query("SELECT MAX(jornada) as MAX from rankings where temporada = \"$temporada\" ")[1]["MAX"];
  $ranking_col = array();

  for($i=1;$i<=$num_jorn;$i++){
    $datos = $con->query("SELECT equipo from rankings where temporada = \"$temporada\" and jornada = $i ORDER BY posicion");
    $ranking = new Ranking();
    foreach($datos as $equipo){
      if (array_key_exists('equipo', $equipo)){
        $ranking->add($equipo["equipo"]);
      }
    }
    $ranking_col[$i-1]=$ranking;
  }
  $rankings = new RankingCollection($ranking_col);
  $con->close();
  //return($rankings->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON());
//}

echo $rankings->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON() . "\n";


//echo(datos_competitividad($season));

?>
