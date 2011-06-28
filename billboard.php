<?php
/*
Plugin Name: uBillboard
Plugin URI: http://code.udesignstudios.net/plugins/uBillboard
Description: uBillboard is a slider plugin by uDesignStudios that allows you to create an eye-catching presentation for your web. (Admin menu icon: http://p.yusukekamiyamane.com/)
Version: 3.0.0
Author: uDesign
Author URI: http://udesignstudios.net
Tags: billboard, slider, jquery, javascript, effects, udesign
*/

// General Options
define('UDS_BILLBOARD_VERSION', '3.0.0');
define('UDS_BILLBOARD_USE_COMPRESSION', false);
define('UDS_BILLBOARD_USE_RELATIVE_PATH', false);

// WARNING!!!
// set this to true only if you are calling uBillboard via shortcodes only!!!
define('UDS_BILLBOARD_ENABLE_SHORTCODE_OPTIMIZATION', false); 

if(uds_billboard_is_plugin()) {
	define('UDS_BILLBOARD_URL', plugin_dir_url(__FILE__));
	define('UDS_BILLBOARD_PATH', plugin_dir_path(__FILE__));
} else {
	define('UDS_BILLBOARD_URL', trailingslashit(get_template_directory_uri() . '/uBillboard'));
	define('UDS_BILLBOARD_PATH', trailingslashit(get_template_directory() . '/uBillboard'));
}

// User configurable options
define('UDS_BILLBOARD_OPTION', 'uds-billboard');

add_option(UDS_BILLBOARD_OPTION, array());

require_once 'lib/uBillboard.class.php';
require_once 'lib/uBillboardSlide.class.php';

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
		'tooltip' => 'Square dimension, applies only to transitions based on squares <img src="'.UDS_BILLBOARD_URL .'images/square_size.png" alt="" />',
		'default' => 100
	),
	'column-width' => array(
		'type' => 'text',
		'label' => 'Column Width',
		'unit' => 'pixels',
		'tooltip' => 'Column width, applies only to column-based transitions <img src="'.UDS_BILLBOARD_URL .'images/column_width.png" alt="" />',
		'default' => 50
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
	'show-controls' => array(
		'type' => 'checkbox',
		'label' => 'Show Controls',
		'unit' => '',
		'tooltip' => 'Controls enable you to switch between slides',
		'default' => ''
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
		'default' => ''
	),
	'show-pause' => array(
		'type' => 'checkbox',
		'label' => 'Show Play/Pause',
		'unit' => '',
		'tooltip' => 'Unchecked will pause on hover, otherwise will show Play/Pause button <img src="'.UDS_BILLBOARD_URL .'images/show-playpause.png" alt="" />',
		'default' => ''
	),
	'show-paginator' => array(
		'type' => 'checkbox',
		'label' => 'Show Paginator',
		'unit' => '',
		'tooltip' => 'Check to show paginator in the bottom right corner <img src="'.UDS_BILLBOARD_URL .'images/paginator.png" alt="" />',
		'default' => 'on'
	),
	'paginator-position' => array(
		'type' => 'select',
		'label' => 'Position',
		'unit' => '',
		'tooltip' => '',
		'options' => array(
			'inside' => 'Inside',
			'outside' => 'Outside',
			'thumbs' => 'Thumbs'
		),
		'default' => ''
	),
	'use-timthumb' => array(
		'type' => 'checkbox',
		'label' => 'Enable',
		'unit' => '',
		'tooltip' => 'When checked, all your images will be resized and zoomed/stretched to fit the Billboard size',
		'default' => ''
	),
	'timthumb-zoom' => array(
		'type' => 'checkbox',
		'label' => 'Crop if doesn\'t fit',
		'unit' => '',
		'tooltip' => 'When checked will crop images that don\'t have the same proportions as Billboard. Otherwise will stretch images to fit the Billboard',
		'default' => ''
	),
	'timthumb-quality' => array(
		'type' => 'text',
		'label' => 'Image Quality',
		'unit' => 'px',
		'tooltip' => 'Image compression - use lower values for faster page loads and lower traffic, use high values to increase image quality. Optimal values are 60-80',
		'default' => 80
	)
);

