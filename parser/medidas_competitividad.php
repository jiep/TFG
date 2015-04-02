<?php

include("autoload.php");
include("../rankings/Ranking.php");
include("../rankings/RankingCollection.php");
include("../rankings/Graph.php");
include("../rankings/EvolutiveCompetitivityGraph.php");
require_once("connection.inc.php");

//$temporada = $_POST["season"] ? $_POST["season"] : "2013/2014";
//$temporada = "2000/2001";

function getLastSeason(){
  try {
    $dbh = new PDO("mysql:host=" . DB_HOST .";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = $dbh->prepare("SELECT DISTINCT temporada from rankings order by temporada desc limit 1");
    $sql->execute();
    $result = $sql->fetch(PDO::FETCH_OBJ);
  }catch(PDOException $e){
    echo $e->getMessage();
  }

  return $result->temporada;
}

function medidas_competitividad($temporada = null){

  try {
    $dbh = new PDO("mysql:host=" . DB_HOST .";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = $dbh->prepare("SELECT DISTINCT temporada from rankings order by temporada desc limit 1");
    $sql->execute();
    $result = $sql->fetch(PDO::FETCH_OBJ);
  }catch(PDOException $e){
    echo $e->getMessage();
  }

  if($temporada == null){
    $temporada = getLastSeason();
  }

  $num_jorn = $dbh->prepare("SELECT MAX(jornada) as max from rankings where temporada = \"$temporada\"");
  $num_jorn->execute();
  $jornadas = $num_jorn->fetch(PDO::FETCH_ASSOC);

  $ranking_col = array();

  for($i = 1;$i <= $jornadas["max"]; $i++){
    $datos = $dbh->prepare("SELECT equipo from rankings where temporada = \"$temporada\" and jornada = $i ORDER BY posicion");
    $datos->execute();
    $equipos = $datos->fetchAll();

    $ranking = new Ranking();
    foreach($equipos as $equipo){
      if (array_key_exists('equipo', $equipo)){
        $ranking->add($equipo["equipo"]);
      }
    }
    $ranking_col[$i-1] = $ranking;
  }
  $rankings = new RankingCollection($ranking_col);

  $graph = $rankings->calculateEvolutiveCompetitivityGraph();
  $mat = $graph->getAdjacencyMatrix();

  $k = 0;
  for($i = 0; $i < count($mat); $i++){
    $sum = 0;
    for($j = 0; $j < count($mat); $j++){
      if($mat[$i][$j] != 0){
        $sum++;
      }
    }
    if($sum > $k){
      $k = $sum;
    }
  }

  $ndd = array();
  $ncdd = array();

  for($i = 0; $i <= $k; $i++){
    $ndd[$i] = $graph->normalizedDegreeDistribution($i);
    $ncdd[$i] = $graph->normalizedCumulativeDegreeDistribution($i);
  }

  $nms = $graph->normalizedMeanStrength();
  $gkt = $graph->generalizedKendallsTau();
  $nmd = $graph->normalizedMeanDegree();


  $json = json_encode(array("ndd" => $ndd, "ncdd" => $ncdd, "measures" => array($nmd, $nms, $gkt)));

  return $json;

}

//echo $json;


?>
