<?php

class uBillboard {
	private $name;
	private $width;
	private $height;
	private $squareSize;
	private $columnWidth;
	private $showPaginator;
	private $showControls;
	private $showPause;
	private $autoplay;
	private $randomize;
	private $useTimthumb;
	private $timthumbZoom;
	private $timthumbQuality;
	
	private $slides;
	
	public static function upgradeFromV2($billboards)
	{
	
	}
	
	public function __construct($options = false)
	{
		$this->name = 'billboard';
		$this->width = 940;
		$this->height = 380;
		$this->squareSize = 100;
		$this->columnWidth = 50;
		$this->showPaginator = true;
		$this->showControls = true;
		$this->showPause = true;
		$this->autoplay = true;
		$this->randomize = false;
		$this->useTimthumb = false;
		$this->timthumbZoom = false;
		$this->timthumbQuality = 70;
		$this->slides = array();
		
		if($options !== false) {
			$this->setOptions($options);
		}
	}
	
	public function __destruct()
	{
		
	}
	
	public function setOptions($options)
	{
		extract($options, EXTR_SKIP);
		
		if(isset($name) && !empty($name)) {
			$this->name = sanitize_title_with_dashes($name);
		}
		
		if(isset($width)) {
			$this->width = (int)$width;
		}
		
		if(isset($height)) {
			$this->height = (int)$height;
		}
		
		if(isset($squareSize)) {
			$this->squareSize = (int)$squareSize;
		}
		
		if(isset($columnWidth)) {
			$this->columnWidth = (int)$columnWidth;
		}
		
		if(isset($showPaginator)) {
			$this->showPaginator = (bool)$showPaginator;
		}
		
		if(isset($showControls)) {
			$this->showControls = (bool)$showControls;
		}
		
		if(isset($showPause)) {
			$this->showPause = (bool)$showPause;
		}
		
		if(isset($autoplay)) {
			$this->autoplay = (bool)$autoplay;
		}
		
		if(isset($randomize)) {
			$this->randomize = (bool)$randomize;
		}
		
		if(isset($useTimthumb)) {
			$this->useTimthumb = (bool)$useTimthumb;
		}
		
		if(isset($timthumbZoom)) {
			$this->timthumbZoom = (bool)$timthumbZoom;
		}
		
		if(isset($timthumbQuality)) {
			$this->timthumbQuality = (int)$timthumbQuality;
		}
		
		if(isset($slides) && is_array($slides)) {
			$this->slides = $slides;
		}
	}
	
	public function __get($key)
	{
		return $this->{$key};
	}
}

?>