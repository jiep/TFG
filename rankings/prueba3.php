<?php

include("autoload.php");

$r1 = new Ranking();
$r1->add("1");
$r1->add("2");
$r1->add("3");

$r2 = new Ranking();
$r2->add("1");
$r2->add("2");
$r2->add("3");
//print_r($r2);

$r3 = new Ranking();
$r3->add("2");
$r3->add("1");
$r3->add("3");
//print_r($r3);

$r = array($r1, $r2, $r3);
$rankings = new RankingCollection($r);
//print_r($rankings);

print_r($rankings->calculateEvolutiveCompetitivityGraph());

?>
