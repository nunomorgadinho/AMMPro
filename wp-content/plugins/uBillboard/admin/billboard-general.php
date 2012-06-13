<!-- General Options -->
<div class="wrap">
	<!-- Heading -->
	<div class="icon32" id="icon-options-general"><br></div>
	<h2><?php _e('uBillboard General Options', uds_billboard_textdomain) ?></h2>
	<p><?php _e('These options are site-wide, they relate to specific optimizations applied to uBillboard to make it faster. If you want to edit the code however, you might find
	it better to have them turned off, here you can comfortably do so.', uds_billboard_textdomain) ?></p>
	
	<!-- Form -->
	<form method="post" action="options.php">
		<?php settings_fields('uds_billboard_general_options'); ?>
		<?php $options = get_option(UDS_BILLBOARD_OPTION_GENERAL); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('JavaScript Compression', uds_billboard_textdomain) ?>:</th>
				<td>
					<label>
						<input name="<?php echo UDS_BILLBOARD_OPTION_GENERAL?>[compression]" type="checkbox" <?php if (isset($options['compression'])) { checked(true, $options['compression']); } ?> />
						<?php _e('Check if you want to use compressed JS', uds_billboard_textdomain) ?>
					</label><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Shortcode Optimization', uds_billboard_textdomain) ?>:</th>
				<td>
					<label>
						<input name="<?php echo UDS_BILLBOARD_OPTION_GENERAL?>[shortcode_optimization]" type="checkbox" <?php if (isset($options['shortcode_optimization'])) { checked(true, $options['shortcode_optimization']); } ?> />
						<?php _e('Check if you are ABSOLUTELY positive that you are using <em>only</em> shortcodes to display uBillboard and not any of the PHP functions.', uds_billboard_textdomain) ?>
					</label><br />
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes', uds_billboard_textdomain) ?>" />
		</p>
	</form>
</div>