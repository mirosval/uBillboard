<?php 
//d(maybe_unserialize(get_option(UDS_BILLBOARD_OPTION))); 

global $uds_billboard_attributes, $uds_billboard_general_options;

$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));

if(!empty($_GET['uds-billboard-edit']) && !empty($billboards[$_GET['uds-billboard-edit']])) {
	$billboard = $billboards[$_GET['uds-billboard-edit']];
}

// safety check
//d(is_a($billboard, 'uBillboard'));
if(!isset($billboard) || !is_a($billboard, 'uBillboard')) {
	$billboard = new uBillboard();
}

//d($billboard);

$billboard->addEmptySlide();

//d($billboards);
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
						<?php uds_billboard_render_general_text('width', $uds_billboard_general_options['width'], $billboard->width); ?>
						<?php uds_billboard_render_general_text('height', $uds_billboard_general_options['height'], $billboard->height); ?>
						<?php uds_billboard_render_general_checkbox('randomize', $uds_billboard_general_options['randomize'], $billboard->randomize); ?>
						<?php uds_billboard_render_general_checkbox('autoplay', $uds_billboard_general_options['autoplay'], $billboard->autoplay); ?>
						<?php uds_billboard_render_general_text('square-size', $uds_billboard_general_options['square-size'], $billboard->squareSize); ?>
						<?php uds_billboard_render_general_select('style', $uds_billboard_general_options['style'], $billboard->style); ?>
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
						<?php uds_billboard_render_general_checkbox('show-controls', $uds_billboard_general_options['show-controls'], $billboard->showControls); ?>
						<?php uds_billboard_render_general_select('controls-position', $uds_billboard_general_options['controls-position'], $billboard->controlsPosition); ?>
						<?php uds_billboard_render_general_checkbox('show-pause', $uds_billboard_general_options['show-pause'], $billboard->showPause); ?>
					</div>
				</div>
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Paginator</span></h3>
					<div class="inside">
						<?php uds_billboard_render_general_checkbox('show-paginator', $uds_billboard_general_options['show-paginator'], $billboard->showPaginator); ?>
						<?php uds_billboard_render_general_select('paginator-position', $uds_billboard_general_options['paginator-position'], $billboard->paginatorPosition); ?>
					</div>
				</div>
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Image Resizing Options</span></h3>
					<div class="inside">
						<?php uds_billboard_render_general_checkbox('use-timthumb', $uds_billboard_general_options['use-timthumb'], $billboard->useTimthumb); ?>
						<?php uds_billboard_render_general_checkbox('timthumb-zoom', $uds_billboard_general_options['timthumb-zoom'], $billboard->timthumbZoom); ?>
						<?php uds_billboard_render_general_text('timthumb-quality', $uds_billboard_general_options['timthumb-quality'], $billboard->timthumbQuality); ?>
					</div>
				</div>
			</div>
			<div class="editor-wrapper">
				<div class="editor-body">
					<div id="titlediv">
						<div id="titlewrap">
							<input type="text" name="uds_billboard[name]" id="title" value="<?php echo $billboard->name ?>" maxlength="255" size="40" />
						</div>
					</div>
					<div class="slides">
						<?php foreach($billboard->slides as $key => $item): ?>
							<div class="postbox slide">
								<div class="handlediv" title="Click to toggle">&nbsp;</div>
								<h3 class="hndle"><span><?php echo sprintf("Slide %u", $key + 1); ?></span></h3>
								<div class="inside">
									<div class="image-wrapper"></div>
									<?php $item->renderAdmin() ?>
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
<?php uds_billboard_render_js_support() ?>