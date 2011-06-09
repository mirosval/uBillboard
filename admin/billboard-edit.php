<?php 
//d(maybe_unserialize(get_option(UDS_BILLBOARD_OPTION))); 

global $uds_billboard_attributes, $uds_billboard_general_options;

$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));

if(!empty($_GET['uds-billboard-edit']) && !empty($billboards[$_GET['uds-billboard-edit']])) {
	$billboard = $billboards[$_GET['uds-billboard-edit']];
}

// safety check
if(! is_array($billboard)) {
	$billboard = array();
}

if(!empty($billboards)) {
	$name = array_search($billboard, $billboards);
}

$billboard['slides'][] = uds_billboard_default_billboard();

?>
<div class="wrap">
	<h2>Edit uBillboard</h2>
	<form id="billboard_update_form" method="post" action="" class="uds-billboard-form">
		<div class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Options</span></h3>
					<div class="inside">
						<?php uds_billboard_render_general_text('width', $uds_billboard_general_options['width'], $billboard['width']); ?>
						<?php uds_billboard_render_general_text('height', $uds_billboard_general_options['height'], $billboard['height']); ?>
						<?php uds_billboard_render_general_checkbox('randomize', $uds_billboard_general_options['randomize'], $billboard['randomize']); ?>
						<?php uds_billboard_render_general_checkbox('autoplay', $uds_billboard_general_options['autoplay'], $billboard['autoplay']); ?>
						<div id="major-publishing-actions" class="submitbox">
							<div id="publishing-action">
								<input class="button-primary" type="submit" style="float:right" value="Save uBillboard" />
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Controls</span></h3>
					<div class="inside">
						<?php uds_billboard_render_general_select('controls-position', $uds_billboard_general_options['controls-position'], $billboard['controls-position']); ?>
						<?php uds_billboard_render_general_checkbox('show-pause', $uds_billboard_general_options['show-pause'], $billboard['show-pause']); ?>
					</div>
				</div>
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Image Resizing Options</span></h3>
					<div class="inside">
						<?php uds_billboard_render_general_checkbox('use-timthumb', $uds_billboard_general_options['use-timthumb'], $billboard['use-timthumb']); ?>
						<?php uds_billboard_render_general_checkbox('timthumb-zoom', $uds_billboard_general_options['timthumb-zoom'], $billboard['timthumb-zoom']); ?>
						<?php uds_billboard_render_general_text('timthumb-quality', $uds_billboard_general_options['timthumb-quality'], $billboard['timthumb-quality']); ?>
					</div>
				</div>
			</div>
			<div class="editor-wrapper">
				<div class="editor-body">
					<div id="titlediv">
						<div id="titlewrap">
							<input type="text" name="name" id="title" value="<?php echo $name ?>" maxlength="255" size="40" />
						</div>
					</div>
					<div class="slides">
						<?php foreach($billboard['slides'] as $key => $item): ?>
							<div class="postbox slide">
								<div class="handlediv" title="Click to toggle">&nbsp;</div>
								<h3 class="hndle"><span><?php echo sprintf("Slide %u", $key + 1); ?></span></h3>
								<div class="inside">
									<div class="image-wrapper"></div>
									<?php foreach($uds_billboard_attributes as $attrib => $options): ?>
										<?php uds_billboard_render_field($item, $attrib, $key) ?>
									<?php endforeach; ?>
									<div class="clear"></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div> <!-- END Slides -->
				</div> <!-- END editor body -->
			</div> <!-- END editor wrapper -->
		</div> <!-- END metabox holder -->
	</form> <!-- END billboard update form -->
</div> <!-- END Wrap -->