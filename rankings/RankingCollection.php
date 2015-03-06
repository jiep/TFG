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

  function sign($number) {
    return ($number > 0) ? 1 : (( $number < 0 ) ? -1 : 0 );
  }

  function calculateEvolutiveCompetitivityGraph(){

    $elements = $this->rankings[0];

    $n = $elements->getLength();
    $adjacencyMatrix = array();

    for($i = 0; $i < $n; $i++){
      for($j = 0; $j < $n; $j++){
        $adjacencyMatrix[$i][$j] = 0;
      }
    }

    for($k = 0;$k < count($this->rankings)-1;$k++){
      $k1 = $this->get($k);
      $k2 = $this->get($k+1);
      for($i = 0; $i < $n; $i++){
        for($j = 0; $j < $i; $j++){
          $pos_i_r1 = $k1->getPosition($elements->get($i));
          $pos_j_r1 = $k1->getPosition($elements->get($j));
          $pos_i_r2 = $k2->getPosition($elements->get($i));
          $pos_j_r2 = $k2->getPosition($elements->get($j));
          $sign_r1 = $this->sign($pos_i_r1 - $pos_j_r1);
          $sign_r2 = $this->sign($pos_i_r2 - $pos_j_r2);

          if($sign_r1 !== $sign_r2){
              $adjacencyMatrix[$i][$j]++;
              $adjacencyMatrix[$j][$i]++;
          }
        }
      }
    }

    return new EvolutiveCompetitivityGraph($elements, $adjacencyMatrix);

  }


  function normalizedMeanDegree(){
    $cont=0;
    $graph = $this->calculateEvolutiveCompetitivityGraph()->getAdjacencyMatrix();
    $elements = $this->rankings[0];
    $n = $elements->getLength();
    for ($i=0;$i<$n;$i++){
      for ($j=0;$j<$n;$j++){
        if ($graph[$i][$j]!=0)
          $cont++;
      }
    }

    return $cont/($n*($n-1));
  }

  function normalizedMeanStrength(){
    $sum=0;
    $graph = $this->calculateEvolutiveCompetitivityGraph()->getAdjacencyMatrix();
    $elements = $this->rankings[0];
    $n = $elements->getLength();
    for ($i=0;$i<$n;$i++){
      for ($j=0;$j<$i;$j++){
          $sum=$sum+$graph[$i][$j];
      }
    }

    return 2*$sum/($n*($n-1)*(count($this->rankings)-1));
  }

  function generalizedKendallsTau(){
    return 1-2*$this->normalizedMeanStrength();
  }



}//Fin de clase




?>
