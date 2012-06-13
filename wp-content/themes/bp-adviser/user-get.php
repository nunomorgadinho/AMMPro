<?php
/**
 * Template Name: User Profile
 *
 * Allow users to update their profiles from Frontend.
 *
 */

/* Get user info. */
global $current_user, $wp_roles;
get_currentuserinfo();

/* Load the registration file. */
require_once( ABSPATH . WPINC . '/registration.php' );

/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    /* Update user password. */
    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
        if ( $_POST['pass1'] == $_POST['pass2'] )
            wp_update_user( array( 'ID' => $current_user->id, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
        else
            $error = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
    }

    /* Update user information. */
    if ( !empty( $_POST['url'] ) )
        update_usermeta( $current_user->id, 'user_url', esc_url( $_POST['url'] ) );
    if ( !empty( $_POST['email'] ) )
        update_usermeta( $current_user->id, 'user_email', esc_attr( $_POST['email'] ) );
    if ( !empty( $_POST['first-name'] ) )
        update_usermeta( $current_user->id, 'first_name', esc_attr( $_POST['first-name'] ) );
    if ( !empty( $_POST['last-name'] ) )
        update_usermeta($current_user->id, 'last_name', esc_attr( $_POST['last-name'] ) );
    if ( !empty( $_POST['description'] ) )
        update_usermeta( $current_user->id, 'description', esc_attr( $_POST['description'] ) );

    /* Redirect so the page will show updated info. */
    if ( !$error ) {
        wp_redirect( get_permalink() );
        exit;
    }
}
?>
<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_page' ) ?>

		<div class="page" id="blog-page" role="main">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>">
            <div class="entry-content entry">
                <?php the_content(); ?>
                <?php if ( !is_user_logged_in() ) : ?>
                        <p class="warning">
                            <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                        </p><!-- .warning -->
                <?php else : ?>
                    <?php if ( $error ) echo '<p class="error">' . $error . '</p>'; ?>
                    <form method="post" id="adduser" action="<?php the_permalink(); ?>">
                        <p class="form-username">
                            <label for="first-name"><?php _e('First Name', 'profile'); ?></label>
                            <input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'user_firstname', $current_user->id ); ?>" />
                        </p><!-- .form-username -->
                        <p class="form-username">
                            <label for="last-name"><?php _e('Last Name', 'profile'); ?></label>
                            <input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'user_lastname', $current_user->id ); ?>" />
                        </p><!-- .form-username -->
                        <p class="form-email">
                            <label for="email"><?php _e('E-mail *', 'profile'); ?></label>
                            <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->id ); ?>" />
                        </p><!-- .form-email -->
                        <p class="form-url">
                            <label for="url"><?php _e('Website', 'profile'); ?></label>
                            <input class="text-input" name="url" type="text" id="url" value="<?php the_author_meta( 'user_url', $current_user->id ); ?>" />
                        </p><!-- .form-url -->
                        <p class="form-password">
                            <label for="pass1"><?php _e('Password *', 'profile'); ?> </label>
                            <input class="text-input" name="pass1" type="password" id="pass1" />
                        </p><!-- .form-password -->
                        <p class="form-password">
                            <label for="pass2"><?php _e('Repeat Password *', 'profile'); ?></label>
                            <input class="text-input" name="pass2" type="password" id="pass2" />
                        </p><!-- .form-password -->
                        <p class="form-textarea">
                            <label for="description"><?php _e('Biographical Information', 'profile') ?></label>
                            <textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->id ); ?></textarea>
                        </p><!-- .form-textarea -->
                        <p class="form-submit">
                            <?php echo $referer; ?>
                            <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
                            <?php wp_nonce_field( 'update-user' ) ?>
                            <input name="action" type="hidden" id="action" value="update-user" />
                        </p><!-- .form-submit -->
                    </form><!-- #adduser -->
                    <?php endif; ?>
                </div><!-- .entry-content -->
            </div><!-- .hentry .post -->
            <?php comments_template( '', true ); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-data">
                <?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
            </p><!-- .no-data -->
        <?php endif; ?>

		</div><!-- .page -->

		<?php do_action( 'bp_after_blog_page' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar() ?>

<?php get_footer(); ?>
