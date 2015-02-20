<?php

class Ranking {

  var $length;
  var $ranking;

  function Ranking(){
    $this->length = 0;
    $this->ranking = array();
  }

  function getRanking(){
    return $this->ranking;
  }

  function getLength(){
    return $this->length;
  }

  function add($element){
    if (($key = array_search($element, $this->ranking)) === false) {
      $this->ranking[$this->length] = $element;
      $this->length += 1;
    }
  }

  function remove($element){
    if(in_array($element, $this->ranking)){
      if (($key = array_search($element, $this->ranking)) !== false) {
        unset($this->ranking[$key]);
        $this->ranking = array_values($this->ranking);
        $this->length--;
      }
    }
  }

  function get($i){
    $key = null;
    if($key = array_search($i, $this->ranking) !== false){
    }

    return $key;
  }

}

?>
