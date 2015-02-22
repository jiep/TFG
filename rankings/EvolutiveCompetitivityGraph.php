<?php

class EvolutiveCompetitivityGraph extends Graph {

  function EvolutiveCompetitivityGraph($elements, $adjacenyMatrix){
    $this->elements = $elements;
    $this->adjacenyMatrix = $adjacenyMatrix;
  }

  function getAdjacencyMatrix(){
    return $this->adjacenyMatrix;
  }

  function getElements(){
    return $this->elements;
  }

}


?>
