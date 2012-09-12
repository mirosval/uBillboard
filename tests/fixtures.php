<?php

$uds_bb_fixtures = array();

$uds_bb_fixtures[] = array(
	'name' => 'billboard',
	'width' => 960,
	'height' => 380,
	'squareSize' => 100,
	'columnSidth' => 50,
	'showPaginator' => true,
	'showControls' => true,
	'showPause' => true,
	'autoplay' => true,
	'useTimthumb' => true,
	'timthumbZoom' => true,
	'timthumbQuality' => 100,
	'slides' => array(
		array(
			'image' => UDS_BILLBOARD_TESTS . '/images/1.jpg',
			'description' => '',
			'delay' => 1000,
			'layout' => 'none',
			'transition' => 'fade'
		),
		array(
			'image' => UDS_BILLBOARD_TESTS . '/images/2.jpg',
			'description' => '<h2>Test Description Left<h2><p>Donec id elit non mi porta gravida at eget metus. 
				Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Cras mattis consectetur 
				purus sit amet fermentum. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor 
				auctor.</p> [button link="http://google.com"]123[/button]',
			'delay' => 1000,
			'layout' => 'left',
			'transition' => 'fade'
		),
		array(
			'image' => UDS_BILLBOARD_TESTS . '/images/3.jpg',
			'description' => '<h2>Test Description Right<h2><p>Donec id elit non mi porta gravida at eget metus. 
				Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Cras mattis consectetur 
				purus sit amet fermentum. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor 
				auctor.</p> [button link="http://google.com"]123[/button]',
			'delay' => 1000,
			'layout' => 'right',
			'transition' => 'fade'
		)
	)
);

$uds_bb_posts = array();

$uds_bb_posts[] = array(
	'name' => 'billboard',
	'width' => 960,
	'height' => 380,
	'squareSize' => 100,
	'columnSidth' => 50,
	'showPaginator' => true,
	'showControls' => true,
	'showPause' => true,
	'autoplay' => true,
	'useTimthumb' => true,
	'timthumbZoom' => true,
	'timthumbQuality' => 100,
	'slides' => array(
		'images' => array(
			UDS_BILLBOARD_TESTS . '/images/1.jpg',
			UDS_BILLBOARD_TESTS . '/images/2.jpg',
			UDS_BILLBOARD_TESTS . '/images/3.jpg'
		),
		'descriptions' => array(
			'',
			'<h2>Test Description Left<h2><p>Donec id elit non mi porta gravida at eget metus. 
				Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Cras mattis consectetur 
				purus sit amet fermentum. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor 
				auctor.</p> [button link="http://google.com"]123[/button]',
			'<h2>Test Description Left<h2><p>Donec id elit non mi porta gravida at eget metus. 
				Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Cras mattis consectetur 
				purus sit amet fermentum. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor 
				auctor.</p> [button link="http://google.com"]123[/button]'
		),
		'layouts' => array(
			'none',
			'left',
			'right'
		),
		'delays' => array(
			1000,
			2000,
			3000
		),
		'transitions' => array(
			'fade',
			'fade',
			'fade'
		)
	)
);

$uds_bb_original = 's:2170:"a:1:{s:5:"Mirko";a:13:{s:6:"slides";a:4:{i:0;a:7:{s:5:"image";s:74:"http://code.udesignstudios.net/plugins/wp-content/uploads/2010/09/ubb1.jpg";s:5:"title";s:37:"Best slider plugin out there. Period.";s:4:"link";s:1:"#";s:4:"text";s:461:"Welcome to <b>uBillboard</b> - premium wordpress slider plugin.
<br />
<ul>
<li>Sleek slider front end and admin</li>
<li>23 transition effects + Random</li>
<li>Square, column, fade, slide transitions</li>
<li>Loader that displays real loading progress</li>
<li>Great looking and easy to use admin interface</li>
<li>Per-slide options - layout, transition, text etc. </li>
<li>Support for HTML markup in descriptions</li>
</ul>

And so much more :)";s:5:"delay";s:4:"5000";s:6:"layout";s:11:"stripe-left";s:10:"transition";s:10:"columnWave";}i:1;a:7:{s:5:"image";s:82:"http://code.udesignstudios.net/plugins/wp-content/uploads/2010/09/emo1_940x380.jpg";s:5:"title";s:24:"uBillboard, the Ultimate";s:4:"link";s:0:"";s:4:"text";s:44:"The <b>last billboard</b> you\'ll ever need!";s:5:"delay";s:4:"5000";s:6:"layout";s:17:"stripe-bottom alt";s:10:"transition";s:14:"squaresMoveOut";}i:2;a:7:{s:5:"image";s:74:"http://code.udesignstudios.net/plugins/wp-content/uploads/2010/09/bb21.jpg";s:5:"title";s:19:"Animate as you want";s:4:"link";s:1:"#";s:4:"text";s:183:"With 23 different transitions, uBillboard is simply made for everyone. Combine this with incredibly easy to use interface and you get a perfect slider for your next wordpress project.";s:5:"delay";s:4:"5000";s:6:"layout";s:12:"stripe-right";s:10:"transition";s:12:"curtainRight";}i:3;a:7:{s:5:"image";s:82:"http://code.udesignstudios.net/plugins/wp-content/uploads/2010/09/emo2_940x380.jpg";s:5:"title";s:0:"";s:4:"link";s:0:"";s:4:"text";s:0:"";s:5:"delay";s:4:"5000";s:6:"layout";s:4:"none";s:10:"transition";s:13:"squaresRandom";}}s:4:"name";s:5:"Mirko";s:5:"width";s:3:"940";s:6:"height";s:3:"380";s:11:"square-size";s:3:"100";s:12:"column-width";s:2:"50";s:14:"show-paginator";s:2:"on";s:13:"show-controls";s:2:"on";s:10:"show-pause";s:2:"on";s:8:"autoplay";s:2:"on";s:12:"use-timthumb";s:0:"";s:13:"timthumb-zoom";s:0:"";s:16:"timthumb-quality";s:0:"";}}";';


?>