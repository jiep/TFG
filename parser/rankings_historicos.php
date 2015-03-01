<?php

require("simple_html_dom.php");
require_once("connection.inc.php");
require_once("Connection.php");

$connection = new Connection(DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
$connection->connect();
$connection->selectDatabase();

$ACTUAL = date("Y");

for($temporada = 1928; $temporada <= $ACTUAL-2; $temporada++){
  if($temporada >= 2000){
    $temp = "1" . substr($temporada, 2, 2);
  }else{
    $temp = "0" . substr($temporada, 2, 2);
  }

  $temp2 = $temporada . "-" . substr($temporada+1, 2, 2);
  $temp3 = $temporada . "/" . ($temporada+1);

  echo "Temporada $temp3\n";

  $ch = curl_init("http://www.lfp.es/includes/ajax.php");
  curl_setopt($ch, CURLOPT_POSTFIELDS, "input_competicion=Primera&input_equipo=&input_jornada=&input_temporada=" . $temp . "&input_temporada_nombre=" . $temp2 . "&action=estadisticas_historicas&tipo=calendario");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $respuesta = curl_exec($ch);

  $datos = new simple_html_dom($respuesta);

  $jornadas1 = $datos->find("div[class=jornada_calendario_historico]");
  $jornadas2 = $datos->find("div[class=jornada_calendario_historico_last]");
  $jornadas = array_merge($jornadas1, $jornadas2);

  foreach($jornadas as $jornada){

    $jornada_numero = substr($jornada->find("span[class=cabecera_span_estadisticas_historicas]",0)->innertext,9,2);

    echo "Jornada $jornada_numero \n\n";

    // Descargamos los rankings históricos

    $ch2 = curl_init("http://www.lfp.es/includes/ajax.php");
    curl_setopt($ch2, CURLOPT_POSTFIELDS, "input_competicion=Primera&input_equipo=&input_jornada=" . $jornada_numero . "&input_temporada=" . $temp . "&input_temporada_nombre=" . $temp2 . "&action=estadisticas_historicas&tipo=clasificacion");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);

    $respuesta2 = curl_exec($ch2);
    $datos_rankings = new simple_html_dom($respuesta2);

    $filas_equipo = $datos_rankings->find("tr");

    /*
    Como saca también la cabecera de la tabla, la eliminamos
    */
    unset($filas_equipo[0]);

    foreach($filas_equipo as $fila_equipo){
      $posicion = substr($fila_equipo->find("td", 0)->innertext, 0, -2);
      $nombre_equipo = $fila_equipo->find("td span", 0)->innertext;
      if($nombre_equipo == "Atlético Madrid"){
        $nombre_equipo = "At. de Madrid";
      }else if($nombre_equipo == "Elche C.F."){
        $nombre_equipo = "Elche CF";
      }else if($nombre_equipo == "Getafe C.F."){
        $nombre_equipo = "Getafe CF";
      }else if($nombre_equipo == "Granada C.F."){
        $nombre_equipo = "Granada CF";
      }else if($nombre_equipo == "Levante U.D."){
        $nombre_equipo = "Levante UD";
      }else if($nombre_equipo == "Málaga C.F."){
        $nombre_equipo = "Málaga CF";
      }else if($nombre_equipo == "Real Betis B. S."){
        $nombre_equipo = "Real Betis";
      }else if($nombre_equipo == "Real Madrid C.F."){
        $nombre_equipo = "Real Madrid CF";
      }else if($nombre_equipo == "Sevilla F.C."){
        $nombre_equipo = "Sevilla FC";
      }else if($nombre_equipo == "U.D. Almería"){
        $nombre_equipo = "UD Almería";
      }else if($nombre_equipo == "Valencia C.F."){
        $nombre_equipo = "Valencia CF";
      }else if($nombre_equipo == "Villarreal C.F."){
        $nombre_equipo = "Villarreal CF";
      }else if($nombre_equipo == "C. At. Osasuna"){
        $nombre_equipo = "CA Osasuna";
      }else if($nombre_equipo == "F.C. Barcelona"){
        $nombre_equipo = "FC Barcelona";
      }
      $puntos = $fila_equipo->find("td", 2)->innertext;
      $partidos_ganados = $fila_equipo->find("td", 4)->innertext;
      $partidos_empatados = $fila_equipo->find("td", 5)->innertext;
      $partidos_perdidos = $fila_equipo->find("td", 6)->innertext;
      $goles_favor = $fila_equipo->find("td", 7)->innertext;
      $goles_contra = $fila_equipo->find("td", 8)->innertext;
      $diferencia_goles = $fila_equipo->find("td", 9)->innertext;

      echo "$posicion $nombre_equipo $puntos $partidos_ganados $partidos_empatados $partidos_perdidos $goles_favor $goles_contra $diferencia_goles\n";

      $db_query = sprintf("INSERT INTO rankings(equipo, posicion, puntos, partidos_ganados, partidos_empatados, partidos_perdidos, goles_favor, goles_contra, diferencia_goles, jornada, temporada) VALUES ('%s', %u, %u, %u, %u, %u, %u, %u, %d, %u, '%s')",
        $nombre_equipo,
        $posicion,
        intval($puntos),
        intval($partidos_ganados),
        intval($partidos_empatados),
        intval($partidos_perdidos),
        intval($goles_favor),
        intval($goles_contra),
        intval($diferencia_goles),
        intval($jornada_numero),
        $temp3);

      $resultado = $connection->query($db_query);
    }

    echo "\n";

    $partidos = $jornada->find("table tbody tr td span");
    foreach($partidos as $partido){
      $s = explode("<br />", $partido->innertext);
      $l1 = explode(":",$s[0]);
      $equipo_local = $l1[0];
      $goles_local = substr($l1[1], 4, 1);

      $v1 = explode(":",$s[1]);
      $equipo_visitante = $v1[0];
      $goles_visitante = substr($v1[1], 4, 1);

      if($equipo_visitante == "Atlético Madrid"){
        $equipo_visitante = "At. de Madrid";
      }else if($equipo_visitante == "Elche C.F."){
        $equipo_visitante = "Elche CF";
      }else if($equipo_visitante == "Getafe C.F."){
        $equipo_visitante = "Getafe CF";
      }else if($equipo_visitante == "Granada C.F."){
        $equipo_visitante = "Granada CF";
      }else if($equipo_visitante == "Levante U.D."){
        $equipo_visitante = "Levante UD";
      }else if($equipo_visitante == "Málaga C.F."){
        $equipo_visitante = "Málaga CF";
      }else if($equipo_visitante == "Real Betis B. S."){
        $equipo_visitante = "Real Betis";
      }else if($equipo_visitante == "Real Madrid C.F."){
        $equipo_visitante = "Real Madrid CF";
      }else if($equipo_visitante == "Sevilla F.C."){
        $equipo_visitante = "Sevilla FC";
      }else if($equipo_visitante == "U.D. Almería"){
        $equipo_visitante = "UD Almería";
      }else if($equipo_visitante == "Valencia C.F."){
        $equipo_visitante = "Valencia CF";
      }else if($equipo_visitante == "Villarreal C.F."){
        $equipo_visitante = "Villarreal CF";
      }else if($equipo_visitante == "C. At. Osasuna"){
        $equipo_visitante = "CA Osasuna";
      }else if($equipo_visitante == "F.C. Barcelona"){
        $equipo_visitante = "FC Barcelona";
      }

      if($equipo_local == "Atlético Madrid"){
        $equipo_local = "At. de Madrid";
      }else if($equipo_local == "Elche C.F."){
        $equipo_local = "Elche CF";
      }else if($equipo_local == "Getafe C.F."){
        $equipo_local = "Getafe CF";
      }else if($equipo_local == "Granada C.F."){
        $equipo_local = "Granada CF";
      }else if($equipo_local == "Levante U.D."){
        $equipo_local = "Levante UD";
      }else if($equipo_local == "Málaga C.F."){
        $equipo_local = "Málaga CF";
      }else if($equipo_local == "Real Betis B. S."){
        $equipo_local = "Real Betis";
      }else if($equipo_local == "Real Madrid C.F."){
        $equipo_local = "Real Madrid CF";
      }else if($equipo_local == "Sevilla F.C."){
        $equipo_local = "Sevilla FC";
      }else if($equipo_local == "U.D. Almería"){
        $equipo_local = "UD Almería";
      }else if($equipo_local == "Valencia C.F."){
        $equipo_local = "Valencia CF";
      }else if($equipo_local == "Villarreal C.F."){
        $equipo_local = "Villarreal CF";
      }else if($equipo_local == "C. At. Osasuna"){
        $equipo_local = "CA Osasuna";
      }else if($equipo_local == "F.C. Barcelona"){
        $equipo_local = "FC Barcelona";
      }

      $db_query = sprintf("INSERT INTO partidos(temporada, jornada, equipo_local, equipo_visitante, goles_local, goles_visitante) VALUES ('%s', '%u','%s','%s','%u','%u')", $temp3, $jornada_numero, $equipo_local, $equipo_visitante, $goles_local, $goles_visitante);
      $resultado = $connection->query($db_query);

      echo "$equipo_local $goles_local - $goles_visitante $equipo_visitante \n";
    }
    echo "-------------------------------------------\n\n";
  }

  curl_close($ch2);
}

curl_close($ch);

$connection->close();
?>
