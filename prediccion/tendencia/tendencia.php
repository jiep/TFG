<?php

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

        $query="SELECT * FROM partidos WHERE temporada = \"2014/2015\" AND jornada = " . $jorn . " AND equipo_local = \"" . $team . "\";";
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
        $query="SELECT * FROM partidos WHERE temporada = \"2014/2015\" AND jornada = " . $jorn . " AND equipo_visitante = \"" . $team . "\";";
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
  $local_win=0;
  $tie=0;
  $visitor_win=0;

  for($i=0;$i<sizeOf($tend);$i++){
    if($tend[$i]==0){$local_win+=memory($i);}
    else if($tend[$i]==1){$tie+=memory($i);}
    else {$visitor_win+=memory($i);}
  }

  $den=$local_win+$tie+$visitor_win;
  $prob["local_win"]= $local_win/$den;
  $prob["tie"]= $tie/$den;
  $prob["visitor_win"]= $visitor_win/$den;

  return $prob;
}

function memory($position){

  return 1;
}

function combine($prob_local,$prob_visitante){
  $prob = array();

  $prob["local_win"]=
  $prob_local["local_win"]*$prob_visitante["local_win"]+(1/2)*$prob_local["tie"]*$prob_visitante["local_win"]+(1/4)*$prob_local["visitor_win"]*$prob_visitante["local_win"]+
  (1/2)*$prob_local["local_win"]*$prob_visitante["tie"]+(1/6)*$prob_local["visitor_win"]*$prob_visitante["tie"]+
  (1/4)*$prob_local["local_win"]*$prob_visitante["visitor_win"]+(1/6)*$prob_local["tie"]*$prob_visitante["visitor_win"];

  $prob["tie"]=
  (1/3)*$prob_local["tie"]*$prob_visitante["local_win"]+(1/2)*$prob_local["visitor_win"]*$prob_visitante["local_win"]+
  (1/3)*$prob_local["local_win"]*$prob_visitante["tie"]+$prob_local["tie"]*$prob_visitante["tie"]+(1/2)*$prob_local["visitor_win"]*$prob_visitante["tie"]+
  (1/2)*$prob_local["local_win"]*$prob_visitante["visitor_win"]+(1/2)*$prob_local["tie"]*$prob_visitante["visitor_win"];

  $prob["visitor_win"]=
  (1/6)*$prob_local["tie"]*$prob_visitante["local_win"]+(1/4)*$prob_local["visitor_win"]*$prob_visitante["local_win"]+
  (1/6)*$prob_local["local_win"]*$prob_visitante["tie"]+(1/3)*$prob_local["visitor_win"]*$prob_visitante["tie"]+
  (1/4)*$prob_local["local_win"]*$prob_visitante["visitor_win"]+(1/3)*$prob_local["tie"]*$prob_visitante["visitor_win"]+$prob_local["visitor_win"]*$prob_visitante["visitor_win"];

  return $prob;

}

function prob_match($temp,$jorn){

  $prob_jorn=array();

  try {
      $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = $dbh->prepare("SELECT * FROM partidos WHERE temporada = \"2014/2015\" AND jornada = " . $jorn . ";");
      $sql->execute();
      $partidos = $sql->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
      echo $e->getMessage();
  }

  for($i=0;$i<10;$i++){

    $tend_local=array();
    $tend_visitante=array();
    $prob_local=array();
    $prob_visitante=array();
    $prob_match=array();


    $eq_local = $partidos[$i]["equipo_local"];
    $equipo_visitante = $partidos[$i]["equipo_visitante"];

    $tend_local=getLocal($temp,$jorn-1,$eq_local);
    $tend_visitante=getVisitante($temp,$jorn-1,$equipo_visitante);
    $prob_local=probability($tend_local);
    $prob_visitante=probability($tend_visitante);
    $prob_match=combine($prob_local,$prob_visitante);
    $prob_match["local_team"]=$eq_local;
    $prob_match["visitor_team"]=$equipo_visitante;

    $prob_jorn[$i]=$prob_match;
  }

  return $prob_jorn;

}

//getVisitante(" ",38,"Real Madrid CF");
//print_r(probability(getVisitante(" ",38,"Real Madrid CF")));
//print_r(combine(probability(getLocal(" ",37,"Real Madrid CF")),probability(getVisitante(" ",37,"Getafe CF"))));

//print_r(prob_match("",38));

?>
