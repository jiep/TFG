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

    //$elements = $this->rankings->get(0)->getRanking();
    $elements = null;
    $n = count($elements);
    $adjacencyMatrix = array();

    foreach($this->rankings as $ranking){
      for($i = 0; $i < $ranking->getLength() - 1; $i++){
        for($j = 0; $j < $ranking->getLength() - 1 - $i; $j++){
          if($ranking->get($j) < $ranking->get($j+1)){
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
