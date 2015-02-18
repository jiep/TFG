<?php

class CompetitivityGraph extends Graph {

  function CompetitivityGraph($elements, $adjacenyMatrix){
    $this->elements = $elements;
    $this->adjacencyMatrix = $adjacenyMatrix;
  }

  function getAdjacencyMatrix(){
    return $this->adjacenyMatrix;
  }

  function getElements(){
    return $this->elements;
  }

}


?>
