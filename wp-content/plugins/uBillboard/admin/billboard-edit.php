<?php 

// Load Billboards
$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));

// Select billboard for editing
if(!empty($_GET['uds-billboard-edit']) && !empty($billboards[$_GET['uds-billboard-edit']])) {
	$billboard = $billboards[$_GET['uds-billboard-edit']];
}

// safety check
if(!isset($billboard) || !is_a($billboard, 'uBillboard')) {
	$billboard = new uBillboard();
	$billboard->setUniqueName();
}

// Add new empty slide at the end, if the user wants to add new slide
$billboard->addEmptySlide();

?>
<div class="wrap">
	<!-- Heading -->
	<div id="icon-edit" class="icon32 icon32-posts-post"><br /></div>
	<h2><?php _e('Edit uBillboard', uds_billboard_textdomain) ?></h2>
	<!-- Form -->
	<form id="billboard_update_form" method="post" action="<?php echo admin_url('admin.php?page=uds_billboard_edit&uds-billboard='.$billboard->name) ?>" class="uds-billboard-form">
		<?php wp_nonce_field('uds-billboard-update', 'uds-billboard-update-nonce'); ?>
		<input type="hidden" name="uds_billboard[regenerate-thumbs]" class="uds-regenerate-marker" value="0" />
		<!-- Sidebar -->
		<div class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<!-- General Options -->
					<div class="postbox">
						<div class="handlediv" title="<?php esc_attr_e('Click to toggle', uds_billboard_textdomain) ?>"><br /></div>
						<h3 class="hndle"><span><?php _e('Options', uds_billboard_textdomain) ?></span></h3>
						<div class="inside">
							<div id="minor-publishing-actions">
								<div id="preview-action">
									<a href="<?php echo admin_url("admin.php?page=uds_billboard_edit&action=preview&noheader") ?>" class="preview button"><?php _e('Preview', uds_billboard_textdomain) ?></a>
									<div class="clear"></div>
								</div>
							</div>
							<hr />
							<?php $billboard->renderAdminOption('width'); ?>
							<?php $billboard->renderAdminOption('height'); ?>
							<?php $billboard->renderAdminOption('randomize'); ?>
							<?php $billboard->renderAdminOption('autoplay'); ?>
							<?php $billboard->renderAdminOption('pause-on-video'); ?>
							<?php $billboard->renderAdminOption('square-size'); ?>
							<?php $billboard->renderAdminOption('style'); ?>
							<hr />
							<div id="major-publishing-actions" class="submitbox">
								<div id="delete-action">
									<a href="<?php echo admin_url("admin.php?page=uds_billboard_admin&uds-billboard-delete={$billboard->name}&uds-billboard-delete-nonce=".wp_create_nonce('uds-billboard-delete-nonce')) ?>" class="submitdelete deletion"><?php _e('Delete', uds_billboard_textdomain) ?></a>
								</div>
								<div id="publishing-action">
									<input class="button-primary" type="submit" style="float:right" value="<?php esc_attr_e('Save uBillboard', uds_billboard_textdomain) ?>" />
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					<!-- Slide Order -->
					<div class="postbox">
						<div class="handlediv" title="<?php esc_attr_e('Click to toggle', uds_billboard_textdomain) ?>"><br /></div>
						<h3 class="hndle"><span><?php _e('Slide Order', uds_billboard_textdomain) ?></span></h3>
						<div class="inside">
							<ul class="uds-slides-order">
								<?php foreach($billboard->slides as $key => $item): ?>
									<li id="uds-slide-handle-<?php echo $key ?>" class="uds-slide-handle"><?php printf(__('Slide %u', uds_billboard_textdomain), $key + 1) ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<!-- Controls options -->
					<div class="postbox">
						<div class="handlediv" title="<?php esc_attr_e('Click to toggle', uds_billboard_textdomain) ?>"><br /></div>
						<h3 class="hndle"><span><?php _e('Controls', uds_billboard_textdomain) ?></span></h3>
						<div class="inside">
							<br />
							<?php $billboard->renderAdminOption('show-timer'); ?>
							<hr />
							<label><?php _e('Controls skin', uds_billboard_textdomain) ?>:</label>
							<?php $billboard->renderAdminOption('controls-skin'); ?>
							<label><?php _e('Controls Position', uds_billboard_textdomain) ?>:</label>
							<?php $billboard->renderAdminOption('controls-position'); ?>
							<hr />
							<label><?php _e('Show Controls', uds_billboard_textdomain) ?>:</label>
							<?php $billboard->renderAdminOption('show-controls'); ?>
							<hr />
							<label><?php _e('Show Play/Pause', uds_billboard_textdomain) ?>:</label>
							<?php $billboard->renderAdminOption('show-pause'); ?>
							<hr />
							<label><?php _e('Show Paginator', uds_billboard_textdomain) ?>:</label>
							<?php $billboard->renderAdminOption('show-paginator'); ?>
							<hr />
							<label><?php _e('Thumbnails', uds_billboard_textdomain) ?>:</label>
							<?php $billboard->renderAdminOption('show-thumbnails'); ?>
							<?php $billboard->renderAdminOption('thumbnails-position'); ?>
							<?php $billboard->renderAdminOption('thumbnails-inside'); ?>
							<?php $billboard->renderAdminOption('thumbnails-width'); ?>
							<?php $billboard->renderAdminOption('thumbnails-height'); ?>
							<?php $billboard->renderAdminOption('thumbnails-hover-color'); ?>
						</div>
					</div>
				</div>
			</div>
			<!-- Main editor -->
			<div class="editor-wrapper">
				<div class="editor-body">
					<div id="titlediv">
						<div id="titlewrap">
							<input type="text" name="uds_billboard[name]" id="title" value="<?php echo $billboard->name ?>" maxlength="255" size="40" />
						</div>
					</div>
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<div class="slides">
							<?php foreach($billboard->slides as $key => $item): ?>
								<div class="postbox slide" id="uds-slide-<?php echo $key ?>">
									<div class="handlediv" title="Click to toggle"><br /></div>
									<div class="deletediv" title="Click to delete slide"><br /></div>
									<div class="adddiv" title="Click to add slide"><br /></div>
									<h3 class="hndle"><span><?php echo sprintf(__("Slide %u", uds_billboard_textdomain), $key + 1); ?></span></h3>
									<div class="inside">
										<?php $item->renderAdmin() ?>
										<div class="clear"></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div> <!-- END Slides -->
					</div> <!-- END Normal Sortables -->
					<input type="button" class="button secondary" id="uds-add-slide" value="<?php _e('Add New Slide', uds_billboard_textdomain) ?>" />
				</div> <!-- END editor body -->
			</div> <!-- END editor wrapper -->
		</div> <!-- END metabox holder -->
	</form> <!-- END billboard update form -->
</div> <!-- END Wrap -->
<div id="colorpicker"></div>