<?php

include("autoload.php");

$r1 = new Ranking();
$r1->add("1");
$r1->add("2");
$r1->add("3");
$r1->add("4");
$r1->add("5");
$r1->add("6");
//print_r($r1);


$r2 = new Ranking();
$r2->add("1");
$r2->add("3");
$r2->add("4");
$r2->add("2");
$r2->add("5");
$r2->add("6");
//print_r($r2);


$r3 = new Ranking();
$r3->add("1");
$r3->add("2");
$r3->add("5");
$r3->add("3");
$r3->add("4");
$r3->add("6");
//print_r($r3);

$r4 = new Ranking();
$r4->add("3");
$r4->add("2");
$r4->add("6");
$r4->add("1");
$r4->add("5");
$r4->add("4");

$r = array($r1, $r2, $r3, $r4);
$rankings = new RankingCollection($r);
//print_r($rankings);

print_r($rankings->calculateEvolutiveCompetitivityGraph());

//echo $rankings->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON() . "\n";

echo $rankings->normalizedMeanStrength() . "\n";

?>
