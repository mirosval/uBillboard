<?php

// define data structure for billboard
$uds_billboard_attributes = array(
	'image'=> array(
		'type' => 'image',
		'label' => 'Image',
		'default' => ''
	),
	'resize' => array(
		'type' => 'checkbox',
		'label' => 'Apply Automatic resizing',
		'default' => ''
	),
	'background' => array(
		'type' => 'text',
		'label' => 'Background Color',
		'default' => ''
	),
	'link' => array(
		'type' => 'text',
		'label' => 'Link URL',
		'default' => ''
	),
	'delay' => array(
		'type' => 'select',
		'label' => 'Delay',
		'options' => array(
			'1000' => '1s',
			'2000' => '2s',
			'3000' => '3s',
			'4000' => '4s',
			'5000' => '5s',
			'10000' => '10s',
		),
		'default' => '5000'
	),
	'transition' => array(
		'type' => 'select',
		'label' => 'Transition',
		'options' => array(
			'random' => 'Random',
			'fade' => 'Fade',
			'fadeSquaresRandom' => 'Fade Random Squares',
			'fadeSquaresRows' => 'Fade Squares by Rows',
			'fadeSquaresCols' => 'Fade Squares by Columns',
			'fadeSquaresSpiralIn' => 'Fade Squares Spiral In',
			'fadeSquaresSpiralOut' => 'Fade Squares Spiral Out',
			'slide' => 'Slide',
			'scale' => 'Scale',
			//////////////////////////////////////////////////
			'slideLeft' => 'Slide from Left',
			'slideTop' => 'Slide from Top',
			'slideRight' => 'Slide from Right',
			'slideBottom' => 'Slide from Bottom',
			'scaleTop' => 'Scale from Top',
			'scaleCenter' => 'Scale from Center',
			'scaleBottom' => 'Scale from Bottom',
			'scaleRight' => 'Scale from Right',
			'scaleLeft' => 'Scale from Left',
			'squaresRandom' => 'Squares Random',
			'squaresRows' => 'Squares by Rows',
			'squaresCols' => 'Squares by Columns',
			'squaresMoveIn' => 'Squares Fly in',
			'squaresMoveOut' => 'Squares Fly out',
			'columnsRandom' => 'Columns Random',
			'columnWave' => 'Column Wave',
			'curtainRight' => 'Curtain Right',
			'curtainLeft' => 'Curtain Left',
			'curtainRotateRight' => 'Curtain Rotate Right',
			'curtainRotateLeft' => 'Curtain Rotate Left',
			'interweaveLeft' => 'Interweave Left',
			'interweaveRight' => 'Interweave Right'
		),
		'default' => 'fade'
	),
	'direction' => array(
		'type' => 'select',
		'label' => 'Transition Direction',
		'options' => array(
			'' => '--',
			'random' => 'Random',
			'left' => 'From Left',
			'right' => 'From Right',
			'top' => 'From Top',
			'bottom' => 'From Bottom'
		),
		'default' => ''
	),
	'text' => array(
		'type' => 'textarea',
		'label' => 'Text'
	)
);

class uBillboardSlide {
	private $slider;
	
	public static function getSlides($options, $slider)
	{
		global $uds_billboard_attributes;
		
		$slides = array();

		$n = 0;
		while(!empty($options) && $n < 100) {
			$attributes = array();
			foreach($uds_billboard_attributes as $key => $option) {
				if(!isset($options[$key][$n]) && $option['type'] != 'checkbox') {
					break;
				}
				
				if($option['type'] == 'checkbox') {
					$attributes[$key] = '';
				} else {
					$attributes[$key] = $options[$key][$n];
				}
				
				unset($options[$key][$n]);
				
				if(empty($options[$key])) {
					unset($options[$key]);
				}
			}

			if(!empty($attributes)) {
				$slide = new uBillboardSlide($attributes, $slider);
				if($slide !== null) $slides[] = $slide;
			}

			$n++;
		}
		
		return $slides;
	}
	
