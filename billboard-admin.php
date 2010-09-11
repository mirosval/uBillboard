<?php

global $uds_billboard_attributes;

if(!empty($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'uds-billboard')){
	die('Security check failed');
}

uds_billboard_proces_updates();

$billboard_items = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));

// safety check
if(! is_array($billboard_items)) {
	$billboard_items = array();
}

$billboard_items[] = uds_billboard_default_billboard();

?>
<div class="wrap">
	<h2>uBillboard</h2>
	<?php if(get_option(UDS_BILLBOARD_OPTION_USE_TIMTHUMB) == 'on' && !is_writable(UDS_BILLBOARD_PATH.'cache')): ?>
		<div class="updated uds-warn"><strong>Warning!</strong> Directory <?php echo UDS_BILLBOARD_PATH ?>cache is not writable!</div>
	<?php endif; ?>
	<form action="" method="post" class="uds-billboard-form">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('uds-billboard') ?>" />
		<div class="uds-billboard-options">
			<div class="close"></div>
			<h3>General Options</h3>
			<input type="submit" value="Update"  class="submit button-primary" />
			<div class="inside">
				<?php uds_billboard_admin_options() ?>
				<div class="clear"></div>
			</div>
		</div>
		<table id="uds-billboard-table">
			<?php foreach($billboard_items as $key => $item): ?>
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