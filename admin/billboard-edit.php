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
	$billboard->setUniqueName();
}

//d($billboard);

$billboard->addEmptySlide();

//d($billboards);
?>
<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br /></div>
	<h2>Edit uBillboard</h2>
	<form id="billboard_update_form" method="post" action="" class="uds-billboard-form">
		<?php wp_nonce_field('uds-billboard-update', 'uds-billboard-update-nonce'); ?>
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
							<?php $billboard->renderAdminOption('width'); ?>
							<?php $billboard->renderAdminOption('height'); ?>
							<?php $billboard->renderAdminOption('randomize'); ?>
							<?php $billboard->renderAdminOption('autoplay'); ?>
							<?php $billboard->renderAdminOption('square-size'); ?>
							<?php $billboard->renderAdminOption('style'); ?>
							<hr />
							<div id="major-publishing-actions" class="submitbox">
								<div id="delete-action">
									<a href="<?php echo admin_url("admin.php?page=uds_billboard_admin&uds-billboard-delete={$billboard->name}&nonce=".wp_create_nonce('uds-billboard-delete-nonce')) ?>" class="submitdelete deletion">Delete</a>
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
							<?php $billboard->renderAdminOption('controls-skin'); ?>
							<hr />
							<label>Show Controls:</label>
							<?php $billboard->renderAdminOption('show-controls'); ?>
							<label>Controls Position:</label>
							<?php $billboard->renderAdminOption('controls-position'); ?>
							<hr />
							<label>Show Play/Pause:</label>
							<?php $billboard->renderAdminOption('show-pause'); ?>
							<hr />
							<label>Show Paginator:</label>
							<?php $billboard->renderAdminOption('show-paginator'); ?>
							<label>Paginator Position:</label>
							<?php $billboard->renderAdminOption('paginator-position'); ?>
							<hr />
							<label>Thumbnails:</label>
							<?php $billboard->renderAdminOption('show-thumbnails'); ?>
							<?php $billboard->renderAdminOption('thumbnails-position'); ?>
							<?php $billboard->renderAdminOption('thumbnails-inside'); ?>
							<?php $billboard->renderAdminOption('thumbnails-width'); ?>
							<?php $billboard->renderAdminOption('thumbnails-height'); ?>
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