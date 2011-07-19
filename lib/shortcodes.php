<?php

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
	
	if(empty($url)) return __('URL Must not be empty', uds_billboard_textdomain);
	
	$width = (int)$uds_bb_params['width'];
	$height = (int)$uds_bb_params['height'];
	
	$url = 'http://api.embed.ly/1/oembed?url='.urlencode($url)."&maxwidth=$width&maxheight=$height&format=json";

	$response = @file_get_contents($url);
	
	if(empty($response)) {
		return __('There was an error when loading the video', uds_billboard_textdomain);
	}
	
	$response = json_decode($response);
	
	return apply_filters('uds_shortcode_out_filter', $response->html);
}

?>