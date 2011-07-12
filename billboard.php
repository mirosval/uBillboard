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
define('UDS_BILLBOARD_OPTION', 'uds-billboard-3');

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
	'controls-skin' => array(
		'type' => 'select',
		'label' => 'Skin',
		'unit' => '',
		'tooltip' => 'How the controls should look',
		'options' => array(
			'mini' => 'Minimal Style Controls',
			'oldskool' => 'Old School uBillboard'
		),
		'default' => 'mini'
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
		'type' => 'select',
		'label' => 'Show Play/Pause',
		'unit' => '',
		'tooltip' => 'Display show pause button',
		'options' => array(
			'no' => 'Don\'t show Play/Pause',
			'hover' => 'Show on Mouse Hover',
			'yes' => 'Show at all times'
		),
		'default' => ''
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
		'default' => 'on'
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
		'default' => ''
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
		'default' => 'no'
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
		'label' => 'Image',
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
	
	$dir = end(explode(DIRECTORY_SEPARATOR, dirname(__FILE__)));
	return in_array($dir . DIRECTORY_SEPARATOR . basename(__FILE__), $plugins);
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

add_action('admin_init', 'uds_billboard_editor_admin_init');
add_action('admin_head', 'uds_billboard_editor_admin_head');

function uds_billboard_editor_admin_init() {
  wp_enqueue_script('word-count');
  wp_enqueue_script('post');
  wp_enqueue_script('editor');
  wp_enqueue_script('media-upload');
}

function uds_billboard_editor_admin_head() {
  wp_tiny_mce();
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
		wp_enqueue_script("jquery-ui-tabs");
		
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
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js');
	}
	
	wp_enqueue_script("easing", $dir."js/jquery.easing.js", array('jquery'), '1.3', true);
	if(UDS_BILLBOARD_USE_COMPRESSION){
		wp_enqueue_script("uds-billboard", $dir."js/billboard.min.js", array('jquery', 'easing'), '3.0', true);
	} else {
		wp_enqueue_script("uds-billboard", $dir."js/billboard.js", array('jquery', 'easing'), '3.0', true);
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
	$position = null;
	
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

	$billboard = new uBillboard();
	$billboard->update($post);

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
				<option value="<?php echo $key ?>" <?php echo $key == $value ? 'selected="selected"' : '' ?>><?php echo $label ?></option>
			<?php endforeach; ?>
		</select>
		<span class="unit"><?php echo $field['unit'] ?></span>
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

add_action('wp_footer', 'uds_billboard_footer_scripts');
function uds_billboard_footer_scripts()
{
	global $uds_billboard_footer_scripts;
	
	echo "
	<script language='JavaScript' type='text/javascript'>
		jQuery(document).ready(function($){
			$uds_billboard_footer_scripts
		});
	</script>";
}

function get_uds_billboard($name = 'billboard', $options = array())
{
	global $uds_billboard_footer_scripts;
	static $id = 0;
	
	$bbs = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	if(!isset($bbs[$name])) {
		return "Billboard named &quot;$name&quot; does not exist";
	}
	
	$bb = $bbs[$name];
	
	if(!$bb->isValid()) {
		return "Billboard is invalid";
	}
	
	$out = $bb->render($id);
	
	$uds_billboard_footer_scripts .= $bb->renderJS($id);
	
	$id++;
	
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

add_shortcode('uds-description', 'uds_billboard_description');
function uds_billboard_description($atts, $content = null)
{
	extract(shortcode_atts(array(
		'top' => '20px',
		'left' => '20px',
		'width' => '200px',
		'height' => '80%',
		'bg' => 'white'
	), $atts));

	$out = "<div class='uds-bb-description' style='top:$top;left:$left;width:$width;height:$height;background:$bg'>$content</div>";
	
	return $out;
}

add_shortcode('uds-embed', 'uds_billboard_embed');
function uds_billboard_embed($atts, $content = null)
{
	global $uds_bb_params;
	extract(shortcode_atts(array(
		'url' => ''
	), $atts));
	
	if(empty($url)) return __('URL Must not be empty');
	
	$width = (int)$uds_bb_params['width'];
	$height = (int)$uds_bb_params['height'];
	
	$url = 'http://api.embed.ly/1/oembed?url='.urlencode($url)."&maxwidth=$width&maxheight=$height&format=json";

	$response = @file_get_contents($url);
	
	if(empty($response)) {
		return __('There was an error when loading the video');
	}
	
	$response = json_decode($response);
	
	return apply_filters('uds_shortcode_out_filter', $response->html);
}

?>