<?php

// define general options for billboard
$uds_billboard_general_options = array(
	'name' => array(
		'type' => 'text',
		'label' => 'Billboard Name',
		'unit' => '',
		'tooltip' => 'Enter a name for this Billboard. You will use this name to create the Billboard on your site.',
		'default' => 'billboard'
	),
	'width' => array(
		'type' => 'text',
		'label' => 'Width',
		'unit' => 'px',
		'tooltip' => 'Billboard Width in pixels',
		'default' => 940
	),
	'height' => array(
		'type' => 'text',
		'label' => 'Height',
		'unit' => 'px',
		'tooltip' => 'Billboard Height in pixels',
		'default' => 380
	),
	'autoplay' => array(
		'type' => 'checkbox',
		'label' => 'Autoplay',
		'unit' => '',
		'tooltip' => 'Automatically start playing slides, makes sense to turn this off only if Show Controls is enabled.',
		'default' => 'on'
	),
	'randomize' => array(
		'type' => 'checkbox',
		'label' => 'Shuffle Slides',
		'unit' => '',
		'tooltip' => 'Shuffle slides each time the slider is loaded',
		'default' => ''
	),
	'square-size' => array(
		'type' => 'text',
		'label' => 'Square Size',
		'unit' => 'pixels',
		'tooltip' => 'Square dimension, applies only to transitions based on squares',
		'default' => 100
	),
	'style' => array(
		'type' => 'select',
		'label' => 'Style',
		'unit' => '',
		'tooltip' => '',
		'options' => array(
			'dark' => 'Dark',
			'bright' => 'Bright'
		),
		'default' => ''
	),
	'show-timer' => array(
		'type' => 'checkbox',
		'label' => 'Show Timer',
		'unit' => '',
		'tooltip' => 'Shows countdown until the next slide transition when playing',
		'default' => 'on'
	),
	'controls-skin' => array(
		'type' => 'select',
		'label' => 'Skin',
		'unit' => '',
		'tooltip' => 'How the controls should look',
		'options' => array(
			'mini' => 'Minimal Style Controls',
			'oldskool' => 'Old School uBillboard',
			'oldskool-bright' => 'Bright Old School uBillboard',
			'utube' => 'uTube'
		),
		'default' => 'oldskool'
	),
	'show-controls' => array(
		'type' => 'select',
		'label' => 'Show Controls',
		'unit' => '',
		'tooltip' => 'Controls enable you to switch between slides',
		'options' => array(
			'no' => 'Don\'t show controls',
			'hover' => 'Show on Mouse Hover',
			'yes' => 'Show at all times'
		),
		'default' => 'yes'
	),
	'controls-position' => array(
		'type' => 'select',
		'label' => 'Position',
		'unit' => '',
		'tooltip' => '',
		'options' => array(
			'inside' => 'Inside',
			'outside' => 'Outside',
			'below' => 'Below'
		),
		'default' => 'inside'
	),
	'show-pause' => array(
		'type' => 'select',
		'label' => 'Show Play/Pause',
		'unit' => '',
		'tooltip' => 'Display show pause button',
		'options' => array(
			'no' => 'Don\'t show Play/Pause',
			'hover' => 'Show on Mouse Hover',
			'yes' => 'Show at all times'
		),
		'default' => 'yes'
	),
	'show-paginator' => array(
		'type' => 'select',
		'label' => 'Show Paginator',
		'unit' => '',
		'tooltip' => 'Display pagination control',
		'options' => array(
			'no' => 'Don\'t show Paginator',
			'hover' => 'Show on Mouse Hover',
			'yes' => 'Show at all times'
		),
		'default' => 'yes'
	),
	'paginator-position' => array(
		'type' => 'select',
		'label' => 'Position',
		'unit' => '',
		'tooltip' => '',
		'options' => array(
			'inside' => 'Inside',
			'outside' => 'Outside'
		),
		'default' => 'inside'
	),
	'show-thumbnails' => array(
		'type' => 'select',
		'label' => 'Show Thumbnails',
		'unit' => '',
		'tooltip' => 'Small preview images for all slides',
		'options' => array(
			'no' => 'Don\'t show Thumbnails',
			'hover' => 'Show on Mouse Hover',
			'yes' => 'Show at all times'
		),
		'default' => 'yes'
	),
	'thumbnails-position' => array(
		'type' => 'select',
		'label' => 'Thumbnail Position',
		'unit' => '',
		'tooltip' => 'Where do you want thumbs to show',
		'options' => array(
			'top' => 'Top',
			'bottom' => 'Bottom',
			'right' => 'Right',
			'left' => 'Left'
		),
		'default' => 'bottom'
	),
	'thumbnails-inside' => array(
		'type' => 'checkbox',
		'label' => 'Inside',
		'unit' => '',
		'tooltip' => 'Where do you want thumbs to show',
		'default' => ''
	),
	'thumbnails-width' => array(
		'type' => 'text',
		'label' => 'Thumbnail Width',
		'unit' => 'px',
		'tooltip' => 'Width of the thumbnail images',
		'default' => '80'
	),
	'thumbnails-height' => array(
		'type' => 'text',
		'label' => 'Thumbnail Height',
		'unit' => 'px',
		'tooltip' => 'Height of the thumbnail images',
		'default' => '60'
	)
);


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
	
	public function importFromXML($billboard)
	{
		global $uds_billboard_general_options;
		
		foreach($billboard->properties[0] as $property){
			foreach($uds_billboard_general_options as $key => $option) {
				if($property->key == $key) {
					$camelized = $this->camelize($key);
					$this->{$camelized} = (string)$property->value;
				}
			}
		}
		
		foreach($billboard->slides->slide as $slide) {
			$s = new uBillboardSlide(false, $this);
			$s->importFromXML($slide);
			$this->slides[] = $s;
		}
	}
	
	public function setUniqueName()
	{
		$billboards = array_keys(maybe_unserialize(get_option(UDS_BILLBOARD_OPTION)));
	
		$guess = $root = 'billboard';
	
		$i = 1;
		while(in_array($guess, $billboards)) {
			$guess = $root . '-' . $i;
			$i++;
		}
		
		$this->name = $guess;
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
	
	public function export()
	{
		global $uds_billboard_general_options;
		
		$out = '  <udsBillboard>' . "\n";
		
		$out .= '     <properties>'. "\n";
		
		foreach($uds_billboard_general_options as $key => $option) {
			$camelKey = $this->camelize($key);
			$out .= '     <property>' . "\n";
			$out .= '      <key>' . $key . '</key>' . "\n";
			$out .= '      <value>' . $this->{$key} . '</value>' . "\n";
			$out .= '     </property>' . "\n";
		}
		
		$out .= '   </properties>'. "\n";
		
		$out .= '   <slides>' . "\n";
		
		foreach($this->slides as $slide) {
			$out .= $slide->export();
		}
		
		$out .= '   </slides>' . "\n";
		
		$out .= '  </udsBillboard>' . "\n";
		
		return $out;
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
				break;
			case 'oldskool-bright':
				$out .= $this->paginatorOldskoolBright();
				break;
			case 'utube':
				$out .= $this->paginatoruTube();
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
		$showTimer = $this->showTimer === 'on' ? 'true' : 'false';
		
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
				showThumbnails: $showThumbnails,
				showTimer: $showTimer,
			});
		";
		
		return $scripts;
	}
	
	public function renderAdminOption($option)
	{
		global $uds_billboard_general_options;
		$field = $uds_billboard_general_options[$option];
		$camelized = $this->camelize($option);
		$value = $this->{$camelized};
		
		switch($uds_billboard_general_options[$option]['type']) {
			case 'text':
				$this->renderAdminOptionText($option, $field, $value);
				break;
			case 'checkbox':
				$this->renderAdminOptionCheckbox($option, $field, $value);
				break;
			case 'select':
				$this->renderAdminOptionSelect($option, $field, $value);
				break;
		}
	}
	
	// Functions to render single fields in general options for each billboard
	public function renderAdminOptionText($option, $field, $value)
	{
		?>
		<div class="uds-billboard-<?php echo $option ?> option-container">
			<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
			<input type="text" id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" value="<?php echo empty($value) ? $field['default'] : $value ?>" class="text" />
			<span class="unit"><?php echo $field['unit'] ?></span>
			<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
			<div class="clear"></div>
		</div>
		<?php
	}
	
	public function renderAdminOptionCheckbox($option, $field, $value)
	{
		$checked = ( $value === null ? $field['default'] : $value ) == 'on' ? 'checked="checked"' : '';
		?>
		<div class="uds-billboard-<?php echo $option ?> option-container">
			<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
			<input type="checkbox" id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" <?php echo $checked ?> class="checkbox" />
			<span class="unit"><?php echo $field['unit'] ?></span>
			<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
			<div class="clear"></div>
		</div>
		<?php
	}
	
	function renderAdminOptionSelect($option, $field, $value)
	{
		$checked = ( $value === null ? $field['default'] : $value ) == 'on' ? 'checked="checked"' : '';
		?>
		<div class="uds-billboard-<?php echo $option ?> option-container select">
			<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
			<select id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" class="select">
				<option value="" disabled="disabled"><?php echo $field['label'] ?></option>
				<?php foreach($field['options'] as $key => $label): ?>
					<option value="<?php echo $key ?>" <?php echo $key == $value ? 'selected="selected"' : '' ?>><?php echo $label ?></option>
				<?php endforeach; ?>
			</select>
			<span class="unit"><?php echo $field['unit'] ?></span>
			<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
			<div class="clear"></div>
		</div>
		<?php
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
	
	private function paginatorOldskoolBright()
	{
		$out = '';
		$out .= "<div class='uds-bb-paginator oldskool-bright'>";
			$out .= "<div class='uds-bb-button uds-bb-playpause uds-center'><span>Play</span></div>";
			$out .= "<div class='uds-bb-button uds-bb-prev uds-center-vertical'><span>Prev</span></div>";
			$out .= "<div class='uds-bb-button uds-bb-next uds-center-vertical'><span>Next</span></div>";
			$out .= "<div class='uds-bb-position-indicator-bullets'></div>";
		$out .= "</div>";
		return $out;
	}
	
	private function paginatoruTube()
	{
		$out = '';
		$out .= "<div class='uds-bb-paginator uTube'>";
			$out .= "<div class='uds-bb-button uds-bb-prev'><span>Prev</span></div>";
			$out .= "<div class='uds-bb-button uds-bb-playpause uds-left'><span>Play</span></div>";
			$out .= "<div class='uds-bb-button uds-bb-next'><span>Next</span></div>";
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