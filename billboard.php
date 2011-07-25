<?php
/*
Plugin Name: uBillboard 3 Beta
Plugin URI: http://code.udesignstudios.net/plugins/uBillboard
Description: <strong>uBillboard 3 Beta is not recommended for use on production servers!</strong> <br /> uBillboard is a slider plugin by uDesignStudios that allows you to create complex and eye-catching presentations for your web.
Version: 3.0.0 Beta
Author: uDesign
Author URI: http://udesignstudios.net
Tags: billboard, slider, jquery, javascript, effects, udesign
*/

// General Options
define('UDS_BILLBOARD_VERSION', '3.0.0');

if(uds_billboard_is_plugin()) {
	define('UDS_BILLBOARD_URL', plugin_dir_url(__FILE__));
	define('UDS_BILLBOARD_PATH', plugin_dir_path(__FILE__));
} else {
	define('UDS_BILLBOARD_URL', trailingslashit(get_template_directory_uri() . '/uBillboard'));
	define('UDS_BILLBOARD_PATH', trailingslashit(get_template_directory() . '/uBillboard'));
}

// User configurable options
define('UDS_BILLBOARD_OPTION', 'uds-billboard-3');
define('UDS_BILLBOARD_OPTION_GENERAL', 'uds-billboard-general-3');

define('uds_billboard_textdomain', 'uBillboard');

require_once 'lib/compat.php';
require_once 'lib/embed.php';
require_once 'lib/classTextile.php';
require_once 'lib/uBillboard.class.php';
require_once 'lib/uBillboardSlide.class.php';
require_once 'lib/tinymce/tinymce.php';
require_once 'lib/shortcodes.php';

global $uds_billboard_errors;

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
	if(uds_billboard_use_shortcode_optimization() && !is_admin()) {
		if(function_exists('uds_active_shortcodes')) {
			$active_shortcodes = uds_active_shortcodes();
			if( ! in_array('uds-billboard', $active_shortcodes)) {
				return false;
			}
		}
	}
	
	return true;
}

add_action('admin_head', 'uds_billboard_editor_admin_head');
add_action('admin_notices', 'uds_billboard_admin_notices');

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