// define data structure for billboard
$uds_billboard_attributes = array(
	'image'=> array(
		'type' => 'image',
		'label' => 'Image'
	),
	'link' => array(
		'type' => 'text',
		'label' => 'Link URL'
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
			'stripe-bottom' => 'Stripe Bottom'
		),
		'default' => 'none'
	),
	'transition' => array(
		'type' => 'select',
		'label' => 'Transition',
		'options' => array(
			'random' => 'Random',
			'fade' => 'Fade',
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
	'text' => array(
		'type' => 'textarea',
		'label' => 'Text'
	)
);

global $uds_billboard_errors;

if(!function_exists('d')) {
	function d($var) {
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
}

// returns true if used as a standalone plugin, false when it's used as part of a theme
function uds_billboard_is_plugin()
{
	$plugins = get_option('active_plugins', array());
	return in_array('uBillboard/billboard.php', $plugins);
}

function uds_billboard_cache_is_writable()
{
	if(uds_billboard_is_plugin()) {
		return is_writable(UDS_BILLBOARD_PATH . 'cache');
	} else {
		return is_writable(get_template_directory() . '/cache');
	}
}

function uds_billboard_is_active()
{
	if(true == UDS_BILLBOARD_ENABLE_SHORTCODE_OPTIMIZATION) {
		if(function_exists('uds_active_shortcodes')) {
			$active_shortcodes = uds_active_shortcodes();
			if( ! in_array('uds-billboard', $active_shortcodes)) {
				return false;
			}
		}
	}
	
	return true;
}

// initialize billboard
add_action('init', 'uds_billboard_init');
function uds_billboard_init()
{
	global $uds_billboard_general_options, $uds_billboard_attributes;
	
	// Basic init
	$dir = UDS_BILLBOARD_URL;
	if(is_admin()){
		add_thickbox();
		
		// process updates
		if(!empty($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'uds-billboard')){
			die('Security check failed');
		} else {
			uds_billboard_proces_updates();
		}
		
		// process imports/exports
		if(isset($_POST['uds-billboard']) && isset($_GET['page']) && $_GET['page'] == 'uds_billboard_import_export') {
			if(isset($_GET['download_export']) && wp_verify_nonce($_GET['download_export'], 'uds-billboard-export')) {
				uds_billboard_export();
			}
			if(is_uploaded_file($_FILES['uds-billboard-import']['tmp_name'])) {
				uds_billboard_import($_FILES['uds-billboard-import']['tmp_name']);
			}
		}
	}
}

add_action('wp_print_scripts', 'uds_billboard_scripts');
function uds_billboard_scripts()
{
	global $wp_version;
	if(!uds_billboard_is_active()) return;
	
	$dir = UDS_BILLBOARD_URL;
	
	// We need to override jQuery on WP < 3.0 because the default there is jQuery 1.3 and we need 1.4
	if(version_compare($wp_version, '3.0.0', '<=')){
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js');
	}
	
	wp_enqueue_script("easing", $dir."js/jquery.easing.js", array('jquery'));
	if(UDS_BILLBOARD_USE_COMPRESSION){
		wp_enqueue_script("uds-billboard", $dir."js/billboard.min.js", array('jquery', 'easing'));
	} else {
		wp_enqueue_script("uds-billboard", $dir."js/billboard.js", array('jquery', 'easing'));
	}
}

add_action('wp_print_styles', 'uds_billboard_styles');
function uds_billboard_styles()
{
	if(!uds_billboard_is_active()) return;
	
	$dir = UDS_BILLBOARD_URL;
	wp_enqueue_style('uds-billboard', $dir.'css/billboard.css', false, false, 'screen');
}

////////////////////////////////////////////////////////////////////////////////
//
//	Plugin update
//
////////////////////////////////////////////////////////////////////////////////

//add_filter('plugins_api_result', 'uds_billboard_updater');
//function uds_billboard_updater($res, $action, $args)
//{
//	d($res);
//	d($action);
//	d($args);
//}

////////////////////////////////////////////////////////////////////////////////
//
//	Activation hooks
//
////////////////////////////////////////////////////////////////////////////////

if(uds_billboard_is_plugin()) {
	register_activation_hook(__FILE__, 'uds_billboard_activation_hook');
	register_activation_hook(__FILE__, 'uds_billboard_deactivation_hook');
}

function uds_billboard_activation_hook()
{
	add_option(UDS_BILLBOARD_OPTION, array());
}

function uds_billboard_deactivation_hook()
{
	//delete_option(UDS_BILLBOARD_OPTION);
}

////////////////////////////////////////////////////////////////////////////////
//
//	Admin menus
//
////////////////////////////////////////////////////////////////////////////////

add_action('admin_menu', 'uds_billboard_menu');
function uds_billboard_menu()
{
	global $menu;
	$position = 61;
	if(!empty($menu[$position])) $position = null;
	
	$icon = UDS_BILLBOARD_URL . 'images/menu-icon.png';
	$ubillboard = add_menu_page("uBillboard", "uBillboard", 'manage_options', 'uds_billboard_admin', 'uds_billboard_admin', $icon, $position);
	$ubillboard_add = add_submenu_page('uds_billboard_admin', "Add Billboard", 'Add Billboard', 'manage_options', 'uds_billboard_add', 'uds_billboard_add');
	$ubillboard_importexport = add_submenu_page('uds_billboard_admin', "Import/Export", 'Import/Export', 'manage_options', 'uds_billboard_import_export', 'uds_billboard_import_export');
	
	add_action("admin_print_styles-$ubillboard", 'uds_billboard_enqueue_admin_styles');
	add_action("admin_print_styles-$ubillboard_add", 'uds_billboard_enqueue_admin_styles');
	add_action("admin_print_styles-$ubillboard_importexport", 'uds_billboard_enqueue_admin_styles');
	
	add_action("admin_print_scripts-$ubillboard", 'uds_billboard_enqueue_admin_scripts');
	add_action("admin_print_scripts-$ubillboard_add", 'uds_billboard_enqueue_admin_scripts');
	add_action("admin_print_scripts-$ubillboard_importexport", 'uds_billboard_enqueue_admin_scripts');
}

// Admin menu entry handling
function uds_billboard_admin()
{
	//include 'billboard-admin.php';
	include 'admin/billboard-list.php';
}

// Admin menu entry handling
function uds_billboard_add()
{
	//include 'billboard-add.php';
	include 'admin/billboard-edit.php';
}

// Admin menu entry handling
function uds_billboard_import_export()
{
	global $uds_billboard_errors;
	include 'billboard-import-export.php';
}

function uds_billboard_enqueue_admin_styles()
{
	$dir = UDS_BILLBOARD_URL;
	wp_enqueue_style('uds-billboard', $dir.'css/billboard-admin.css', false, false, 'screen');
}

function uds_billboard_enqueue_admin_scripts()
{
	$dir = UDS_BILLBOARD_URL;
	wp_enqueue_script("jquery-ui-sortable");
	wp_enqueue_script("jquery-ui-draggable");
	wp_enqueue_script('uds-cookie', $dir."js/jquery_cookie.js");
	wp_enqueue_script('uds-billboard', $dir."js/billboard-admin.js");
}

////////////////////////////////////////////////////////////////////////////////
//
//	Importer and Exporter
//
////////////////////////////////////////////////////////////////////////////////

function uds_billboard_import($file)
{
	global $uds_billboard_errors, $uds_billboard_attributes;
	$import = @file_get_contents($file);
	
	if(empty($import)) {
		$uds_billboard_errors[] = 'Import file is empty';
		return;
	}
	
	$import = maybe_unserialize($import);
	
	if(empty($import['data'])) {
		$uds_billboard_errors[] = 'Import file is corrupted';
		return;
	}
	
	$billboards = $import['data'];
	
	if($_POST['import-attachments'] == 'on') {
		foreach($billboards as $bbname => $billboard) {
			foreach($billboard['slides'] as $key => $slide) {
				$urlinfo = parse_url($slide['image']);
				$localurl = parse_url(get_option('siteurl'));
				//if($urlinfo['hostname'] == $localurl['hostname']) continue;
				
				//echo "Downloading attachment";
				$image = @file_get_contents($slide['image']);
				if(!empty($image)) {
					$uploads = wp_upload_dir();
					if(false === $uploads['error']) {
						$filename = pathinfo($urlinfo['path']);
						$path = trailingslashit($uploads['path']) . wp_unique_filename($uploads['path'], $filename['basename']);
						if(! (false === @file_put_contents($path, $image)) ) {
							$filename = pathinfo($path);
							$billboards[$bbname]['slides'][$key]['image'] = $uploads['url'] . '/' . $filename['basename'];
							
							$wp_filetype = wp_check_filetype(basename($path), null );
							$attachment = array(
								'post_mime_type' => $wp_filetype['type'],
								'post_title' => preg_replace('/\.[^.]+$/', '', basename($path)),
								'post_content' => '',
								'post_status' => 'inherit'
							);
							$attach_id = wp_insert_attachment( $attachment, $path );
							// you must first include the image.php file
							// for the function wp_generate_attachment_metadata() to work
							require_once(ABSPATH . "wp-admin" . '/includes/image.php');
							$attach_data = wp_generate_attachment_metadata( $attach_id, $path );
							wp_update_attachment_metadata( $attach_id,  $attach_data );
							//echo "Attachment saved in ".$billboards[$bbname]['slides'][$key]->image;
						} else {
							$uds_billboard_errors[] = "Failed to save image to ".$path;
							break;
						}
					} else {
						$uds_billboard_errors[] = "Uploads dir is not writable";
						break;
					}
				} else {
					$uds_billboard_errors[] = "Failed to download image";
					break;
				}
			}
			
			if(!empty($uds_billboards_errors)) break;
		}
	}
	
	update_option(UDS_BILLBOARD_OPTION, $billboards);
	
	if(empty($uds_billboards_errors))
		wp_redirect('admin.php?page=uds_billboard_admin');
}

function uds_billboard_export()
{
	$export = array();
	$export['version'] = UDS_BILLBOARD_VERSION;
	$export['data'] = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));
	
	$export = serialize($export);
	
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="uBillboard.txt"');
	die($export);
}

