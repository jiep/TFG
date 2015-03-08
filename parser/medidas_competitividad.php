<?php

include("autoload.php");
include("../rankings/Ranking.php");
include("../rankings/RankingCollection.php");
include("../rankings/Graph.php");
include("../rankings/EvolutiveCompetitivityGraph.php");
require_once("connection.inc.php");

//$temporada = $_POST["season"] ? $_POST["season"] : "2013/2014";
$temporada = "2000/2001";

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
$labels_array = array();

for($i = 0; $i <= $k; $i++){
  $labels_array[$i] = $i;
  $ndd[$i] = $graph->normalizedDegreeDistribution($i);
  $ncdd[$i] = $graph->normalizedCumulativeDegreeDistribution($i);
}

$nms = $graph->normalizedMeanStrength();
$gkt = $graph->generalizedKendallsTau();
$nmd = $graph->normalizedMeanDegree();

$labels = array("Fuerza media normalizada", "Tau de Kendall generalizada", "Grado medio normalizado");

$json = json_encode(array("labels_array" => $labels_array, "labels" => $labels, "ndd" => $ndd, "ncdd" => $ncdd, "medidas" => array($nmd, $nms, $gkt)));

echo $json;


?>