add_action('init', 'uds_billboard_init');
function uds_billboard_init()
{
	load_plugin_textdomain(uds_billboard_textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/');
}

// initialize billboard
add_action('admin_init', 'uds_billboard_admin_init');
function uds_billboard_admin_init()
{
	global $uds_billboard_general_options, $uds_billboard_attributes;
	
	// Register settings
	register_setting('uds_billboard_general_options', UDS_BILLBOARD_OPTION_GENERAL, 'uds_billboard_general_validate');
	
	// Basic init
	$dir = UDS_BILLBOARD_URL;
	
	$nonce = isset($_REQUEST['uds-billboard-update-nonce']) && wp_verify_nonce('uds-billboard-update-nonce', $_REQUEST['uds-billboard-update-nonce']);
	
	// process updates
	if(!empty($_POST['uds-billboard']) && !$nonce && !is_ajax()){
		die('Security check failed');
	} else {
		uds_billboard_process_updates();
	}
	
	// process deletes
	if(!empty($_REQUEST['uds-billboard-delete']) && wp_verify_nonce('uds-billboard-delete-nonce', $_REQUEST['nonce'])) {
		uds_billboard_delete();
	}
	
	// process imports/exports
	if(isset($_GET['page']) && $_GET['page'] == 'uds_billboard_import_export') {
		if(isset($_GET['download_export']) && wp_verify_nonce($_GET['download_export'], 'uds-billboard-export')) {
			if(isset($_GET['uds-billboard-export'])) {
				uds_billboard_export($_GET['uds-billboard-export']);
			} else {
				uds_billboard_export();
			}
		}
		
		if(isset($_FILES['uds-billboard-import']) && is_uploaded_file($_FILES['uds-billboard-import']['tmp_name'])) {
			uds_billboard_import($_FILES['uds-billboard-import']['tmp_name']);
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
		//wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js');
	}
	
	//wp_enqueue_script("easing", $dir."js/jquery.easing.js", array('jquery'), '1.3', true);
	if(uds_billboard_use_compression()){
		wp_enqueue_script("uds-billboard", $dir."js/billboard.min.js", array('jquery'), '3.0', true);
	} else {
		wp_enqueue_script("uds-billboard", $dir."js/billboard.js", array('jquery'), '3.0', true);
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
//	Activation hooks
//
////////////////////////////////////////////////////////////////////////////////

register_activation_hook(__FILE__, 'uds_billboard_activation_hook');
register_uninstall_hook(__FILE__, 'uds_billboard_uninstall_hook');

function uds_billboard_activation_hook()
{
	$option = get_option(UDS_BILLBOARD_OPTION);
	if(!$option) {
		add_option(UDS_BILLBOARD_OPTION, array());
	}
	
	$option = get_option(UDS_BILLBOARD_OPTION_GENERAL);
	if(!$option) {
		add_option(UDS_BILLBOARD_OPTION_GENERAL, array(
			'compression' => true,
			'shortcode_optimization' => false
		));
	}
}

function uds_billboard_uninstall_hook()
{
	delete_option(UDS_BILLBOARD_OPTION);
	delete_option(UDS_BILLBOARD_OPTION_GENERAL);
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
	$ubillboard = add_menu_page("uBillboard", "uBillboard", 'edit_pages', 'uds_billboard_admin', 'uds_billboard_admin', $icon, $position);
	
	$add_title = __("Add Billboard", uds_billboard_textdomain);
	$general_title = __("General Options", uds_billboard_textdomain);
	$import_title = __("Import/Export", uds_billboard_textdomain);
	
	$ubillboard_add = add_submenu_page('uds_billboard_admin', $add_title, $add_title, 'edit_pages', 'uds_billboard_edit', 'uds_billboard_edit');
	$ubillboard_general = add_submenu_page('uds_billboard_admin', $general_title, $general_title, 'manage_options', 'uds_billboard_general', 'uds_billboard_general');
	$ubillboard_importexport = add_submenu_page('uds_billboard_admin', $import_title, $import_title, 'import', 'uds_billboard_import_export', 'uds_billboard_import_export');
	
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
	if(!current_user_can('edit_pages')) {
		wp_die(__('You do not have sufficient permissions to access this page', uds_billboard_textdomain));
	}
	
	include 'admin/billboard-list.php';
}

// Admin menu entry handling
function uds_billboard_edit()
{
	if(!current_user_can('edit_pages')) {
		wp_die(__('You do not have sufficient permissions to access this page', uds_billboard_textdomain));
	}

	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'preview') {
		include 'admin/billboard-preview.php';
	} else {
		include 'admin/billboard-edit.php';
	}
}

// Admin menu entry handling
function uds_billboard_general()
{
	if(!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page', uds_billboard_textdomain));
	}

	include 'admin/billboard-general.php';
}

// Admin menu entry handling
function uds_billboard_import_export()
{
	global $uds_billboard_errors;
	
	if(!current_user_can('import')) {
		wp_die(__('You do not have sufficient permissions to access this page', uds_billboard_textdomain));
	}
	
	include 'admin/billboard-import-export.php';
}

function uds_billboard_enqueue_admin_styles()
{
	$dir = UDS_BILLBOARD_URL;
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	wp_enqueue_style('uds-billboard', $dir.'css/billboard-admin.css', false, false, 'screen');
}

function uds_billboard_enqueue_admin_scripts()
{
	$dir = UDS_BILLBOARD_URL;
	
	wp_enqueue_script("swfupload");
	wp_enqueue_script("jquery-ui-core");
	wp_enqueue_script("jquery-ui-tabs");
	wp_enqueue_script("jquery-ui-dialog");
	wp_enqueue_script("jquery-ui-sortable");
	wp_enqueue_script("jquery-ui-resizable");
	wp_enqueue_script("jquery-ui-draggable");
	wp_enqueue_script("uds-colorpicker", $dir."js/colorpicker/jscolor.js", UDS_BILLBOARD_VERSION, false);
	wp_enqueue_script('jquery-cookie', $dir."js/jquery_cookie.js", array('jquery'), UDS_BILLBOARD_VERSION, false);
	wp_enqueue_script('uds-billboard', $dir."js/billboard-admin.js", array('jquery', 'jquery-cookie', 'jquery-ui-tabs'), UDS_BILLBOARD_VERSION, false);
	
	wp_localize_script('uds-billboard', 'udsAdminL10n', array(
		'billboardDeleteConfirmation' => __('Really delete? This is not undoable', uds_billboard_textdomain),
		'slideDeleteConfirmation' => __('Really delete slide?', uds_billboard_textdomain),
		'addAnImage' => __('Add an Image', uds_billboard_textdomain),
		'slideN' => __('Slide %s', uds_billboard_textdomain),
		'billboardPreview' => __('uBillboard Preview', uds_billboard_textdomain)
	));
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
		$uds_billboard_errors[] = __('Import file is empty', uds_billboard_textdomain);
		return;
	}
	
	try {
		libxml_use_internal_errors(true);
		$import = new SimpleXMLElement($import);
	} catch(Exception $e) {
		$uds_billboard_errors[] = sprintf(__('An error has occurred during XML Parsing: %s', uds_billboard_textdomain), $e->getMessage());
		return;
	}
	
	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));
	
	foreach($import->udsBillboards as $udsBillboard) {
		$billboard = new uBillboard();
		$billboard->importFromXML($udsBillboard->udsBillboard);
		$billboards[$billboard->name] = $billboard;
	}
	
	if(!$billboard->isValid()) {
		$uds_billboard_errors[] = __('Import file is corrupted', uds_billboard_textdomain);
		return;
	}

	if($_POST['import-attachments'] == 'on') {
		foreach($billboards as $bbname => $billboard) {
			foreach($billboard->slides as $slide) {
				$urlinfo = parse_url($slide->image);
				$localurl = parse_url(get_option('siteurl'));
				//if($urlinfo['hostname'] == $localurl['hostname']) continue;
				
				//echo "Downloading attachment";
				$image = @file_get_contents($slide->image);
				if(!empty($image)) {
					$uploads = wp_upload_dir();
					if(false === $uploads['error']) {
						$filename = pathinfo($urlinfo['path']);
						$path = trailingslashit($uploads['path']) . wp_unique_filename($uploads['path'], $filename['basename']);
						if(! (false === @file_put_contents($path, $image)) ) {
							$filename = pathinfo($path);
							$slide->image = $uploads['url'] . '/' . $filename['basename'];
							
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
							$uds_billboard_errors[] = sprintf(__("Failed to save image to %s", uds_billboard_textdomain), $path);
							break;
						}
					} else {
						$uds_billboard_errors[] = __("Uploads dir is not writable", uds_billboard_textdomain);
						break;
					}
				} else {
					$uds_billboard_errors[] = __("Failed to download image", uds_billboard_textdomain);
					break;
				}
			}
			
			if(!empty($uds_billboards_errors)) break;
		}
	}
	
	update_option(UDS_BILLBOARD_OPTION, maybe_serialize($billboards));
	
	if(empty($uds_billboards_errors))
		wp_redirect('admin.php?page=uds_billboard_admin');
}

