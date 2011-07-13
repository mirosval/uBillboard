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
	<div id="icon-edit" class="icon32 icon32-posts-post"><br /></div>
	<h2>Edit uBillboard</h2>
	<form id="billboard_update_form" method="post" action="" class="uds-billboard-form">
		<div class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span>Options</span></h3>
						<div class="inside">
							<div id="minor-publishing-actions">
								<div id="preview-action">
									<a href="" class="preview button">Preview</a>
									<div class="clear"></div>
								</div>
							</div>
							<hr />
							<?php uds_billboard_render_general_text('width', $uds_billboard_general_options['width'], $billboard->width); ?>
							<?php uds_billboard_render_general_text('height', $uds_billboard_general_options['height'], $billboard->height); ?>
							<?php uds_billboard_render_general_checkbox('randomize', $uds_billboard_general_options['randomize'], $billboard->randomize); ?>
							<?php uds_billboard_render_general_checkbox('autoplay', $uds_billboard_general_options['autoplay'], $billboard->autoplay); ?>
							<?php uds_billboard_render_general_text('square-size', $uds_billboard_general_options['square-size'], $billboard->squareSize); ?>
							<?php uds_billboard_render_general_select('style', $uds_billboard_general_options['style'], $billboard->style); ?>
							<hr />
							<div id="major-publishing-actions" class="submitbox">
								<div id="delete-action">
									<a href="" class="submitdelete deletion">Delete</a>
								</div>
								<div id="publishing-action">
									<input class="button-primary" type="submit" style="float:right" value="Save uBillboard" />
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					<div class="postbox">
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span>Slide Order</span></h3>
						<div class="inside">
							<ul class="uds-slides-order">
								<?php foreach($billboard->slides as $key => $item): ?>
									<li id="uds-slide-handle-<?php echo $key ?>" class="uds-slide-handle">Slide <?php echo $key + 1 ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<div class="postbox">
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span>Controls</span></h3>
						<div class="inside">
							<br />
							<label>Controls skin:</label>
							<?php uds_billboard_render_general_select('controls-skin', $uds_billboard_general_options['controls-skin'], $billboard->controlsSkin); ?>
							<hr />
							<label>Show Controls:</label>
							<?php uds_billboard_render_general_select('show-controls', $uds_billboard_general_options['show-controls'], $billboard->showControls); ?>
							<label>Controls Position:</label>
							<?php uds_billboard_render_general_select('controls-position', $uds_billboard_general_options['controls-position'], $billboard->controlsPosition); ?>
							<hr />
							<label>Show Play/Pause:</label>
							<?php uds_billboard_render_general_select('show-pause', $uds_billboard_general_options['show-pause'], $billboard->showPause); ?>
							<hr />
							<label>Show Paginator:</label>
							<?php uds_billboard_render_general_select('show-paginator', $uds_billboard_general_options['show-paginator'], $billboard->showPaginator); ?>
							<label>Paginator Position:</label>
							<?php uds_billboard_render_general_select('paginator-position', $uds_billboard_general_options['paginator-position'], $billboard->paginatorPosition); ?>
							<hr />
							<label>Thumbnails:</label>
							<?php uds_billboard_render_general_select('show-thumbnails', $uds_billboard_general_options['show-thumbnails'], $billboard->showThumbnails); ?>
							<?php uds_billboard_render_general_select('thumbnails-position', $uds_billboard_general_options['thumbnails-position'], $billboard->thumbnailsPosition); ?>
							<?php uds_billboard_render_general_checkbox('thumbnails-inside', $uds_billboard_general_options['thumbnails-inside'], $billboard->thumbnailsInside); ?>
							<?php uds_billboard_render_general_text('thumbnails-width', $uds_billboard_general_options['thumbnails-width'], $billboard->thumbnailsWidth); ?>
							<?php uds_billboard_render_general_text('thumbnails-height', $uds_billboard_general_options['thumbnails-height'], $billboard->thumbnailsHeight); ?>
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
			</div>
			<div class="editor-wrapper">
				<div class="editor-body">
					<div id="titlediv">
						<div id="titlewrap">
							<input type="text" name="uds_billboard[name]" id="title" value="<?php echo $billboard->name ?>" maxlength="255" size="40" />
						</div>
					</div>
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<div class="slides">
							<?php foreach($billboard->slides as $key => $item): ?>
								<div class="postbox slide" id="uds-slide-<?php echo $key ?>">
									<div class="handlediv" title="Click to toggle"><br /></div>
									<div class="deletediv" title="Click to delete slide"><br /></div>
									<h3 class="hndle"><span><?php echo sprintf("Slide %u", $key + 1); ?></span></h3>
									<div class="inside">
										<?php $item->renderAdmin() ?>
										<div class="clear"></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div> <!-- END Slides -->
					</div> <!-- END Normal Sortables -->
				</div> <!-- END editor body -->
			</div> <!-- END editor wrapper -->
		</div> <!-- END metabox holder -->
	</form> <!-- END billboard update form -->
</div> <!-- END Wrap -->
<?php uds_billboard_render_js_support() ?>