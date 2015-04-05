<?php

include 'autoload.php';
include '../rankings/Ranking.php';
include '../rankings/RankingCollection.php';
include '../rankings/Graph.php';
include '../rankings/EvolutiveCompetitivityGraph.php';
require_once 'connection.inc.php';

function getLastSeason()
{
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

function getLineChart($temporada = null)
{
    $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USERNAME, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($temporada == null) {
        $temporada = getLastSeason();
    }

    $num_equipos = $dbh->prepare("SELECT DISTINCT equipo from rankings where temporada = \"$temporada\"");
    $num_equipos->execute();
    $n_equipos = $num_equipos->fetchAll();

    $num_jorn = $dbh->prepare("SELECT MAX(jornada) as MAX from rankings where temporada = \"$temporada\" ");
    $num_jorn->execute();
    $jornada = $num_jorn->fetch();
    $equipos = array();

    foreach ($n_equipos as $equipo) {
        if (array_key_exists('equipo', $equipo)) {
            $equipos[] = $equipo['equipo'];
        }
    }

    $jornadas = array();

    for ($i = 0; $i < $jornada['MAX']; $i++) {
        $jornadas[$i] = $i + 1;
    }

    $data = array();

    foreach ($equipos as $equipo) {
        $ranking = $dbh->prepare("SELECT posicion from rankings where temporada = \"$temporada\" and equipo = \"$equipo\" order by jornada");
        $ranking->execute();
        $rankings = $ranking->fetchAll();

        $datos = array();
        foreach ($rankings as $rank) {
            if (array_key_exists('posicion', $rank)) {
                $datos[] = $rank['posicion'];
            }
        }
        $data[] = $datos;
    }

    $json = json_encode(array('teams' => $equipos, 'labels' => $jornadas, 'datasets' => $data));

    return $json;
}
