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
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
					<th id="title">Billboard Name</th>
				</tr>
			</tfoot>
			<tbody id="the-list">
				<?php $n = 0; ?>
				<?php foreach($billboards as $key => $billboard): ?>
					<tr class="<?php echo $n % 2 == 0 ? 'alternate' : '' ?>">
						<th class="check-column" scope="row">
							<input type="checkbox" value="<?php echo $key ?>" name="billboard[]" />
						</th>
						<td>
							<strong>
								<a href="admin.php?page=uds_billboard_add&uds-billboard-edit=<?php echo $key ?>">
									<?php echo $billboard['name'] ?>
								</a>
							</strong>
							<div class="row-actions">
								<span class="edit">
									<a href="admin.php?page=uds_billboard_add&uds-billboard-edit=<?php echo $key ?>">Edit</a> | 
								</span>
								<span class="trash">
									<a href="admin.php?page=uds_billboard_add&uds-billboard-delete=<?php echo $key ?>">Trash</a>
								</span>
							</div>
						</td>
					</tr>
					<?php $n++; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<p>There are no uBillboards defined yet.</p>
	<?php endif; ?>
</div>