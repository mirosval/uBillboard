<?php

if(! defined('ALLOW_EXTERNAL') ) 		define ('ALLOW_EXTERNAL', true);			// Allow image fetching from external websites. Will check against ALLOWED_SITES if ALLOW_ALL_EXTERNAL_SITES is false
if(! defined('ALLOW_ALL_EXTERNAL_SITES') ) 	define ('ALLOW_ALL_EXTERNAL_SITES', false);		// Less secure. 
if(! defined('FILE_CACHE_DIRECTORY') ) 		define ('FILE_CACHE_DIRECTORY', '../cache');		// Directory where images are cached. Left blank it will use the system temporary directory (which is better for security)

if(! isset($ALLOWED_SITES)){
	$ALLOWED_SITES = array (
			'flickr.com',
			'picasa.com',
			'img.youtube.com',
			'upload.wikimedia.org',
			'photobucket.com',
			'imgur.com',
			'imageshack.us',
			'tinypic.com'
	);
}

?>