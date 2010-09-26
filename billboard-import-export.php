<div class="wrap">
	<h2>Import/Export uBillboards</h2>
	<?php if(!empty($uds_billboard_errors)): ?>
		<div class="updated uds-warn">
			<p><?php echo implode('</p><p>', $uds_billboard_errors) ?></p>
		</div>
	<?php endif; ?>
	<div class="uds-billboard-export">
		<h3>Export</h3>
		Download your exported uBillboards <a href="admin.php?page=uds_billboard_import_export&download_export=<?php echo wp_create_nonce('uds-billboard-export') ?>">here</a>.
	</div>
	<div class="uds-billboard-import">
		<h3>Import</h3>
		<form method="post" action="" enctype="multipart/form-data">
			<label for="uds-billboard-import-attachments">
				Import attachments: <input type="checkbox" name="import-attachments" id="uds-billboard-import-attachments" />
			</label><br />
			<input type="file" name="uds-billboard-import" value="Upload Exported uBillboard" />
			<input type="submit" name="" value="Import" />
		</form>
	</div>
	<p><em>Note:</em> Importer will attempt to download all slide images that are not located on this host.</p>
</div>