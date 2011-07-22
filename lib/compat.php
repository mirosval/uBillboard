<?php

if(!function_exists('d')) {
	function d($var) {
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
}

if(false === function_exists('lcfirst')){ 
    function lcfirst( $str ) 
    {
    	return (string)(strtolower(substr($str,0,1)).substr($str,1));
    } 
}

?>