////////////////////////////////////////////////////////////////////////////////
//
//	Slide Add/Update logic
//
////////////////////////////////////////////////////////////////////////////////

// check for POST data and update billboard accordingly
function uds_billboard_proces_updates()
{
	global $uds_billboard_attributes, $uds_billboard_general_options;

	$post = isset($_POST['uds_billboard']) ? $_POST['uds_billboard'] : array();
	
	if(empty($post) || !is_admin()) return;
	
	$billboard = new uBillboard($post);
	
	if($billboard->isValid()){
	
		$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));	
		$billboards[$billboard->name] = $billboard;
	
		update_option(UDS_BILLBOARD_OPTION, maybe_serialize($billboards));
	}
	
	wp_redirect('admin.php?page=uds_billboard_add&uds-billboard-edit='.$billboard->name);
}

// Initialize empty billboard instance
function uds_billboard_default_billboard()
{
	global $uds_billboard_attributes;

	$attribs = $uds_billboard_attributes;

	$bb = array();
	foreach($attribs as $att => $options){
		if(isset($options['default'])){
			$bb[$att] = $options['default'];
		} else {
			$bb[$att] = '';
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
function uds_billboard_render_field($item, $attrib, $unique_key)
{
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
	echo '<input type="text" name="uds_billboard['. $attrib .'][]" value="' . htmlspecialchars(stripslashes($item->{$attrib})) . '" id="billboard-'. $attrib .'-'. $unique_id .'" class="billboard-'. $attrib .'" />';
	echo '</div>';
}

// Render textarea
function uds_billboard_render_textarea($item, $attrib, $unique_id)
{
	global $uds_billboard_attributes;
	$attrib_full = $uds_billboard_attributes[$attrib];
	echo '<div class="'. $attrib .'-wrapper">';
	echo '<label for="billboard-'. $attrib .'-'. $unique_id .'">'. $attrib_full['label'] .'</label>';
	echo '<textarea name="uds_billboard['. $attrib .'][]" class="billboard-'. $attrib .'">'. htmlspecialchars(stripslashes($item->{$attrib})) .'</textarea>';
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
	echo '<option disabled="disabled">'. $attrib_full['label'] .'</option>';
	if(is_array($attrib_full['options'])){
		foreach($attrib_full['options'] as $key => $option){
			$selected = '';
			if($item->{$attrib} == $key){
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
	static $unique_id = 0;
	
	echo '<div class="'. $attrib .'-url-wrapper">';
	echo '	<label for="billboard-'. $attrib .'-'. $unique_id .'-hidden">Image URL</label>';
	echo '	<input type="text" name="uds_billboard['. $attrib .'][]" value="'. $item->{$attrib} .'" id="billboard-'. $attrib .'-'. $unique_id .'-hidden" />';
	echo '	<a class="thickbox" title="Add an Image" href="media-upload.php?type=image&TB_iframe=true&width=640&height=345">';
	echo '		<img alt="Add an Image" src="'. admin_url() . 'images/media-button-image.gif" id="billboard-'. $attrib .'-'. $unique_id .'" class="billboard-'. $attrib .'" />';
	echo '	</a>';
	echo '</div>';
	
	$unique_id++;
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
		//jQuery('#'+window.receiver).attr('src', src);
		jQuery("#"+window.receiver_hidden).val(src).change();
	}
	jQuery('<?php echo $selector; ?>').click(function(){
		set_receiver(this);
	});
	</script>
	<?php
}

// Functions to render single fields in general options for each billboard
function uds_billboard_render_general_text($option, $field, $value)
{
	?>
	<div class="uds-billboard-<?php echo $option ?> option-container">
		<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
		<input type="text" id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" value="<?php echo empty($value) ? $field['default'] : $value ?>" class="text" />
		<span class="unit"><?php echo $field['unit'] ?></span>
		<span class="tooltip">?</span>
		<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
		<div class="clear"></div>
	</div>
	<?php
}

function uds_billboard_render_general_checkbox($option, $field, $value)
{
	$checked = ( $value === null ? $field['default'] : $value ) == 'on' ? 'checked="checked"' : '';
	?>
	<div class="uds-billboard-<?php echo $option ?> option-container">
		<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
		<input type="checkbox" id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" <?php echo $checked ?> class="checkbox" />
		<span class="unit"><?php echo $field['unit'] ?></span>
		<span class="tooltip">?</span>
		<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
		<div class="clear"></div>
	</div>
	<?php
}

function uds_billboard_render_general_select($option, $field, $value)
{
	$checked = ( $value === null ? $field['default'] : $value ) == 'on' ? 'checked="checked"' : '';
	?>
	<div class="uds-billboard-<?php echo $option ?> option-container select">
		<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
		<select id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" class="select">
			<option value="" disabled="disabled"><?php echo $field['label'] ?></option>
			<?php foreach($field['options'] as $key => $label): ?>
				<option value="<?php echo $key ?>" <?php echo $field['options'][$key] == $value ? 'selected="selected"' : '' ?>><?php echo $label ?></option>
			<?php endforeach; ?>
		</select>
		<span class="unit"><?php echo $field['unit'] ?></span>
		<span class="tooltip">?</span>
		<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
		<div class="clear"></div>
	</div>
	<?php
}

////////////////////////////////////////////////////////////////////////////////
//
//	Frontend rendering functions
//
////////////////////////////////////////////////////////////////////////////////

add_action('wp_print_scripts', 'uds_billboard_options_javascript');
function uds_billboard_options_javascript()
{
    ?>
    <script type="text/javascript">
        var uds_billboard_url = '<?php echo UDS_BILLBOARD_URL ?>';
    </script>
    <?php
}

function get_uds_billboard($name = 'billboard', $options = array())
{
	$bbs = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	if(!isset($bbs[$name])) {
		return "Billboard named &quot;$name&quot; does not exist";
	}
	
	$bb = $bbs[$name];
	
	if(!$bb->isValid()) {
		return "Billboard is invalid";
	}
	
	$out = "<div class='uds-bb'>";
		$out .= "<div class='uds-bb-slides'>";
			foreach($bb->slides as $slide) {
				$out .= "<div class='uds-bb-slide'>";
					$out .= "<img src='{$slide->image}' alt='' class='uds-bb-bg-image' />";
					$out .= $slide->text;
				$out .= "</div>";
			}
		$out .= "</div>";
		$out .= "<div class='uds-bb-controls'>";
			
		$out .= "</div>";
	$out .= "</div>";
	
	return $out;
}

function the_uds_billboard($name = 'billboard')
{
	echo get_uds_billboard($name);
}

add_shortcode('uds-billboard', 'uds_billboard_shortcode');
function uds_billboard_shortcode($atts, $content = null)
{	
	extract(shortcode_atts(array(
		'name' => 'billboard'
	), $atts));
	return get_uds_billboard($name);
}

?>