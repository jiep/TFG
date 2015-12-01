<?php
require_once '../../parser/connection.inc.php';

define("LAMBDA",0);

function conjugar($array_interp,$array_tend){
	
	$conj = array();
	$lambda = LAMBDA;	

	for($i=0;$i<10;$i++){
		$conj[$i]["local_team"]= $array_interp[$i]["local_team"];
		$conj[$i]["visitor_team"]= $array_interp[$i]["visitor_team"];
		$conj[$i]["local_win"]=	$lambda * $array_interp[$i]["local_win"] + (1 - $lambda) * $array_tend[$i]["local_win"];
		$conj[$i]["tie"]= $lambda * $array_interp[$i]["tie"] + (1 - $lambda) * $array_tend[$i]["tie"];
		$conj[$i]["visitor_win"]= $lambda * $array_interp[$i]["visitor_win"] + (1 - $lambda) * $array_tend[$i]["visitor_win"];
	}	

	return $conj;
}

function conjugarconlambda($array_interp,$array_tend,$lambda){
	
	$conj = array();	

	for($i=0;$i<10;$i++){
		$conj[$i]["local_win"]=	$lambda * $array_interp[$i]["local_win"] + (1 - $lambda) * $array_tend[$i]["local_win"];
		$conj[$i]["tie"]= $lambda * $array_interp[$i]["tie"] + (1 - $lambda) * $array_tend[$i]["tie"];
		$conj[$i]["visitor_win"]= $lambda * $array_interp[$i]["visitor_win"] + (1 - $lambda) * $array_tend[$i]["visitor_win"];
	}	

	return $conj;
}



?>
