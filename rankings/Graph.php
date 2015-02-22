<?php

abstract class Graph {

  var $elements;
  var $adjacencyMatrix;

  function Graph($elements, $adjacenyMatrix){
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
