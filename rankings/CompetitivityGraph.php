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

}


?>
