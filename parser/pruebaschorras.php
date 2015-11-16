
<?php

//include 'autoload.php';
include 'interpolation.php';
require_once 'connection.inc.php';



function partidos(){
	$jorn = 38;
    try {
        $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $dbh->prepare("select * from partidos where temporada = \"2014/2015\" and jornada = $jorn ;");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $result;
}


	$dev = partidos();
	$ar=array();
      for($i=0;$i<10;$i++){
		
          $ar[$i] = interpolation($dev[$i]['equipo_local'],$dev[$i]['equipo_visitante']);
          $ar[$i][3] = $dev[$i]['equipo_local'];
	  $ar[$i][4] = $dev[$i]['equipo_visitante'];
      }


print_r($ar);
?>
