<?php

function uds_billboard_oembed($url, $width, $height)
{
	$services = array(
		'youtube.com' => 'http://www.youtube.com/oembed?',
		'vimeo.com' => 'http://www.vimeo.com/api/oembed.json?'
	);
	
	foreach($services as $pattern => $endpoint) {
		if(strpos($url, $pattern) !== false) {
			$url = $endpoint . 'url='.urlencode($url)."&maxwidth=$width&maxheight=$height&format=json";
		}	
	}	

	$response = @file_get_contents($url);
	
	if(empty($response)) {
		$text .= __('There was an error when loading the video', uds_billboard_textdomain);
		break;
	}
	
	$response = json_decode($response);

	return $response;
}

?>