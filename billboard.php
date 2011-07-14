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
require 'lib/tinymce/tinymce.php';

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
add_action('admin_notices', 'uds_billboard_admin_notices');

function uds_billboard_editor_admin_init() {
	//wp_enqueue_script('word-count');
	//wp_enqueue_script('post');
	//wp_enqueue_script('editor');
	//wp_enqueue_script('media-upload');
}

function uds_billboard_editor_admin_head() {
	wp_tiny_mce();
}

function uds_billboard_admin_notices() {
	if(!empty($_REQUEST['uds-message'])) {
		$message = $_REQUEST['uds-message'];
		$class = $_REQUEST['uds-class'];
		echo "<div id='message' class='$class'>$message</div>";
	}
}

// initialize billboard
add_action('init', 'uds_billboard_init');
function uds_billboard_init()
{
	global $uds_billboard_general_options, $uds_billboard_attributes;
	
	// Basic init
	$dir = UDS_BILLBOARD_URL;
	if(is_admin()){
		//
		add_thickbox();
		wp_enqueue_script("jquery-ui-tabs");
		
		// process updates
		if(!empty($_POST['uds-billboard']) && !wp_verify_nonce('uds-billboard-update-nonce', $_REQUEST['uds-billboard-update-nonce'])){
			die('Security check failed');
		} else {
			uds_billboard_proces_updates();
		}
		
		// process deletes
		if(!empty($_REQUEST['uds-billboard-delete']) && wp_verify_nonce('uds-billboard-delete-nonce', $_REQUEST['nonce'])) {
			uds_billboard_delete();
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
		$message = 'uds-message='.urlencode('Billboard updated successfully').'&uds-class='.urlencode('updated fade');
	}
	
	wp_safe_redirect(admin_url('admin.php?page=uds_billboard_add&uds-billboard-edit='.$billboard->name.'&'.$message));
	exit();
}

function uds_billboard_delete()
{
	$billboard = $_REQUEST['uds-billboard-delete'];

	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	$message = '';
	if(!isset($billboards[$billboard])) {
		$message = 'uds-message='.urlencode(sprintf('Billboard %s does not exist', esc_html($billboard))).'&uds-class='.urlencode('error');
	} else {
		unset($billboards[$billboard]);
		$message = 'uds-message='.urlencode(sprintf('Billboard %s has been successfully deleted', esc_html($billboard))).'&uds-class='.urlencode('update fade');
	}
	
	wp_safe_redirect(admin_url('admin.php?page=uds_billboard_admin&'.$message));
	exit();
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
		
		window.send_to_editor = function(img){
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
	}
	
	//jQUery(document).ready(function(){
		jQuery('<?php echo $selector; ?>').click(function(){
			set_receiver(this);
		});
	//});
	</script>
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
		'bg' => 'white',
		'skin' => ''
	), $atts));

	if(!empty($skin)) $skin = 'uds-' . $skin;

	$out = "<div class='uds-bb-description $skin' style='top:$top;left:$left;width:$width;height:$height;background-color:$bg'>$content</div>";
	
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