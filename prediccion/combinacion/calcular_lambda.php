<?php
include '../../parser/autoload.php';
include 'comb_convexa.php';
require_once '../../parser/connection.inc.php';

define("SEASON","2014/2015");

function calcularLambda(){

    $season=SEASON;

    $success_rate=array();
    for ($i=0;$i<=1;$i=$i+0.01){
	     $success_rate[(string)$i]=0;
    }


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

	    //Predicción
	    $curl = curl_init("http://localhost/TFG/web/api/sport/football/bbva/prediction?season=".$season."&fixture=".$fixture);
	    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	    $response = curl_exec($curl);
	    curl_close($curl);
	    $pred = json_decode($response, true);

	    $lambda = 0;
	    while($lambda<=1){
	    	$success=0;
    		$conj = conjugarconlambda($pred["results1"],$pred["results2"],$lambda);
    		for($i=0;$i<10;$i++){
			      if($result[$i]["goles_local"] > $result[$i]["goles_visitante"]) {$winner=0;}
			      else if($result[$i]["goles_local"] == $result[$i]["goles_visitante"]) {$winner=1;}
			      else {$winner=2;}
			      if($conj[$i]["local_win"]>$conj[$i]["tie"] && $conj[$i]["local_win"] > $conj[$i]["visitor_win"]){$winner_p=0;}
			      else if($conj[$i]["tie"]>$conj[$i]["local_win"] && $conj[$i]["tie"] > $conj[$i]["visitor_win"]){$winner_p=1;}
			      else {$winner_p=2;}
			      if($winner==$winner_p){$success++;}
		    }
    		$success_rate[(string)$lambda]=$success_rate[(string)$lambda]+$success;
    		$lambda = $lambda + 0.01;
	    }

    }

    return $success_rate;
}

print_r(calcularLambda());

?>
