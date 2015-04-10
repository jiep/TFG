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
  $data = json_decode($rankings->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON());

  $equipos = $dbh->prepare("SELECT DISTINCT equipo from rankings where temporada = \"$temporada\" order by equipo");
  $equipos->execute();
  $arrayequipos = $equipos->fetchAll();

  $i = 0;
  foreach($arrayequipos as $equipo){
    if (array_key_exists('equipo', $equipo)){
      $eq = $equipo["equipo"];
      $res = $dbh->prepare("SELECT id from equipos where nombre = \"$eq\"");
      $res->execute();
      $elem=$res->fetch()[0];
      $style[$i] = array("selector" => "#$i", "css" => array("background-image" => "img/escudos/$elem.png"));
      $i++;
    }
  }

  $selector_n = array("selector" => "node", "css" => array('height' => 40, 'width' => 40, 'background-fit' => 'cover', 'border-width' => 2, 'border-opacity' => 0.1, 'background-color' => '#dfdfdf', 'background-opacity' => 0.1, 'content' => 'data(name)', 'text-valign' => 'center', 'text-halign' => 'center'));
  $selector_e = array("selector" => "edge", "css" => array('width' => 2, 'line-color' => '#ff0000'));

  array_unshift($style, $selector_n, $selector_e);

  return(json_encode(array("data"=>$data,"style"=>$style),JSON_UNESCAPED_SLASHES));

}

//datos_competitividad();

//echo $rankings->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON() . "\n";


?>
