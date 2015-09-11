<?php
/*
 * The template for displaying one article of blog streampage with style "Fullpost"
 * 
 * @package puzzles
*/
?>
<?php 

$avg_author = 0;
if ( get_custom_option('show_reviews', null, $post_id)=='yes') {
	$avg_author = marksToDisplay(get_post_meta($post_id, 'reviews_avg'.(get_theme_option('reviews_first')=='author' ? '' : '2'), true));
}
?>
<article <?php post_class('theme_article post_format_'.$post_format.' '.($post_number%2==0 ? 'even' : 'odd') . ($post_number==0 ? ' first' : '') . ($post_number==$per_page? ' last' : '') . ($addViewMoreClass ? ' viewmore' : '')); ?>>
	<?php 
	if (!$post_protected) { 
		// If post have thumbnail - show it
		if ( $post_thumb ) {	// && get_custom_option('show_featured_image')=='yes'
		?>
			<div class="post_thumb image_wrapper no_thumb">
				<?php 
				if ($post_format=='link' && $post_url!='')
					echo '<a href="'.$post_url.'"'.($post_url_target ? ' target="'.$post_url_target.'"' : '').'>'.$post_thumb.'</a>';
				else if ($post_link!='')
					echo '<a href="'.$post_link.'">'.$post_thumb.'</a>';
				else
					echo $post_thumb; 
				?>
				<span class="post_format theme_accent_bg <?php echo $post_icon; ?>"<?php echo themerex_substr($post_accent_color, 0, 1)=='#' ? ' style="background-color: '.$post_accent_color.'"' : ''; ?>></span>
				<?php if (get_custom_option('puzzles_style')=='heavy' && $post_accent_category!='') { ?>
					<span class="post_category theme_accent_bg"<?php echo themerex_substr($post_accent_color, 0, 1)=='#' ? ' style="background-color: '.$post_accent_color.'"' : ''; ?>><?php echo $post_accent_category; ?></span>
				<?php } ?>
			</div>
		<?php
		}
	}
	?>
	
	<div class="post_content">
		<div class="post_info post_info_top theme_info">
			<?php _e('Posted ', 'themerex'); ?><span class="post_date theme_text"><?php echo $post_date; ?></span>
			<!-- <span class="post_info_delimiter theme_border"></span> -->
			<span class="post_author"><?php _e('by ', 'themerex'); ?><a href="<?php echo $post_author_url; ?>" class="post_author"><?php echo $post_author; ?></a></span>
			<!-- <span class="post_info_delimiter theme_border"></span> -->
			<?php if ($post_categories_str!='') { ?>
			<span class="post_cats"><?php _e('in ', 'themerex'); ?><?php echo $post_categories_str; ?></span>
			<?php } ?>
			<?php if ($counters!='none') { ?>
			<span class="post_comments"><a href="<?php echo $post_comments_link; ?>"><span class="comments_icon theme_info icon-<?php echo $counters=='comments' ? 'chat-1' : 'eye'; ?>"></span><span class="comments_number"><?php echo $counters=='comments' ? $post_comments : $post_views; ?></span></a></span>
			<?php } ?>
		</div>
				
		<?php if (!in_array($post_format, array('quote', 'aside'))) { ?>	
			<div class="title_area">
				<h1 class="post_title"><a href="<?php echo $post_link; ?>" class="theme_title"><?php echo $post_title; ?></a></h1>
				<?php
				if ($avg_author > 0) {
					?>
					<div class="reviews_summary blog_reviews">
						<div class="criteria_summary criteria_row">
							<?php echo getReviewsSummaryStars($avg_author); ?>
						</div>
					</div>
					<?php 
				}
				?>
			</div>
		<?php } ?>

		<div class="post_text_area">
		<?php
		if ($post_protected) { 
			echo $post_descr; 
		} else {
			if ($post_excerpt) {
				echo $post_excerpt;
				?>
				<a href="<?php echo $post_link; ?>" class="more-link"><?php _e('Read more', 'themerex'); ?></a>
				<?php 
			} else {
				echo $post_content; 
			}
			?>
			<div class="post_info post_info_bottom theme_info">
				<?php 
				if ($post_tags_str != '') {
				?>
					<span class="post_tags">
						<span class="tags_label"><?php _e('Tags:', 'themerex'); ?></span>
						<?php echo $post_tags_str; ?>
					</span>
				<?php 
				} // post_tags 
				?>
			</div>
		<?php } ?>
		</div>
	</div>

</article>
