<?php
/*
 * The template for displaying "No search results"
 * 
 * @package puzzles
*/
?>
<article class="theme_article post_format_standard page_no_results page_no_search odd first last">
	<span class="icon-search icon_no_results theme_link"></span>
	<div class="post_content">
		<div class="title_area">
			<h3 class="post_title theme_subtitle"><?php _e( 'Search Results for:', 'themerex' ); ?></h3>
			<h1 class="post_subtitle theme_title"><?php echo get_search_query(); ?></h1>
		</div>
		<div class="post_text_area">
			<div class="text">
				<p class="post_text">
					<?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'themerex' ); ?>
				</p>
			</div>
		</div>
	</div>
</article>
