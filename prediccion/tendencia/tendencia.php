<?php

include '../../parser/autoload.php';
require_once '../../parser/connection.inc.php';


function getLocal($temp,$jorn,$team){

    $local=array();
    $result=array();

    try {
        $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $cont = 0;
    do{

        $query='SELECT * FROM partidos WHERE temporada = "2014/2015" AND jornada =' . $jorn . ' AND equipo_local ="' . $team . '"';
        $sql = $dbh->prepare($query);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        if (sizeOf($result)>0){

          if($result[0]['goles_local'] > $result[0]['goles_visitante']) {$local[$cont]=0;}
          else if($result[0]['goles_local'] == $result[0]['goles_visitante']) {$local[$cont]=1;}
          else {$local[$cont]=2;}

          $cont++;
        }

        $jorn--;

    } while($cont<5);

    return $local;

}

function getVisitante($temp,$jorn,$team){

    $visitante=array();
    $result=array();

    try {
        $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $cont = 0;
    do{

        $query='SELECT * FROM partidos WHERE temporada = "2014/2015" AND jornada =' . $jorn . ' AND equipo_visitante ="' . $team . '"';
        $sql = $dbh->prepare($query);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        if (sizeOf($result)>0){

          if($result[0]['goles_local'] > $result[0]['goles_visitante']) {$visitante[$cont]=0;}
          else if($result[0]['goles_local'] == $result[0]['goles_visitante']) {$visitante[$cont]=1;}
          else {$visitante[$cont]=2;}

          $cont++;
        }

        $jorn--;

    } while($cont<5);

    return $visitante;

}

function probability($tend){
  $prob=array();
  $gana_local=0;
  $tie=0;
  $gana_visitante=0;

  for($i=0;$i<sizeOf($tend);$i++){
    if($tend[$i]==0){$gana_local+=memory($i);}
    else if($tend[$i]==1){$tie+=memory($i);}
    else {$gana_visitante+=memory($i);}
  }

  $den=$gana_local+$tie+$gana_visitante;
  $prob["gana_local"]= $gana_local/$den;
  $prob["empate"]= $tie/$den;
  $prob["gana_visitante"]= $gana_visitante/$den;

  return $prob;
}

function memory($position){

  return 1;
}

function combine($prob_local,$prob_visitante){
  $prob = array();

  $prob["gana_local"]=
  $prob_local["gana_local"]*$prob_visitante["gana_local"]+(1/2)*$prob_local["empate"]*$prob_visitante["gana_local"]+(1/4)*$prob_local["gana_visitante"]*$prob_visitante["gana_local"]+
  (1/2)*$prob_local["gana_local"]*$prob_visitante["empate"]+(1/6)*$prob_local["gana_visitante"]*$prob_visitante["empate"]+
  (1/4)*$prob_local["gana_local"]*$prob_visitante["gana_visitante"]+(1/6)*$prob_local["empate"]*$prob_visitante["gana_visitante"];

  $prob["empate"]=
  (1/3)*$prob_local["empate"]*$prob_visitante["gana_local"]+(1/2)*$prob_local["gana_visitante"]*$prob_visitante["gana_local"]+
  (1/3)*$prob_local["gana_local"]*$prob_visitante["empate"]+$prob_local["empate"]*$prob_visitante["empate"]+(1/2)*$prob_local["gana_visitante"]*$prob_visitante["empate"]+
  (1/2)*$prob_local["gana_local"]*$prob_visitante["gana_visitante"]+(1/2)*$prob_local["empate"]*$prob_visitante["gana_visitante"];

  $prob["gana_visitante"]=
  (1/6)*$prob_local["empate"]*$prob_visitante["gana_local"]+(1/4)*$prob_local["gana_visitante"]*$prob_visitante["gana_local"]+
  (1/6)*$prob_local["gana_local"]*$prob_visitante["empate"]+(1/3)*$prob_local["gana_visitante"]*$prob_visitante["empate"]+
  (1/4)*$prob_local["gana_local"]*$prob_visitante["gana_visitante"]+(1/3)*$prob_local["empate"]*$prob_visitante["gana_visitante"]+$prob_local["gana_visitante"]*$prob_visitante["gana_visitante"];

  return $prob;

}

//print_r(getVisitante(" ",38,"Real Madrid CF"));
//print_r(probability(getVisitante(" ",38,"Real Madrid CF")));
print_r(combine(probability(getLocal(" ",37,"Real Madrid CF")),probability(getVisitante(" ",37,"Getafe CF"))));
?>
