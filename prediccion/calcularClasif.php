<?php

require_once '../../parser/connection.inc.php';



function searchForId($id, $array) {
   for ($i=0;$i<20;$i++) {
       if ($array[$i]['equipo'] == $id) {
           return $i;
       }
   }
   return null;
}

function calcularRanking($pred,$temp,$jornada){
  $clasifAnt=array();
  try {
      $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query="SELECT * FROM rankings WHERE temporada = \"$temp\" AND jornada =" . $jornada;
      $sql = $dbh->prepare($query);
      $sql->execute();
      $clasifAnt = $sql->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
      echo $e->getMessage();
  }

  for ($i = 0; $i < 10; $i++) {

      if ($pred[$i]["local_win"]>$pred[$i]["tie"] && $pred[$i]["local_win"]>$pred[$i]["visitor_win"]){
          $eq = $pred[$i]["local_team"];
          $index=searchForId($eq,$clasifAnt);
          $clasifAnt[$index]["puntos"]+=3;
      } else if ($pred[$i]["tie"]>$pred[$i]["local_win"] && $pred[$i]["tie"]>$pred[$i]["visitor_win"]){
          $eq1 = $pred[$i]["local_team"];
          $eq2 = $pred[$i]["visitor_team"];
          $index1=searchForId($eq1,$clasifAnt);
          $index2=searchForId($eq2,$clasifAnt);
          $clasifAnt[$index1]["puntos"]+=1;
          $clasifAnt[$index2]["puntos"]+=1;
      } else if ($pred[$i]["visitor_win"]>$pred[$i]["local_win"] && $pred[$i]["visitor_win"]>$pred[$i]["tie"]) {
          $eq = $pred[$i]["visitor_team"];
          $index=searchForId($eq,$clasifAnt);
          $clasifAnt[$index]["puntos"]+=3;
      }
  }

  foreach ($clasifAnt as $clave => $fila) {
      $puntos[$clave] = $fila['puntos'];
      $goles[$clave] = $fila['goles_favor'];
      $dif[$clave] = $fila['diferencia_goles'];
  }
  array_multisort($puntos, SORT_DESC, $goles, SORT_DESC, $dif, SORT_DESC, $clasifAnt);


  for($i=0;$i<20;$i++){
    $clasifAnt[$i]["posicion"]=($i+1);
  }

  return $clasifAnt;
}

?>
