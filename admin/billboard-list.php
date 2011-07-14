<?php

$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));

if(!empty($_GET['uds-billboard-delete'])) {
	unset($billboards[$_GET['uds-billboard-delete']]);
	update_option(UDS_BILLBOARD_OPTION, serialize($billboards));
}

// safety check
if(! is_array($billboards)) {
	$billboards = array();
}

?>
<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br /></div>
	<h2>uBillboard</h2>
	<?php if(!empty($billboards)): ?>
		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="action">
					<option selected="selected" value="-1">Bulk Actions</option>
					<option value="edit">Edit</option>
					<option value="trash">Trash</option>
				</select>
			</div>
		</div>
		<table class="wp-list-table widefat fixed billboards">
			<thead>
				<tr>
					<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
					<th id="title">Billboard Name</th>
					<th>Shortcode</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
					<th id="title">Billboard Name</th>
					<th>Shortcode</th>
				</tr>
			</tfoot>
			<tbody id="the-list">
				<?php $n = 0; ?>
				<?php foreach($billboards as $key => $billboard): ?>
					<?php if($billboard->name == '_uds_temp_billboard') continue; ?>
					<tr class="<?php echo $n % 2 == 0 ? 'alternate' : '' ?>">
						<th class="check-column" scope="row">
							<input type="checkbox" value="<?php echo $key ?>" name="billboard[]" />
						</th>
						<td>
							<strong>
								<a href="<?php echo admin_url('admin.php?page=uds_billboard_edit&uds-billboard-edit='.$key) ?>">
									<?php echo $billboard->name ?>
								</a>
							</strong>
							<div class="row-actions">
								<span class="edit">
									<a href="<?php echo admin_url('admin.php?page=uds_billboard_edit&uds-billboard-edit='.$key) ?>">Edit</a> | 
								</span>
								<span class="trash">
									<a href="<?php echo admin_url('admin.php?page=uds_billboard_admin&uds-billboard-delete='.$key.'&nonce='.wp_create_nonce('uds-billboard-delete-nonce')) ?>">Trash</a>
								</span>
							</div>
						</td>
						<td>
							<?php echo "[uds-billboard name=\"{$billboard->name}\"]"?>
						</td>
					</tr>
					<?php $n++; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<p>There are no uBillboards defined yet. Create your first one <a href="<?php echo admin_url('admin.php?page=uds_billboard_edit') ?>">here</a>!</p>
	<?php endif; ?>
</div>