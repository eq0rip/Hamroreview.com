<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package puzzles
 */
?>

<?php 
if (get_custom_option('show_sidebar_main') != 'fullwidth') {
?>
        <div id="sidebar_main" class="widget_area sidebar_main theme_<?php echo get_custom_option('sidebar_main_theme'); ?>" role="complementary">
            <?php do_action( 'before_sidebar' ); ?>
            <?php if ( ! dynamic_sidebar( get_custom_option('sidebar_main') ) ) { ?>
    			<?php // Put here html if user no set widgets in sidebar ?>
            <?php } // end sidebar widget area ?>
        </div>
<?php
}
?>