<?php

include("autoload.php");

$r1 = new Ranking();
$r1->add("Real Madrid");
$r1->add("Betis");
$r1->add("Barça");
//print_r($r1);

//echo "Es: " . $r1->getPosition("Barça") . "\n";

$r2 = new Ranking();
$r2->add("Barça");
$r2->add("Real Madrid");
$r2->add("Betis");
//print_r($r2);


$r3 = new Ranking();
$r3->add("Barça");
$r3->add("Real Madrid");
$r3->add("Betis");
//print_r($r3);

$r = array($r1, $r2, $r3);
$rankings = new RankingCollection($r);
//print_r($rankings);

//print_r($rankings->calculateEvolutiveCompetitivityGraph());

echo $rankings->normalizedMeanDegree() . "\n";


?>
