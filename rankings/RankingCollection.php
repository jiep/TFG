<?php

class RankingCollection {

  var $rankings;

  function RankingCollection($rankings){
    foreach($rankings as $ranking){
      if(($ranking instanceof Ranking)){
        break;
      }
    }
    $this->rankings = $rankings;
  }

  function getRankings(){
    return $this->rankings;
  }

  function get($i){
    $rank = null;
    if(array_key_exists($i, $this->rankings)){
      $rank = $this->rankings[$i];
    }

    return $rank;
  }

  function calculateCompetitivityGraph(){

    $elements = $this->rankings[0];
    //$elements = null;
    $n = $elements->getLength();
    $adjacencyMatrix = array();

    for($k = 0;$k<=count($this->rankings)-2;$k++){
      $k1 = $this->get($k);
      $k2 = $this->get($k+1);
      print_r($k1);
      print_r($k2);
      for($i = 0; $i < $n - 1; $i++){
        for($j = 0; $j < $n - 1 - $i; $j++){
          $pos_i_r1 = $k1->get($elements->getRanking()[$i]);
          $pos_j_r1 = $k1->get($elements[$j]);
          $pos_i_r2 = $k2->get($elements[$i]);
          $pos_j_r2 = $k2->get($elements[$j]);
          $sign_r1 = gmp_sign($pos_i_r1 - $pos_j_r1);
          $sign_r2 = gmp_sign($pos_i_r2 - $pos_j_r2);
          echo "Signo r1: " . $sign_r1 . "\n";
          echo "Signo r2: " . $sign_r2 . "\n";

          if($sign_r1 !== $sign_r2){
              $adjacencyMatrix[$i][$j]++;
              $adjacencyMatrix[$j][$i]++;
          }
        }
      }
    }

    return new CompetitivityGraph($elements, $adjacencyMatrix);

  }

}


?>
