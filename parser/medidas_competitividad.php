<?php

include 'autoload.php';
include '../../rankings/Ranking.php';
include '../../rankings/RankingCollection.php';
include '../../rankings/Graph.php';
include '../../rankings/EvolutiveCompetitivityGraph.php';
include '../../rankings/CompetitivityGraph.php';
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

function medidas_competitividad($temporada = null)
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

    if ($temporada == null) {
        $temporada = getLastSeason();
    }

    $num_jorn = $dbh->prepare("SELECT MAX(jornada) as max from rankings where temporada = \"$temporada\"");
    $num_jorn->execute();
    $jornadas = $num_jorn->fetch(PDO::FETCH_ASSOC);

    $ranking_col = array();

    for ($i = 1;$i <= $jornadas['max']; $i++) {
        $datos = $dbh->prepare("SELECT equipo from rankings where temporada = \"$temporada\" and jornada = $i ORDER BY posicion");
        $datos->execute();
        $equipos = $datos->fetchAll();

        $ranking = new Ranking();
        foreach ($equipos as $equipo) {
            if (array_key_exists('equipo', $equipo)) {
                $ranking->add($equipo['equipo']);
            }
        }
        $ranking_col[$i-1] = $ranking;
    }
    $rankings = new RankingCollection($ranking_col);

    $graph = $rankings->calculateEvolutiveCompetitivityGraph();
    $graph2 = $rankings->calculateCompetitivityGraph();
    $mat = $graph->getAdjacencyMatrix();

    $k = 0;
    for ($i = 0; $i < count($mat); $i++) {
        $sum = 0;
        for ($j = 0; $j < count($mat); $j++) {
            if ($mat[$i][$j] != 0) {
                $sum++;
            }
        }
        if ($sum > $k) {
            $k = $sum;
        }
    }

    $w = 0;
    for ($i = 0; $i < count($mat); $i++) {
        $sum = 0;
        for ($j = 0; $j < count($mat); $j++) {
            if ($mat[$i][$j] != 0) {
                $sum++;
            }
        }
        if ($sum > $w) {
            $w = $sum;
        }
    }

    $ndd = array();
    $ncdd = array();
    $nsd = array();
    $ncsd = array();
    $labels_array = array();

    for($i=0;$i<=max($k,$w);$i++){
        $labels_array[$i] = $i;
    }

    for ($i = 0; $i <= $k; $i++) {
        $ndd[$i] = $graph->normalizedDegreeDistribution($i);
        $nsd[$i] = $graph->normalizedStrengthDistribution($i);
        $ncsd[$i] = $graph->normalizedCumulativeStrengthDistribution($i);
    }

    for ($i = 0; $i <= $w; $i++) {
        $ncdd[$i] = $graph2->normalizedCumulativeDegreeDistribution($i);
    }

    $labels = array('Fuerza media normalizada', 'Tau de Kendall generalizada', 'Grado medio normalizado','Diámetro','Longitud del camino característico','Eficiencia');

    $nms = $graph->normalizedMeanStrength();
    $gkt = $graph->generalizedKendallsTau();
    $nmd = $graph->normalizedMeanDegree();
    $diam = $graph2->diameter();
    $cpl = $graph2->characteristicPathLength();
    $efic = $graph2->efficiency();

    $json = json_encode(array('labels_array' => $labels_array, 'labels' => $labels, 'ndd' => $ndd, 'ncdd' => $ncdd, 'nsd' => $nsd, 'ncsd' => $ncsd, 'measures' => array($nmd, $nms, $gkt,  $diam,  $cpl, $efic)));

    return $json;
}

//echo $json;


?>