function uds_billboard_export($what = false)
{
	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	$export = '<?xml version="1.0"?>' . "\n";
	$export .= '<udsBillboardExport>' . "\n";
	$export .= ' <version>'.UDS_BILLBOARD_VERSION.'</version>' . "\n";
	$export .= ' <udsBillboards>' . "\n";
	
	foreach($billboards as $name => $billboard) {
		if($what !== false) {
			if(is_array($what) && !in_array($name, $what)) {
				continue;
			} elseif($name !== $what) {
				continue;
			}
		}
		
		$export .= $billboard->export() . "\n";
	}
	
	$export .= ' </udsBillboards>' . "\n";
	$export .= '</udsBillboardExport>' . "\n";
	
	header('Content-type: text/xml');
	header('Content-Disposition: attachment; filename="uBillboard.xml"');
	die($export);
}

////////////////////////////////////////////////////////////////////////////////
//
//	Slide Add/Update logic
//
////////////////////////////////////////////////////////////////////////////////

add_action('wp_ajax_uds_billboard_update', 'uds_billboard_process_updates');

// check for POST data and update billboard accordingly
function uds_billboard_process_updates()
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
		$message = 'uds-message='.urlencode(__('Billboard updated successfully', uds_billboard_textdomain)).'&uds-class='.urlencode('updated fade');
		
		if(is_ajax()) {
			die('OK');
		}
	}
	
	if(is_ajax()) {
		die('ERROR');
	}
	
	wp_safe_redirect(admin_url('admin.php?page=uds_billboard_edit&uds-billboard-edit='.$billboard->name.'&'.$message));
	exit();
}

