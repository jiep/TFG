<?php

include("autoload.php");
include("../rankings/Ranking.php");
include("../rankings/RankingCollection.php");
include("../rankings/Graph.php");
include("../rankings/EvolutiveCompetitivityGraph.php");
require_once("connection.inc.php");

//$temporada = $_POST["season"] ? $_POST["season"] : "2013/2014";

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


function datos_competitividad($temporada = NULL){

  try {
    $dbh = new PDO("mysql:host=" . DB_HOST .";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }catch(PDOException $e){
    echo $e->getMessage();
  }

  if($temporada == null){
    $temporada = getLastSeason();
  }

  $num_jorn = $dbh->prepare("SELECT MAX(jornada) as max from rankings where temporada = \"$temporada\"");
  $num_jorn->execute();
  $jornadas = $num_jorn->fetch(PDO::FETCH_ASSOC);

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
    $ranking_col[$i - 1] = $ranking;
  }
  $rankings = new RankingCollection($ranking_col);
  return($rankings->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON());
}

//echo $rankings->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON() . "\n";

//echo(datos_competitividad($season));

?>
