<?php

global $uds_billboard_attributes;

if(!empty($_GET['nonce']) && !wp_verify_nonce($_GET['nonce'], 'uds-billboard-delete')){
	die('Security check failed');
}

$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));

if(!empty($_GET['uds-billboard-delete'])) {
	unset($billboards[$_GET['uds-billboard-delete']]);
	update_option(UDS_BILLBOARD_OPTION, serialize($billboards));
}

// safety check
if(! is_array($billboards)) {
	$billboards = array();
}

//echo "<pre>";
//var_dump($billboards);

?>
<div class="wrap">
	<h2>Add/Edit uBillboards</h2>
	<?php if(get_option(UDS_BILLBOARD_OPTION_USE_TIMTHUMB) == 'on' && !is_writable(UDS_BILLBOARD_PATH.'cache')): ?>
		<div class="updated uds-warn"><strong>Warning!</strong> Directory <?php echo UDS_BILLBOARD_PATH ?>cache is not writable!</div>
	<?php endif; ?>
	<?php if(!empty($billboards)): ?>
		<table class="uds-billboard-admin-table">
			<tr>
				<th>ID</th>
				<th>Billboard name</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
			<?php $n = 1; foreach($billboards as $key => $billboard): ?>
			<tr>
				<td class="id"><?php echo $n ?></td>
				<td class="uds-billboard-name"><?php echo $key ?> (<?php echo count($billboard['slides']); echo count($billboard['slides']) == 1 ? ' slide' : ' slides' ?>)</td>
				<td class="edit">
					<a href="admin.php?page=uds_billboard_add&uds-billboard-edit=<?php echo $key ?>" class="billboard-edit" title="Edit Item">Edit</a>
				</td>
				<td class="delete">
					<a href="admin.php?page=uds_billboard_admin&uds-billboard-delete=<?php echo $key ?>&nonce=<?php echo wp_create_nonce('uds-billboard-delete')?>" class="billboard-delete" title="Delete Item">Delete</a>
				</td>
			</tr>
			<?php $n++; endforeach; ?>
		</table>
	<?php else: ?>
		<p>There are no billboards yet. You can create a billboard <a href="admin.php?page=uds_billboard_add">here</a>.</p>
	<?php endif; ?>
</div>