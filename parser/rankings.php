<?php

require 'simple_html_dom.php';
require_once 'connection.inc.php';
require_once 'Connection.php';

$connection = new Connection(DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);
print_r($connection);
$connection->connect();
$connection->selectDatabase();

$N_JORNADAS = 20;
$TEMPORADA = '2014/2015';

echo "Descargando datos de lfp.es ... \n\n";

$ch = curl_init('http://www.lfp.es/includes/ajax.php?action=cambiar_jornada_widget_liga');

for ($i = 1; $i <= $N_JORNADAS; $i++) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'num_jornada='.$i.'&competicion=1&temporada=2');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $respuesta = curl_exec($ch);

    $datos = new simple_html_dom($respuesta);

    $locales = $datos->find('div[id=div_jornada_'.$i.'_1_2] * a span[class=equipo left local] span[class=team]');
    $visitantes = $datos->find('div[id=div_jornada_'.$i.'_1_2] * a span[class=equipo left visitante] span[class=team]');
    $resultados = $datos->find('div[id=div_jornada_'.$i.'_1_2] * a span[class=hora_resultado left] span[class=horario_partido]');

    echo "Jornada $i \n-------------------------------- \n";

    for ($j = 0; $j < count($locales); $j++) {
        $resultado = $resultados[$j]->innertext;
        if (strpos($resultado, '-') !== false) {
            $goles = explode('-', $resultado);
            $equipo_local = $locales[$j]->innertext;
            $equipo_visitante = $visitantes[$j]->innertext;

            if ($nombre_visitante == 'At. de Madrid') {
                $equipo_visitante = 'Atlético Madrid';
            } elseif ($equipo_visitante == 'Elche C.F.') {
                $equipo_visitante = 'Elche CF';
            } elseif ($equipo_visitante == 'Getafe C.F.') {
                $equipo_visitante = 'Getafe CF';
            } elseif ($equipo_visitante == 'Granada C.F.') {
                $equipo_visitante = 'Granada CF';
            } elseif ($equipo_visitante == 'Levante U.D.') {
                $equipo_visitante = 'Levante UD';
            } elseif ($equipo_visitante == 'Málaga C.F.') {
                $equipo_visitante = 'Málaga CF';
            } elseif ($equipo_visitante == 'Real Betis B. S.') {
                $equipo_visitante = 'Real Betis';
            } elseif ($equipo_visitante == 'Real Madrid C.F.') {
                $equipo_visitante = 'Real Madrid CF';
            } elseif ($equipo_visitante == 'Sevilla F.C.') {
                $equipo_visitante = 'Sevilla FC';
            } elseif ($equipo_visitante == 'U.D. Almería') {
                $equipo_visitante = 'UD Almería';
            } elseif ($equipo_visitante == 'Valencia C.F.') {
                $equipo_visitante = 'Valencia CF';
            } elseif ($equipo_visitante == 'Villarreal C.F.') {
                $equipo_visitante = 'Villarreal CF';
            } elseif ($equipo_visitante == 'C. At. Osasuna') {
                $equipo_visitante = 'CA Osasuna';
            } elseif ($equipo_visitante == 'F.C. Barcelona') {
                $equipo_visitante = 'FC Barcelona';
            }

            if ($equipo_local == 'Atlético Madrid') {
                $equipo_local = 'At. de Madrid';
            } elseif ($equipo_local == 'Elche C.F.') {
                $equipo_local = 'Elche CF';
            } elseif ($equipo_local == 'Getafe C.F.') {
                $equipo_local = 'Getafe CF';
            } elseif ($equipo_local == 'Granada C.F.') {
                $equipo_local = 'Granada CF';
            } elseif ($equipo_local == 'Levante U.D.') {
                $equipo_local = 'Levante UD';
            } elseif ($equipo_local == 'Málaga C.F.') {
                $equipo_local = 'Málaga CF';
            } elseif ($equipo_local == 'Real Betis B. S.') {
                $equipo_local = 'Real Betis';
            } elseif ($equipo_local == 'Real Madrid C.F.') {
                $equipo_local = 'Real Madrid CF';
            } elseif ($equipo_local == 'Sevilla F.C.') {
                $equipo_local = 'Sevilla FC';
            } elseif ($equipo_local == 'U.D. Almería') {
                $equipo_local = 'UD Almería';
            } elseif ($equipo_local == 'Valencia C.F.') {
                $equipo_local = 'Valencia CF';
            } elseif ($equipo_local == 'Villarreal C.F.') {
                $equipo_local = 'Villarreal CF';
            } elseif ($equipo_local == 'C. At. Osasuna') {
                $equipo_local = 'CA Osasuna';
            } elseif ($equipo_local == 'F.C. Barcelona') {
                $equipo_local = 'FC Barcelona';
            } elseif ($equipo_local == 'R.C.D. Espanyol') {
                $equipo_local = 'RCD Espanyol';
            }

            echo($equipo_local.' '.$goles[0].'-'.$goles[1].' '.$equipo_visitante."\n");

            $db_query = sprintf("INSERT INTO partidos(temporada, jornada, equipo_local, equipo_visitante, goles_local, goles_visitante) VALUES ('%s', '%u','%s','%s','%u','%u')", $TEMPORADA, $i, $equipo_local, $equipo_visitante, $goles[0], $goles[1]);
            $resultado = $connection->query($db_query);
        }
    }

    echo "--------------------------------\n";
}

curl_close($ch);

$connection->close();
