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
	
	public function __construct($options = false)
	{
		global $uds_billboard_attributes;

		if($options === false) {
			foreach($uds_billboard_attributes as $key => $option) {
				if(isset($option['default'])) {
					$this->{$key} = $option['default'];
				} else {
					$this->{$key} = '';
				}
			}
			
			return;
		}

		if(empty($options['image']) && empty($options['text'])){
			return null;
		}

		foreach($uds_billboard_attributes as $key => $option) {
			$this->{$key} = $options[$key];
		}
	}
	
	public function update($options)
	{
		global $uds_billboard_attributes;

		if($options === false) {
			foreach($uds_billboard_attributes as $key => $option) {
				if(isset($option['default'])) {
					$this->{$key} = $option['default'];
				} else {
					$this->{$key} = '';
				}
			}
			
			return;
		}

		if(empty($options['image'])){
			return null;
		}

		foreach($uds_billboard_attributes as $key => $option) {
			$this->{$key} = $options[$key];
		}
	}
	
	public function isValid()
	{
		return !empty($this->image) || !empty($this->text);
	}
	
	public function renderAdmin()
	{
		global $uds_billboard_attributes;

		foreach($uds_billboard_attributes as $attrib => $options) {
			uds_billboard_render_field($this, $attrib);
		}
	}
	
	public function render()
	{
		// Transition, make nil when embedded content exists
		$transition = $this->transition;
		if(strpos($this->text, 'uds-embed')) {
			$transition = 'none';
		}
		
		// Direction
		$direction = $this->direction;
		if($direction === 'random') {
			$directions = array('left', 'right', 'top', 'bottom');
			$direction = $directions[array_rand($directions)];
		}
		
		$out = "<div class='uds-bb-slide'>";
			if(empty($this->link)) {
				$this->link = '#';
			}
			$out .= "<a href='{$this->link}' class='uds-bb-link'>";
			$out .= "<img src='{$this->image}' alt='' class='uds-bb-bg-image' />";
			$out .= "</a>";
			$out .= "<span style='display:none' class='uds-delay'>{$this->delay}</span>";
			$out .= "<span style='display:none' class='uds-transition'>$transition</span>";
			$out .= "<span style='display:none' class='uds-direction'>{$direction}</span>";
			$out .= do_shortcode($this->text);
		$out .= "</div>\n";
		return $out;
	}
}

?>