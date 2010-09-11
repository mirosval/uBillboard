<?php
/*
Plugin Name: uBillboard
Plugin URI: http://code.udesignstudios.net/plugins/uBillboard
Description: uBillboard is a slider plugin by uDesignStudios that allows you to create an eye-catching presentation for your web. (Admin menu icon: http://p.yusukekamiyamane.com/)
Version: 1.0.0
Author: uDesign
Author URI: http://udesignstudios.net
Tags: billboard, slider, jquery, javascript, effects, udesign
*/

// General Options
define('UDS_BILLBOARD_URL', plugin_dir_url(__FILE__));
define('UDS_BILLBOARD_PATH', plugin_dir_path(__FILE__));
define('UDS_BILLBOARD_USE_COMPRESSION', true);

// User configurable options
define('UDS_BILLBOARD_OPTION', 'uds-billboard');
define('UDS_BILLBOARD_OPTION_WIDTH', 'uds-billboard-width');
define('UDS_BILLBOARD_OPTION_HEIGHT', 'uds-billboard-height');
define('UDS_BILLBOARD_OPTION_SQUARE_SIZE', 'uds-billboard-square-size');
define('UDS_BILLBOARD_OPTION_COLUMN_WIDTH', 'uds-billboard-column-width');
define('UDS_BILLBOARD_OPTION_SHOW_PAGINATOR', 'uds-billboard-show-paginator');
define('UDS_BILLBOARD_OPTION_USE_TIMTHUMB', 'uds-billboard-use-timthumb');
define('UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM', 'uds-billboard-timthumb-zoom');
define('UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY', 'uds-billboard-timthumb-quality');

add_option(UDS_BILLBOARD_OPTION, array());
add_option(UDS_BILLBOARD_OPTION_WIDTH, 940);
add_option(UDS_BILLBOARD_OPTION_HEIGHT, 380);
add_option(UDS_BILLBOARD_OPTION_SQUARE_SIZE, 100);
add_option(UDS_BILLBOARD_OPTION_COLUMN_WIDTH, 50);
add_option(UDS_BILLBOARD_OPTION_SHOW_PAGINATOR, 'on');
add_option(UDS_BILLBOARD_OPTION_USE_TIMTHUMB, '');
add_option(UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM, 'on');
add_option(UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY, 75);

