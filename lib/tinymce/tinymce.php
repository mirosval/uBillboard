<?php

global $uds_billboard_tinymce_extensions;

$uds_billboard_tinymce_extensions = array(
	array(
		'row' => 3,
		'dir' => 'uds-description'
	),
	array(
		'row' => 3,
		'dir' => 'uds-embed'
	)
);

class uds_billboard_tinymce_extensions {
	
	var $plugin_name = "udsExtensions";
	
	function uds_billboard_tinymce_extensions()  {
		// Modify the version when tinyMCE plugins are changed.
		add_filter('tiny_mce_version', array (&$this, 'change_tinymce_version') );
		
		// init process for button control
		add_action('init', array (&$this, 'add_button') );
	}

	function add_button() {
		// Don't bother doing this stuff if the current user lacks permissions
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;
		
		// Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
		 
			// add the button for wp2.5 in a new way
			add_filter("mce_external_plugins", array (&$this, "add_tinymce_plugin" ), 5);
			add_filter('mce_buttons', array (&$this, 'register_button' ), 5);
		}
	}
	
	// used to insert button in wordpress 2.5x editor
	function register_button($buttons) {
		global $uds_billboard_tinymce_extensions;
		
		foreach($uds_billboard_tinymce_extensions as $ext) {
			if($ext['row'] == 3 && $ext['dir'] == 'divider'){
				$buttons[] = '|';
				continue;
			}
			if($ext['row'] == 3) {
				$buttons[] = $this->dash_to_camel($ext['dir']);
			}
		}
		
		return $buttons;
	}		
	
	// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	function add_tinymce_plugin($plugin_array) {   
		global $uds_billboard_tinymce_extensions;
		
		foreach($uds_billboard_tinymce_extensions as $ext) {
			if($ext['dir'] == 'divider') continue;
			$plugin_array[$this->dash_to_camel($ext['dir'])] =  UDS_BILLBOARD_URL.'lib/tinymce/'.$ext['dir'].'/editor_plugin.js';
		}
		
		return $plugin_array;
	}
	
	function change_tinymce_version($version) {
		return ++$version;
	}
	
	function dash_to_camel($name) {
		$elements = explode('-', $name);
		return array_shift($elements) . implode('', array_map('ucfirst', $elements));
	}
	
}

// Call it now
new uds_billboard_tinymce_extensions();

?>