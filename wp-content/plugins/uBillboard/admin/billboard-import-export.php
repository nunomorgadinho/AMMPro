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
		
		<!-- V2 Import -->
		<h4><?php _e('Import from uBillboard v2.x.x', uds_billboard_textdomain) ?></h4>
		<?php if(uds_billboard_can_import_from_v2()): ?>
			<p><?php _e('You have the following v2.x.x uBillboards on this WordPress installation', uds_billboard_textdomain) ?>:</p>
			<?php uds_billboard_list_v2() ?>
			<br>
			<a href="<?php echo admin_url("admin.php?page=uds_billboard_import_export&uds-billboard-import-v2=" . wp_create_nonce('uds-billboard-import-v2')) ?>" class="button secondary"><?php _e('Import', uds_billboard_textdomain) ?></a>
		<?php else: ?>
			<p><?php _e('You do not have any uBillboard v2 billboards present on this WordPress installation', uds_billboard_textdomain) ?></p>
		<?php endif; ?>
		
		<!-- V3 Import via XML -->
		<h4><?php _e('Import from uBillboard v3 and up', uds_billboard_textdomain) ?></h4>
		<form method="post" action="" enctype="multipart/form-data">
			<label for="uds-billboard-import-attachments">
				<?php _e('Import attachments', uds_billboard_textdomain) ?>: <input type="checkbox" name="import-attachments" id="uds-billboard-import-attachments" />
			</label><br />
			<input type="file" name="uds-billboard-import" value="<?php esc_attr_e('Upload Exported uBillboard', uds_billboard_textdomain) ?>" />
			<input type="submit" name="" value="Import" class="button secondary" />
		</form>
	</div>
	<p><?php _e('<em>Note:</em> Importer will attempt to download all slide images that are not located on this host.', uds_billboard_textdomain) ?></p>
</div>