<?php

abstract class Graph {

  var $elements;
  var $adjacencyMatrix;

  function Grafo($elements, $adjacenyMatrix){
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
