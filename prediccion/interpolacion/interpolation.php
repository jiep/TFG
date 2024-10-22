<?php

include '../../parser/autoload.php';
require_once '../../parser/connection.inc.php';


define("MED", 20/38);

//Desde 1990-2015
define("X1",0.8235294117647058);
define("Y1",0.4782608695652174);
define("Z1",0.2916666666666667);
define("X2",0.8235294117647058);
define("Y2",0.736842105263158);
define("Z2",0.5416666666666667);


function sintildes($incoming_string){
        $tofind = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
        $replac = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
        return utf8_encode(strtr(utf8_decode($incoming_string),utf8_decode($tofind),$replac));
}

function getLastSeason(){
    try {
        $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $dbh->prepare('SELECT DISTINCT temporada from rankings order by temporada desc limit 1');
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $result->temporada;
}

function getLastFixture($temp){
    try {
        $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $dbh->prepare("SELECT DISTINCT jornada from rankings where temporada = \"$temp\" order by jornada desc limit 1");
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return (int) $result->jornada;
}

function getPosition($team,$temp,$rank_prev){

    $enlace = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
    if (!$enlace) {
        die('No se pudo conectar: ' . mysql_error());
    }
    if (!mysql_select_db(DB_NAME)) {
        die('No se pudo seleccionar la base de datos: ' . mysql_error());
    }
    $resultado = mysql_query("SELECT posicion from rankings where temporada = \"$temp\" and jornada = $rank_prev and equipo = \"$team\" ");
    if (!$resultado) {
        die('No se pudo consultar:' . mysql_error());
    }


    $res= mysql_fetch_row($resultado);


    mysql_close($enlace);

    return $res[0];

}

function normalize($pos1,$pos2){
    $dif=$pos1-$pos2;
    return ($dif+19)/38;
}

function interpolatef($value){
    //Interpolacion lineal
    if($value<=MED){
        $res = X2 + ($value * (Y2-X2)/MED);
    }else{
        $res = Y2 + ((($value-MED) * (Z2-Y2))/(1-MED));
    }

    //Interpolacion cuadratica
    //$res = X2 * ((($value-MED)*($value-1))/MED) + Y2 * ((($value)*($value-1))/((MED)*(MED-1))) + Z2 * ((($value)*($value-MED))/(1-MED));

    return $res;
}

function interpolateg($value){
    //Interpolacion lineal
    if($value<=MED){
        $res = X1 + ($value * (Y1-X1)/MED);
    }else{
        $res = Y1 + ((($value-MED) * (Z1-Y1))/(1-MED));
    }

    //Interpolacion cuadratica
    //$res = X1 * ((($value-MED)*($value-1))/MED) + Y1 * ((($value)*($value-1))/((MED)*(MED-1))) + Z1 * ((($value)*($value-MED))/(1-MED));

    return $res;
}

function interpolation($eq1,$eq2,$temp,$jorn){
    $perc = array();
    $pos1=getPosition(sintildes($eq1),$temp,$jorn-1);
    $pos2=getPosition(sintildes($eq2),$temp,$jorn-1);
    $v2 = interpolatef(normalize($pos1,$pos2));
    $v1 = interpolateg(normalize($pos1,$pos2));
    $perc["local_win"] = $v1;
    $perc["tie"] = $v2-$v1;
    $perc["visitor_win"] = 1 - $v2;

    return $perc;
}

function prob_interpolation($temp,$jorn){

  $prob_jorn = array();

  try {
      $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = $dbh->prepare("SELECT * FROM partidos WHERE temporada = \"$temp\" AND jornada = " . $jorn . ";");
      $sql->execute();
      $partidos = $sql->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
      echo $e->getMessage();
  }

  for($i=0;$i<10;$i++){
      $prob_match=array();

      $prob_match = interpolation($partidos[$i]['equipo_local'],$partidos[$i]['equipo_visitante'],$temp,$jorn);
      $prob_match["local_team"] = $partidos[$i]['equipo_local'];
      $prob_match["visitor_team"] = $partidos[$i]['equipo_visitante'];

      $prob_jorn[$i]=$prob_match;

  }

  return $prob_jorn;

}

?>
