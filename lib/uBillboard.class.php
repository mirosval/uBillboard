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
		
		$this->slides = array();
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
		
		foreach($this->slides as $slide) {
			$slide->setSlider($this);
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
		
		$this->slides = uBillboardSlide::getSlides($options, $this);
		
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
		$this->slides[] = new uBillboardSlide(false, $this);
	}
	
	public function render($id = 0)
	{
		global $uds_bb_params; // parameters of the currently rendered billboard
		
		// store width and height into the params
		$uds_bb_params = array(
			'width' => $this->width,
			'height' => $this->height
		);
		
		$out = "<div class='uds-bb uds-{$this->style}' id='uds-bb-$id'>";
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
		
		switch($this->controlsSkin){
			case 'mini':
				$out .= $this->paginatorMini();
				break;
			case 'oldskool':
				$out .= $this->paginatorOldskool();
		}

		if($this->showThumbnails !== 'no') {
			$out .= $this->thumbnails();
		}
			
		$out .= "</div>";
		$out .= "</div>";
		
		return $out;
	}
	
	public function renderJS($id = 0)
	{
		$autoplay = $this->autoplay === 'on' ? 'true' : 'false';
		
		$showControls = 'false';
		if($this->showControls == 'hover') 	$showControls = "'hover'";
		if($this->showControls == 'yes') 	$showControls = 'true';

		$showPause = 'false';
		if($this->showPause == 'hover') $showPause = "'hover'";
		if($this->showPause == 'yes') 	$showPause = 'true';

		$showPaginator = 'false';
		if($this->showPaginator == 'hover') $showPaginator = "'hover'";
		if($this->showPaginator == 'yes') 	$showPaginator = 'true';
		
		$showThumbnails = 'false';
		if($this->showThumbnails == 'hover')	$showThumbnails = "'hover'";
		if($this->showThumbnails == 'yes')		$showThumbnails = 'true';

		$scripts = "
			$('#uds-bb-$id').uBillboard({
				width: '{$this->width}px',
				height: '{$this->height}px',
				squareSize: '{$this->squareSize}px',
				autoplay: $autoplay,
				showControls: $showControls,
				showPause: $showPause,
				showPaginator: $showPaginator,
				showThumbnails: $showThumbnails
			});
		";
		
		return $scripts;
	}
	
	private function paginatorMini()
	{
		$out = '';
		$out .= "<div class='uds-bb-paginator mini'>";
			$out .= "<div class='uds-bb-button uds-bb-playpause'>Play</div>";
			$out .= "<div class='uds-bb-button uds-bb-prev'>Prev</div>";
			$out .= "<div class='uds-bb-button uds-bb-next'>Next</div>";
			$out .= "<div class='uds-bb-position-indicator'></div>";
		$out .= "</div>";
		return $out;
	}
	
	private function paginatorOldskool()
	{
		$out = '';
		$out .= "<div class='uds-bb-paginator oldskool'>";
			$out .= "<div class='uds-bb-button uds-bb-playpause uds-center'><span>Play</span></div>";
			$out .= "<div class='uds-bb-button uds-bb-prev uds-center-vertical'><span>Prev</span></div>";
			$out .= "<div class='uds-bb-button uds-bb-next uds-center-vertical'><span>Next</span></div>";
			$out .= "<div class='uds-bb-position-indicator-bullets'></div>";
		$out .= "</div>";
		return $out;
	}
	
	private function thumbnails()
	{
		$position = $this->thumbnailsPosition;
		$inside = $this->thumbnailsInside === 'on' ? 'inside' : '';
		
		$out = '';
		$out .= "<div class='uds-bb-thumbnails $position $inside'>";
		$out .= "<div class='uds-bb-thumbnail-container'>";
		foreach($this->slides as $slide) {
			$out .= $slide->renderThumb();
		}
		$out .= "</div>";
		$out .= "</div>";
		return $out;
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