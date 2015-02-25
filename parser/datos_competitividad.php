<?php

include("autoload.php");
include("../rankings/Ranking.php");
include("../rankings/RankingCollection.php");


require_once("connection.inc.php");

function datos_competitividad($temporada,$jornada){

  $ranking = new Ranking();

  $con = new Connection (DB_HOST,DB_PORT,DB_USERNAME,DB_PASSWORD,DB_NAME);

  $con->connect();

  $con->selectDatabase();

  $num_jorn = $con->query("SELECT MAX(jornada) from rankings where temporada = \"$temporada\");

  $ranking_col = array();

  for($i=1;$i<=$num_jorn;$i++){

    $datos = $con->query("SELECT equipo from rankings where temporada = \"$temporada\" and jornada = $i");
    foreach($datos as $equipo ){
      if (array_key_exists('equipo', $equipo))
        $ranking->add($equipo["equipo"]);
    }

    $ranking_col[$i]=$ranking;
  }
  $rankings = new RankingCollection($ranking_cs
  print_r($rankings);
  $con->close();
}




datos_competitividad('1928/1929',1);

?>
