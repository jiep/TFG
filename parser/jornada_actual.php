<?php

require 'simple_html_dom.php';
require_once 'connection.inc.php';
require_once 'Connection.php';

$connection = new Connection(DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
print_r($connection);
$connection->connect();
$connection->selectDatabase();

$N_JORNADA = $argv[1];
$TEMPORADA = '2014/2015';

    $ch = curl_init('http://www.lfp.es/includes/ajax.php?action=cambiar_jornada_widget_liga');
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'num_jornada='.$N_JORNADA.'&competicion=1&temporada=2');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $respuesta = curl_exec($ch);

    $datos = new simple_html_dom($respuesta);

    $locales = $datos->find('div[id=div_jornada_'.$N_JORNADA.'_1_2] * a span[class=equipo left local] span[class=team]');
    $visitantes = $datos->find('div[id=div_jornada_'.$N_JORNADA.'_1_2] * a span[class=equipo left visitante] span[class=team]');
    $resultados = $datos->find('div[id=div_jornada_'.$N_JORNADA.'_1_2] * a span[class=hora_resultado left] span[class=horario_partido]');

    $clas = array();

    $k = 0;
    for ($j = 0; $j < count($locales); $j++) {
        $resultado = $resultados[$j]->innertext;
        if (strpos($resultado, '-') !== false) {
            $goles = explode('-', $resultado);
            $equipo_local = $locales[$j]->innertext;
            $equipo_visitante = $visitantes[$j]->innertext;
            echo($equipo_local.' '.$goles[0].'-'.$goles[1].' '.$equipo_visitante."\n");

            if ($equipo_visitante == 'Atlético') {
                $equipo_visitante = 'Atlético Madrid';
            } elseif ($equipo_visitante == 'Elche') {
                $equipo_visitante = 'Elche CF';
            } elseif ($equipo_visitante == 'Getafe') {
                $equipo_visitante = 'Getafe CF';
            } elseif ($equipo_visitante == 'Granada') {
                $equipo_visitante = 'Granada CF';
            } elseif ($equipo_visitante == 'Levante') {
                $equipo_visitante = 'Levante UD';
            } elseif ($equipo_visitante == 'Málaga') {
                $equipo_visitante = 'Málaga CF';
            } elseif ($equipo_visitante == 'R. Madrid') {
                $equipo_visitante = 'Real Madrid CF';
            } elseif ($equipo_visitante == 'Sevilla') {
                $equipo_visitante = 'Sevilla FC';
            } elseif ($equipo_visitante == 'Almería') {
                $equipo_visitante = 'UD Almería';
            } elseif ($equipo_visitante == 'Valencia') {
                $equipo_visitante = 'Valencia CF';
            } elseif ($equipo_visitante == 'Villarreal') {
                $equipo_visitante = 'Villarreal CF';
            } elseif ($equipo_visitante == 'FC Barcelona') {
                $equipo_visitante = 'FC Barcelona';
            } elseif ($equipo_visitante == 'Espanyol') {
                $equipo_visitante = 'RCD Espanyol';
            } elseif ($equipo_visitante == 'Athletic') {
                $equipo_visitante = 'Athletic Club';
            } elseif ($equipo_visitante == 'Celta') {
                $equipo_visitante = 'RC Celta de Vigo';
            } elseif ($equipo_visitante == 'R. Sociedad') {
                $equipo_visitante = 'Real Sociedad';
            } elseif ($equipo_visitante == 'Rayo') {
                $equipo_visitante = 'Rayo Vallecano';
            } elseif ($equipo_visitante == 'Eibar') {
                $equipo_visitante = 'SD Eibar';
            } elseif ($equipo_visitante == 'Deportivo') {
                $equipo_visitante = 'R.C. Deportivo';
            } elseif ($equipo_visitante == 'Córdoba') {
                $equipo_visitante = 'Córdoba C.F.';
            }

            if ($equipo_local == 'Atlético') {
                $equipo_local = 'Atlético Madrid';
            } elseif ($equipo_local == 'Elche') {
                $equipo_local = 'Elche CF';
            } elseif ($equipo_local == 'Getafe') {
                $equipo_local = 'Getafe CF';
            } elseif ($equipo_local == 'Granada') {
                $equipo_local = 'Granada CF';
            } elseif ($equipo_local == 'Levante') {
                $equipo_local = 'Levante UD';
            } elseif ($equipo_local == 'Málaga') {
                $equipo_local = 'Málaga CF';
            } elseif ($equipo_local == 'R. Madrid') {
                $equipo_local = 'Real Madrid CF';
            } elseif ($equipo_local == 'Sevilla') {
                $equipo_local = 'Sevilla FC';
            } elseif ($equipo_local == 'Almería') {
                $equipo_local = 'UD Almería';
            } elseif ($equipo_local == 'Valencia') {
                $equipo_local = 'Valencia CF';
            } elseif ($equipo_local == 'Villarreal') {
                $equipo_local = 'Villarreal CF';
            } elseif ($equipo_local == 'FC Barcelona') {
                $equipo_local = 'FC Barcelona';
            } elseif ($equipo_local == 'Espanyol') {
                $equipo_local = 'RCD Espanyol';
            } elseif ($equipo_local == 'Athletic') {
                $equipo_local = 'Athletic Club';
            } elseif ($equipo_local == 'Celta') {
                $equipo_local = 'RC Celta de Vigo';
            } elseif ($equipo_local == 'R. Sociedad') {
                $equipo_local = 'Real Sociedad';
            } elseif ($equipo_local == 'Rayo') {
                $equipo_local = 'Rayo Vallecano';
            } elseif ($equipo_local == 'Eibar') {
                $equipo_local = 'SD Eibar';
            } elseif ($equipo_local == 'Deportivo') {
                $equipo_local = 'R.C. Deportivo';
            } elseif ($equipo_local == 'Córdoba') {
                $equipo_local = 'Córdoba C.F.';
            }

            $clas[$k]['equipo'] = $equipo_local;
            $clas[$k]['goles_favor'] = $goles[0];
            $clas[$k]['goles_contra'] = $goles[1];
            $k++;
            $clas[$k]['equipo'] = $equipo_visitante;
            $clas[$k]['goles_favor'] = $goles[1];
            $clas[$k]['goles_contra'] = $goles[0];
            $k++;

            $db_query = sprintf("INSERT INTO partidos(temporada, jornada, equipo_local, equipo_visitante, goles_local, goles_visitante) VALUES ('%s', '%u','%s','%s','%u','%u')", $TEMPORADA, $N_JORNADA, $equipo_local, $equipo_visitante, $goles[0], $goles[1]);
            $resultado = $connection->query($db_query);
        }
    }
    curl_close($ch);

    $clasificacion = array();
    if ($N_JORNADA == 1) {
        for ($i = 0; $i < count($clas); $i++) {
            $clasificacion[$i]['equipo'] = $clas[$i]['equipo'];
            $clasificacion[$i]['goles_favor'] = $clas[$i]['goles_favor'];
            $clasificacion[$i]['goles_contra'] = $clas[$i]['goles_contra'];
            if ($clas[$i]['goles_favor'] > $clas[$i]['goles_contra']) {
                $clasificacion[$i]['partidos_ganados'] = 1;
                $clasificacion[$i]['partidos_empatados'] = 0;
                $clasificacion[$i]['partidos_perdidos'] = 0;
                $clasificacion[$i]['puntos'] = 3;
            } elseif ($clas[$i]['goles_favor'] < $clas[$i]['goles_contra']) {
                $clasificacion[$i]['partidos_perdidos'] = 1;
                $clasificacion[$i]['partidos_empatados'] = 0;
                $clasificacion[$i]['partidos_ganados'] = 0;
                $clasificacion[$i]['puntos'] = 0;
            } elseif ($clas[$i]['goles_favor'] == $clas[$i]['goles_contra']) {
                $clasificacion[$i]['partidos_empatados'] = 1;
                $clasificacion[$i]['partidos_ganados'] = 0;
                $clasificacion[$i]['partidos_perdidos'] = 0;
                $clasificacion[$i]['puntos'] = 1;
            }
            $clasificacion[$i]['diferencia_goles'] = $clas[$i]['goles_favor'] - $clas[$i]['goles_contra'];
        }
    } else {
        $N_JORNADA_anterior = $N_JORNADA - 1;

        for ($i = 0; $i < count($clas); $i++) {
            $clasificacion[$i]['equipo'] = $clas[$i]['equipo'];
            $equipo = $clasificacion[$i]['equipo'];
            $db_query = sprintf("SELECT puntos, partidos_ganados, partidos_perdidos, partidos_empatados, goles_favor, goles_contra, diferencia_goles from rankings where temporada = \"$TEMPORADA\" and jornada = \"$N_JORNADA_anterior\" and equipo=\"$equipo\"");
            $resultado = $connection->query($db_query);
            print_r($resultado);
            $clasificacion[$i]['goles_favor'] = $clas[$i]['goles_favor'] + $resultado[9]['goles_favor'];
            $clasificacion[$i]['goles_contra'] = $clas[$i]['goles_contra'] + $resultado[11]['goles_contra'];
            if ($clas[$i]['goles_favor'] > $clas[$i]['goles_contra']) {
                $clasificacion[$i]['partidos_ganados'] = $resultado[3]['partidos_ganados'] + 1;
                $clasificacion[$i]['partidos_empatados'] = $resultado[7]['partidos_empatados'];
                $clasificacion[$i]['partidos_perdidos'] = $resultado[5]['partidos_perdidos'];
                $clasificacion[$i]['puntos'] = $resultado[1]['puntos'] + 3;
            } elseif ($clas[$i]['goles_favor'] < $clas[$i]['goles_contra']) {
                $clasificacion[$i]['partidos_perdidos'] = $resultado[5]['partidos_perdidos'] + 1;
                $clasificacion[$i]['partidos_empatados'] = $resultado[7]['partidos_empatados'];
                $clasificacion[$i]['partidos_ganados'] = $resultado[3]['partidos_ganados'];
                $clasificacion[$i]['puntos'] = $resultado[1]['puntos'];
            } elseif ($clas[$i]['goles_favor'] == $clas[$i]['goles_contra']) {
                $clasificacion[$i]['partidos_empatados'] =  $resultado[7]['partidos_empatados'] + 1;
                $clasificacion[$i]['partidos_ganados'] = $resultado[3]['partidos_ganados'];
                $clasificacion[$i]['partidos_perdidos'] = $resultado[5]['partidos_perdidos'];
                $clasificacion[$i]['puntos'] = $resultado[1]['puntos'] + 1;
            }
            $clasificacion[$i]['diferencia_goles'] = $resultado[13]['diferencia_goles'] + $clas[$i]['goles_favor'] - $clas[$i]['goles_contra'];
        }
    }

    foreach ($clasificacion as $clave => $fila) {
        $puntos[$clave] = $fila['puntos'];
        $goles[$clave] = $fila['goles_favor'];
        $dif[$clave] = $fila['diferencia_goles'];
    }
    array_multisort($puntos, SORT_DESC, $goles, SORT_DESC, $dif, SORT_DESC, $clasificacion);

    for ($i = 0; $i < count($clasificacion); $i++) {
        $db_query = sprintf("INSERT INTO rankings(equipo, posicion, puntos, partidos_ganados, partidos_empatados, partidos_perdidos, goles_favor, goles_contra, diferencia_goles, jornada, temporada) VALUES ('%s', %u, %u, %u, %u, %u, %u, %u, %d, %u, '%s')",
        $clasificacion[$i]['equipo'],
      $i+1,
      intval($clasificacion[$i]['puntos']),
      intval($clasificacion[$i]['partidos_ganados']),
      intval($clasificacion[$i]['partidos_empatados']),
      intval($clasificacion[$i]['partidos_perdidos']),
      intval($clasificacion[$i]['goles_favor']),
      intval($clasificacion[$i]['goles_contra']),
      intval($clasificacion[$i]['diferencia_goles']),
      intval($N_JORNADA),
      $TEMPORADA);
        $resultado = $connection->query($db_query);
    }
    print_r($clasificacion);


    $connection->close();
