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

require_once 'lib/uBillboard.class.php';
require_once 'lib/uBillboardSlide.class.php';
require_once 'lib/tinymce/tinymce.php';
require_once 'lib/shortcodes.php';

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

// initialize billboard
add_action('admin_init', 'uds_billboard_init');
function uds_billboard_init()
{
	global $uds_billboard_general_options, $uds_billboard_attributes;
	
	// Register settings
	register_setting('uds_billboard_general_options', UDS_BILLBOARD_OPTION_GENERAL, 'uds_billboard_general_validate');
	
	// Basic init
	$dir = UDS_BILLBOARD_URL;
	//
	add_thickbox();
	
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

register_activation_hook(__FILE__, 'uds_billboard_activation_hook');
register_deactivation_hook(__FILE__, 'uds_billboard_deactivation_hook');
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

function uds_billboard_deactivation_hook()
{
	//delete_option(UDS_BILLBOARD_OPTION);
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
	$ubillboard_add = add_submenu_page('uds_billboard_admin', "Add Billboard", 'Add Billboard', 'edit_pages', 'uds_billboard_edit', 'uds_billboard_edit');
	$ubillboard_general = add_submenu_page('uds_billboard_admin', "General Options", 'General Options', 'manage_options', 'uds_billboard_general', 'uds_billboard_general');
	$ubillboard_importexport = add_submenu_page('uds_billboard_admin', "Import/Export", 'Import/Export', 'import', 'uds_billboard_import_export', 'uds_billboard_import_export');
	
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
		wp_die(__('You do not have sufficient permissions to access this page'));
	}
	
	include 'admin/billboard-list.php';
}

// Admin menu entry handling
function uds_billboard_edit()
{
	if(!current_user_can('edit_pages')) {
		wp_die(__('You do not have sufficient permissions to access this page'));
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
		wp_die(__('You do not have sufficient permissions to access this page'));
	}

	include 'admin/billboard-general.php';
}

// Admin menu entry handling
function uds_billboard_import_export()
{
	global $uds_billboard_errors;
	
	if(!current_user_can('import')) {
		wp_die(__('You do not have sufficient permissions to access this page'));
	}
	
	include 'admin/billboard-import-export.php';
}

function uds_billboard_enqueue_admin_styles()
{
	$dir = UDS_BILLBOARD_URL;
	wp_enqueue_style('uds-billboard', $dir.'css/billboard-admin.css', false, false, 'screen');
}

function uds_billboard_enqueue_admin_scripts()
{
	$dir = UDS_BILLBOARD_URL;
	
	wp_enqueue_script("jquery-ui-tabs");
	wp_enqueue_script("jquery-ui-sortable");
	wp_enqueue_script("jquery-ui-draggable");
	
	wp_enqueue_script('jquery-cookie', $dir."js/jquery_cookie.js", array('jquery'), UDS_BILLBOARD_VERSION, false);
	wp_enqueue_script('uds-billboard', $dir."js/billboard-admin.js", array('jquery', 'jquery-cookie', 'jquery-ui-tabs'), UDS_BILLBOARD_VERSION, false);
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
	
	$import = new SimpleXMLElement($import);
	
	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));
	
	foreach($import->udsBillboards as $udsBillboard) {
		$billboard = new uBillboard();
		$billboard->importFromXML($udsBillboard->udsBillboard);
		$billboards[$billboard->name] = $billboard;
	}
	
	if(!$billboard->isValid()) {
		$uds_billboard_errors[] = 'Import file is corrupted';
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
			} elseif($name === $what) {
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
		$message = 'uds-message='.urlencode('Billboard updated successfully').'&uds-class='.urlencode('updated fade');
		
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

?>