<?php
/**
 * The template for displaying all attached images.
 *
 * @package puzzles
 */

get_header(); 

$counters = get_theme_option("blog_counters");
$blog_style = 'fullpost'; //get_custom_option('blog_style');
$thumb_size = array(
	'fullpost' => array('w' => get_custom_option('show_sidebar_main')=='fullwidth' ? 1243 : 932, 'h' => null)
);
?>
	<div id="main_inner" class="clearboth blog_style_<?php echo $blog_style; ?>">
		<div id="content" class="content_blog post_single" role="main">

            <?php while ( have_posts() ) : the_post(); ?>
                <?php setPostViews(get_the_ID()); ?>

                <?php
				$post_id = get_the_ID();
				$post_protected = post_password_required();
				$post_format = get_post_format();
				if (empty($post_format)) $post_format = 'standard';
				$post_link = get_permalink();
				$post_comments_link = $counters=='comments' ? get_comments_link( $post_id ) : $post_link;
				$post_date = getDateOrDifference(get_the_date('Y-m-d H:i:s'));
				$post_comments = get_comments_number();
				$post_views = getPostViews($post_id);
				$post_attachment = wp_get_attachment_url();
				$post_title = getPostTitle();
				if (($post_content = get_the_content())=='')
					$post_content = getPostDescription();				//get_the_excerpt();
				$post_content = apply_filters('the_content', $post_content);
				?>

				<article <?php post_class('theme_article post_format_'.$post_format); ?>>
					<?php 
					if (!$post_protected) { 
						// If post have thumbnail - show it
						if ( $post_attachment) {
						?>
							<div class="post_thumb image_wrapper no_thumb">
								<a href="<?php echo $post_attachment; ?>"><img src="<?php echo $post_attachment; ?>" alt="<?php echo $post_title; ?>"></a>
							</div>
							<nav class="nav_pages_parts nav_pages_attachment" role="navigation">
								<?php 
								ob_start();
								previous_image_link( false, '<span class="meta-nav">&larr;</span> '.__('Previous', 'themerex'));
								$prev = @ob_get_contents();
								@ob_end_clean();
								if (!empty($prev)) echo '<span class="page_num">'.$prev.'</span>';
								ob_start();
								next_image_link( false, __( 'Next', 'themerex').' <span class="meta-nav">&rarr;</span>');
								$next = @ob_get_contents();
								@ob_end_clean();
								if (!empty($next)) echo '<span class="page_num">'.$next.'</span>';
								?>
							</nav>
						<?php
						}
					}
					?>
					
					<div class="post_content<?php echo get_custom_option('without_paddings')=='yes' ? ' without_paddings' : ''; ?>">

						<?php
						// Post info
						if ( get_custom_option('show_post_info')=='yes') {
						?>
							<div class="post_info post_info_top theme_info">
								<?php _e('Posted ', 'themerex'); ?><span class="post_date theme_text"><?php echo $post_date; ?></span>
								<?php if ($counters!='none') { ?>
								<span class="post_comments"><a href="<?php echo $post_comments_link; ?>"><span class="comments_icon theme_info icon-<?php echo $counters=='comments' ? 'chat-1' : 'eye'; ?>"></span><span class="comments_number"><?php echo $counters=='comments' ? $post_comments : $post_views; ?></span></a></span>
								<?php } ?>
							</div>
						<?php 
						}								

						// Post title
						if ( get_custom_option('show_post_title')=='yes' && !in_array($post_format, array('quote', 'aside'))) {
						?>	
							<div class="title_area">
								<h1 class="post_title theme_title"><?php echo $post_title; ?></h1>
							</div>
						<?php
						}
						?>
						
						<div class="post_text_area">
							<?php
							echo $post_content;
							wp_link_pages( array( 
								'before' => '<div class="nav_pages_parts"><span class="pages">' . __( 'Pages:', 'themerex' ) . '</span>', 
								'after' => '</div>',
								'link_before' => '<span class="page_num">',
								'link_after' => '</span>'
							) ); 
							?>
						</div>

					</div>
				</article>

                <?php 
				if (!$post_protected) {
					//===================================== Comments =====================================
					if (get_custom_option("show_post_comments") == 'yes') {
						if ( comments_open() || get_comments_number() != 0 ) {
							comments_template();
						}
					}
                } // if (!post_password_required())
                ?>
    
            <?php endwhile; // end of the loop. ?>

		</div><!-- #content -->

		<?php get_sidebar(); ?>

	</div><!-- #main_inner -->

<?php get_footer(); ?>