	public function __construct($options = false, $slider)
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
			if(isset($options[$key])) {
				$this->{$key} = $options[$key];
			} else {
				$this->{$key} = '';	
			}
		}
		
		$this->setSlider($slider);
	}
	
	public function __wakeup()
	{
		global $uds_billboard_attributes;
		
		foreach($uds_billboard_attributes as $key => $option) {
			if(!isset($this->{$key})) {
				$this->{$key} = $option['default'];
			}
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

		static $id = 0;
		?>
		<div class="image-wrapper"></div>
		<div class="uds-slide-tabs">	
			<ul>
				<li><a href="#uds-slide-tab-background-<?php echo $id ?>">Background</a></li>
				<li><a href="#uds-slide-tab-content-<?php echo $id ?>">Content</a></li>
				<li><a href="#uds-slide-tab-link-<?php echo $id ?>">Link</a></li>
				<li><a href="#uds-slide-tab-transition-<?php echo $id ?>">Transition</a></li>
			</ul>
			<div id="uds-slide-tab-background-<?php echo $id ?>">
				<?php $this->renderAdminField('image') ?>
				<?php $this->renderAdminField('resize') ?>
				<?php $this->renderAdminField('background') ?>
			</div>
			<div id="uds-slide-tab-content-<?php echo $id ?>">
				<?php $this->renderAdminField('text') ?>
			</div>
			<div id="uds-slide-tab-link-<?php echo $id ?>">
				<?php $this->renderAdminField('link') ?>
			</div>
			<div id="uds-slide-tab-transition-<?php echo $id ?>">
				<?php $this->renderAdminField('delay') ?>
				<?php $this->renderAdminField('transition') ?>
				<?php $this->renderAdminField('direction') ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php
		
		$id++;
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
		
		// Image
		if($this->resize == 'on') {
			$timthumb = UDS_BILLBOARD_URL . 'lib/timthumb.php?';
		
			$width = $this->slider->width;
			$height = $this->slider->height;
		
			$image = $timthumb . 'src=' . str_replace(WP_CONTENT_URL . '/', '', $this->image) . '&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1';
		} else {
			$image = $this->image;
		}
		
		$out = "<div class='uds-bb-slide'>";
			if(empty($this->link)) {
				$this->link = '#';
			}
			$out .= "<a href='{$this->link}' class='uds-bb-link'>";
			$out .= "<img src='$image' alt='' class='uds-bb-bg-image' />";
			$out .= "</a>";
			$out .= "<span style='display:none' class='uds-delay'>{$this->delay}</span>";
			$out .= "<span style='display:none' class='uds-transition'>$transition</span>";
			$out .= "<span style='display:none' class='uds-direction'>{$direction}</span>";
			$out .= do_shortcode($this->text);
		$out .= "</div>\n";
		return $out;
	}
	
	public function renderThumb()
	{
		$timthumb = UDS_BILLBOARD_URL . 'lib/timthumb.php?';
		
		$width = $this->slider->thumbnailsWidth;
		$height = $this->slider->thumbnailsHeight;
		
		$image = $timthumb . 'src=' . str_replace(WP_CONTENT_URL . '/', '', $this->image) . '&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1';
		
		return "<div class='uds-bb-thumb'><img src='$image' alt='' width='$width' height='$height' /></div>";
	}
	
	public function setSlider($slider)
	{
		if(is_a($slider, 'uBillboard')) {
			$this->slider = $slider;
		}
	}
	
	private function renderAdminField($attrib)
	{
		global $uds_billboard_attributes;
	
		$attrib_full = $uds_billboard_attributes[$attrib];
		switch($attrib_full['type']){
			case 'input':
			case 'text':
				$this->renderAdminText($attrib);
				break;
			case 'checkbox':
				$this->renderAdminCheckbox($attrib);
				break;
			case 'textarea':
				$this->renderAdminTextarea($attrib);
				break;
			case 'select':
				$this->renderAdminSelect($attrib);
				break;
			case 'image':
				$this->renderAdminImage($attrib);
				break;
			default:
		}
	}
	
	private function renderAdminText($attrib)
	{
		global $uds_billboard_attributes;
		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<input type="text" name="uds_billboard['. $attrib .'][]" value="' . htmlspecialchars(stripslashes($this->{$attrib})) . '" id="billboard-'. $attrib .'" class="billboard-'. $attrib .'" />';
		echo '</div>';
	}
	
	private function renderAdminCheckbox($attrib)
	{
		global $uds_billboard_attributes;
		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<input type="checkbox" name="uds_billboard['. $attrib .'][]" '.checked($this->{$attrib}, 'on', false) . '" id="billboard-'. $attrib .'" class="billboard-'. $attrib .' checkbox" />';
		echo '</div>';
	}
	
	private function renderAdminTextarea($attrib)
	{
		global $uds_billboard_attributes;
		
		static $id = 0;
		
		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<textarea name="uds_billboard['. $attrib .'][]" class="billboard-'. $attrib .'" id="uds-text-'.$id.'">'. htmlspecialchars(stripslashes($this->{$attrib})) .'</textarea>';
		echo "<script language='javascript' type='text/javascript'>\n";
		echo "	tinyMCE.init({\n";
		echo "		theme : 'advanced',\n";
		echo "		mode: 'exact',\n";
		echo "		elements : 'uds-text-$id',\n";
		echo "		theme_advanced_toolbar_location : 'top',\n";
		echo "		theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,'\n";
		echo "		+ 'justifyleft,justifycenter,justifyright,justifyfull,formatselect,'\n";
		echo "		+ 'bullist,numlist',\n";
		echo "		theme_advanced_buttons2 : 'link,unlink,image,separator,'\n";
		echo "		+'undo,redo,cleanup,code,separator,sub,sup,charmap,outdent,indent',\n";
		echo "		theme_advanced_buttons3 : '',\n";
		echo "		theme_advanced_resizing : true, \n";
		echo "		theme_advanced_statusbar_location : 'bottom', \n";
		echo "		width : '100%' \n";
		echo "	});\n";
		echo "</script>\n";
		echo '</div>';
		
		$id++;
	}
	
	private function renderAdminSelect($attrib)
	{
		global $uds_billboard_attributes;
		$attrib_full = $uds_billboard_attributes[$attrib];
		
		if($attrib_full['type'] != 'select') return;
	
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<select name="uds_billboard['. $attrib .'][]" class="billboard-'. $attrib .'">';
		echo '<option disabled="disabled">'. $attrib_full['label'] .'</option>';
		if(is_array($attrib_full['options'])){
			foreach($attrib_full['options'] as $key => $option){
				$selected = '';
				if($this->{$attrib} == $key){
					$selected = 'selected="selected"';
				}
				echo '<option value="'. $key .'" '. $selected .'>'. $option .'</option>';
			}
		}
		echo '</select>';
		echo '</div>';
	}
	
	private function renderAdminImage($attrib)
	{
		static $unique_id = 0;
		
		echo '<div class="'. $attrib .'-url-wrapper">';
		echo '	<label for="billboard-'. $attrib .'-'. $unique_id .'-hidden">Image URL</label>';
		echo '	<input type="text" name="uds_billboard['. $attrib .'][]" value="'. $this->{$attrib} .'" id="billboard-'. $attrib .'-'. $unique_id .'-hidden" />';
		echo '	<a class="thickbox" title="Add an Image" href="media-upload.php?type=image&TB_iframe=true&width=640&height=345">';
		echo '		<img alt="Add an Image" src="'. admin_url() . 'images/media-button-image.gif" id="billboard-'. $attrib .'-'. $unique_id .'" class="billboard-'. $attrib .'" />';
		echo '	</a>';
		echo '</div>';
		
		$unique_id++;
	}
	
}

?>