function uds_billboard_delete()
{
	$billboard = $_REQUEST['uds-billboard-delete'];

	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	$message = '';
	if(!isset($billboards[$billboard])) {
		$message = 'uds-message='.urlencode(sprintf(__('Billboard %s does not exist', uds_billboard_textdomain), esc_html($billboard))).'&uds-class='.urlencode('error');
	} else {
		unset($billboards[$billboard]);
		$message = 'uds-message='.urlencode(sprintf(__('Billboard %s has been successfully deleted', uds_billboard_textdomain), esc_html($billboard))).'&uds-class='.urlencode('update fade');
	}
	
	wp_safe_redirect(admin_url('admin.php?page=uds_billboard_admin&'.$message));
	exit();
}

add_action('wp_ajax_uds_billboard_list', 'uds_billboard_list');
function uds_billboard_list()
{
	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	foreach($billboards as $name => $billboard) {
		if($name == '_uds_temp_billboard') continue;
		
		echo '<option name="'.$name.'">'.$name.'</option>';
	}
	
	die();
}

add_action('wp_ajax_uds_billboard_list_images', 'uds_billboard_list_images');
function uds_billboard_list_images()
{
	$count_array = wp_count_attachments();

	$count = 0;
	$count += isset($count_array->{'image/jpeg'}) ? $count_array->{'image/jpeg'} : 0;
	$count += isset($count_array->{'image/png'}) ? $count_array->{'image/png'} : 0;
	
	if($count == 0) {
		die('<p>' . __('You have no images in your Media Library', uds_billboard_textdomain) . '</p>');
	}
	
	$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
	$numberposts = 5;
	
	$posts = get_posts(array('post_type' => 'attachment', 'numberposts' => $numberposts, 'offset' => $offset, 'post_status' => null, 'post_parent' => null));
	
	echo '<p>' . __('Click an image to add it to the Slide', uds_billboard_textdomain) . '</p>';
	echo '<div class="uds-images-select">';
	foreach($posts as $post) {
		if(!wp_attachment_is_image($post->ID)) continue;
		$metadata = wp_get_attachment_metadata($post->ID);
		echo '<div class="uds-image-select">';
		echo wp_get_attachment_image($post->ID, 'thumb');
		$image = wp_get_attachment_image_src($post->ID, 'full');
		$full = $image[0];
		echo '<input type="hidden" class="uds-image" value="'.$full.'" />';
		echo '<div class="meta">Filename: '.$metadata['file'].'<br />Size: '.$metadata['width'].'x'.$metadata['height'].'</div>';
		echo '<div class="clear"></div>';
		echo '</div>';
	}
	echo '</div>';
	
	if($count > $numberposts) {
		echo '<div class="uds-paginate">';
		for($i = 0; $i < $count / $numberposts; $i++) {
			echo '<a href="">'.($i+1).'</a>';
		}
		echo '</div>';
	}
	
	die();
}

function uds_billboard_use_compression()
{
	$option = get_option(UDS_BILLBOARD_OPTION_GENERAL);
	return $option['compression'];
}

function uds_billboard_use_shortcode_optimization()
{
	$option = get_option(UDS_BILLBOARD_OPTION_GENERAL);
	return $option['shortcode_optimization'];
}

if(!function_exists('is_ajax')) {
/**
 *	Is Ajax
 *	Simple tag that detects if the current request is an AJAX Call
 *
 *	@return bool True if current page has been requested via AJAX
 */
function is_ajax()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}
}

function uds_billboard_general_validate($input)
{
	$input['compression'] = isset($input['compression']) && in_array($input['compression'], array('', 'on')) ? true : false;
	$input['shortcode_optimization'] = isset($input['shortcode_optimization']) && in_array($input['shortcode_optimization'], array('', 'on')) ? true : false;

	return $input;
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
		//<![CDATA[
		jQuery(document).ready(function($){
			$uds_billboard_footer_scripts
		});
		//]]>
	</script>";
}

function get_uds_billboard($name = 'billboard', $options = array())
{
	global $uds_billboard_footer_scripts;
	static $id = 0;
	
	$bbs = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	if(!isset($bbs[$name])) {
		return sprintf(__("Billboard named &quot;%s&quot; does not exist", uds_billboard_textdomain), $name);
	}
	
	$bb = $bbs[$name];
	
	if(!$bb->isValid()) {
		return __("Billboard is invalid", uds_billboard_textdomain);
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

?>