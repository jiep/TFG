<?php

include '../../parser/autoload.php';
require_once '../../parser/connection.inc.php';

define("JOR",37);

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
  echo $jorn;
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
  echo $jorn;
          $cont++;
        }

        $jorn--;

    } while($cont<5);

    return $visitante;

}

print_r(getLocal(" ",38,"Real Madrid CF"));
print_r(getVisitante(" ",38,"Real Madrid CF"));
?>
