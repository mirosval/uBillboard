<div class="wrap">
	<div class="icon32" id="icon-options-general"><br></div>
	<h2>uBillboard General Options</h2>
	<p>These options are site-wide, they relate to specific optimizations applied to uBillboard to make it faster. If you want to edit the code however, you might find
	it better to have them turned off, here you can comfortably do so.</p>

	<form method="post" action="options.php">
		<?php settings_fields('uds_billboard_general_options'); ?>
		<?php $options = get_option(UDS_BILLBOARD_OPTION_GENERAL); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">JavaScript Compression:</th>
				<td>
					<label>
						<input name="<?php echo UDS_BILLBOARD_OPTION_GENERAL?>[compression]" type="checkbox" <?php if (isset($options['compression'])) { checked(true, $options['compression']); } ?> />
						Check if you want to use compressed JS
					</label><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Shortcode Optimization:</th>
				<td>
					<label>
						<input name="<?php echo UDS_BILLBOARD_OPTION_GENERAL?>[shortcode_optimization]" type="checkbox" <?php if (isset($options['shortcode_optimization'])) { checked(true, $options['shortcode_optimization']); } ?> />
						Check if you are ABSOLUTELY positive that you are using <em>only</em> shortcodes to display uBillboard and not any of the PHP functions.
					</label><br />
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>