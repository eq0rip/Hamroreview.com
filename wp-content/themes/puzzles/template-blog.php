<?php
/*
Template Name: Blog streampage
*/
get_header(); 



global $THEMEREX_only_reviews, $THEMEREX_only_video, $THEMEREX_only_audio, $THEMEREX_only_gallery;

$counters = get_theme_option("blog_counters");
$blog_style = get_custom_option('blog_style');
if (!in_array($blog_style, array('puzzles', 'excerpt', 'fullpost'))) $blog_style = 'puzzles';
$show_sidebar_main = get_custom_option('show_sidebar_main');
$thumb_size = array(
	'puzzles'  => array('w' => 310, 'h' => 310),
	'excerpt'  => array('w' => 466, 'h' => 310),
	'fullpost' => array('w' => $show_sidebar_main=='fullwidth' ? 1243 : 932, 'h' => 465),
);
$ppp = (int) get_custom_option('posts_per_page');
?>
	<div id="main_inner" class="clearboth blog_style_<?php echo $blog_style; ?>">
		<div id="content" class="content_blog" role="main" itemscope itemtype="http://schema.org/Blog">
		<?php
			global $wp_query, $post;

			$page_number = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
			$wp_query_need_restore = false;
			
			$args = $wp_query->query_vars;

			if ( is_page() || isset($THEMEREX_only_reviews) || isset($THEMEREX_only_video) || isset($THEMEREX_only_audio) || isset($THEMEREX_only_gallery) ) {
				$args['post_type'] = 'post';
				$args['post_status'] = current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish';
				unset($args['p']);
				unset($args['page_id']);
				unset($args['pagename']);
				unset($args['name']);
				$args['posts_per_page'] = $ppp;
				if ($page_number > 1) {
					$args['paged'] = $page_number;
					$args['ignore_sticky_posts'] = 1;
				}
				if (isset($THEMEREX_only_reviews)) {
					$args['meta_query'] = array(
						   array(
							   'key' => 'reviews_avg',
							   'value' => 0,
							   'compare' => '>',
							   'type' => 'NUMERIC'
						   )
					);
				} else if (isset($THEMEREX_only_video)) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'post_format',
							'field' => 'slug',
							'terms' => array( 'post-format-video' )
						)
					);
				} else if (isset($THEMEREX_only_audio)) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'post_format',
							'field' => 'slug',
							'terms' => array( 'post-format-audio' )
						)
					);
				} else if (isset($THEMEREX_only_gallery)) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'post_format',
							'field' => 'slug',
							'terms' => array( 'post-format-gallery' )
						)
					);
				}
				$args = addSortOrderInQuery($args);
				query_posts( $args );
				$wp_query_need_restore = true;
			}

			$per_page = count($wp_query->posts);
			$post_number = 0;

			/*
			global $more;
			$oldmore = $more;
			$more = 0;
			*/
			
			$addViewMoreClass = false;

			$parent_cat_id = (int) get_custom_option('category_id');

			while ( have_posts() ) : the_post();
				require(get_template_directory() . '/template-blog-loop.php');
			endwhile; 
			
			// $more = $oldmore;
			
			if (!$post_number) { 
				if (is_404()) {
					require(get_template_directory() . '/template-blog-404.php');
				} else if (is_search()) {
					require(get_template_directory() . '/template-blog-no-search-results.php');
				} else {
					require(get_template_directory() . '/template-blog-no-articles.php');
				}
			} else {
				$pagination_style = get_theme_option('blog_pagination');
				if (in_array($pagination_style, array('viewmore', 'infinite'))) {
					if ($page_number < $wp_query->max_num_pages) {
						?>
						<div id="viewmore" class="pagination_<?php echo $pagination_style; ?>">
							<a href="#" id="viewmore_link" class="theme_button view_more_button"><span class="icon-spin3 animate-spin viewmore_loading"></span><span class="viewmore_text_1"><?php _e('View more', 'themerex'); ?></span><span class="viewmore_text_2"><?php _e('Loading ...', 'themerex'); ?></span></a>
							<input type="hidden" value="<?php echo $page_number; ?>" id="viewmore_page" name="viewmore_page" />
							<input type="hidden" value="<?php echo htmlspecialchars(serialize($args)); ?>" id="viewmore_data" name="viewmore_data" />
							<input type="hidden" value="<?php echo htmlspecialchars(serialize(array(
								'blog_style' => $blog_style,
								'show_sidebar_main' => $show_sidebar_main,
								'parent_cat_id' => $parent_cat_id,
								'ppp' => $ppp
								))); ?>" id="viewmore_vars" name="viewmore_vars" />
						</div>
						<?php
					}
				} else
					showPagination(array('class'=>'theme_paginaton'));
			}

			if ( $wp_query_need_restore ) wp_reset_query();
			wp_reset_postdata();
		?>

		</div><!-- #content -->

		<?php get_sidebar(); ?>

	</div><!-- #main_inner -->

<?php get_footer(); ?>
