<?php

global $uds_billboard_attributes;

$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));

if(!empty($_GET['uds-billboard-edit']) && !empty($billboards[$_GET['uds-billboard-edit']])) {
	$billboard = $billboards[$_GET['uds-billboard-edit']];
}

// safety check
if(! is_array($billboard)) {
	$billboard = array();
}

$name = array_search($billboard, $billboards);

$billboard['slides'][] = uds_billboard_default_billboard();

?>
<div class="wrap">
	<?php if(empty($_GET['uds-billboard-edit'])): ?>
		<h2>Create new uBillboard</h2>
	<?php else: ?>
		<h2>Edit uBillboard</h2>
	<?php endif; ?>
	<?php if(!uds_billboard_cache_is_writable()): ?>
		<div class="updated uds-warn"><strong>Warning!</strong> Directory <?php echo UDS_BILLBOARD_PATH ?>cache is not writable!</div>
	<?php endif; ?>
	<form action="" method="post" class="uds-billboard-form">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('uds-billboard') ?>" />
		<input type="hidden" name="original_name" value="<?php echo $name ?>" />
		<div class="uds-billboard-options">
			<div class="close"></div>
			<h3>General Options</h3>
			<input type="submit" value="Update"  class="submit button-primary" />
			<div class="inside">
				<?php uds_billboard_admin_options($billboard, $name) ?>
				<div class="clear"></div>
			</div>
		</div>
		<table id="uds-billboard-table">
			<?php foreach($billboard['slides'] as $key => $item): ?>
			<tr>
				<td>
					<a href="#" class="billboard-move" title="Reorder Billboard Items">Move</a>
					<a href="#" class="billboard-delete" title="Delete Item">Delete</a>
				</td>
				<td>
					<?php foreach($uds_billboard_attributes as $attrib => $options): ?>
						<?php uds_billboard_render_field($item, $attrib, $key) ?>
					<?php endforeach; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
		<input type="submit" value="Update"  class="submit button-primary" />
	</form>
</div>
<?php uds_billboard_render_js_support() ?>