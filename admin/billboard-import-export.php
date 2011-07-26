<!-- Import/Export -->
<div class="wrap">
	<!-- Heading -->
	<h2><?php _e('Import/Export uBillboards', uds_billboard_textdomain) ?></h2>
	
	<!-- Warnings handler -->
	<?php if(!empty($uds_billboard_errors)): ?>
		<div class="updated uds-warn">
			<p><?php echo implode('</p><p>', $uds_billboard_errors) ?></p>
		</div>
	<?php endif; ?>
	
	<!-- Export -->
	<div class="uds-billboard-export">
		<h3><?php _e('Export', uds_billboard_textdomain) ?></h3>
		<?php printf(__('Download your exported uBillboards %1$shere%2$s.', uds_billboard_textdomain), '<a href="admin.php?page=uds_billboard_import_export&download_export='. wp_create_nonce('uds-billboard-export') .'">', '</a>') ?>
	</div>
	
	<!-- Import -->
	<div class="uds-billboard-import">
		<h3><?php _e('Import', uds_billboard_textdomain) ?></h3>
		<form method="post" action="" enctype="multipart/form-data">
			<label for="uds-billboard-import-attachments">
				<?php _e('Import attachments', uds_billboard_textdomain) ?>: <input type="checkbox" name="import-attachments" id="uds-billboard-import-attachments" />
			</label><br />
			<input type="file" name="uds-billboard-import" value="<?php esc_attr_e('Upload Exported uBillboard', uds_billboard_textdomain) ?>" />
			<input type="submit" name="" value="Import" />
		</form>
	</div>
	<p><?php _e('<em>Note:</em> Importer will attempt to download all slide images that are not located on this host.', uds_billboard_textdomain) ?></p>
</div>