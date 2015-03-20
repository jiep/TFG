<?php

class EvolutiveCompetitivityGraph extends Graph {

  var $rankings_number;

  function EvolutiveCompetitivityGraph($elements, $adjacencyMatrix, $rankings_number){
    $this->elements = $elements;
    $this->adjacencyMatrix = $adjacencyMatrix;
    $this->rankings_number = $rankings_number;
  }

  function getAdjacencyMatrix(){
    return $this->adjacencyMatrix;
  }

  function getElements(){
    return $this->elements;
  }

  function exportAsCytoscapeJSON(){
    $nodes = array();
    $edges = array();

    $count = 0;
    for($i = 0; $i < $this->elements->getLength(); $i++){
      $nodes[$i] = array("data" => array("id" => "$i", "name"=>$this->elements->get($i)));
      for($j = 0; $j < $i; $j++){
        if($this->adjacencyMatrix[$i][$j] !== 0){
          $edges[$count] = array("data" => array("source" => "$i", "target" => "$j", "weight" => $this->adjacencyMatrix[$i][$j]));
          $count++;
        }
      }
    }

    return json_encode(array("elements" => array("nodes" => $nodes, "edges" => $edges)));

  }

  function normalizedMeanDegree(){
    $cont = 0;
    $graph = $this->getAdjacencyMatrix();
    $elements = $this->elements;
    $n = $elements->getLength();
    for ($i = 0; $i <$n; $i++){
      for ($j = 0; $j < $n; $j++){
        if($graph[$i][$j] != 0){
          $cont++;
        }
      }
    }

    return $cont/($n*($n-1));
  }

  function normalizedDegreeDistribution($k){
    $sum = 0;
    $graph = $this->getAdjacencyMatrix();
    $elements = $this->elements;
    $n = $elements->getLength();
    for ($i = 0; $i < $n; $i++){
      $deg = 0;
      for($j = 0; $j < $n; $j++){
        if($graph[$i][$j] !=0 ){
          $deg++;
        }
      }
      if($deg == $k){
        $sum++;
      }
    }

    return $sum/$n;
  }

  function normalizedCumulativeDegreeDistribution($k){
    $sum = 0;
    $graph = $this->getAdjacencyMatrix();
    $elements = $this->elements;
    $n = $elements->getLength();
    for($i = 0; $i < $n; $i++){
      $deg = 0;
      for($j = 0; $j < $n; $j++){
        if($graph[$i][$j] != 0){
          $deg++;
        }
      }

      if ($deg>=$k){
        $sum++;
      }
    }

    return $sum/$n;
  }

  function normalizedMeanStrength(){
    $sum = 0;
    $graph = $this->getAdjacencyMatrix();
    $elements = $this->elements;
    $n = $elements->getLength();
    for ($i = 0;$i < $n; $i++){
      for ($j = 0; $j < $i; $j++){
          $sum += $graph[$i][$j];
      }
    }

    return 2*$sum/($n*($n - 1)*($this->rankings_number - 1));
  }

  function normalizedStrengthDistribution($k){
    $sum = 0;
    $graph = $this->getAdjacencyMatrix();
    $elements = $this->elements;
    $n = $elements->getLength();
    for ($i = 0; $i < $n; $i++){
      $str = 0;
      for($j = 0; $j < $n; $j++){
        if($graph[$i][$j] !=0 ){
          $str+=$graph[$i][$j];
        }
      }
      if($str == $k){
        $sum++;
      }
    }

    return $sum/$n;
  }

  function normalizedCumulativeStrengthDistribution($k){
    $sum = 0;
    $graph = $this->getAdjacencyMatrix();
    $elements = $this->elements;
    $n = $elements->getLength();
    for($i = 0; $i < $n; $i++){
      $str = 0;
      for($j = 0; $j < $n; $j++){
        if($graph[$i][$j] != 0){
          $str++;
        }
      }

      if ($str>=$k){
        $sum++;
      }
    }

    return $sum/$n;
  }

  function generalizedKendallsTau(){
    return 1 - 2*$this->normalizedMeanStrength();
  }

  /*private function depthFirstSearch($v) {
      $count = 0;
      $graph = $this->calculateEvolutiveCompetitivityGraph()->getAdjacencyMatrix();
      $elements = $this->rankings[0];
      $n = $elements->getLength();
      $position = $elements->getPosition($v);
      $marked = array();
      $id = array();
      $size = array();
      $marked[$v] = true;
      $id[$v] = $count;
      $size[$count]++;
      for ($i=0;$i<$n;$i++) {
        if ((!$marked[$i])&&($graph[$position][$i]!=0)) {
          depthFirstSearch($i);
        }
      }
      return array($count,$marked,$id,$size);
    }


  function connectedComponents() {
     $marked = array();
     $id = array();
     $size = array();
     $graph = $this->calculateEvolutiveCompetitivityGraph()->getAdjacencyMatrix();
     $elements = $this->rankings[0];
     $n = $elements->getLength();
     for ($i=0;$i<$n;$i++) {
         if (!$marked[$i]) {
           $this->depthFirstSearch($i);
           $count++;
       }
     }
     return $count;
   }
   */

}


?>
