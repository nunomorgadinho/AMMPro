<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>
<?php get_header( 'buddypress' ); ?>

	<?php do_action( 'bp_before_directory_groups_page' ); ?>

	<div id="content">
		<h1><?php _e( 'Groups Directory', 'buddypress' ); ?></h1>
		<div class="buddybox">
        <form action="" method="post" id="groups-directory-form" class="dir-form">
        <div id="group-dir-search" class="dir-search" role="search">
        <div id="whats-new-avatar" class="left">
        <h5>Your Groups</h5>
        </div>
        <?php bp_directory_groups_search_form() ?> 
        </div>
       </form>
		<?php do_action( 'bp_before_directory_groups' ); ?>
		
			<?php do_action( 'bp_before_directory_groups_content' ); ?>

			<!-- #group-dir-search -->

			<?php do_action( 'template_notices' ); ?>
			<aside class="bbaside">
            <?php get_sidebar('bbcontrols'); ?>
			<div class="item-list-tabs" role="navigation">
				<ul>
					<li class="selected" id="groups-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><?php printf( __( 'All Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count() ); ?></a></li>

					<?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>

						<li id="groups-personal"><a href="<?php echo trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups' ); ?>"><?php printf( __( 'My Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>

					<?php endif; ?>

					<?php do_action( 'bp_groups_directory_group_filter' ); ?>

				</ul>
			</div><!-- .item-list-tabs -->
			
			<div class="item-list-tabs" id="subnav" role="navigation">
				<ul>

					<?php do_action( 'bp_groups_directory_group_types' ); ?>

					<li id="groups-order-select" class="last filter">

						<label for="groups-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
						<select id="groups-order-by">
							<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
							<option value="popular"><?php _e( 'Most Members', 'buddypress' ); ?></option>
							<option value="newest"><?php _e( 'Newly Created', 'buddypress' ); ?></option>
							<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>

							<?php do_action( 'bp_groups_directory_order_options' ); ?>

						</select>
					</li>
				</ul>
			</div>
			</aside>
			<div id="bbactive" class="groups dir-list">
            <div class="add_deal">
			<?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?> &nbsp;<a class="button" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>"><?php _e( 'Create a Group', 'buddypress' ); ?></a>
			<?php endif; ?>
            </div>
				<?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>
			</div><!-- #groups-dir-list -->

			<?php do_action( 'bp_directory_groups_content' ); ?>

			<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

			<?php do_action( 'bp_after_directory_groups_content' ); ?>

		<?php do_action( 'bp_after_directory_groups' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->
	<?php do_action( 'bp_after_directory_groups_page' ); ?>

<?php get_footer( 'buddypress' ); ?>

