<?php

class uBillboardSlide {	
	public static function getSlides($options)
	{
		global $uds_billboard_attributes;
		
		$slides = array();
		
		$n = 0;
		while(!empty($options) && $n < 100) {
			$attributes = array();
			foreach($uds_billboard_attributes as $key => $option) {
				if(!isset($options[$key][$n])) {
					break;
				}
				$attributes[$key] = $options[$key][$n];
				unset($options[$key][$n]);
				
				if(empty($options[$key])) {
					unset($options[$key]);
				}
			}

			if(!empty($attributes)) {
				$slide = new uBillboardSlide($attributes);
				if($slide !== null) $slides[] = $slide;
			}
			
			$n++;
		}
		
		return $slides;
	}
	
	public function __construct($options)
	{
		global $uds_billboard_attributes;

		if(empty($options['image'])){
			return null;
		}

		foreach($uds_billboard_attributes as $key => $option) {
			$this->{$key} = $options[$key];
		}
	}
}

?>