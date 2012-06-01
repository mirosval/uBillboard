<?php

// define data structure for billboard
$uds_billboard_attributes = array(
	'image'=> array(
		'type' => 'image',
		'label' => __('Image', uds_billboard_textdomain),
		'default' => ''
	),
	'image-alt' => array(
		'type' => 'text',
		'label' => __('Image Description (for SEO)', uds_billboard_textdomain),
		'default' => ''
	),
	'resize' => array(
		'type' => 'checkbox',
		'label' => __('Apply Automatic resizing', uds_billboard_textdomain),
		'default' => ''
	),
	'relative-paths' => array(
		'type' => 'checkbox',
		'label' => __('Use Relative Paths', uds_billboard_textdomain),
		'default' => 'on'
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
	'background-repeat' => array(
		'type' => 'checkbox',
		'label' => __('Tile background image', uds_billboard_textdomain),
		'default' => 'on'
	),
	'link' => array(
		'type' => 'text',
		'label' => __('Link URL', uds_billboard_textdomain),
		'default' => ''
	),
	'link-target' => array(
		'type' => 'checkbox',
		'label' => __('Open in a new window', uds_billboard_textdomain),
		'default' => ''
	),
	'stop' =>  array(
		'type' => 'checkbox',
		'label' => __('Stop animation on this slide', uds_billboard_textdomain),
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
			'crossFade'	=> __('Crossfade', uds_billboard_textdomain),
			'slide' 	=> __('Slide', uds_billboard_textdomain),
			'scale' 	=> __('Scale', uds_billboard_textdomain)
		),
		'default' => 'fade'
	),
	'direction' => array(
		'type' => 'select',
		'label' => __('Transition Direction', uds_billboard_textdomain),
		'options' => array(
			'none'				=> '--',
			'random' 			=> __('Random Direction', uds_billboard_textdomain),
			'intuitive'			=> __('Intuitive', uds_billboard_textdomain),
			'intuitiveVertical'	=> __('Intuitive Vertical', uds_billboard_textdomain),
			'center'			=> __('From Center', uds_billboard_textdomain),
			'left' 				=> __('From Left', uds_billboard_textdomain),
			'right' 			=> __('From Right', uds_billboard_textdomain),
			'top' 				=> __('From Top', uds_billboard_textdomain),
			'bottom' 			=> __('From Bottom', uds_billboard_textdomain),
			'randomSquares' 	=> __('Random Squares', uds_billboard_textdomain),
			'spiralIn' 			=> __('Spiral in', uds_billboard_textdomain),
			'spiralOut'			=> __('Spiral Out', uds_billboard_textdomain),
			'chess'				=> __('Chessboard', uds_billboard_textdomain),
			'zigzagHorizontal'	=> __('Zig-zag Horizontal', uds_billboard_textdomain),
			'zigzagVertical'	=> __('Zig-zag Vertical', uds_billboard_textdomain)
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
	'pause-slider' => array(
		'type' => 'checkbox',
		'label' => __('Pause uBillboard', uds_billboard_textdomain),
		'default' => 'on'
	),
	'autoplay-video' => array(
		'type' => 'checkbox',
		'label' => __('Automatically play video', uds_billboard_textdomain),
		'default' => 'on'
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
	),
	'dynamic-category' => array(
		'type' => 'blog-category',
		'label' => __('Post category', uds_billboard_textdomain),
		'default' => ''
	),
	'dynamic-title' => array(
		'type' => 'checkbox',
		'label' => __('Show post title', uds_billboard_textdomain),
		'default' => 'on'
	),
	'dynamic-content' => array(
		'type' => 'select',
		'label' => __('Show', uds_billboard_textdomain),
		'options' => array(
			'none' => __('No Content', uds_billboard_textdomain),
			'excerpt' => __('Post Excerpt', uds_billboard_textdomain),
			'content' => __('Post Content', uds_billboard_textdomain)
		),
		'default' => 'content'
	),
	'dynamic-link' => array(
		'type' => 'checkbox',
		'label' => __('Show &quot;Read More&quot; link', uds_billboard_textdomain),
		'default' => 'on'
	),
	'dynamic-position' => array(
		'type' => 'text',
		'label' => __('Position override', uds_billboard_textdomain),
		'default' => ''
	),
	'ken-burns' => array(
		'type' => 'checkbox',
		'label' => __('Ken Burns Effect', uds_billboard_textdomain),
		'default' => ''
	)
);

class uBillboardSlide {

	private $id;
	/**
	 *	@var $slider backlink back to the slider that contains this slide
	 */
	private $slider;
	private $thumb;
	private $thumbType;
	private $imageType;
	
	public static function upgradeFromV2($slide, $bbv2, $slider)
	{
		global $uds_billboard_attributes;
		
		// Solve transition
		$transition = 'fade';
		$transitions = array_keys($uds_billboard_attributes['transition']['options']);
		foreach($transitions as $t) {
			if(strpos(strtolower($slide['transition']), $t) !== false) {
				$transition = $t;
			}
		}
		
		// Solve Direction
		$direction = 'none';
		$directions = array_keys($uds_billboard_attributes['direction']['options']);
		foreach($directions as $d) {
			if(strpos(strtolower($slide['transition']), $d) !== false) {
				$direction = $d;
			}
		}
		
		// Transform legacy descriptions into uds-description format
		$content = 'none';
		$text = '';
		$text_evaluation = 'html';

		if($slide['layout'] !== 'none') {
			$content = 'editor';
			
			$padding_correction = 0;
			
			if(strpos($slide['layout'], 'left') !== false) {
				$width = round(0.3 * (int)$slider->width) - $padding_correction;
				$height = (int)$slider->height - $padding_correction;
				$top = 0;
				$left = 0;
			} elseif(strpos($slide['layout'], 'right') !== false) {
				$width = round(0.3 * (int)$slider->width) - $padding_correction;
				$height = (int)$slider->height - $padding_correction;
				$top = 0;
				$left = (int)$slider->width - $width;
			} else { // bottom
				$width = (int)$slider->width - $padding_correction;
				$height = round(0.3 * (int)$slider->height) - $padding_correction;
				$top = (int)$slider->height - $height;
				$left = 0;
			}
			
			if(strpos($slide['layout'], 'alt')) {
				$skin = 'bright';
			} else {
				$skin = 'dark';
			}
			
			$text = '<h2>' . stripslashes($slide['title']) . '</h2>';
			$text .= '<p>' . stripslashes($slide['text']) . '</p>';
			
			$text = '[uds-description top="'.$top.'px" left="'.$left.'px" width="'.$width.'px" height="'.$height.'px" skin="'.$skin.'"]' . $text . '[/uds-description]';
		}
		
		// Create slide
		$v3slide = new uBillboardSlide(array(
			'image' => $slide['image'],
			'image-alt' => '',
			'resize' => $bbv2['use-timthumb'],
			'background' => '000000',
			'background-transparent' => 'on',
			'link' => $slide['link'],
			'delay' => $slide['delay'],
			'transition' => $transition,
			'direction' => $direction,
			'content' => $content,
			'text' => $text,
			'text-evaluation' => $text_evaluation
		), $slider);
		
		return $v3slide;
	}
	
	/**
	 *	Static function to parse form arrays and transform them to
	 *	array of slides
	 *	
	 *	@param array $options keyed by slide and attribute
	 *	@param uBillboard $slider parent slider
	 *	@return array of slides
	 */
	public static function getSlides($options, $slider)
	{
		global $uds_billboard_attributes;

		$slides = array();
		
		// Loop through all form fields
		$n = 0;
		while(!empty($options) && $n < 100) {
			// Loop through all attributes
			$attributes = array();
			foreach($uds_billboard_attributes as $key => $option) {
				// if attribute is in form
				if(!isset($options[$key][$n])) {
					break;
				}
				
				// add it to the array
				$attributes[$key] = $options[$key][$n];
				
				// and remove it from form fields
				unset($options[$key][$n]);
				
				if(empty($options[$key])) {
					unset($options[$key]);
				}
			}
			
			// create a new slide from attributes
			if(!empty($attributes)) {
				$slide = new uBillboardSlide($attributes, $slider);
				if($slide !== null) $slides[] = $slide;
			}

			$n++;
		}
		
		return $slides;
	}
	
	/**
	 *	Constructor, create a new slide from options, or a default slide
	 *	$options:
	 *		bool false -> create default empty slide
	 *		array -> fill slide attributes from this array
	 *
	 *	@param bool|array $options
	 *	@param uBillboard $slider
	 *	
	 *	@return uBillboardSlide
	 */
	public function __construct($options = false, $slider)
	{
		global $uds_billboard_attributes;
		
		$this->thumb = '';
		
		// fill in with defaults
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
		
		// fill in from attributes
		foreach($uds_billboard_attributes as $key => $option) {
			if(isset($options[$key])) {
				$this->{$key} = $options[$key];
			} else {
				$this->{$key} = '';	
			}
		}
		
		// set parent slider
		$this->setSlider($slider);
	}
	
	/**
	 *	unserialize, make sure that every attribute is set,
	 *	if its not, set it up with default value
	 *	
	 *	@return void
	 */
	public function __wakeup()
	{
		global $uds_billboard_attributes;
		
		foreach($uds_billboard_attributes as $key => $option) {
			if(!isset($this->{$key})) {
				$this->{$key} = $option['default'];
			}
		}
	}
	
	/**
	 *	Generalized getter 
	 *	
	 *	@return mixed
	 */
	public function __get($key)
	{
		$key = $this->camelize($key);
		return $this->{$key};
	}
	
	/**
	 *	Importer, fills current instance attributes from
	 *	a SimpleXMLElement object
	 *	
	 *	@return void
	 */
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
	
	/**
	 *	go through attributes and copy them from the options array
	 *	while handling the default values correctly
	 *	
	 *	@param array $options
	 *
	 *	@return void
	 */
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
	
	/**
	 *	Exporter, processes all attributes and saves them in an xml
	 *	format that can be read by the importer.
	 *	
	 *	@return string exported XML
	 */
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
	
	/**
	 *	Performs internal checks on the uBillboardSlide if everything is ok
	 *	
	 *	@return bool
	 */
	public function isValid()
	{
		$text = isset($this->text) ? strip_tags($this->text) : '';
		
		$hasImage = !empty($this->image);

		$hasText = $this->content == 'editor' && !empty($text);
		$hasEmbed = $this->content == 'embed' && !empty($this->{'embed-url'});
		$hasDynamic = $this->content == 'dynamic';
		
		return $hasImage || $hasText || $hasEmbed || $hasDynamic;
	}
	
	/**
	 *	Helper function to render administration screens for this slide
	 *	
	 *	@return void
	 */
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
				<li><a href="#uds-slide-tab-effects-<?php echo $id ?>"><?php _e('Effects', uds_billboard_textdomain) ?></a></li>
			</ul>
			<div id="uds-slide-tab-background-<?php echo $id ?>" class="uds-slide-tab-background">
				<?php $this->renderAdminField('image') ?>
				<?php $this->renderAdminField('image-alt') ?>
				<?php $this->renderAdminField('resize') ?>
				<?php $this->renderAdminField('relative-paths') ?>
				<?php $this->renderAdminField('background') ?>
				<?php $this->renderAdminField('background-transparent') ?>
				<?php $this->renderAdminField('background-repeat') ?>
			</div>
			<div id="uds-slide-tab-content-<?php echo $id ?>" class="uds-slide-tab-content">
				<?php $this->renderAdminField('content') ?>
				<?php $this->renderAdminField('text') ?>
				<?php $this->renderAdminField('text-evaluation') ?>
				<?php $this->renderAdminField('embed-url') ?>
				<?php $this->renderAdminField('pause-slider') ?>
				<?php $this->renderAdminField('autoplay-video') ?>
				<?php $this->renderAdminField('dynamic-offset') ?>
				<?php $this->renderAdminField('dynamic-category') ?>
				<?php $this->renderAdminField('dynamic-title') ?>
				<?php $this->renderAdminField('dynamic-content') ?>
				<?php $this->renderAdminField('dynamic-link') ?>
				<?php $this->renderAdminField('dynamic-position') ?>
			</div>
			<div id="uds-slide-tab-link-<?php echo $id ?>" class="uds-slide-tab-link">
				<?php $this->renderAdminField('link') ?>
				<?php $this->renderAdminField('link-target') ?>
			</div>
			<div id="uds-slide-tab-transition-<?php echo $id ?>" class="uds-slide-tab-transition">
				<?php $this->renderAdminField('delay') ?>
				<?php $this->renderAdminField('transition') ?>
				<?php $this->renderAdminField('direction') ?>
				<?php $this->renderAdminField('stop') ?>
				<div class="clear"></div>
			</div>
			<div id="uds-slide-tab-effects-<?php echo $id ?>" class="uds-slide-tab-effects">
				<?php $this->renderAdminField('ken-burns') ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php
		
		$id++;
	}
	
	/**
	 *	Main Frontend renderer
	 *	
	 *	@return string rendered html content
	 */
	public function render()
	{
		global $uds_billboard_text_evaluation;
				
		// Thumb
		$this->thumb = $this->image;
		
		$out = "\t\t\t\t<div class='uds-bb-slide'>";
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

					$response = uds_billboard_oembed($url, $width, $height);

					if(is_object($response)) {
						$text = $response->html;
						$this->thumb = isset($response->thumbnail_url) ? $response->thumbnail_url : '';
					} else {
						$text = $response;
					}
					
					break;
				case 'dynamic':
					$query = new WP_Query(array(
						'offset' => $this->{'dynamic-offset'},
						'cat' => $this->{'dynamic-category'}
					));

					if($query->have_posts()){
						$query->the_post();
						
						$width = $this->slider->width / 3;
						$height = $this->slider->height - 40;
						
						$skin = 'uds-' . $this->slider->style;
						
						$position = !empty($this->{'dynamic-position'}) ? esc_attr($this->{'dynamic-position'}) : '';
						
						$text = '<div class="uds-bb-description '.$skin.'" style="top:20px;left:20px;width:'.$width.'px;height:'.$height.'px;'.$position.'"><div class="uds-bb-description-inside">';
						
						if($this->{'dynamic-title'} == 'on') {
							$text .= '<h3>' . get_the_title() . '</h3>';
						}
						
						if($this->{'dynamic-content'} != 'none') {
							if($this->{'dynamic-content'} == 'excerpt') {
								$text .= get_the_excerpt();
							} else {
								$text .= get_the_content();
							}
						}
						
						if($this->{'dynamic-link'} == 'on') {
							$text .= '<br /><a href="'.get_permalink().'">'.__('Read More', uds_billboard_textdomain).'</a>';
						}
						
						$text .= '</div></div>';
						
						// Do not draw the box when there is no content
						if($this->{'dynamic-title'} != 'on' && $this->{'dynamic-content'} == 'none' && $this->{'dynamic-link'} != 'on') {
							$text = '';
						}
						
						if(has_post_thumbnail()) {
							$id = get_post_thumbnail_id();
							$image_src = wp_get_attachment_image_src($id, 'full');
							
							if(!empty($image_src[0])) {
								$this->image = $this->thumb = $image_src[0];
							}
						}
					}
					
					// clean up
					wp_reset_query();
					
					break;
				default:
			}
			
			// Image
			$image = $this->image();
			
			if(is_wp_error($image)) {
				$image = '';
			}
			
			$image_alt = $this->{'image-alt'};

			if(!empty($text)) {
				$text = "\n\t\t\t\t" . $text;
			}

			$out .= "
					<a href='{$this->link}' class='uds-bb-link'>
						<img src='$image' alt='$image_alt' class='uds-bb-bg-image' />
					</a>$text
				</div>\n";
		return $out;
	}
	
	public function renderJS()
	{
		global $uds_billboard_attributes;
		
		$hasVideo = 'false';
		
		$target = $this->{'link-target'} == 'on' ? '_blank' : '';
		$delay = (int)$this->delay;
		
		// Transition, make nil when embedded content exists
		$transition = $this->transition;
		
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
		
		// Video
		if($this->content == 'embed') {
			$hasVideo = 'true';
			//$transition = 'none';
			if(!in_array($direction, array('intuitive', 'intuitiveVertical'))) {
				$direction = 'intuitive';
			}
		}
		
		// Background
		$background = '#' . $this->background;
		if($this->{'background-transparent'} == 'on') {
			$background = 'transparent';
		}
		
		// Background Repeat
		$background_repeat = $this->{'background-repeat'};
		if($background_repeat == 'on') {
			$background_repeat = 'repeat';
		} else {
			$background_repeat = 'no-repeat';
		}
		
		$stop = $this->stop == 'on' ? 'true': 'false';
	
		$pause_slider = $this->{'pause-slider'} == 'on' ? 'true' : 'false';
		$autoplay_video = $this->{'autoplay-video'} == 'on' ? 'true' : 'false';
		
		$ken_burns = $this->{'ken-burns'} == 'on' ? 'true' : 'false';
		
		$out = "{
						hasVideo: {$hasVideo},
						linkTarget: '{$target}',
						delay: {$delay},
						transition: '{$transition}',
						direction: '{$direction}',
						bgColor: '{$background}',
						repeat: '{$background_repeat}',
						stop: {$stop},
						autoplayVideo: {$autoplay_video},
						kenBurns: {$ken_burns}
					}";
		
		return $out;
	}
	
	/**
	 *	Render thumbnail image
	 *	
	 *	@return string thumbnail html
	 */
	public function renderThumb()
	{
		$width = $this->slider->thumbnailsWidth;
		$height = $this->slider->thumbnailsHeight;
		
		$thumb = $this->thumb();
		
		if(is_wp_error($thumb)) {
			print $thumb->get_error_message();
			$thumb = '';
		}
		
		$thumb = apply_filters('uds_billboard_slide_thumb', $thumb, $this);
		
		$image = esc_attr($thumb);

		return "						<div class='uds-bb-thumb'>
							<img src='$image' alt='' width='$width' height='$height' />
						</div>\n";
	}
	
	/**
	 *	Setter for parent slider
	 *	
	 *	@return void
	 */
	public function setSlider($slider)
	{
		if(is_a($slider, 'uBillboard')) {
			$this->slider = $slider;
		}
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function resizeImages($force_recreate = false)
	{
		// populate the image and thumb fields with correct values
		$this->render();
		
		// try to render the thumb
		$image = $this->thumb(true);
		if(is_wp_error($image)) {
			return $image;
		}
		
		// try to render the image
		$image = $this->image(true);
		if(is_wp_error($image)) {
			return $image;
		}
		
		return true;
	}
	
	/**
	 *	Helper function to render every admin field
	 *	
	 *	@return void
	 */
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
			case 'color':
				$this->renderAdminColorpicker($attrib);
				break;
			case 'blog-category':
				$this->renderAdminBlogCategory($attrib);
				break;
			default:
		}
	}
	
	/**
	 *	Helper function to render admin text fields
	 *	
	 *	@return
	 */
	private function renderAdminText($attrib)
	{
		global $uds_billboard_attributes;
		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<input type="text" name="uds_billboard['. $attrib .'][]" value="' . htmlspecialchars(stripslashes($this->{$attrib})) . '" id="billboard-'. $attrib .'" class="billboard-'. $attrib .'" />';
		echo '</div>';
	}
	
	/**
	 *	Helper function to render admin colorpicker field
	 *	
	 *	@return void
	 */
	private function renderAdminColorpicker($attrib)
	{
		global $uds_billboard_attributes;

		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '#<input type="text" name="uds_billboard['. $attrib .'][]" value="'.$this->{$attrib}.'" id="billboard-'. $attrib .'" class="billboard-'. $attrib .' color" />';
		echo '</div>';
	}
	
	/**
	 *	Helper function to render admin checkbox field
	 *	
	 *	@return void
	 */
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
	
	/**
	 *	Helper function to render the content editor
	 *	
	 *	@return void
	 */
	private function renderAdminTextarea($attrib)
	{
		global $uds_billboard_attributes, $uds_description_mode;
		
		static $id = 0;
		
		$attrib_full = $uds_billboard_attributes[$attrib];
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<input type="button secondary" class="uds-content-editor button" value="Content Editor" />';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<textarea name="uds_billboard['. $attrib .'][]" class="billboard-'. $attrib .'" id="uds-text-'.$id.'">'. htmlspecialchars(stripslashes($this->{$attrib})) .'</textarea>';
		echo '<div class="content-editor" title="'.__('Content Editor', uds_billboard_textdomain).'">';
		echo '<div class="toolbar-buttons">';
		echo '<input type="button" class="button secondary uds-add-box" value="'.__('Add new box', uds_billboard_textdomain).'" />';
		echo '<input type="button" class="button secondary uds-remove-box" value="'.__('Remove currently focused box', uds_billboard_textdomain).'" />';
		echo '<label>Current Box Skin: </label>';
		echo '<select class="box-skin">';
		echo '<option value="" disabled="disabled"></option>';
		echo '<option value="inherit">'.__('Inherit', uds_billboard_textdomain).'</option>';
		echo '<option value="transparent">'.__('Transparent', uds_billboard_textdomain).'</option>';
		echo '<option value="dark">'.__('Dark', uds_billboard_textdomain).'</option>';
		echo '<option value="bright">'.__('Bright', uds_billboard_textdomain).'</option>';
		echo '</select>';
		echo '<a href="" class="content-editor-help">'.__('Help', uds_billboard_textdomain).'</a>';
		echo '<div class="clear"></div>';
		echo '</div>';
		echo '<div class="editor-area">';
		$uds_description_mode = 'editor';
		echo do_shortcode(stripslashes($this->{$attrib}));
		echo '</div>';
		echo '<input type="button" class="button secondary save uds-save-content" value="Save" />';
		echo '</div>';
		echo '<div class="clear"></div>';
		echo '</div>';
		
		$id++;
	}
	
	/**
	 *	Helper function to render admin select
	 *	
	 *	@return void
	 */
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
	
	/**
	 *	Helper function to render images
	 *	
	 *	@return void
	 */
	private function renderAdminImage($attrib)
	{
		static $unique_id = 0;
		
		echo '<div class="'. $attrib .'-url-wrapper">';
		echo '	<label for="billboard-'. $attrib .'-'. $unique_id .'-hidden">' . __('Image URL', uds_billboard_textdomain) . '</label>';
		echo '	<input type="text" name="uds_billboard['. $attrib .'][]" value="'. $this->{$attrib} .'" id="billboard-'. $attrib .'-'. $unique_id .'-hidden" />';
		echo '	<a class="image-upload" title="Add an Image" href="">';
		echo '		<img alt="Add an Image" src="'. admin_url() . 'images/media-button-image.gif" id="billboard-'. $attrib .'-'. $unique_id .'" class="billboard-'. $attrib .'" />';
		echo '	</a>';
		echo '</div>';
		
		$unique_id++;
	}
	
	/**
	 *	Helper function to render admin select
	 *	
	 *	@return void
	 */
	private function renderAdminBlogCategory($attrib)
	{
		global $uds_billboard_attributes;
		$attrib_full = $uds_billboard_attributes[$attrib];
		
		if($attrib_full['type'] != 'blog-category') return;
	
		$categories = get_categories();
	
		echo '<div class="'. $attrib .'-wrapper">';
		echo '<label for="billboard-'. $attrib .'">'. $attrib_full['label'] .'</label>';
		echo '<select name="uds_billboard['. $attrib .'][]" class="billboard-'. $attrib .'">';
		echo '<option disabled="disabled">'. $attrib_full['label'] .'</option>';
		foreach($categories as $category){
			$selected = '';
			if($this->{$attrib} == $category->cat_ID){
				$selected = 'selected="selected"';
			}
			echo '<option value="'. $category->cat_ID .'" '. $selected .'>'. $category->name .'</option>';
		}
		echo '</select>';
		echo '</div>';
	}
		
	/**
	 *	Helper function to transform attributes from attribute-text to attributeText
	 *	
	 *	@return stirng camelized string
	 */
	private function camelize($string) 
	{
		$string = str_replace(array('-', '_'), ' ', $string); 
		$string = ucwords($string); 
		$string = str_replace(' ', '', $string);  
		
		$string = lcfirst($string);
		
		return $string;
	}
	
	private function thumb($force_recreate = false)
	{
		$width = $this->slider->thumbnailsWidth;
		$height = $this->slider->thumbnailsHeight;
		
		$resizedImage = $this->resizeImage($this->thumb, $width, $height, 'thumb', $force_recreate);
		$resizedImageRetina = $this->resizeImage($this->thumb, $width * 2, $height * 2, 'thumb-retina', $force_recreate);
		return $resizedImage;
	}
	
	private function image($force_recreate = false)
	{
		if($this->resize == 'on' && !empty($this->image)) {
			$width = $this->slider->width;
			$height = $this->slider->height;
		
			$resizedImage = $this->resizeImage($this->image, $width, $height, 'full', $force_recreate);
			$resizedImage = $this->resizeImage($this->image, $width * 2, $height * 2, 'full-retina', $force_recreate);
			
			if($this->{'ken-burns'} == 'on') {
				$this->resizeImage($this->image, $width * 1.2, $height * 1.2, 'ken', $force_recreate);
				$this->resizeImage($this->image, $width * 2.4, $height * 2.4, 'ken-retina', $force_recreate);
			}
			
			return $resizedImage;
		} else {
			return $this->image;
		}
	}
	
	private function imageName($type = 'full')
	{
		$name = sanitize_title_with_dashes($this->slider->name) . '-' . $this->id . "-$type.";
		
		if(empty($this->imageType)) {
			if(file_exists(UDS_CACHE_PATH . '/' . $name . 'jpg')) {
				return $name . 'jpg';
			}
			
			if(file_exists(UDS_CACHE_PATH . '/' . $name . 'png')) {
				return $name . 'png';
			}
		}
		
		return $name . $this->imageType;
	}
	
	private function thumbName($type = 'thumb')
	{
		$name = sanitize_title_with_dashes($this->slider->name) . '-' . $this->id . "-$type.";
		
		if(empty($this->thumbType)) {
			if(file_exists(UDS_CACHE_PATH . '/' . $name . 'jpg')) {
				return $name . 'jpg';
			}
			
			if(file_exists(UDS_CACHE_PATH . '/' . $name . 'png')) {
				return $name . 'png';
			}
		}
		
		return $name . $this->thumbType;
	}
	
	private function thumbExists($type)
	{
		return file_exists(UDS_CACHE_PATH . '/' . $this->thumbName($type));
	}
	
	private function imageExists($type)
	{
		return file_exists(UDS_CACHE_PATH . '/' . $this->imageName($type));
	}
	
	private function resizeImage($src, $new_width, $new_height, $type = 'full', $force_recreate = false)
	{
		if(!$force_recreate && $type == 'thumb' && $this->thumbExists($type)) {
			return UDS_CACHE_URL . '/' . $this->thumbName($type);
		}
		
		if(!$force_recreate && $type != 'thumb' && $this->imageExists($type)) {
			return UDS_CACHE_URL . '/' . $this->imageName($type);
		}
		
		// check if the cache dir is writable
		if(!is_writable(UDS_CACHE_PATH)) {
			return new WP_Error('uds_billboard_slide', sprintf(__('Path: "%s" is not writable!',uds_billboard_textdomain), UDS_CACHE_PATH));
		}
		
		// attempt to download the image original
		if($type == 'thumb' || $type == 'thumb-retina') {
			// $this->thumb might be different from $this->image (e.g. in case of videos) so we get it separately
			$url = $this->thumb;
		} else {
			$url = $this->image;
		}
		
		$response = wp_remote_get($url);
		
		if(is_wp_error($response)) {
			return $response;
		}
		
		// catch wp_remote_get errors
		if($response['response']['code'] != 200) {
			// try if file_get_contents would work...
			$res = @file_get_contents($url);
			
			if($res === false) {
				$error = sprintf(__('Failed to load original image: %s %s',uds_billboard_textdomain), $response['response']['code'], $response['response']['message']);
				return new WP_Error('uds_billboard_slide', $error);
			}
			
			// get the image type
			$info = @getimagesize($url);
			if(is_array($info)) {
				$response['headers']['content-type'] = $info['mime'];
			}
			
			$response['body'] = $res;
		}

		// attempt to guess image type
		$image_type = 'png';
		if(isset($response['headers']['content-type']) && ($response['headers']['content-type'] == 'image/jpeg' || $response['headers']['content-type'] == 'image/jpg')) {
			$image_type = 'jpg';
		}
		
		// set up image type for storage
		if($type == 'thumb') {
			$this->thumbType = $image_type;
			$imagePath = UDS_CACHE_PATH . '/' . $this->thumbName($type);
		} else {
			$this->imageType = $image_type;
			$imagePath = UDS_CACHE_PATH . '/' . $this->imageName($type);
		}
		
		$src = @imagecreatefromstring($response['body']);
				
		if(!$src) {
			return new WP_Error('uds_billboard_slide', __('Failed to create image. (imagecreatefromstring())',uds_billboard_textdomain));
		}
		
		$dst = @imagecreatetruecolor($new_width, $new_height);
		if(!$dst) {
			return new WP_Error('uds_billboard_slide', __('Failed to create new image context.',uds_billboard_textdomain));
		}
		
		// Get original width and height
		$width = imagesx($src);
		$height = imagesy($src);
		
		$origin_x = 0;
		$origin_y = 0;

		// generate new w/h if not provided
		if($new_width && !$new_height) {
			$new_height = floor($height * ($new_width / $width));
		} else if($new_height && !$new_width) {
			$new_width = floor($width * ($new_height / $height));
		}
		
		$src_x = $src_y = 0;
		$src_w = imagesx($src);
		$src_h = imagesy($src);

		$cmp_x = $width / $new_width;
		$cmp_y = $height / $new_height;

		if ($cmp_x > $cmp_y) {
			$src_w = round($width / $cmp_x * $cmp_y);
			$src_x = round(($width - ($width / $cmp_x * $cmp_y)) / 2);
		} else if($cmp_y > $cmp_x) {
			$src_h = round($height / $cmp_y * $cmp_x);
			$src_y = round(($height - ($height / $cmp_y * $cmp_x)) / 2);
		}

		imagefill($dst, 0, 0, imagecolortransparent($dst));
		
		if(!imagecopyresampled($dst, $src, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h)) {
			return new WP_Error('uds_billboard_slide', __('Failed to resize image',uds_billboard_textdomain));
		}
		
		if($image_type == 'jpg') {
			if(!imagejpeg($dst, $imagePath, 80)){
				return new WP_Error('uds_billboard_slide', __('Failed to save image',uds_billboard_textdomain));
			}
		} else {
			if(!imagepng($dst, $imagePath, 8)){
				return new WP_Error('uds_billboard_slide', __('Failed to save image',uds_billboard_textdomain));
			}
		}
		
		
		if($thumb) {
			return UDS_CACHE_URL . '/' . $this->thumbName($type);
		} else {
			return UDS_CACHE_URL . '/' . $this->imageName($type);
		}
	}
}

?>