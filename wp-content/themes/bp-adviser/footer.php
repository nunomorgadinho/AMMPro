</div>
<!-- #container -->
<?php do_action( 'bp_after_container' ) ?>
<?php do_action( 'bp_before_footer' ) ?>
<footer>
    <div class="nineighty">
    <?php get_sidebar( 'footer-help' ) ?>
        <div class="lastmenu">
          <?php get_sidebar( 'footer' ) ?>
        </div>
    <div class="lastmenu">
      <?php do_action( 'bp_dtheme_credits' ) ?>
      <p>&copy Copyright <?php echo date('Y') . " " . esc_attr( get_bloginfo( 'name', 'display' ) ); ?> <a href="http://wordpress.org/" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'themename' ); ?>" rel="generator"></a></p> 
    </div>
    <?php do_action( 'bp_footer' ) ?>
    </div>
</footer>
<!-- #footer -->
<?php do_action( 'bp_after_footer' ) ?>
<?php if ( is_user_logged_in() ) { ?>
<?php wp_footer(); ?>
<?php } else {   ?>
<?php } ?>
</body>
</html>