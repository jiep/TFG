<?php

include("autoload.php");


function randomRanking($number_teams){

  $ranking = new Ranking();

  $numbers = range(1, $number_teams);
  shuffle($numbers);
  foreach ($numbers as $number) {
    $ranking->add("$number");
  }

  return $ranking;
}



$rankings_number = $_POST["rankings"] != null ? $_POST["rankings"] : 38;
$number_teams = $_POST["teams"] != null ? $_POST["teams"] : 20;

$rankings = array();

for($i = 0; $i < $number_teams; $i++){
  $rankings[$i] = randomRanking($number_teams);
}

$season = new RankingCollection($rankings);
$json = $season->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON();

echo($json . "\n") ;

?>
