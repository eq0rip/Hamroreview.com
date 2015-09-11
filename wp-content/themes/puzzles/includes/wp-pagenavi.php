<?php
/*
Plugin Name: WP-PageNavi
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: Adds a more advanced paging navigation to your WordPress blog.
Version: 2.50
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
*/


/*  
	Copyright 2009  Lester Chan  (email : lesterchan@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Create Text Domain For Translations
//add_action('init', 'pagenavi_textdomain');
function pagenavi_textdomain() {
	load_plugin_textdomain('wp-pagenavi', false, 'wp-pagenavi');
}

### Function: Enqueue PageNavi Stylesheets
//add_action('wp_print_styles', 'pagenavi_stylesheets');
function pagenavi_stylesheets() {
	if(@file_exists(TEMPLATEPATH.'/pagenavi-css.css')) {
		wp_enqueue_style('wp-pagenavi', get_stylesheet_directory_uri().'/pagenavi-css.css', array(), null, 'all');
	} else {
		wp_enqueue_style('wp-pagenavi', plugins_url('wp-pagenavi/pagenavi-css.css'), array(), null, 'all');
	}	
}


### Function: Page Navigation: Boxed Style Paging
function themerex_wp_pagenavi($pagenavi_options) {
	global $wpdb, $wp_query;

	$page_text = '';
	$output = '';
	
	if (!is_single()) {
		$request = $wp_query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$paged = intval(get_query_var('paged'));
		if ($paged==0) $paged = intval(get_query_var('page'));
		//$pagenavi_options = get_option('pagenavi_options');
		$numposts = $wp_query->found_posts - ($pagenavi_options['offset'] > 0 ? $pagenavi_options['offset'] : 0);	//$wp_query->found_posts
		$max_page = ceil($numposts / $posts_per_page);																//$wp_query->max_num_pages;
		if(empty($paged) || $paged == 0) {
			$paged = 1;
		}
		$pages_to_show = intval($pagenavi_options['num_pages']);
		$larger_page_to_show = intval($pagenavi_options['num_larger_page_numbers']);
		$larger_page_multiple = intval($pagenavi_options['larger_page_numbers_multiple']);
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start = floor($pages_to_show_minus_1/2);
		$half_page_end = ceil($pages_to_show_minus_1/2);
		$start_page = $paged - $half_page_start;
		if($start_page <= 0) {
			$start_page = 1;
		}
		$end_page = $paged + $half_page_end;
		if(($end_page - $start_page) != $pages_to_show_minus_1) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if($start_page <= 0) {
			$start_page = 1;
		}
		$larger_per_page = $larger_page_to_show*$larger_page_multiple;
		$larger_start_page_start = (n_round($start_page, 10) + $larger_page_multiple) - $larger_per_page;
		$larger_start_page_end = n_round($start_page, 10) + $larger_page_multiple;
		$larger_end_page_start = n_round($end_page, 10) + $larger_page_multiple;
		$larger_end_page_end = n_round($end_page, 10) + ($larger_per_page);
		if($larger_start_page_end - $larger_page_multiple == $start_page) {
			$larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
			$larger_start_page_end = $larger_start_page_end - $larger_page_multiple;
		}
		if($larger_start_page_start <= 0) {
			$larger_start_page_start = $larger_page_multiple;
		}
		if($larger_start_page_end > $max_page) {
			$larger_start_page_end = $max_page;
		}
		if($larger_end_page_end > $max_page) {
			$larger_end_page_end = $max_page;
		}
		if($max_page > 1 || intval($pagenavi_options['always_show']) == 1) {
			$pages_text = str_replace("%CURRENT_PAGE%", number_format_i18n($paged), $pagenavi_options['pages_text']);
			$pages_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pages_text);
			
			$output .= $pagenavi_options['before'] . '<ul>';

			switch(intval($pagenavi_options['style'])) {
				case 1:
					if(!empty($pages_text)) {
						$output .= '<li class="pager_pages"><span>' . $pages_text . '</span></li>';
					}
					if ($start_page >= 2 && $pages_to_show < $max_page) {
						$first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['first_text']);
						$output .= '<li class="pager_first"><a href="'.esc_url(get_pagenum_link()).'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
						if(!empty($pagenavi_options['dotleft_text'])) {
							$output .= '<li class="pager_dotleft"><a href="'.esc_url(get_pagenum_link(max(1, $start_page-$pages_to_show))).'" title="'.$pagenavi_options['dotleft_text'].'">'.$pagenavi_options['dotleft_text'].'</a></li>';
						}
					}
					if($larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page) {
						for($i = $larger_start_page_start; $i < $larger_start_page_end; $i+=$larger_page_multiple) {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
						}
					}

					if ($start_page != $paged && !empty($pagenavi_options['prev_text'])) {
						$output .= '<li class="pager_prev"><a href="'.esc_url(get_pagenum_link($paged-1)).'">'.$pagenavi_options['prev_text'].'</a></li>';
					}
					
					
					for($i = $start_page; $i  <= $end_page; $i++) {

						if ($i == $paged) {
							$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
							$output .= '<li class="pager_current"><span title="'.$page_text.'">'.$current_page_text.'</span></li>';
						} else {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							$output .= '<li><a href="'.esc_url(get_pagenum_link($i)).'" title="'.$page_text.'">'.$page_text.'</a></li>';
						}
					}

					if ($end_page > $paged && !empty($pagenavi_options['next_text'])) {
						$output .= '<li class="pager_next"><a href="'.esc_url(get_pagenum_link($paged+1)).'">'.$pagenavi_options['next_text'].'</a></li>';
					}
					
					/*
					if ($larger_page_to_show > 0 && $larger_end_page_start < $max_page) {
						for ($i = $larger_end_page_start; $i <= $larger_end_page_end; $i+=$larger_page_multiple) {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text'].'....');
						}
					}
					*/
					if ($end_page < $max_page) {
						if(!empty($pagenavi_options['dotright_text'])) {
							$output .= '<li class="pager_dotright"><a href="'.esc_url(get_pagenum_link(min($max_page, $start_page+$pages_to_show))).'" title="'.$pagenavi_options['dotright_text'].'">'.$pagenavi_options['dotright_text'].'</a></li>';
						}
						$last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['last_text']);
						$output .= '<li class="pager_last"><a href="'.esc_url(get_pagenum_link($max_page)).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
					}
					break;
			}
			
			$output .= '
				</ul>
			'.$pagenavi_options['after'];
			
		}
	}
	
	return $output;
}


