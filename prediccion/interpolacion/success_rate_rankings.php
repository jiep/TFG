<?php

include '../../parser/autoload.php';
require_once '../../parser/connection.inc.php';

define("SEASON","2014/2015");
define("MAX",20);


function devolver($eq, $rank){
  $n = -1;

  for ($i = 0; $i < MAX ;$i++){
    if($eq==$rank[$i]["equipo"])
        $n = $i;
  }

  if($n==-1){echo "ERROR";}

  return $n;

}

function comp(){
  $success_rate=array();

  $season=SEASON;

  $n=MAX;

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

    //Predicción
    $curl = curl_init("http://localhost/TFG/web/api/sport/football/bbva/prediction?season=".$season."&fixture=".$fixture);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    $response = curl_exec($curl);
    curl_close($curl);
    $pred = json_decode($response, true);


    //Comparacion
    /*$success=0;
    for ($i=0;$i<20;$i++){

      if($result[$i]["equipo"]==$pred["ranking1"][$i]["equipo"]){$success++;}

    }

    $success_rate[$fixture] = $success;*/


    //Tau de Kendall
    $c = 0;
    $d = 0;
    $i = 0;
    $j = 0;

    while($i<$n-1){
      $eq1=$result[$i]["equipo"];
      $j=$i+1;

      while($j<$n){
        $eq2=$result[$j]["equipo"];
        $pos11=devolver($eq1,$result);
        $pos21=devolver($eq2,$result);
        $pos12=devolver($eq1,$pred["ranking1"]);
        $pos22=devolver($eq2,$pred["ranking1"]);

        if((($pos11>$pos21)&&($pos12>$pos22))||(($pos11<$pos21)&&($pos12<$pos22))){$c++;}
        else{$d++;}

        $j++;

      }

      $i++;

    }

    $tau=(($c-$d))/(((1/2)*$n*($n-1)));

    $success_rate[$fixture] = $tau;


    //Rho de Spearman
    /*$rho=0;
    $i=0;

    while($i<$n){
        $eq=$result[$i]["equipo"];
        $pos1=devolver($eq,$result)+1;
        $pos2=devolver($eq,$pred["ranking1"])+1;
        $rho=$rho+(abs($pos1-$pos2)/min($pos1,$pos2));
        $i++;
    }

    $success_rate[$fixture] = $rho;*/

  }

  print_r($success_rate);
  echo array_sum($success_rate)/18;
}

comp();
?>
