<?php

/**
 * BuddyPress Notification Settings
 *
 * @package BuddyPress
 * @subpackage bp-default
 */
?>

<?php get_header( 'buddypress' ) ?>

	<div id="content">
		<h1><?php the_title(); ?></h1>
		<div class="buddybox">

			<?php do_action( 'bp_before_member_settings_template' ); ?>
            <div class="nineighty">
			<form id="whats-new-form" class="left">
                <h5>Settings</h5>
			</form>
			<div id="item-header" class="block1 right">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->
            </div>
            <aside class="bbaside">  
            <?php get_sidebar('bbcontrols'); ?>
			<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->
			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php bp_get_displayed_user_nav(); ?>

						<?php do_action( 'bp_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->
			</aside>
			<div id="bbactive" role="main">

				<?php do_action( 'bp_before_member_body' ); ?>

				

				<h3><?php _e( 'Email Notification', 'buddypress' ); ?></h3>

				<?php do_action( 'bp_template_content' ) ?>

				<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form" id="settings-form">
					<label><?php _e( 'Send a notification by email when:', 'buddypress' ); ?></label>

					<?php do_action( 'bp_notification_settings' ); ?>

					<?php do_action( 'bp_members_notification_settings_before_submit' ); ?>

					<div class="add_deal">
						<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
					</div>

					<?php do_action( 'bp_members_notification_settings_after_submit' ); ?>

					<?php wp_nonce_field('bp_settings_notifications'); ?>

				</form>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_settings_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_footer( 'buddypress' ) ?>