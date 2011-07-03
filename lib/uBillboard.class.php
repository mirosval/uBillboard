<?php

class uBillboard {
	private $slides;
	
	public static function upgradeFromV2($billboards)
	{
		
	}
	
	public function __construct($options = false)
	{
		global $uds_billboard_general_options;

		foreach($uds_billboard_general_options as $key => $option) {
			$camelized = $this->camelize($key);
			if(isset($options[$key])) {
				$this->{$camelized} = $this->sanitizeOption($key, $options[$key]);
				unset($options[$key]);
			} else {
				$this->{$camelized} = $option['default'];
			}
		}
		
		$this->slides = new uBillboardSlide();
	}
	
	public function __destruct()
	{
		
	}
	
	public function __get($key)
	{
		$key = $this->camelize($key);
		return $this->{$key};
	}
	
	public function __wakeup()
	{
		global $uds_billboard_general_options;
		
		foreach($uds_billboard_general_options as $key => $option) {
			$camelized = $this->camelize($key);
			if(!isset($this->{$camelized})) {
				$this->{$camelized} = $option['default'];
			}
		}
	}
	
	public function update($options)
	{
		global $uds_billboard_general_options;

		foreach($uds_billboard_general_options as $key => $option) {
			$camelized = $this->camelize($key);
			if(isset($options[$key])) {
				$this->{$camelized} = $this->sanitizeOption($key, $options[$key]);
				unset($options[$key]);
			} else {
				$this->{$camelized} = '';
			}
		}
		
		$this->slides = uBillboardSlide::getSlides($options);
		
		if(!empty($this->slides)) {
			foreach($this->slides as $key => $slide) {
				if(!$slide->isValid()) {
					unset($this->slides[$key]);
				}
			}
		}
	}
	
	public function isValid()
	{
		return !empty($this->slides);
	}
	
	public function addEmptySlide()
	{
		$this->slides[] = new uBillboardSlide();
	}
	
	public function render($id = 0)
	{		
		$out = "<div class='uds-bb' id='uds-bb-$id'>";
		$out .= "<div class='uds-bb-slides'>";
		
		$slides = $this->slides;
		
		if($this->randomize === "on") {
			shuffle($slides);
		}
		
		foreach($slides as $slide) {
			$out .= $slide->render();
		}
		
		$out .= "</div>";
		$out .= "<div class='uds-bb-controls'>";
			
		$out .= "</div>";
		$out .= "</div>";
		
		return $out;
	}
	
	public function renderJS($id = 0)
	{
		$autoplay = $this->autoplay === 'on' ? 'true' : 'false';
		
		$scripts = "
			$('#uds-bb-$id').uBillboard({
				width: '{$this->width}px',
				height: '{$this->height}px',
				squareSize: '{$this->squareSize}px',
				autoplay: $autoplay
			});
		";
		
		return $scripts;
	}
	
	private function sanitizeOption($key, $option)
	{
		global $uds_billboard_general_options;
		
		if(!array_key_exists($key, $uds_billboard_general_options)){
			return NULL;
		}
		
		if(in_array($key, array_keys($uds_billboard_general_options))) {
			if($uds_billboard_general_options[$key]['type'] == 'select') {
				if(in_array($option, array_keys($uds_billboard_general_options[$key]['options']))) {
					return $option;
				} else {
					return $uds_billboard_general_options[$key]['default'];
				}
			}
			
			return $option;
		}
		
		return $uds_billboard_general_options[$key]['default'];
	}
	
	private function camelize($string) 
	{
		$string = str_replace(array('-', '_'), ' ', $string); 
		$string = ucwords($string); 
		$string = str_replace(' ', '', $string);  
		
		$string = lcfirst($string);
		
		return $string;
	}
}

?>