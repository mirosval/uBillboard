<?php

// define data structure for billboard
$uds_billboard_attributes = array(
	'image'=> array(
		'type' => 'image',
		'label' => __('Image', uds_billboard_textdomain),
		'default' => ''
	),
	'resize' => array(
		'type' => 'checkbox',
		'label' => __('Apply Automatic resizing', uds_billboard_textdomain),
		'default' => ''
	),
	'background' => array(
		'type' => 'color',
		'label' => __('Background Color', uds_billboard_textdomain),
		'default' => 'ffffff'
	),
	'background-transparent' => array(
		'type' => 'checkbox',
		'label' => __('Transparent Background', uds_billboard_textdomain),
		'default' => 'on'
	),
	'link' => array(
		'type' => 'text',
		'label' => __('Link URL', uds_billboard_textdomain),
		'default' => ''
	),
	'delay' => array(
		'type' => 'select',
		'label' => __('Delay', uds_billboard_textdomain),
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
		'label' => __('Transition', uds_billboard_textdomain),
		'options' => array(
			'random' 	=> __('Random', uds_billboard_textdomain),
			'fade' 		=> __('Fade', uds_billboard_textdomain),
			'slide' 	=> __('Slide', uds_billboard_textdomain),
			'scale' 	=> __('Scale', uds_billboard_textdomain)
		),
		'default' => 'fade'
	),
	'direction' => array(
		'type' => 'select',
		'label' => __('Transition Direction', uds_billboard_textdomain),
		'options' => array(
			'none'			=> '--',
			'random' 		=> __('Random Direction', uds_billboard_textdomain),
			'center'		=> __('From Center', uds_billboard_textdomain),
			'left' 			=> __('From Left', uds_billboard_textdomain),
			'right' 		=> __('From Right', uds_billboard_textdomain),
			'top' 			=> __('From Top', uds_billboard_textdomain),
			'bottom' 		=> __('From Bottom', uds_billboard_textdomain),
			'randomSquares' => __('Random Squares', uds_billboard_textdomain),
			'spiralIn' 		=> __('Spiral in', uds_billboard_textdomain),
			'spiralOut'		=> __('Spiral Out', uds_billboard_textdomain),
			//'zigzag'		=> __('Zig-zag', uds_billboard_textdomain)
		),
		'default' => 'none'
	),
	'content' => array(
		'type' => 'select',
		'label' => __('Content Type', uds_billboard_textdomain),
		'options' => array(
			'none'		=> __('No Content', uds_billboard_textdomain),
			'editor' 	=> __('Content Editor', uds_billboard_textdomain),
			'embed' 	=> __('Embedded Content', uds_billboard_textdomain),
			'dynamic'	=> __('Blog-based Dynamic Content', uds_billboard_textdomain)
		),
		'default' => 'none'
	),
	'text' => array(
		'type' => 'textarea',
		'label' => __('Text', uds_billboard_textdomain)
	),
	'text-evaluation' => array(
		'type' => 'select',
		'label' => __('Text Evaluation', uds_billboard_textdomain),
		'options' => array(
			'textile'	=> __('Textile', uds_billboard_textdomain),
			'html' 		=> __('HTML', uds_billboard_textdomain)
		),
		'default' => 'textile'
	),
	'embed-url' => array(
		'type' => 'text',
		'label' => __('URL of the Embedded Content', uds_billboard_textdomain)
	),
	'dynamic-offset' => array(
		'type' => 'select',
		'label' => __('Blog post offset', uds_billboard_textdomain),
		'options' => array(
			'0' => '0',
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10'
		),
		'default' => '0'
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
	
	public function importFromXML($slide)
	{
		global $uds_billboard_attributes;
		
		foreach($uds_billboard_attributes as $key => $option) {
			foreach($slide->property as $property) {
				if($property->key == $key) {
					$camelized = $this->camelize($key);
					$this->{$key} = (string)$property->value;
				}
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
	
	public function export()
	{
		global $uds_billboard_attributes;
		
		$out = '    <slide>' . "\n";
		
		foreach($uds_billboard_attributes as $key => $option) {
			$camelKey = $this->camelize($key);
			$out .= '     <property>' . "\n";
			$out .= '      <key>' . $key . '</key>' . "\n";
			$out .= '      <value><![CDATA[' . $this->{$key} . ']]></value>' . "\n";
			$out .= '     </property>' . "\n";
		}
		
		$out .= '    </slide>' . "\n";
		
		return $out;
	}
	
	public function isValid()
	{
		$text = isset($this->text) ? strip_tags($this->text) : '';
		return !empty($this->image) || !empty($text);
	}
	
	public function renderAdmin()
	{
		global $uds_billboard_attributes;

		static $id = 0;
		?>
		<div class="image-wrapper"></div>
		<div class="uds-slide-tabs">	
			<ul>
				<li><a href="#uds-slide-tab-background-<?php echo $id ?>"><?php _e('Background' , uds_billboard_textdomain) ?></a></li>
				<li><a href="#uds-slide-tab-content-<?php echo $id ?>"><?php _e('Content', uds_billboard_textdomain) ?></a></li>
				<li><a href="#uds-slide-tab-link-<?php echo $id ?>"><?php _e('Link', uds_billboard_textdomain) ?></a></li>
				<li><a href="#uds-slide-tab-transition-<?php echo $id ?>"><?php _e('Transition', uds_billboard_textdomain) ?></a></li>
			</ul>
			<div id="uds-slide-tab-background-<?php echo $id ?>" class="uds-slide-tab-background">
				<?php $this->renderAdminField('image') ?>
				<?php $this->renderAdminField('resize') ?>
				<?php $this->renderAdminField('background') ?>
				<?php $this->renderAdminField('background-transparent') ?>
			</div>
			<div id="uds-slide-tab-content-<?php echo $id ?>" class="uds-slide-tab-content">
				<?php $this->renderAdminField('content') ?>
				<?php $this->renderAdminField('text') ?>
				<?php $this->renderAdminField('text-evaluation') ?>
				<?php $this->renderAdminField('embed-url') ?>
				<?php $this->renderAdminField('dynamic-offset') ?>
			</div>
			<div id="uds-slide-tab-link-<?php echo $id ?>" class="uds-slide-tab-link">
				<?php $this->renderAdminField('link') ?>
			</div>
			<div id="uds-slide-tab-transition-<?php echo $id ?>" class="uds-slide-tab-transition">
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
		global $uds_billboard_attributes, $uds_billboard_text_evaluation;

		// Transition, make nil when embedded content exists
		$transition = $this->transition;
		if($this->content == 'embed') {
			$transition = 'none';
		}
		
		if($transition === 'random') {
			$transitions = array_keys($uds_billboard_attributes['transition']['options']);
			$transitions = array_diff($transitions, array('random'));
			$transition = $transitions[array_rand($transitions)];
		}
		
		// Direction
		$direction = $this->direction;
		if($direction === 'random') {
			$directions = array_keys($uds_billboard_attributes['direction']['options']);
			$directions = array_diff($directions, array('random'));
			$direction = $directions[array_rand($directions)];
		}
		
		// Background
		$background = '#' . $this->background;
		if($this->{'background-transparent'} == 'on') {
			$background = 'transparent';
		}
		
		
		// Thumb
		$this->thumb = $this->image;
		
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
			
			$text = '';
			switch($this->content){
				case 'editor':
					$uds_billboard_text_evaluation = $this->{'text-evaluation'};
					
					$text = do_shortcode(stripslashes($this->text));
					break;
				case 'embed':
					$url = $this->{'embed-url'};
					if(empty($url)) {
						$text = __('URL Must not be empty', uds_billboard_textdomain);
						break;
					}
	
					$width = (int)$this->slider->width;
					$height = (int)$this->slider->height;
					
					$url = 'http://api.embed.ly/1/oembed?url='.urlencode($url)."&maxwidth=$width&maxheight=$height&format=json";
				
					$response = @file_get_contents($url);
					
					if(empty($response)) {
						$text .= __('There was an error when loading the video', uds_billboard_textdomain);
						break;
					}
					
					$response = json_decode($response);
					$text = $response->html;
					
					break;
				case 'dynamic':
					$query = new WP_Query(array(
						'offset' => $this->{'dynamic-offset'}
					));
					
					if($query->have_posts()){
						$query->the_post();
						$text = get_the_content();
						if(has_post_thumbnail()) {
							$id = get_post_thumbnail_id();
							$image_src = wp_get_attachment_image_src($id, 'full');

							// change the thumbnail also
							$this->thumb = $image_src[0];

							$width = $this->slider->width;
							$height = $this->slider->height;
							
							$timthumb = UDS_BILLBOARD_URL . 'lib/timthumb.php?';
							$image = $timthumb . 'src=' . str_replace(WP_CONTENT_URL . '/', '', $image_src[0]) . '&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1';
						}
					}
					
					break;
				default:
			}
			
			$out .= "<a href='{$this->link}' class='uds-bb-link'>";
			$out .= "<img src='$image' alt='' class='uds-bb-bg-image' />";
			$out .= "</a>";
			$out .= "<span style='display:none' class='uds-delay'>{$this->delay}</span>";
			$out .= "<span style='display:none' class='uds-transition'>$transition</span>";
			$out .= "<span style='display:none' class='uds-direction'>{$direction}</span>";
			$out .= "<span style='display:none' class='uds-background'>{$background}</span>";
			$out .= $text;
		$out .= "</div>\n";
		return $out;
	}
	
	public function renderThumb()
	{
		$timthumb = UDS_BILLBOARD_URL . 'lib/timthumb.php?';
		
		$width = $this->slider->thumbnailsWidth;
		$height = $this->slider->thumbnailsHeight;
		
		$image = $timthumb . 'src=' . str_replace(WP_CONTENT_URL . '/', '', $this->thumb) . '&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1';
		
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
			case 'color';
				$this->renderAdminColorpicker($attrib);
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
	
	private function renderAdminColorpicker($attrib)
	{
		global $uds_billboard_attributes;

		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '#<input type="text" name="uds_billboard['. $attrib .'][]" value="'.$this->{$attrib}.'" id="billboard-'. $attrib .'" class="billboard-'. $attrib .' color" />';
		echo '</div>';
	}
	
	
	private function renderAdminCheckbox($attrib)
	{
		global $uds_billboard_attributes;
		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<input type="hidden" name="uds_billboard['. $attrib .'][]" id="billboard-'. $attrib .'" class="billboard-'. $attrib .'" value="" />';
		echo '<input type="checkbox" name="uds_billboard['. $attrib .'][]" '.checked($this->{$attrib}, 'on', false) . ' id="billboard-'. $attrib .'" class="billboard-'. $attrib .' checkbox" />';
		echo '</div>';
	}
	
	private function renderAdminTextarea($attrib)
	{
		global $uds_billboard_attributes, $uds_description_mode;
		
		static $id = 0;
		
		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<input type="button" class="uds-content-editor button" value="Content Editor" />';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<textarea name="uds_billboard['. $attrib .'][]" class="billboard-'. $attrib .'" id="uds-text-'.$id.'">'. htmlspecialchars(stripslashes($this->{$attrib})) .'</textarea>';
		echo '<div class="content-editor" title="'.__('Content Editor', uds_billboard_textdomain).'">';
		echo '<div class="toolbar-buttons">';
		echo '<span class="button add">'.__('Add new box', uds_billboard_textdomain).'</span>';
		echo '<span class="button remove">'.__('Remove currently focused box', uds_billboard_textdomain).'</span>';
		echo '<label>Current Box Skin: </label>';
		echo '<select class="box-skin">';
		echo '<option value="" disabled="disabled"></option>';
		echo '<option value="inherit">'.__('Inherit', uds_billboard_textdomain).'</option>';
		echo '<option value="transparent">'.__('Transparent', uds_billboard_textdomain).'</option>';
		echo '<option value="dark">'.__('Dark', uds_billboard_textdomain).'</option>';
		echo '<option value="bright">'.__('Bright', uds_billboard_textdomain).'</option>';
		echo '</select>';
		echo '<div class="clear"></div>';
		echo '</div>';
		echo '<div class="editor-area">';
		$uds_description_mode = 'editor';
		echo do_shortcode(stripslashes($this->{$attrib}));
		echo '</div>';
		echo '<input type="button" value="Save" class="button primary save" />';
		echo '</div>';
		echo '<div class="clear"></div>';
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
		echo '	<a class="image-upload" title="Add an Image" href="">';
		echo '		<img alt="Add an Image" src="'. admin_url() . 'images/media-button-image.gif" id="billboard-'. $attrib .'-'. $unique_id .'" class="billboard-'. $attrib .'" />';
		echo '	</a>';
		echo '</div>';
		
		$unique_id++;
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