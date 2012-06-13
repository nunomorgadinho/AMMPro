<?php

// Load Billboards
$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));

// safety check
if(! is_array($billboards)) {
	$billboards = array();
}

if(isset($billboards['_uds_temp_billboard'])) {
	unset($billboards['_uds_temp_billboard']);
}

// add 'button' class to header add new link
$link_class = 'add-new-h2';
if(version_compare(get_bloginfo('version'), '3.2', '<')) {
	$link_class .= ' button';
}

?>
<div class="wrap">
	<!-- Header -->
	<div id="icon-edit" class="icon32 icon32-posts-post"><br /></div>
	<?php  ?>
	<h2>uBillboard <a href="<?php echo admin_url('admin.php?page=uds_billboard_edit') ?>" class="<?php echo $link_class ?>"><?php _e('Add New', uds_billboard_textdomain) ?></a></h2>
	
	<!-- Billboards List -->
	<?php if(!empty($billboards)): ?>	
		<form action="" method="post" class="uds-bulk-actions-form">
			<?php wp_nonce_field("uds-billboard-bulk-actions", "uds-billboard-bulk-actions") ?>
			<!-- Bulk Actions -->
			<div class="tablenav top">
				<div class="alignleft actions">
					<select name="action" class="uds-bulk-actions">
						<option selected="selected" value="-1"><?php _e('Bulk Actions', uds_billboard_textdomain) ?></option>
						<option value="export"><?php _e('Export', uds_billboard_textdomain) ?></option>
						<option value="delete"><?php _e('Delete', uds_billboard_textdomain) ?></option>
					</select>
					<input type="submit" id="doaction" class="button-secondary action" value="<?php _e('Apply', uds_billboard_textdomain) ?>" />
				</div>
			</div>
			
			<!-- Billboard List -->
			<table class="wp-list-table widefat fixed billboards">
				<!-- Table Header -->
				<thead>
					<tr>
						<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
						<th id="title"><?php _e('Billboard Name', uds_billboard_textdomain) ?></th>
						<th class="slide_count"><?php _e('Slides', uds_billboard_textdomain) ?></th>
						<th class="shortcode"><?php _e('Shortcode', uds_billboard_textdomain) ?></th>
					</tr>
				</thead>
				<!-- Table Footer -->
				<tfoot>
					<tr>
						<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
						<th id="title"><?php _e('Billboard Name', uds_billboard_textdomain) ?> </th>
						<th class="slide_count"><?php _e('Slides', uds_billboard_textdomain) ?></th>
						<th class="shortcode"><?php _e('Shortcode', uds_billboard_textdomain) ?></th>
					</tr>
				</tfoot>
				<!-- Billboards -->
				<tbody id="the-list">
					<?php $n = 0; ?>
					<?php foreach($billboards as $key => $billboard): ?>
						<tr class="<?php echo $n % 2 == 0 ? 'alternate' : '' ?>">
							<th class="check-column" scope="row">
								<input type="checkbox" value="<?php echo esc_attr(stripslashes($key)) ?>" name="billboards[]" />
							</th>
							<td>
								<strong>
									<a href="<?php echo admin_url('admin.php?page=uds_billboard_edit&uds-billboard-edit='.urlencode(stripslashes($key))) ?>">
										<?php echo esc_html(stripslashes($billboard->name)) ?>
									</a>
								</strong>
								<div class="row-actions">
									<span class="edit">
										<a href="<?php echo admin_url('admin.php?page=uds_billboard_edit&uds-billboard-edit='.urlencode(stripslashes($key))) ?>"><?php _e('Edit', uds_billboard_textdomain) ?></a> | 
									</span>
									<span class="preview-action">
										<a href="<?php echo admin_url('admin.php?page=uds_billboard_edit&uds-billboard-edit='.urlencode(stripslashes($key)).'#preview') ?>"><?php _e('Preview', uds_billboard_textdomain) ?></a> | 
									</span>
									<span class="export">
										<a href="<?php echo admin_url('admin.php?page=uds_billboard_import_export&uds-billboard-export='.urlencode(stripslashes($key)).'&download_export='.wp_create_nonce('uds-billboard-export')) ?>"><?php _e('Export', uds_billboard_textdomain) ?></a> | 
									</span>
									<span class="trash">
										<a href="<?php echo admin_url('admin.php?page=uds_billboard_admin&uds-billboard-delete='.urlencode(stripslashes($key)).'&uds-billboard-delete-nonce='.wp_create_nonce('uds-billboard-delete-nonce')) ?>"><?php _e('Delete', uds_billboard_textdomain) ?></a>
									</span>
								</div>
							</td>
							<td class="slide_count">
								<?php echo count($billboard->slides) ?>
							</td>
							<td class="shortcode">
								<?php echo "[uds-billboard name=\"" . esc_html(stripslashes($key)) . "\"]"?>
							</td>
						</tr>
						<?php $n++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	<?php else: ?>
		<p><?php printf(__('There are no uBillboards defined yet. Create your first one %1$shere%2$s!', uds_billboard_textdomain), '<a href="'.admin_url('admin.php?page=uds_billboard_edit').'">', '</a>') ?></p>
	<?php endif; ?>
</div>