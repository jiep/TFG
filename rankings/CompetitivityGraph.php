<?php

class CompetitivityGraph extends Graph {

  function CompetitivityGraph($elements, $adjacenyMatrix){
    $this->elements = $elements;
    $this->adjacenyMatrix = $adjacenyMatrix;
  }

  function getAdjacencyMatrix(){
    return $this->adjacenyMatrix;
  }

  function getElements(){
    return $this->elements;
  }

  function exportAsCytoscapeJSON(){
    $nodes = array();
    $edges = array();

    $count = 0;
    for($i = 0; $i < $this->elements->getLength(); $i++){
      $nodes[$i] = array("data" => array("id" => $this->elements->get($i)));
      for($j = 0; $j < $i; $j++){
        if($this->adjacencyMatrix[$i][$j] !== 0){
          $edges[$count] = array("data" => array("source" => $this->elements->get($i), "target" => $this->elements->get($j), "weight" => $this->adjacencyMatrix[$i][$j]));
          $count++;
        }
      }
    }


    return json_encode(array("elements" => array("nodes" => $nodes, "edges" => $edges)));

  }

  function floyd(){
    $infinito = 10000;
    $graph = $this->getAdjacencyMatrix();
    $elements = $this->elements;
    $n = $elements->getLength();
    $ans = (new ArrayObject($graph))->getArrayCopy();

    for($i=0;$i<$n;$i++){
      for ($j = 0; $j < $n; $j++){
        if(($i!=$j)&&($ans[$i][$j]==0)){
          $ans[$i][$j]=$infinito;
        }
      }
    }

    for ($k = 0; $k < $n; $k++){
      for ($i = 0; $i <$n; $i++){
        for ($j = 0; $j < $n; $j++){
          if($ans[$i][$k]+$ans[$k][$j]<$ans[$i][$j]){
            $ans[$i][$j]=$ans[$i][$k]+$ans[$k][$j];
          }
        }
      }
    }

    return $ans;

  }

  function diameter(){
    $ans = $this->floyd();
    $elements = $this->elements;
    $n = $elements->getLength();
    $max=0;
    for($i=0;$i<$n;$i++){
      for ($j = 0; $j < $n; $j++){
        if($ans[$i][$j]>$max) {
          $max=$ans[$i][$j];
        }
      }
    }
    return $max;
  }

  function characteristicPathLength(){
    $ans = $this->floyd();
    $elements = $this->elements;
    $n = $elements->getLength();
    $sum=0;
    for($i=0;$i<$n;$i++){
      for ($j = 0; $j < $n; $j++){
        if($i!=$j) {
          $sum+=$ans[$i][$j];
        }
      }
    }
    return 2*$sum/($n*($n-1));
  }

  function efficiency(){
    $ans = $this->floyd();
    $elements = $this->elements;
    $n = $elements->getLength();
    $sum=0;
    for($i=0;$i<$n;$i++){
      for ($j = 0; $j < $n; $j++){
        if($i!=$j) {
          $sum+=1/$ans[$i][$j];
        }
      }
    }
    return 2*$sum/($n*($n-1));
  }

}//FIN

?>
