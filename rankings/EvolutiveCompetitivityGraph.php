<?php

class EvolutiveCompetitivityGraph extends Graph {

  function EvolutiveCompetitivityGraph($elements, $adjacencyMatrix){
    $this->elements = $elements;
    $this->adjacencyMatrix = $adjacencyMatrix;
  }

  function getAdjacencyMatrix(){
    return $this->adjacenyMatrix;
  }

  function getElements(){
    return $this->elements;
  }

  function exportAsCytoscapeJSON(){
    $container = htmlentities("document.getElementById(\"cy\")");
    $nodes = array();
    $edges = array();

    $count = 0;
    for($i = 0; $i < $this->elements->getLength(); $i++){
      $nodes[$i] = array("data" => array("id" => $this->elements->get($i)));
      for($j = 0; $j < $i; $j++){
        if($this->adjacencyMatrix[$i][$j] !== 0){
          $edges[$count] = array("data" => array("source" => $i, "target" => $j, "weight" => $this->adjacencyMatrix[$i][$j]));
          $count++;
        }
      }
    }


    return json_encode(array("nodes" => $nodes, "edges" => $edges));

  }

}


?>
