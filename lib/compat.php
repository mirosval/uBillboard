<?php

if(!function_exists('d')) {
	/**
	 *	Function, debug facility
	 *	
	 *	@return void
	 */
	function d($var) {
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
}

if(false === function_exists('lcfirst')){
	/**
	 *	Function, used in camelize methods of uBillboard and uBillboardSlide classes
	 *	lowercases the first letter of a string
	 *	
	 *	@param string $str string to lowercase first
	 *	@return string string with lowercased first letter
	 */
    function lcfirst( $str ) 
    {
    	return (string)(strtolower(substr($str,0,1)).substr($str,1));
    } 
}

?>