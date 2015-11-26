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
        $sql = $dbh->prepare("select equipo, posicion from rankings where temporada = \"$season\" and jornada = $fixture;");
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
    for ($i=0;$i<20;$i++){

      if($result[$i]["equipo"]==$pred["ranking1"][$i]["equipo"]){$success++;}

    }

    $success_rate[$fixture] = $success;
  }

  print_r($success_rate);

}

comp();
?>