### Function: Page Navigation: Drop Down Menu (Deprecated)
function wp_pagenavi_dropdown() { 
	wp_pagenavi(); 
}

### Function: Round To The Nearest Value
function n_round($num, $tonearest) {
   return floor($num/$tonearest)*$tonearest;
}

### Function: Page Navigation Options
add_action('init', 'pagenavi_init');
function pagenavi_init() {
	pagenavi_textdomain();
	// Add Options
	$pagenavi_options = array();
	$pagenavi_options['pages_text']    = __('Page %CURRENT_PAGE% of %TOTAL_PAGES%', 'themerex');
	$pagenavi_options['current_text']  = '%PAGE_NUMBER%';
	$pagenavi_options['page_text']     = '%PAGE_NUMBER%';
	$pagenavi_options['first_text']    = __('First', 'themerex');
	$pagenavi_options['last_text']     = __('Last', 'themerex');
	$pagenavi_options['next_text']     = __('Next', 'themerex');
	$pagenavi_options['prev_text']     = __('Prev', 'themerex');
	$pagenavi_options['dotright_text'] = __('...', 'themerex');
	$pagenavi_options['dotleft_text']  = __('...', 'themerex');
	$pagenavi_options['style'] = 1;
	$pagenavi_options['num_pages'] = 5;
	$pagenavi_options['always_show'] = 0;
	$pagenavi_options['num_larger_page_numbers'] = 3;
	$pagenavi_options['larger_page_numbers_multiple'] = 10;
	add_option('pagenavi_options', $pagenavi_options);
}
?>