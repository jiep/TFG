<?php

include '../../parser/autoload.php';
require_once '../../parser/connection.inc.php';



function comp(){
  $success_rate=array();

  $season="2014/2015";

  for($fixture=21;$fixture<39;$fixture++){

    //Resultados
    try {
        $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $dbh->prepare("select * from partidos where temporada = \"$season\" and jornada = $fixture;");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    //echo print_r($result);

    //Predicción
    $curl = curl_init("http://localhost/TFG/web/api/sport/football/bbva/prediction?season=".$season."&fixture=".$fixture);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    $response = curl_exec($curl);
    curl_close($curl);
    //echo ($response);
    $pred = json_decode($response, true);
    //print_r($pred);


    //Comparación
    $success=0;
    for ($i=0;$i<10;$i++){

      if($result[$i]["goles_local"] > $result[$i]["goles_visitante"]) {$winner=0;}
      else if($result[$i]["goles_local"] == $result[$i]["goles_visitante"]) {$winner=1;}
      else {$winner=2;}

      if($pred[$i]["local_win"]>$pred[$i]["tie"] && $pred[$i]["local_win"] > $pred[$i]["visitor_win"]){$winner_p=0;}
      else if($pred[$i]["tie"]>$pred[$i]["local_win"] && $pred[$i]["tie"] > $pred[$i]["visitor_win"]){$winner_p=1;}
      else {$winner_p=2;}

      if($winner==$winner_p){$success++;}

    }

    $success_rate[$fixture] = $success;
  }

  print_r($success_rate);

}

comp();
?>