// define data structure for billboard
$uds_billboard_attributes = array(
	'image'=> array(
		'type' => 'image',
		'label' => 'Image'
	),
	'title' => array(
		'type' => 'text',
		'label' => 'Title'
	),
	'link' => array(
		'type' => 'text',
		'label' => 'Link URL'
	),
	'text' => array(
		'type' => 'textarea',
		'label' => 'Text'
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
	'layout' => array(
		'type' => 'select',
		'label' => 'Slide Layout',
		'options' => array(
			'none' => 'No Description',
			'stripe-left' => 'Stripe Left',
			'stripe-right' => 'Stripe Right',
			'stripe-bottom' => 'Stripe Bottom',
			'stripe-left alt' => 'Alternate Stripe Left',
			'stripe-right alt' => 'Alternate Stripe Right',
			'stripe-bottom alt' => 'Alternate Stripe Bottom',
		),
		'default' => 'none'
	),
	'transition' => array(
		'type' => 'select',
		'label' => 'Transition',
		'options' => array(
			'random' => 'Random',
			'fade' => 'Fade',
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
			'columnWave' => 'Column Wave',
			'curtainRight' => 'Curtain Right',
			'curtainLeft' => 'Curtain Left',
			'curtainRotateRight' => 'Curtain Rotate Right',
			'curtainRotateLeft' => 'Curtain Rotate Left',
			'interweaveLeft' => 'Interweave Left',
			'interweaveRight' => 'Interweave Right'
		),
		'default' => 'fade'
	)
);

// initialize billboard
add_action('init', 'uds_billboard_init');
function uds_billboard_init()
{
	$dir = UDS_BILLBOARD_URL;
	if(is_admin()){
		add_thickbox();
		wp_enqueue_script("jquery-ui-sortable");
		wp_enqueue_script("jquery-ui-draggable");
		wp_enqueue_script('uds-cookie', $dir."js/jquery_cookie.js");
		wp_enqueue_script('uds-billboard', $dir."js/billboard-admin.js");
		wp_enqueue_style('uds-billboard', $dir.'css/billboard-admin.css', false, false, 'screen');
	} else {
		wp_enqueue_style('uds-billboard', $dir.'css/billboard.css', false, false, 'screen');
		wp_enqueue_script("easing", $dir."js/jquery.easing.js", array('jquery'));
		if(UDS_BILLBOARD_USE_COMPRESSION){
			wp_enqueue_script("uds-billboard", $dir."js/billboard.min.js", array('jquery', 'easing'));
		} else {
			wp_enqueue_script("uds-billboard", $dir."js/billboard.js", array('jquery', 'easing'));
		}
	}
}

add_action('wp_head', 'uds_billboard_options_javascript', 1);
function uds_billboard_options_javascript()
{
	?>
	<script type="text/javascript">
		var uds_billboard_width = <?php echo get_option(UDS_BILLBOARD_OPTION_WIDTH) ?>;
		var uds_billboard_height = <?php echo get_option(UDS_BILLBOARD_OPTION_HEIGHT) ?>;
		var uds_billboard_square_size = <?php echo get_option(UDS_BILLBOARD_OPTION_SQUARE_SIZE) ?>;
		var uds_billboard_column_width = <?php echo get_option(UDS_BILLBOARD_OPTION_COLUMN_WIDTH) ?>;
		var uds_billboard_show_paginator = <?php echo get_option(UDS_BILLBOARD_OPTION_SHOW_PAGINATOR) == 'on' ? 'true' : 'false' ?>;
	</script>
	<?php
}

register_activation_hook(__FILE__, 'uds_billboard_activation_hook');
function uds_billboard_activation_hook()
{
	add_option(UDS_BILLBOARD_OPTION, array());
	add_option(UDS_BILLBOARD_OPTION_WIDTH, 940);
	add_option(UDS_BILLBOARD_OPTION_HEIGHT, 380);
	add_option(UDS_BILLBOARD_OPTION_SQUARE_SIZE, 100);
	add_option(UDS_BILLBOARD_OPTION_COLUMN_WIDTH, 50);
	add_option(UDS_BILLBOARD_OPTION_SHOW_PAGINATOR, 'on');
	add_option(UDS_BILLBOARD_OPTION_USE_TIMTHUMB, '');
	add_option(UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM, 'on');
	add_option(UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY, 75);
}

register_activation_hook(__FILE__, 'uds_billboard_deactivation_hook');
function uds_billboard_deactivation_hook()
{
	delete_option(UDS_BILLBOARD_OPTION);
	delete_option(UDS_BILLBOARD_OPTION_WIDTH);
	delete_option(UDS_BILLBOARD_OPTION_HEIGHT);
	delete_option(UDS_BILLBOARD_OPTION_SQUARE_SIZE);
	delete_option(UDS_BILLBOARD_OPTION_COLUMN_WIDTH);
	delete_option(UDS_BILLBOARD_OPTION_SHOW_PAGINATOR);
	delete_option(UDS_BILLBOARD_OPTION_USE_TIMTHUMB);
	delete_option(UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM);
	delete_option(UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY);
}

add_action('admin_menu', 'uds_billboard_menu');
function uds_billboard_menu()
{	
	$icon = UDS_BILLBOARD_URL . 'images/menu-icon.png';
	add_menu_page("uBillboard", "uBillboard", 'manage_options', 'uds_billboard_admin', 'uds_billboard_admin', $icon, 61);
}

// Admin menu entry handling
function uds_billboard_admin()
{
	include 'billboard-admin.php';
}

// check for POST data and update billboard accordingly
function uds_billboard_proces_updates()
{
	global $uds_billboard_attributes;

	$post = $_POST['uds_billboard'];
	//d($post);
	if(empty($post)) return;
	
	if(!is_admin()) return;
	
	// update billboard array
	$billboards = array();
	foreach($uds_billboard_attributes as $attrib => $options){
		foreach($post[$attrib] as $key => $item){
			if($billboards[$key] == null){
				$billboard = uds_billboard_default_billboard();
			} else {
				$billboard = $billboards[$key];
			}
			
			$billboard->$attrib = $item;
			$billboards[$key] = $billboard;
		}
	}
	
	// delete empty billboards
	$bb_default = uds_billboard_default_billboard();
	foreach($billboards as $key => $bb){
		$delete = true;
		foreach($uds_billboard_attributes as $attrib => $options){
			if($bb->$attrib != $bb_default->$attrib){
				$delete = false;
			}
		}
		
		if($delete){
			unset($billboards[$key]);
		}
	}
	
	update_option(UDS_BILLBOARD_OPTION, serialize($billboards));
	update_option(UDS_BILLBOARD_OPTION_WIDTH, (int)$_POST[UDS_BILLBOARD_OPTION_WIDTH]);
	update_option(UDS_BILLBOARD_OPTION_HEIGHT, (int)$_POST[UDS_BILLBOARD_OPTION_HEIGHT]);
	update_option(UDS_BILLBOARD_OPTION_SQUARE_SIZE, (int)$_POST[UDS_BILLBOARD_OPTION_SQUARE_SIZE]);
	update_option(UDS_BILLBOARD_OPTION_COLUMN_WIDTH, (int)$_POST[UDS_BILLBOARD_OPTION_COLUMN_WIDTH]);
	update_option(UDS_BILLBOARD_OPTION_SHOW_PAGINATOR, $_POST[UDS_BILLBOARD_OPTION_SHOW_PAGINATOR] == '' ? '' : 'on');
	update_option(UDS_BILLBOARD_OPTION_USE_TIMTHUMB, $_POST[UDS_BILLBOARD_OPTION_USE_TIMTHUMB] == '' ? '' : 'on');
	update_option(UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM, $_POST[UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM] == '' ? '' : 'on');
	update_option(UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY, (int)$_POST[UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY]);
	//delete_option(UDS_BILLBOARD_OPTION);
}

// Initialize empty billboard instance
function uds_billboard_default_billboard()
{
	global $uds_billboard_attributes;

	$bb = new StdClass();
	foreach($uds_billboard_attributes as $att => $options){
		if(isset($options['default'])){
			$bb->$att = $options['default'];
		} else {
			$bb->$att = '';
		}
	}
	return $bb;
}

////////////////////////////////////////////////////////////////////////////////
//
//	Functions to render billboard admin form based on the data structure
//
////////////////////////////////////////////////////////////////////////////////

// Render a single input field
function uds_billboard_render_field($item, $attrib, $unique_key){
	global $uds_billboard_attributes;

	$attrib_full = $uds_billboard_attributes[$attrib];
	switch($attrib_full['type']){
		case 'input':
		case 'text':
			uds_billboard_render_text($item, $attrib, $unique_key);
			break;
		case 'textarea':
			uds_billboard_render_textarea($item, $attrib, $unique_key);
			break;
		case 'select':
			uds_billboard_render_select($item, $attrib, $unique_key);
			break;
		case 'image':
			uds_billboard_render_image($item, $attrib, $unique_key);
			break;
		default:
	}
}

// Render text field
function uds_billboard_render_text($item, $attrib, $unique_id)
{
	global $uds_billboard_attributes;
	$attrib_full = $uds_billboard_attributes[$attrib];
	echo '<div class="'. $attrib .'-wrapper">';
	echo '<label for="billboard-'. $attrib .'-'. $unique_id .'">'. $attrib_full['label'] .'</label>';
	echo '<input type="text" name="uds_billboard['. $attrib .'][]" value="' . htmlspecialchars(stripslashes($item->$attrib)) . '" id="billboard-'. $attrib .'-'. $unique_id .'" class="billboard-'. $attrib .'" />';
	echo '</div>';
}

// Render textarea
function uds_billboard_render_textarea($item, $attrib, $unique_id)
{
	global $uds_billboard_attributes;
	$attrib_full = $uds_billboard_attributes[$attrib];
	echo '<div class="'. $attrib .'-wrapper">';
	echo '<label for="billboard-'. $attrib .'-'. $unique_id .'">'. $attrib_full['label'] .'</label>';
	echo '<textarea name="uds_billboard['. $attrib .'][]" class="billboard-'. $attrib .'">'. htmlspecialchars(stripslashes($item->$attrib)) .'</textarea>';
	echo '</div>';
}

// Render Select field
function uds_billboard_render_select($item, $attrib, $unique_id)
{
	global $uds_billboard_attributes;
	$attrib_full = $uds_billboard_attributes[$attrib];
	
	if($attrib_full['type'] != 'select') return;

	echo '<div class="'. $attrib .'-wrapper">';
	echo '<label for="billboard-'. $attrib .'-'. $unique_id .'">'. $attrib_full['label'] .'</label>';
	echo '<select name="uds_billboard['. $attrib .'][]" class="billboard-'. $attrib .'">';
	if(is_array($attrib_full['options'])){
		foreach($attrib_full['options'] as $key => $option){
			$selected = '';
			if($item->$attrib == $key){
				$selected = 'selected="selected"';
			}
			echo '<option value="'. $key .'" '. $selected .'>'. $option .'</option>';
		}
	}
	echo '</select>';
	echo '</div>';
}

// Render Image input
function uds_billboard_render_image($item, $attrib, $unique_id)
{
	echo '<div class="'. $attrib .'-wrapper">';
	echo '<a class="thickbox" title="Add an Image" href="media-upload.php?type=image&TB_iframe=true&width=640&height=345">';
	if(!empty($item->image)){
		echo '<img alt="Add an Image" src="'. $item->$attrib .'" id="billboard-'. $attrib .'-'. $unique_id .'" class="billboard-'. $attrib  .'" />';
	} else {
		echo '<img alt="Add an Image" src="'. UDS_BILLBOARD_URL .'images/noimg385x180.jpg" id="billboard-'. $attrib .'-'. $unique_id .'" class="billboard-'. $attrib .'" />';
	}
	echo '</a>';
	echo '<input type="hidden" name="uds_billboard['. $attrib .'][]" value="'. $item->$attrib .'" id="billboard-'. $attrib .'-'. $unique_id .'-hidden" />';
	echo '</div>';
}

// render JS support for image input
function uds_billboard_render_js_support()
{
	global $uds_billboard_attributes;
	$selector = '';
	foreach($uds_billboard_attributes as $attrib => $options){
		if($options['type'] == 'image'){
			$selector .= '.billboard-'.$attrib;
		}
	}
	?>
	<script language='JavaScript' type='text/javascript'>
	var set_receiver = function(rec){
		//console.log(rec);
		window.receiver = jQuery(rec).attr('id');
		window.receiver_hidden = jQuery(rec).attr('id')+'-hidden';
	}
	var send_to_editor = function(img){
		tb_remove();
		if(jQuery(jQuery(img)).is('a')){ // work around Link URL supplied
		   var src = jQuery(jQuery(img)).find('img').attr('src');
		} else {
		   var src = jQuery(jQuery(img)).attr('src');
		}
	 
		//console.log(window.receiver);
		//console.log(src);
		jQuery('#'+window.receiver).attr('src', src);
		jQuery("#"+window.receiver_hidden).val(src);
	}
	jQuery('<?php echo $selector; ?>').click(function(){
		set_receiver(this);
	});
	</script>
	<?php
}

function uds_billboard_admin_options() 
{
	?>
	<div class="uds-billboard-width">
		<label for="uds-billboard-width">Billboard Width</label>
		<input type="text" id="uds-billboard-width" name="<?php echo UDS_BILLBOARD_OPTION_WIDTH ?>" value="<?php echo get_option(UDS_BILLBOARD_OPTION_WIDTH) ?>" />
		<span class="unit">pixels</span>
		<span class="tooltip">?</span>
		<div class="tooltip-content">Billboard Width in pixels</div>
	</div>
	<div class="uds-billboard-height">
		<label for="uds-billboard-height">Billboard Height</label>
		<input type="text" id="uds-billboard-height" name="<?php echo UDS_BILLBOARD_OPTION_HEIGHT ?>" value="<?php echo get_option(UDS_BILLBOARD_OPTION_HEIGHT) ?>" />
		<span class="unit">pixels</span>
		<span class="tooltip">?</span>
		<div class="tooltip-content">Billboard Height in pixels</div>
	</div>
	<div class="uds-billboard-square-size">
		<label for="uds-billboard-square-size">Square Size</label>
		<input type="text" id="uds-billboard-square-size" name="<?php echo UDS_BILLBOARD_OPTION_SQUARE_SIZE ?>" value="<?php echo get_option(UDS_BILLBOARD_OPTION_SQUARE_SIZE) ?>" />
		<span class="unit">pixels</span>
		<span class="tooltip">?</span>
		<div class="tooltip-content">Square dimension, applies only to transitions based on squares <img src="<?php echo UDS_BILLBOARD_URL ?>images/square_size.png" alt="" /></div>
	</div>
	<div class="uds-billboard-column-width">
		<label for="uds-billboard-column-width">Column Width</label>
		<input type="text" id="uds-billboard-column-width" name="<?php echo UDS_BILLBOARD_OPTION_COLUMN_WIDTH ?>" value="<?php echo get_option(UDS_BILLBOARD_OPTION_COLUMN_WIDTH) ?>" />
		<span class="unit">pixels</span>
		<span class="tooltip">?</span>
		<div class="tooltip-content">Column width, applies only to column-based transitions <img src="<?php echo UDS_BILLBOARD_URL ?>images/column_width.png" alt="" /></div>
	</div>
	<div class="uds-billboard-show-paginator">
		<label for="uds-billboard-show-paginator">Show Paginator</label>
		<input type="checkbox" id="uds-billboard-show-paginator" name="<?php echo UDS_BILLBOARD_OPTION_SHOW_PAGINATOR ?>" <?php echo get_option(UDS_BILLBOARD_OPTION_SHOW_PAGINATOR) == 'on' ? 'checked="checked"' : '' ?> />
		<span class="tooltip">?</span>
		<div class="tooltip-content">Check to show paginator in the bottom right corner <img src="<?php echo UDS_BILLBOARD_URL ?>images/paginator.png" alt="" /></div>
	</div>
	<div class="uds-billboard-use-timthumb">
		<label for="uds-billboard-use-timthumb">Use Automatic Image Resizing</label>
		<input type="checkbox" id="uds-billboard-use-timthumb" name="<?php echo UDS_BILLBOARD_OPTION_USE_TIMTHUMB ?>" <?php echo get_option(UDS_BILLBOARD_OPTION_USE_TIMTHUMB) == 'on' ? 'checked="checked"' : '' ?> />
		<span class="tooltip">?</span>
		<div class="tooltip-content">When checked, all your images will be resized and zoomed/stretched to fit the Billboard size</div>
	</div>
	<div class="uds-billboard-timthumb-zoom">
		<label for="uds-billboard-timthumb-zoom">Zoom Disproportionate Images</label>
		<input type="checkbox" id="uds-billboard-timthumb-zoom" name="<?php echo UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM ?>" <?php echo get_option(UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM) == 'on' ? 'checked="checked"' : '' ?> />
		<span class="tooltip">?</span>
		<div class="tooltip-content">When checked will crop images that don't have the same proportions as Billboard. Otherwise will stretch images to fit the Billboard</div>
	</div>
	<div class="uds-billboard-timthumb-quality">
		<label for="uds-billboard-timthumb-quality">Image Quality (0-100)</label>
		<input type="text" id="uds-billboard-timthumb-quality" name="<?php echo UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY ?>" value="<?php echo get_option(UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY) ?>" />
		<span class="unit">%</span>
		<span class="tooltip">?</span>
		<div class="tooltip-content">Image compression - use lower values for faster page loads and lower traffic, use high values to increase image quality. Optimal values are 60-80 </div>
	</div>
	<?php
}

function get_uds_billboard()
{
	$bb = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	if(empty($bb)) return "";
	
	$out = '
		<div id="uds-billboard-wrapper">
			<div id="uds-loader"><div id="uds-progress"></div></div>
			<div id="uds-next-slide"></div>
			<div id="uds-billboard">';
				foreach($bb as $b):
					if($b->image != ''):
						if(get_option(UDS_BILLBOARD_OPTION_USE_TIMTHUMB, '') == 'on'){
							$width = (int)get_option(UDS_BILLBOARD_OPTION_WIDTH, 940);
							$height = (int)get_option(UDS_BILLBOARD_OPTION_HEIGHT, 380);
							$zoom = get_option(UDS_BILLBOARD_OPTION_TIMTHUMB_ZOOM, 'on') == 'on' ? 1 : 0;
							$quality = (int)get_option(UDS_BILLBOARD_OPTION_TIMTHUMB_QUALITY, 75);
							$url = UDS_BILLBOARD_URL . "/timthumb.php?src=" . urlencode($b->image) . "&amp;w=$width&amp;h=$height&amp;zc=$zoom&amp;q=$quality";
						} else {
							$url = $b->image;
						}
						$out .= '
						<div class="uds-slide">
							<input type="hidden" class="uds-billboard-option" name="uds-billboard-delay" value="'. $b->delay .'" />
							<input type="hidden" class="uds-billboard-option" name="uds-billboard-transition" value="'. $b->transition .'" />
							<input type="hidden" class="uds-billboard-option" name="uds-billboard-layout" value="'. $b->layout .'" />
							<img src="' . $url . '" alt="" />
							<div class="uds-descr-wrapper">
								<div class="uds-descr">';
									if(stripslashes($b->title) != ''):
										$out .= '<h2>'. stripslashes($b->title) .'</h2>';
									endif;
									$out .= stripslashes($b->text);
									if(stripslashes($b->link) != ''):
										$out .= '<br /><a href="'. urlencode(stripslashes($b->link)) .'" class="read-more">Read more</a>';
									endif;
									$out .= '
								</div>
							</div>
						</div>';
					endif;
				endforeach;
			$out .= '
			</div>
			<div id="uds-billboard-controls"></div>
		</div>';
	
	return $out;
}

function the_uds_billboard()
{
	echo get_uds_billboard();
}

add_shortcode('uds-billboard', 'uds_billboard_shortcode');
function uds_billboard_shortcode($atts, $content = null)
{	
	return get_uds_billboard();
}

?>