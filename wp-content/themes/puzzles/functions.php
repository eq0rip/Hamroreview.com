<?php
/**
 * Puzzles theme functions and definitions
 *
 * @package puzzles
 */

/* ========================= Filters and action handlers ============================== */

/* PRE_QUERY - add filter to main query */
add_filter('posts_where', 'themerex_filter_where');  
if ( !function_exists( 'themerex_filter_where' ) ) {
	function themerex_filter_where($where = '') { 
		global $wpdb, $wp_query; 
		if (is_admin() || $wp_query->is_attachment()) return $where;
		$prv = current_user_can('read_private_pages') && current_user_can('read_private_posts') ? " OR {$wpdb->posts}.post_status='private'" : '';
		if (themerex_strpos($where, 'post_status')===false && (!isset($_REQUEST['preview']) || $_REQUEST['preview']!='true')) $where .= " AND ".(!empty($prv) ? '(' : '')."{$wpdb->posts}.post_status='publish'".(!empty($prv) ? $prv : '').(!empty($prv) ? ')' : '');
		return $where;
	}  
}

/* PRE QUERY - posts per page selector */
add_action( 'pre_get_posts', 'themerex_posts_per_page_selector' );
if ( !function_exists( 'themerex_posts_per_page_selector' ) ) {
	function themerex_posts_per_page_selector($query) {
		if (is_admin() || !$query->is_main_query()) return;
		$orderby_set = true;
		$orderby = get_theme_option('blog_sort');
		$order = get_theme_option('blog_order');
		// Set posts per page
		$ppp = (int) get_theme_option('posts_per_page');
		$ppp2 = 0;
		if ( $query->is_category() ) {
			$cat = (int) $query->get('cat');
			if (empty($cat))
				$cat = $query->get('category_name');
			if (!empty($cat)) {
				//$ppp2 = (int) getCategoryInheritedProperty($cat, 'posts_per_page', 0);
				$cat_options = getCategoryInheritedProperties($cat);
				if (isset($cat_options['posts_per_page']) && !empty($cat_options['posts_per_page']) && $cat_options['posts_per_page']!='default') $ppp2 = (int) $cat_options['posts_per_page'];
				if (isset($cat_options['blog_sort']) && !empty($cat_options['blog_sort']) && $cat_options['blog_sort']!='default') $orderby = $cat_options['blog_sort'];
				if (isset($cat_options['blog_order']) && !empty($cat_options['blog_order']) && $cat_options['blog_order']!='default') $order = $cat_options['blog_order'];
			}
		} else {
			if ($query->get('post_type')=='product' || $query->get('product_cat')!='' || $query->get('product_tag')!='') {
				$orderby_set = false;
				$page_id = get_option('woocommerce_shop_page_id');
			} else if ($query->is_archive())
				$page_id = getTemplatePageId('archive');
			else if ($query->is_search()) {
				$page_id = getTemplatePageId('search');
			} else if ($query->is_posts_page==1)
				$page_id = isset($query->queried_object_id) ? $query->queried_object_id : getTemplatePageId('template-blog');
			else
				$page_id = 0;
			if ($page_id > 0) {
				$post_options = get_post_meta($page_id, 'post_custom_options', true);
				if (isset($post_options['posts_per_page']) && !empty($post_options['posts_per_page']) && $post_options['posts_per_page']!='default' ) $ppp2 = (int) $post_options['posts_per_page'];
				if ($orderby_set) {
					if (isset($post_options['blog_sort']) && !empty($post_options['blog_sort']) && $post_options['blog_sort']!='default') $orderby = $post_options['blog_sort'];
					if (isset($post_options['blog_order']) && !empty($post_options['blog_order']) && $post_options['blog_order']!='default') $order = $post_options['blog_order'];
				}
			}
		}
		if ($ppp2 > 0)	$ppp = $ppp2;
		if ($ppp > 0) 	$query->set('posts_per_page', $ppp );
		if ($orderby_set) addSortOrderInQuery($query, $orderby, $order);
		// Exclude categories
		$ex = get_theme_option('exclude_cats');
		if (!empty($ex))
			$query->set('category__not_in', explode(',', $ex) );
	}
}

/* Filter categories list */
add_action( 'widget_categories_args', 'themerex_categories_args_filter' );
add_action( 'widget_categories_dropdown_args', 'themerex_categories_args_filter' );
if ( !function_exists( 'themerex_categories_args_filter' ) ) {
	function themerex_categories_args_filter($args) {
		$ex = get_theme_option('exclude_cats');
		if (!empty($ex)) {
			$args['exclude'] = $ex;
		}
		return $args;
	}
}

/* Exclude post from categories */
add_action( 'widget_posts_args', 'themerex_posts_args_filter' );
if ( !function_exists( 'themerex_posts_args_filter' ) ) {
	function themerex_posts_args_filter($args) {
		$ex = get_theme_option('exclude_cats');
		if (!empty($ex)) {
			$args['category__not_in'] = explode(',', $ex);
		}
		return $args;
	}
}

add_filter( 'widget_text', 'themerex_widget_text_filter' );
if ( !function_exists( 'themerex_widget_text_filter' ) ) {
	function themerex_widget_text_filter( $text ){
		if (get_custom_option('substitute_gallery')=='yes') {
			$text = substituteGallery($text, 0, 275, 200);
		}
		$text = do_shortcode($text);
		if (get_custom_option('substitute_video')=='yes') {
			$text = substituteVideo($text, 275, 200);
		}
		if (get_custom_option('substitute_audio')=='yes') {
			$text = substituteAudio($text);
		}
		return $text;	
	}
}

// Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
add_filter( 'wp_page_menu_args', 'themerex_page_menu_args' );
if ( !function_exists( 'themerex_page_menu_args' ) ) {
	function themerex_page_menu_args( $args ) {
		$args['show_home'] = true;
		return $args;
	}
}

// Adds custom classes to the array of body classes.
add_filter( 'body_class', 'themerex_body_classes' );
if ( !function_exists( 'themerex_body_classes' ) ) {
	function themerex_body_classes( $classes ) {
		// Adds a class of group-blog to blogs with more than 1 published author
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}
	
		return $classes;
	}
}

// Filters wp_title to print a neat <title> tag based on what is being viewed.
add_filter( 'wp_title', 'themerex_wp_title', 10, 2 );
if ( !function_exists( 'themerex_wp_title' ) ) {
	function themerex_wp_title( $title, $sep ) {
		global $page, $paged;
		if ( is_feed() ) return $title;
		// Add the blog name
		$title .= get_bloginfo( 'name' );
		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title .= " $sep $site_description";
		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			$title .= " $sep " . sprintf( __( 'Page %s', 'themerex' ), max( $paged, $page ) );
		return $title;
	}
}

// Add class "widget-number-#' for each widget
add_filter('dynamic_sidebar_params', 'themerex_add_widget_number', 10, 1);
if ( !function_exists( 'themerex_add_widget_number' ) ) {
	function themerex_add_widget_number($prm) {
		if (is_admin()) return $prm;
		static $num=0, $last_id='';
		if ($last_id != $prm[0]['id']) {
			$num = 0;
			$last_id = $prm[0]['id'];
		}
		$num++;
		$prm[0]['before_widget'] = str_replace(' class="', ' class="widget-number-'.$num.' ', $prm[0]['before_widget']);
		return $prm;
	}
}

// Add main menu classes
add_filter('wp_nav_menu_objects', 'themerex_nav_menu_classes', 10, 2);
if ( !function_exists( 'themerex_nav_menu_classes' ) ) {
	function themerex_nav_menu_classes($items, $args) {
		if (is_admin()) return $items;
		if ($args->menu_id == 'mainmenu' && get_theme_option('menu_colored')=='yes') {
			foreach($items as $k=>$item) {
				if ($item->menu_item_parent==0) {
					if ($item->type=='taxonomy' && $item->object=='category') {
						$cur_theme = getCategoryInheritedProperty($item->object_id, 'blog_theme');
						if (!empty($cur_theme) && $cur_theme!='default')
							$items[$k]->classes[] = 'theme_'.$cur_theme;
					}
				}
			}
		}
		return $items;
	}
}


/* ========================= AJAX queries section ============================== */

// Get attachment url
add_action('wp_ajax_get_attachment_url', 'themerex_callback_get_attachment_url');
add_action('wp_ajax_nopriv_get_attachment_url', 'themerex_callback_get_attachment_url');
if ( !function_exists( 'themerex_callback_get_attachment_url' ) ) {
	function themerex_callback_get_attachment_url() {
		global $_REQUEST;
		
		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'ajax_nonce' ) )
			die();
	
		$response = array('error'=>'');
		
		$id = (int) $_REQUEST['attachment_id'];
		
		$response['data'] = wp_get_attachment_url($id);
		
		echo json_encode($response);
		die();
	}
}

// Send contact form data
add_action('wp_ajax_send_contact_form', 'themerex_callback_send_contact_form');
add_action('wp_ajax_nopriv_send_contact_form', 'themerex_callback_send_contact_form');
if ( !function_exists( 'themerex_callback_send_contact_form' ) ) {
	function themerex_callback_send_contact_form() {
		global $_REQUEST;
	
		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'ajax_nonce' ) )
			die();
	
		$response = array('error'=>'');
	
		$user_name = themerex_substr($_REQUEST['user_name'], 0, 20);
		$user_email = themerex_substr($_REQUEST['user_email'], 0, 60);
		$user_msg = getShortString($_REQUEST['user_msg'], 300);
	
		if (!($contact_email = get_theme_option('contact_email')) && !($contact_email = get_theme_option('admin_email'))) 
			$response['error'] = __('Unknown admin email!', 'themerex');
		else {
			$subj = sprintf(__('Site %s - Contact form message from %s', 'themerex'), get_bloginfo('site_name'), $user_name);
			$msg = "
".__('Name:', 'themerex')." $user_name
".__('E-mail:', 'themerex')." $user_email
	
".__('Message:', 'themerex')." $user_msg
	
............ " . get_bloginfo('site_name') . " (" . home_url() . ") ............";

/*	
			$head = "Content-Type: text/plain; charset=\"utf-8\"\n"
				. "X-Mailer: PHP/" . phpversion() . "\n"
				. "Reply-To: $user_email\n"
				. "To: $contact_email\n"
				. "From: $user_email\n"
				. "Subject: $subj\n";
*/
	
			if (!@wp_mail($contact_email, $subj, $msg)) {	//, $head
				$response['error'] = __('Error send message!', 'themerex');
			}
		
			echo json_encode($response);
			die();
		}
	}
}

// Check categories for portfolio style
add_action('wp_ajax_check_reviews_criterias', 'themerex_callback_check_reviews_criterias');
add_action('wp_ajax_nopriv_check_reviews_criterias', 'themerex_callback_check_reviews_criterias');
if ( !function_exists( 'themerex_callback_check_reviews_criterias' ) ) {
	function themerex_callback_check_reviews_criterias() {
		global $_REQUEST;
		
		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'ajax_nonce' ) )
			die();
	
		$response = array('error'=>'', 'criterias' => '');
		
		$ids = explode(',', $_REQUEST['ids']);
		if (count($ids) > 0) {
			foreach ($ids as $id) {
				$id = (int) $id;
				$prop = getCategoryInheritedProperty($id, 'reviews_criterias');
				if (!empty($prop) && $prop!='default' && themerex_substr(trim($prop), 0, 1)!='|') {
					$response['criterias'] = $prop;
					break;
				}
			}
		}
		
		echo json_encode($response);
		die();
	}
}

// Check categories for portfolio style
add_action('wp_ajax_reviews_users_accept', 'themerex_callback_reviews_users_accept');
add_action('wp_ajax_nopriv_reviews_users_accept', 'themerex_callback_reviews_users_accept');
if ( !function_exists( 'themerex_callback_reviews_users_accept' ) ) {
	function themerex_callback_reviews_users_accept() {
		global $_REQUEST;
		
		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'ajax_nonce' ) )
			die();
	
		$response = array('error'=>'');
		
		$post_id = $_REQUEST['post_id'];
		if ($post_id > 0) {
			$marks = $_REQUEST['marks'];
			$users = $_REQUEST['users'];
			$avg = getReviewsRatingAverage($marks);
			update_post_meta($post_id, 'reviews_marks2', marksToSave($marks));
			update_post_meta($post_id, 'reviews_avg2', marksToSave($avg));
			update_post_meta($post_id, 'reviews_users', $users);
		} else {
			$response['error'] = __('Bad post ID', 'themerex');
		}
		
		echo json_encode($response);
		die();
	}
}


// New user registration
add_action('wp_ajax_registration_user', 'themerex_callback_registration_user');
add_action('wp_ajax_nopriv_registration_user', 'themerex_callback_registration_user');
if ( !function_exists( 'themerex_callback_registration_user' ) ) {
	function themerex_callback_registration_user() {
		global $_REQUEST;
	
		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'ajax_nonce' ) ) {
			die();
		}
	
		$user_name  = themerex_substr($_REQUEST['user_name'], 0, 20);
		$user_email = themerex_substr($_REQUEST['user_email'], 0, 60);
		$user_pwd   = themerex_substr($_REQUEST['user_pwd'], 0, 20);
		$user_role  = (int) $_REQUEST['user_role'];
		$user_msg   = themerex_substr($_REQUEST['user_msg'], 0, 300);
	
		$response = array('error' => '');
	
		$id = wp_insert_user( array ('user_login' => $user_name, 'user_pass' => $user_pwd, 'user_email' => $user_email) );
		if ( is_wp_error($id) ) {
			$response['error'] = $id->get_error_message();
		} else if ($user_role==2 && (($contact_email = get_theme_option('contact_email')) || ($contact_email = get_theme_option('admin_email')))) {
			$subj = sprintf(__('Site %s - New author registration: %s', 'themerex'), get_bloginfo('site_name'), $user_name);
			$msg = "
	".__('New registration: user want be an author. Registration data:', 'themerex')."

	".__('Name:', 'themerex')." $user_name
	".__('E-mail:', 'themerex')." $user_email

	".__('Message:', 'themerex')." $user_msg
	
	............ " . get_bloginfo('site_name') . " (" . home_url() . ") ............";
	
			$head = "Content-Type: text/plain; charset=\"utf-8\"\n"
				. "X-Mailer: PHP/" . phpversion() . "\n"
				. "Reply-To: $user_email\n"
				. "To: $contact_email\n"
				. "From: $user_email\n"
				. "Subject: $subj\n";
	
			@wp_mail($contact_email, $subj, $msg, $head);
		}
	
		echo json_encode($response);
		die();
	}
}

// Get next page on blog streampage
add_action('wp_ajax_view_more_posts', 'themerex_callback_view_more_posts');
add_action('wp_ajax_nopriv_view_more_posts', 'themerex_callback_view_more_posts');
if ( !function_exists( 'themerex_callback_view_more_posts' ) ) {
	function themerex_callback_view_more_posts() {
		global $_REQUEST, $post, $wp_query, $more;
		
		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'ajax_nonce' ) )
			die();
	
		$response = array('error'=>'', 'data' => '', 'no_more_data' => 0);
		
		$page = $_REQUEST['page'];
		$args = unserialize(stripslashes($_REQUEST['data']));
		$vars = unserialize(stripslashes($_REQUEST['vars']));
	
		if ($page > 0 && is_array($args) && is_array($vars)) {
			extract($vars);
			$thumb_size = array(
				'puzzles'  => array('w' => 310, 'h' => 310),
				'excerpt'  => array('w' => 466, 'h' => 310),
				'fullpost' => array('w' => $show_sidebar_main=='fullwidth' ? 1243 : 932, 'h' => 465),
			);
			$counters = get_theme_option("blog_counters");
			$args['page'] = $page;
			$args['paged'] = $page;
			$args['ignore_sticky_posts'] = 1;
			if (!isset($wp_query))
				$wp_query = new WP_Query( $args );
			else
				query_posts($args);
			$per_page = count($wp_query->posts);
			$response['no_more_data'] = $page>=$wp_query->max_num_pages;	//$per_page < $ppp;
			$post_number = 0;
			$more = 0;
			$addViewMoreClass = true;
			ob_start();
			while ( have_posts() ) { the_post();
				require(get_template_directory() . '/template-blog-loop.php');
			}
			$response['data'] = ob_get_contents();
			ob_end_clean();
		} else {
			$response['error'] = __('Wrong query arguments', 'themerex');
		}
		
		echo json_encode($response);
		die();
	}
}



/* ========================= Custom lists (sidebars, styles, etc) ============================== */


// Return list of categories
if ( !function_exists( 'getCategoriesList' ) ) {
	function getCategoriesList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$args = array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false );
		$categories = get_categories( $args );
		foreach ($categories as $cat) {
			$list[$cat->term_id] = $cat->name;
		}
		return $list;
	}
}

// Return sliders list, prepended default and main sidebars item (if need)
if ( !function_exists( 'getSlidersList' ) ) {
	function getSlidersList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list["flex"] = __("Flexslider", 'themerex');
		if (is_plugin_active('revslider/revslider.php'))
			$list["revo"] = __("Revolution slider", 'themerex');
		return $list;
	}
}

// Return custom sidebars list, prepended default and main sidebars item (if need)
if ( !function_exists( 'getSidebarsList' ) ) {
	function getSidebarsList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list['sidebar-main']   = __("Main sidebar", 'themerex');
		$list['sidebar-advert'] = __("Advertisement sidebar", 'themerex');
		$list['sidebar-footer'] = __("Footer sidebar", 'themerex');
		$sidebars = explode(',', get_theme_option('custom_sidebars'));
		for ($i=0; $i<count($sidebars); $i++) {
			if (trim(chop($sidebars[$i]))=='') continue;
			$sb = explode('|', $sidebars[$i]);
			if (count($sb)==1) $sb[1] = $i+1;
			$list['custom-sidebar-'.$sb[1]] = $sb[0];
		}
		return $list;
	}
}

// Return sidebars positions
if ( !function_exists( 'getSidebarsPositions' ) ) {
	function getSidebarsPositions($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list['right'] = __('Right', 'themerex');
		$list['left'] = __('Left', 'themerex');
		$list['fullwidth'] = __('Hide (without sidebar)', 'themerex');
		return $list;
	}
}

// Return sidebar class
if ( !function_exists( 'getSidebarClass' ) ) {
	function getSidebarClass($position) {
		if ($position == 'fullwidth')
			$class = 'without_sidebar';
		else if ($position == 'left')
			$class = 'with_sidebar left_sidebar';
		else
			$class = 'with_sidebar right_sidebar';
		return $class;
	}
}

// Return blog styles list, prepended default
if ( !function_exists( 'getBlogStylesList' ) ) {
	function getBlogStylesList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list['puzzles']  = __('Blog Puzzles',  'themerex');
		$list['excerpt']  = __('Blog Excerpt',  'themerex');
		$list['fullpost'] = __('Blog Fullpost', 'themerex');
		return $list;
	}
}

// Return body styles list, prepended default
if ( !function_exists( 'getBodyStylesList' ) ) {
	function getBodyStylesList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list['boxed']  = __('Boxed',  'themerex');
		$list['wide']  = __('Wide',  'themerex');
		$list['fullwidth'] = __('Fullwidth', 'themerex');
		return $list;
	}
}

// Return themes list
if ( !function_exists( 'getThemesList' ) ) {
	function getThemesList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = 'default';
		$dir = get_template_directory() . "/css/themes";
		if ( is_dir($dir) ) {
			$hdir = @ opendir( $dir );
			if ( $hdir ) {
				while (($file = readdir( $hdir ) ) !== false ) {
					$pi = pathinfo( $dir . '/' . $file );
					if ( substr($file, 0, 1) == '.' || is_dir( $dir . '/' . $file ) || $pi['extension'] != 'css' )
						continue;
					$key = themerex_substr($file, 0, themerex_strrpos($file, '.'));
					$list[$key] = themerex_strtoproper(str_replace('_', ' ', $key));
				}
				@closedir( $hdir );
			}
		}
		return $list;
	}
}

// Return socials list
if ( !function_exists( 'getSocialsList' ) ) {
	function getSocialsList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = 'default';
		return array_merge($list, getListFiles("/images/socials", "png"));
	}
}

// Return puzzles positions list
if ( !function_exists( 'getPuzzlesList' ) ) {
	function getPuzzlesList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = 'default';
		return array_merge($list, getListFiles("/images/puzzles", "png"));
	}
}

// Return list with 'Yes' and 'No' items
if ( !function_exists( 'getYesNoList' ) ) {
	function getYesNoList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list["yes"] = __("Yes", 'themerex');
		$list["no"]  = __("No", 'themerex');
		return $list;
	}
}

// Return list with 'Show' and 'Hide' items
if ( !function_exists( 'getShowHideList' ) ) {
	function getShowHideList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list["show"] = __("Show", 'themerex');
		$list["hide"] = __("Hide", 'themerex');
		return $list;
	}
}

// Return list with 'Ascending' and 'Descending' items
if ( !function_exists( 'getOrderingList' ) ) {
	function getOrderingList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list["asc"] = __("Ascending", 'themerex');
		$list["desc"] = __("Descending", 'themerex');
		return $list;
	}
}

// Return list with 'Horizontal' and 'Vertical' items
if ( !function_exists( 'getDirectionList' ) ) {
	function getDirectionList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list["horizontal"] = __("Horizontal", 'themerex');
		$list["vertical"] = __("Vertical", 'themerex');
		return $list;
	}
}

// Return sorting list items
if ( !function_exists( 'getSortingList' ) ) {
	function getSortingList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = __("Inherit", 'themerex');
		$list["date"] = __("Date", 'themerex');
		$list["alpha"] = __("Alphabetically", 'themerex');
		$list["views"] = __("Popular (views count)", 'themerex');
		$list["comments"] = __("Most commented (comments count)", 'themerex');
		$list["author_rating"] = __("Author rating", 'themerex');
		$list["users_rating"] = __("Visitors (users) rating", 'themerex');
		$list["random"] = __("Random", 'themerex');
		return $list;
	}
}

// Return post-format name
if ( !function_exists( 'getPostFormatName' ) ) {
	function getPostFormatName($format, $single=true) {
		$name = '';
		if ($format=='gallery')		$name = $single ? __('gallery', 'themerex') : __('galleries', 'themerex');
		else if ($format=='video')	$name = $single ? __('video', 'themerex') : __('videos', 'themerex');
		else if ($format=='audio')	$name = $single ? __('audio', 'themerex') : __('audios', 'themerex');
		else if ($format=='image')	$name = $single ? __('image', 'themerex') : __('images', 'themerex');
		else if ($format=='quote')	$name = $single ? __('quote', 'themerex') : __('quotes', 'themerex');
		else if ($format=='link')	$name = $single ? __('link', 'themerex') : __('links', 'themerex');
		else if ($format=='status')	$name = $single ? __('status', 'themerex') : __('statuses', 'themerex');
		else if ($format=='aside')	$name = $single ? __('aside', 'themerex') : __('asides', 'themerex');
		else if ($format=='chat')	$name = $single ? __('chat', 'themerex') : __('chats', 'themerex');
		else						$name = $single ? __('standard', 'themerex') : __('standards', 'themerex');
		return $name;
	}
}

// Return post-format icon name (from Fontello library)
if ( !function_exists( 'getPostFormatIcon' ) ) {
	function getPostFormatIcon($format) {
		$icon = 'icon-';
		if ($format=='gallery')		$icon .= 'camera';
		else if ($format=='video')	$icon .= 'video';
		else if ($format=='audio')	$icon .= 'note';
		else if ($format=='image')	$icon .= 'newspaper';
		else if ($format=='quote')	$icon .= 'quote';
		else if ($format=='link')	$icon .= 'link';
		else if ($format=='status')	$icon .= 'tag';
		else if ($format=='aside')	$icon .= 'book-open';
		else if ($format=='chat')	$icon .= 'list';
		else						$icon .= 'doc-text';
		return $icon;
	}
}

// Return Google fonts list
if ( !function_exists( 'getThemeFontsList' ) ) {
	function getThemeFontsList($prepend_default=true) {
		$list = array();
		if ($prepend_default) $list['default'] = 'default';
		$list['Advent Pro'] = array('family'=>'sans-serif', 'link'=>'Advent+Pro:400,500,700');
		$list['Arimo'] = array('family'=>'sans-serif', 'link'=>'Arimo:400,400italic,700,700italic');
		$list['Asap'] = array('family'=>'sans-serif', 'link'=>'Asap:400,400italic,700,700italic');
		$list['Averia Sans Libre'] = array('family'=>'cursive', 'link'=>'Averia+Sans+Libre:400,400italic,700,700italic');
		$list['Averia Serif Libre'] = array('family'=>'cursive', 'link'=>'Averia+Serif+Libre:400,400italic,700,700italic');
		$list['Cabin'] = array('family'=>'sans-serif', 'link'=>'Cabin:400,500,400italic,500italic,700,700italic');
		$list['Cabin Condensed'] = array('family'=>'sans-serif', 'link'=>'Cabin+Condensed:400,500,700');
		$list['Caudex'] = array('family'=>'serif', 'link'=>'Caudex:400,700,400italic,700italic');
		$list['Comfortaa'] = array('family'=>'cursive', 'link'=>'Comfortaa:400,700');
		$list['Cousine'] = array('family'=>'sans-serif', 'link'=>'Cousine:400,400italic,700,700italic');
		$list['Crimson Text'] = array('family'=>'serif', 'link'=>'Crimson+Text:400,400italic,700italic,700');
		$list['Cuprum'] = array('family'=>'sans-serif', 'link'=>'Cuprum:400,400italic,700,700italic');
		$list['Dosis'] = array('family'=>'sans-serif', 'link'=>'Dosis:400,500,700');
		$list['Economica'] = array('family'=>'sans-serif', 'link'=>'Economica:400,700');
		$list['Exo'] = array('family'=>'sans-serif', 'link'=>'Exo:400,400italic,500,500italic,700,700italic');
		$list['Expletus Sans'] = array('family'=>'cursive', 'link'=>'Expletus+Sans:400,500,400italic,500italic,700,700italic');
		$list['Karla'] = array('family'=>'sans-serif', 'link'=>'Karla:400,400italic,700,700italic');
		$list['Lato'] = array('family'=>'sans-serif', 'link'=>'Lato:400,400italic,700,700italic');
		$list['Lekton'] = array('family'=>'sans-serif', 'link'=>'Lekton:400,700,400italic');
		$list['Lobster Two'] = array('family'=>'cursive', 'link'=>'Lobster+Two:400,700,400italic,700italic');
		$list['Maven Pro'] = array('family'=>'sans-serif', 'link'=>'Maven+Pro:400,500,700');
		$list['Merriweather'] = array('family'=>'serif', 'link'=>'Merriweather:400,400italic,700,700italic');
		$list['Neuton'] = array('family'=>'serif', 'link'=>'Neuton:400,400italic,700');
		$list['Noticia Text'] = array('family'=>'serif', 'link'=>'Noticia+Text:400,400italic,700,700italic');
		$list['Old Standard TT'] = array('family'=>'serif', 'link'=>'Old+Standard+TT:400,400italic,700');
		$list['Open Sans'] = array('family'=>'sans-serif', 'link'=>'Open+Sans:400,700,400italic,700italic');
		$list['Orbitron'] = array('family'=>'sans-serif', 'link'=>'Orbitron:400,500,700');
		$list['Oswald'] = array('family'=>'sans-serif', 'link'=>'Oswald:400,700');
		$list['Overlock'] = array('family'=>'cursive', 'link'=>'Overlock:400,700,400italic,700italic');
		$list['Oxygen'] = array('family'=>'sans-serif', 'link'=>'Oxygen:400,700');
		$list['PT Serif'] = array('family'=>'serif', 'link'=>'PT+Serif:400,400italic,700,700italic');
		$list['Puritan'] = array('family'=>'sans-serif', 'link'=>'Puritan:400,400italic,700,700italic');
		$list['Raleway'] = array('family'=>'sans-serif', 'link'=>'Raleway:400,500,700');
		$list['Roboto'] = array('family'=>'sans-serif', 'link'=>'Roboto:400,400italic,500,700,500italic,700italic');
		$list['Roboto Condensed'] = array('family'=>'sans-serif', 'link'=>'Roboto+Condensed:400,400italic,700,700italic');
		$list['Rosario'] = array('family'=>'sans-serif', 'link'=>'Rosario:400,400italic,700,700italic');
		$list['Share'] = array('family'=>'cursive', 'link'=>'Share:400,400italic,700,700italic');
		$list['Signika Negative'] = array('family'=>'sans-serif', 'link'=>'Signika+Negative:400,700');
		$list['Tinos'] = array('family'=>'serif', 'link'=>'Tinos:400,400italic,700,700italic');
		$list['Ubuntu'] = array('family'=>'sans-serif', 'link'=>'Ubuntu:400,400italic,500,500italic,700,700italic');
		$list['Vollkorn'] = array('family'=>'serif', 'link'=>'Vollkorn:400,400italic,700,700italic');
		return $list;
	}
}



/* ========================= Reviews ============================== */

// Get average review rating
if ( !function_exists( 'getReviewsRatingAverage' ) ) {
	function getReviewsRatingAverage($marks) {
		$r = explode(',', $marks);
		$rez = 0;
		$cnt = 0;
		foreach ($r as $v) {
			$rez += $v;
			$cnt++;
		}
		return $cnt > 0 ? round($rez / $cnt, 1) : 0;
	}
}

// Get word-value review rating
if ( !function_exists( 'getReviewsRatingWordValue' ) ) {
	function getReviewsRatingWordValue($r, $words = '') {
		$maxLevel = max(5, (int) get_custom_option('reviews_max_level'));
		if (trim($words) == '') $words = get_theme_option('reviews_criterias_levels');
		$words = explode(',', $words);
		$k = $maxLevel / count($words);
		$r = max(0, min(count($words)-1, floor($r/$k)));
		return isset($words[$r]) ? trim($words[$r]) : __('no rated', 'themerex');
	}
}



/* ========================= Custom options ============================== */


// Return property value from theme custom > category custom > post custom > request parameters
if ( !function_exists( 'get_custom_option' ) ) {
	function get_custom_option($name, $defa=null, $post_id=0, $cat_id=0) {
		$defaults = array('default', 'inherit');
		if ($cat_id > 0) {
			$rez = getCategoryInheritedProperty($cat_id, $name);
			if ($rez=='') $rez = get_theme_option($name, $defa);
		} else if ($post_id > 0) {
			$rez = get_theme_option($name, $defa);
			$custom_options = get_post_meta($post_id, 'post_custom_options', true);
			if (isset($custom_options[$name]) && !in_array(themerex_strtolower($custom_options[$name]), $defaults))
				$rez = $custom_options[$name];
			else {
				$categories = getCategoriesByPostId($post_id);
				$tmp = '';
				for ($cc = 0; $cc < count($categories) && $tmp==''; $cc++) {
					$tmp = getCategoryInheritedProperty($categories[$cc]['term_id'], $name);
				}
				if ($tmp!='') $rez = $tmp;
			}
		} else {
			global $THEMEREX_post_options, $THEMEREX_cat_options, $THEMEREX_custom_options, $THEMEREX_shop_options;
			if (isset($THEMEREX_custom_options[$name])) {
				$rez = $THEMEREX_custom_options[$name];
			} else {
				$rez = get_theme_option($name, $defa);
				if (is_woocommerce_page() && isset($THEMEREX_shop_options[$name]) && !in_array(themerex_strtolower(is_array($THEMEREX_shop_options[$name]) ? $THEMEREX_shop_options[$name][0] : $THEMEREX_shop_options[$name]), $defaults)) {
					$rez = is_array($THEMEREX_shop_options[$name]) ? $THEMEREX_shop_options[$name][0] : $THEMEREX_shop_options[$name];
				}
				if (!is_single() && isset($THEMEREX_post_options[$name]) && !in_array(themerex_strtolower(is_array($THEMEREX_post_options[$name]) ? $THEMEREX_post_options[$name][0] : $THEMEREX_post_options[$name]), $defaults)) {
					$rez = is_array($THEMEREX_post_options[$name]) ? $THEMEREX_post_options[$name][0] : $THEMEREX_post_options[$name];
				}
				if (isset($THEMEREX_cat_options[$name]) && !in_array(themerex_strtolower($THEMEREX_cat_options[$name]), $defaults)) {
					$rez = $THEMEREX_cat_options[$name];
				}
				if (is_single() && isset($THEMEREX_post_options[$name]) && !in_array(themerex_strtolower(is_array($THEMEREX_post_options[$name]) ? $THEMEREX_post_options[$name][0] : $THEMEREX_post_options[$name]), $defaults)) {
					$rez = is_array($THEMEREX_post_options[$name]) ? $THEMEREX_post_options[$name][0] : $THEMEREX_post_options[$name];
				}
				if (get_theme_option('show_theme_customizer') == 'yes') {
					$tmp = getValueGPC($name, $rez);
					if (!in_array(themerex_strtolower($tmp), $defaults))
						$rez = $tmp;
				}
				$THEMEREX_custom_options[$name] = $rez;
			}
		}
		return $rez;
	}
}

// Show theme specific fields in Post (and Page) options
if ( !function_exists( 'show_custom_field' ) ) {
	function show_custom_field($field, $value) {
		$output = '';
		switch ($field['type']) {
			case 'reviews':
				$output .= getReviewsMarkup($field, $value);
				break;
			case 'mediamanager':
				wp_enqueue_media( );
				$output .= '<a id="'.$field['id'].'" class="button mediamanager"
					data-choose="'.(isset($field['multiple']) && $field['multiple'] ? __( 'Choose Images', 'themerex') : __( 'Choose Image', 'themerex')).'"
					data-update="'.(isset($field['multiple']) && $field['multiple'] ? __( 'Add to Gallery', 'themerex') : __( 'Choose Image', 'themerex')).'"
					data-multiple="'.(isset($field['multiple']) && $field['multiple'] ? 'true' : 'false').'"
					data-linked-field="'.$field['media_field_id'].'"
					onclick="showMediaManager(this); return false;"
					>' . (isset($field['multiple']) && $field['multiple'] ? __( 'Choose Images', 'themerex') : __( 'Choose Image', 'themerex')) . '</a>';
				break;
		}
		return $output;
	}
}

// Return Reviews markup html-block
if ( !function_exists( 'getReviewsMarkup' ) ) {
	function getReviewsMarkup($field, $value, $clear=false, $snippets=false) {
		$maxLevel = max(5, (int) get_custom_option('reviews_max_level'));
		$output = '
			<input type="hidden" name="reviews_id" class="reviews_id" value="'. $field['id'] . '" />
			<input type="hidden" name="criterias_list" class="criterias_list" value="'. $field['options'] . '" />
			<input type="hidden" name="marks_list" class="marks_list" value="'. $value . '" />
			<div class="reviews_data">
		';
		$criterias = explode(',', $field['options']);
		$marks = explode(',', $value);
		for ($i=0; $i<count($criterias); $i++) { 
			if (empty($criterias[$i])) continue;
			$sb = explode('|', $criterias[$i]);
			if (empty($sb[0])) continue;
			if (count($sb)==1) $sb[1] = $i+1;
			if (!isset($marks[$i]) || $marks[$i]=='') $marks[$i] = 0;
			$output .= '
				<div class="criteria_row theme_field">
					<input type="hidden" name="'.$field['id'].'[]" value="'. ($clear ? 0 : $marks[$i]) . '" />
					<span class="criteria_label theme_strong">'.$sb[0].'</span>';
			if ($maxLevel < 100) {
				$output .= '<span class="criteria_stars">';// title="'.sprintf(__('%s from %s', 'themerex'), number_format($marks[$i],1), $maxLevel).'">';
				for ($s=1; $s <= $maxLevel; $s++)
					$output .= '<span class="theme_stars" data-mark="'.$s.'"></span>';
				$output .= '</span>';
			} else {
				$output .= '<span class="criteria_stars"><span class="criteria_bar theme_rating_bar"><span class="criteria_level theme_rating_level"></span></span><span class="criteria_dragger theme_rating_drag"></span></span>';
			}
			$output .= '
					<span class="criteria_mark theme_accent_bg">' . ($clear ? '' : $marks[$i] + ($maxLevel == 100 ? '%' : '')) . '</span>
				</div>
				';
		}
		$avg = getReviewsRatingAverage($value);
		$output .= '
			</div>
			<div class="reviews_summary blog_reviews">
				<div class="criteria_summary_text criteria_row theme_field">'.(isset($field['descr']) ? $field['descr'] : '').'</div>
				<div class="criteria_summary criteria_row theme_field">
					<span class="criteria_label theme_strong">'.__('Summary', 'themerex').'</span>
					' . getReviewsSummaryStars($avg, $snippets) . '
					<span class="criteria_mark theme_accent_bg">'.$avg.(themerex_strlen($avg)==1 ? '.0' : '').'</span>
					<span class="criteria_word theme_accent_bg">'.getReviewsRatingWordValue($avg).'</span>
				</div>
			</div>
		';
		return $output;
	}
}

// Return Reviews summary stars html-block
if ( !function_exists( 'getReviewsSummaryStars' ) ) {
	function getReviewsSummaryStars($avg, $snippets=false) {
		$maxLevel = max(5, (int) get_custom_option('reviews_max_level'));
		$output = $snippets ? '<span itemscope itemprop="reviewRating" itemtype="http://schema.org/Rating"><meta itemprop="worstRating" content="0"><meta itemprop="bestRating" content="'.$maxLevel.'"><meta itemprop="ratingValue" content="'.$avg.'">' : '';
		if (get_theme_option('reviews_style')=='text')
			$output .= '<span class="theme_stars_on">' . sprintf($maxLevel<100 ? __('Rating: %s from %s', 'themerex') : __('Rating: %s', 'themerex'), number_format($avg,1).($maxLevel < 100 ? '' : '%'), $maxLevel.($maxLevel < 100 ? '' : '%')) . '</span>';
		else if ($maxLevel < 100) {
			$output .= '<span class="criteria_stars" title="'.sprintf(__('%s from %s', 'themerex'), number_format($avg,1), $maxLevel).'">
					<span class="stars_off">';
			for ($i=1; $i<=$maxLevel; $i++)
				$output .= '<span class="theme_stars"></span>';
			$output .= '</span>
					<span class="stars_on" style="width:'.($avg/$maxLevel*100).'%;">';
			for ($i=1; $i<=$maxLevel; $i++)
				$output .= '<span class="theme_stars theme_stars_on"></span>';
			$output .= '</span>
				</span>';
		} else
			$output .= '<span class="criteria_bar theme_rating_bar" title="'.sprintf($maxLevel<100 ? __('%s / %s', 'themerex') : __('%s', 'themerex'), number_format($avg,1).'%', $maxLevel.'%').'"><span class="criteria_level theme_rating_level" style="width:'.($avg/$maxLevel*100).'%;"></span></span>';
		$output .= $snippets ? '</span>' : '';
		return $output;
	}
}


// Prepare rating marks to save
if ( !function_exists( 'marksToSave' ) ) {
	function marksToSave($marks) {
		$maxLevel = max(5, (int) get_custom_option('reviews_max_level'));
		if ($maxLevel == 100) return $marks;
		$m = explode(',', $marks);
		$kol = count($m);
		for ($i=0; $i < $kol; $i++) {
			$m[$i] = round($m[$i] * 100 / $maxLevel, $kol > 1 ? 0 : 1);
		}
		return implode(',', $m);
	}
}


// Prepare rating marks to display
if ( !function_exists( 'marksToDisplay' ) ) {
	function marksToDisplay($marks) {
		$maxLevel = max(5, (int) get_custom_option('reviews_max_level'));
		if ($maxLevel == 100) return $marks;
		$m = explode(',', $marks);
		$kol = count($m);
		for ($i=0; $i < $kol; $i++) {
			$m[$i] = round($m[$i] / 100 * $maxLevel, 1);//$kol > 1 ? 0 : 1);
		}
		return implode(',', $m);
	}
}

// Update Reviews rating marks (one time)
if ( !function_exists( 'prepareReviewsMarks' ) ) {
	function prepareReviewsMarks() {
		if (get_option('wps_reviews_marks')=='prepared') return;

		$maxLevel = 5;	//max(5, (int) get_custom_option('reviews_max_level'));

		$args = array(
			'post_type' => 'post',
			'post_password' => '',
			'posts_per_page' => -1,
			'order' => 'DESC',
			'orderby' => 'date',
			'meta_query' => array(
				   array(
					   'key' => 'reviews_avg',
					   'value' => 0,
					   'compare' => '>',
					   'type' => 'NUMERIC'
				   )
			   )
		);
		query_posts($args); 
			
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				$post_id = get_the_ID();
				// Author marks
				$custom_options = get_post_meta($post_id, 'post_custom_options', true);
				if (isset($custom_options['reviews_marks'])) {
					$marks = explode(',', $custom_options['reviews_marks']);
					$new = '';
					for ($i=0; $i<count($marks); $i++)
						$new .= ($new ? ',' : '') . round($marks[$i]*100/5);
					$custom_options['reviews_marks'] = $new;
					update_post_meta($post_id, 'post_custom_options', $custom_options);
				}
				// Visitors marks
				if ( ($marks = get_post_meta($post_id, 'reviews_marks2', true)) != '' ) {
					$marks = explode(',', $marks);
					$new = '';
					for ($i=0; $i<count($marks); $i++)
						$new .= ($new ? ',' : '') . round($marks[$i]*100/5);
					update_post_meta($post_id, 'reviews_marks2', $new);
				}
				// Author avg
				if (($avg = get_post_meta($post_id, 'reviews_avg', true))!='')
					update_post_meta($post_id, 'reviews_avg', $avg*100/5);
				// Visitors avg
				if (($avg2 = get_post_meta($post_id, 'reviews_avg2', true))!='')
					update_post_meta($post_id, 'reviews_avg2', $avg2*100/5);
			}
		}
		update_option('wps_reviews_marks', 'prepared');
		wp_reset_query();
		wp_reset_postdata();
	}
}






/* ========================= Theme setup section ============================== */

/**
 * Make theme available for translation
 * Translations can be filed in the /languages/ directory
 */
load_theme_textdomain( 'themerex', get_template_directory() . '/languages' );

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 1320; /* pixels */




add_action( 'after_setup_theme', 'themerex_theme_setup' );
if ( !function_exists( 'themerex_theme_setup' ) ) {
	function themerex_theme_setup() {
		/**
		 * WP core supports
		 */
		// Add default posts and comments RSS feed links to head 
		add_theme_support( 'automatic-feed-links' );
		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		// Custom header setup
		add_theme_support( 'custom-header', array('header-text'=>false));
		// Custom backgrounds setup
		add_theme_support( 'custom-background');
		// Supported posts formats
		add_theme_support( 'post-formats', array('gallery', 'video', 'audio', 'link', 'quote', 'image') ); 
		// Add user menu
		add_theme_support('nav-menus');
		if ( function_exists( 'register_nav_menus' ) ) {
			register_nav_menus(
				array(
					'mainmenu' => 'Main Menu',
					'sidemenu' => 'Side Menu'
				)
			);
		}
		// WooCommerce Support
		add_theme_support( 'woocommerce' );
		// Editor custom stylesheet - for user
		add_editor_style('css/editor-style.css');	
		// Prepare Rating marks
		//prepareReviewsMarks();
	}
}

// TinyMCE styles selector 
/*
add_filter('tiny_mce_before_init', 'themerex_mce_add_styles');
if ( !function_exists( 'themerex_mce_add_styles' ) ) {
	function themerex_mce_add_styles($init) {
		$init['theme_advanced_buttons2_add'] = 'styleselect';
		$init['theme_advanced_styles'] = 
			  'Titles (underline)=sc_title'
		;
		return $init;
	}
}
*/
/*
// TinyMCE add buttons
add_filter( 'mce_buttons', 'themerex_mce_buttons' );
if ( !function_exists( 'themerex_mce_buttons' ) ) {
	function themerex_mce_buttons($arr) {
		return array('bold', 'italic', '|', 'bullist', 'numlist', '|', 'formatselect', 'styleselect', '|', 'link', 'unlink' );
	}
}
*/

/**
 * Register widgetized area and update sidebar with default widgets
 */
add_action( 'widgets_init', 'themerex_widgets_init' );
if ( !function_exists( 'themerex_widgets_init' ) ) {
	function themerex_widgets_init() {
		if ( function_exists('register_sidebar') ) {
			register_sidebar( array(
				'name'          => __( 'Main Sidebar', 'themerex' ),
				'id'            => 'sidebar-main',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget_title theme_title">',
				'after_title'   => '</h3>',
			) );
			register_sidebar( array(
				'name'          => __( 'Advertisement Sidebar', 'themerex' ),
				'id'            => 'sidebar-advert',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget_title theme_title">',
				'after_title'   => '</h3>',
			) );
			register_sidebar( array(
				'name'          => __( 'Footer Sidebar', 'themerex' ),
				'id'            => 'sidebar-footer',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget_title theme_title">',
				'after_title'   => '</h3>',
			) );
			// Custom sidebars
			$sidebars = explode(',', get_theme_option('custom_sidebars'));
			for ($i=0; $i<count($sidebars); $i++) {
				if (trim(chop($sidebars[$i]))=='') continue;
				$sb = explode('|', $sidebars[$i]);
				if (count($sb)==1) $sb[1] = $i+1;
				register_sidebar( array(
					'name'          => $sb[0],
					'id'            => 'custom-sidebar-'.$sb[1],
					'before_widget' => '<aside id="%1$s" class="widget %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h3 class="widget_title theme_title">',
					'after_title'   => '</h3>',
				) );		
			}
		}
	}
}

/**
 * Enqueue scripts and styles
 */
add_action( 'wp_enqueue_scripts', 'themerex_scripts' );
if ( !function_exists( 'themerex_scripts' ) ) {
	function themerex_scripts() {
		global $concatenate_scripts;
		$concatenate_scripts = get_theme_option('compose_scripts')=='yes';

		//Enqueue styles
		$fonts = getThemeFontsList(false);
		$font = get_custom_option('theme_font');
		if (isset($fonts[$font])) {
			$theme_font_link = $fonts[$font]['link'];
		} else {
			$theme_font_link = "Oxygen:400,700";
		}
		$font = get_custom_option('logo_font');
		if (isset($fonts[$font])) {
			$logo_font_link = $fonts[$font]['link'];
		} else {
			$logo_font_link = "Cabin:700";
		}
		// Must be loaded before main stylesheet
		wp_enqueue_style( 'theme-font', 'http://fonts.googleapis.com/css?family='.$theme_font_link.'&subset=latin,cyrillic-ext,latin-ext,cyrillic', array(), null );
		wp_enqueue_style( 'logo-font', 'http://fonts.googleapis.com/css?family='.$logo_font_link.'&subset=latin,cyrillic-ext,latin-ext,cyrillic', array(), null );
		wp_enqueue_style( 'fontello', get_template_directory_uri() . '/includes/fontello/css/fontello.css', array(), null);
		wp_enqueue_style( 'animation', get_template_directory_uri() . '/includes/fontello/css/animation.css', array(), null);
		// Main stylesheet
		wp_enqueue_style( 'main-style', get_stylesheet_uri(), array(), null );
		// Themes
		$themes = array();
		$themes[get_custom_option('blog_theme')] = 1;
		$themes[get_custom_option('sidebar_main_theme')] = 1;
		$themes[get_custom_option('sidebar_advert_theme')] = 1;
		$themes[get_custom_option('sidebar_footer_theme')] = 1;
		foreach($themes as $style=>$v) {
			wp_enqueue_style( 'theme-'.$style,  esc_url(get_template_directory_uri() . '/css/themes/'.$style.'.css'), array('main-style'), null );
		}
		// Shortcodes
		wp_enqueue_style( 'shortcodes',  get_template_directory_uri() . '/css/shortcodes.css', array('main-style'), null );
		// Customizer
		wp_add_inline_style( 'shortcodes', prepareThemeCustomStyles() );
		// Responsive
		if (get_theme_option('responsive_layouts') == 'yes') {
			wp_enqueue_style( 'responsive',  get_template_directory_uri() . '/css/responsive.css', array('main-style'), null );
		}
		// WooCommerce customizer
		if (function_exists('is_woocommerce')) {
			wp_enqueue_style( 'woo-style',  get_template_directory_uri() . '/css/woo-style.css', array('main-style'), null );
		}
		// BuddyPress customizer
		if ( class_exists( 'BuddyPress' ) ) {
			wp_enqueue_style( 'buddy-style',  get_template_directory_uri() . '/css/buddy-style.css', array('main-style'), null );
		}
		// BB Press customizer
		if ( class_exists( 'bbPress' ) ) {
			wp_enqueue_style( 'bbpress-style',  get_template_directory_uri() . '/css/bbpress-style.css', array('main-style'), null );
		}

		// Loads the Internet Explorer specific stylesheet.
		wp_enqueue_style( 'puzzles-ie', get_template_directory_uri() . '/css/ie.css', array( 'main-style'), null);
		wp_style_add_data( 'puzzles-ie', 'conditional', 'lt IE 10' );
		// RTL support
		if  (get_bloginfo('text_direction')=='rtl'){
		wp_enqueue_style( 'shortcodes_rtl', get_template_directory_uri().'/css/shortcodes_rtl.css' );
		wp_enqueue_style( 'bbpress_rtl', get_template_directory_uri().'/css/bbpress-style_rtl.css' );
		wp_enqueue_style( 'buddy-style_rtl', get_template_directory_uri().'/css/buddy-style_rtl.css' );
		wp_enqueue_style( 'woo-style_rtl', get_template_directory_uri().'/css/woo-style_rtl.css' );
		}
	
		wp_enqueue_script( 'jquery', false, array(), null, true );
		wp_enqueue_script( 'jquery-cookie', get_template_directory_uri().'/js/jquery.cookie.js', array('jquery'), null, true);
		wp_enqueue_script( 'jquery-easing', get_template_directory_uri().'/js/jquery.easing.js', array('jquery'), null, true );
	
		wp_enqueue_script( 'superfish', get_template_directory_uri() . '/js/superfish.js', array('jquery'), null, true );
		if (get_theme_option('responsive_layouts')=='yes') {
			wp_enqueue_script( 'mobilemenu', get_template_directory_uri().'/js/jquery.mobilemenu.min.js', array('jquery'), null, true );
		}
		if (get_theme_option('menu_slider')=='yes') {
			wp_enqueue_script( 'slidemenu', get_template_directory_uri().'/js/jquery.slidemenu.js', array('jquery'), null, true );
		}
	
		wp_enqueue_script( 'skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), null, true );
	
		wp_enqueue_script( '_utils', get_template_directory_uri() . '/js/_utils.js', array(), null, true );
		if  (get_bloginfo('text_direction')=='rtl'){wp_enqueue_script( '_front', get_template_directory_uri() . '/js/_frontrtl.js', array(), null, true );} 
			else {wp_enqueue_script( '_front', get_template_directory_uri() . '/js/_front.js', array(), null, true );}
		wp_enqueue_script( '_reviews', get_template_directory_uri() . '/js/_reviews.js', array('jquery'), null, true );
	
		wp_enqueue_style(  'prettyphoto-style', get_template_directory_uri() . '/js/prettyphoto/css/prettyPhoto.css', array(), null );
		wp_enqueue_script( 'prettyphoto', get_template_directory_uri() . '/js/prettyphoto/jquery.prettyPhoto.min.js', array('jquery'), 'no-compose', true );
	
		wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/js/jquery.flexslider.min.js', array('jquery'), null, true );
	
		if (get_theme_option('use_mediaelement')=='yes') {
			wp_enqueue_style(  'mediaplayer-style',  get_template_directory_uri() . '/js/mediaplayer/mediaelementplayer.css', array(), null );
			wp_enqueue_script( 'mediaplayer', get_template_directory_uri() . '/js/mediaplayer/mediaelement-and-player.min.js', array(), null, true );
		}

		if (is_singular() && get_theme_option('show_share')=='yes') {
			wp_enqueue_script( 'social-share', get_template_directory_uri() . '/js/social/social-share.js', array(), null, true );
		}
	
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply', false, array(), null, true );
		}
	
		if (get_theme_option('show_theme_customizer') == 'yes') {
			wp_enqueue_script( 'jquery-ui-draggable', false, array('jquery','jquery-ui-core'), null, true );
		}
	}
}



// Compose scripts in one file
$THEMEREX_scripts_collector = array('', '');
add_action('wp_print_scripts', 'themerex_compose_scripts', 10);
if ( !function_exists( 'themerex_compose_scripts' ) ) {
	function themerex_compose_scripts() {
		global $wp_scripts, $concatenate_scripts, $THEMEREX_scripts_collector;
		if (is_admin() || get_theme_option('compose_scripts')!='yes' || !is_object($wp_scripts)) return;
		//$concatenate_scripts = true;
		$theme_url = get_template_directory_uri();
		foreach($wp_scripts->queue as $script) {
			if (isset($wp_scripts->registered[$script]) && themerex_strpos($wp_scripts->registered[$script]->src, $theme_url)===0 && themerex_strpos($wp_scripts->registered[$script]->ver, 'no-compose')===false) {
				if (file_exists($file = get_template_directory().themerex_substr($wp_scripts->registered[$script]->src, themerex_strlen($theme_url)))) {
					$THEMEREX_scripts_collector[isset($wp_scripts->registered[$script]->extra['group']) && $wp_scripts->registered[$script]->extra['group']==1 ? 1 : 0] .= "\n" . file_get_contents($file) . "\n";
					$wp_scripts->done[] = $script;
				}
			}
		}
		if ($THEMEREX_scripts_collector[0]) {
			echo "\n<!-- Header scripts composer -->\n<script type=\"text/javascript\">\n".$THEMEREX_scripts_collector[0]."\n</script>\n<!-- /Header scripts composer -->\n";
		}
	}
}


// Compose scripts in one file
add_action('wp_print_footer_scripts', 'themerex_footer_scripts', 10);
if ( !function_exists( 'themerex_footer_scripts' ) ) {
	function themerex_footer_scripts() {
		if (is_admin() || get_theme_option('compose_scripts')!='yes') return;
		global $THEMEREX_scripts_collector;
		if ($THEMEREX_scripts_collector[1]) {
			echo "\n<!-- Footer scripts composer -->\n<script type=\"text/javascript\">\n".$THEMEREX_scripts_collector[1]."\n</script>\n<!-- /Footer scripts composer -->\n";
		}
	}
}


// Compose styles in one file
$THEMEREX_styles_collector = '';
add_action('wp_print_styles', 'themerex_compose_styles', 10);
if ( !function_exists( 'themerex_compose_styles' ) ) {
	function themerex_compose_styles() {
		global $wp_styles, $concatenate_scripts, $compress_css, $THEMEREX_styles_collector;
		if (is_admin() || get_theme_option('compose_scripts')!='yes' || !is_object($wp_styles)) return;
		//$concatenate_scripts = $compress_css = true;
		$theme_url = get_template_directory_uri();
		foreach($wp_styles->queue as $style) {
			if (isset($wp_styles->registered[$style]) && themerex_strpos($wp_styles->registered[$style]->src, $theme_url)===0 && themerex_strpos($wp_styles->registered[$style]->ver, 'no-compose')===false) {
				//$dir = dirname(themerex_substr($wp_styles->registered[$style]->src, themerex_strlen($wp_styles->base_url))).'/';
				$dir = dirname($wp_styles->registered[$style]->src).'/';
				if (file_exists($file = get_template_directory().themerex_substr($wp_styles->registered[$style]->src, themerex_strlen($theme_url)))) {
					$css = file_get_contents($file);
					if (isset($wp_styles->registered[$style]->extra['after'])) {
						foreach ($wp_styles->registered[$style]->extra['after'] as $add) {
							$css .= "\n" . $add . "\n";
						}
					}
					$pos = -1;
					while (($pos=themerex_strpos($css, 'url(', $pos+1))!==false) {
						if (themerex_substr($css, $pos, 9)=='url(data:') continue;
						$shift = 0;
						if (($ch=themerex_substr($css, $pos+4, 1))=='"' || $ch=="'") {
							$shift = 1;
						}
						$css = themerex_substr($css, 0, $pos+4+$shift) . $dir . themerex_substr($css, $pos+4+$shift);
					}
					$THEMEREX_styles_collector .= "\n" . $css . "\n";
					$wp_styles->done[] = $style;
				}
			}
		}
		if ($THEMEREX_styles_collector) {
			echo "\n<!-- Stylesheets composer -->\n<style type=\"text/css\">\n".$THEMEREX_styles_collector."\n</style>\n<!-- /Stylesheets composer -->\n";
		}
	}
}


// BuddyPress 1.9 hook
add_filter( 'bp_do_register_theme_directory', '__return_true' );


// WooCommerce setup
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
add_action('woocommerce_before_main_content', 'themerex_wrapper_start', 10);
if ( !function_exists( 'themerex_wrapper_start' ) ) {
	function themerex_wrapper_start() {
		$thumb_size = array(
			'fullpost' => array('w' => get_custom_option('show_sidebar_main')=='fullwidth' ? 1243 : 932, 'h' => null),
		);
		$post_id = get_option('woocommerce_shop_page_id');
		$blog_style = 'fullpost'; //get_custom_option('blog_style');
		$post_thumb = getResizedImageTag($post_id, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h'], null, true, false, true);
	?>
		<div id="main_inner" class="clearboth blog_style_<?php echo $blog_style; ?>">
			<div id="content" class="content_blog post_single" role="main">
				<article <?php post_class('theme_article post_format_standard'); ?>>
					<?php 
					// If post have thumbnail - show it
					if ( $post_thumb && get_custom_option('show_featured_image')=='yes') {
					?>
						<div class="post_thumb image_wrapper no_thumb">
							<?php echo $post_thumb; ?>
						</div>
					<?php
					}
					?>
					<div class="post_content<?php echo get_custom_option('without_paddings')=='yes' ? ' without_paddings' : ''; ?>">
	<?php
	}
}

remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);		
add_action('woocommerce_after_main_content', 'themerex_wrapper_end', 10);
if ( !function_exists( 'themerex_wrapper_end' ) ) {
	function themerex_wrapper_end() {
		if (is_product()) {
			showShareButtons(array(
				'post_id'    => get_the_ID(),
				'post_link'  => get_permalink(),
				'post_title' => getPostTitle(),
				'post_descr' => getPostDescription(),
				'post_thumb' => wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()))
			));
		}
	?>
					</div>
				</article>
			</div><!-- #content -->
			<?php get_sidebar(); ?>
		</div><!-- #main_inner -->
	<?php
	}
}

// Redefine number of related products
function woocommerce_output_related_products() {
	if (($ppp = max(3, get_custom_option('post_related_count'))) > 0)
		woocommerce_related_products($ppp, 3);
}

// Admin side setup
if (is_admin()) {
	add_action('admin_head', 'themerex_admin_setup');
	if ( !function_exists( 'themerex_admin_setup' ) ) {
		function themerex_admin_setup(){
			wp_enqueue_script('jquery', false, array(), null, true);
			wp_enqueue_script('jquery-ui-core', false, array('jquery'), null, true);
			wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
			wp_enqueue_script( 'jquery-cookie', get_template_directory_uri().'/js/jquery.cookie.js', array('jquery'), null, true);
	
			wp_enqueue_style(  'wp-color-picker', array(), null );
			wp_enqueue_script( 'wp-color-picker', false, array(), null, true );
	
			wp_enqueue_style(  'theme-admin-style',  get_template_directory_uri() . '/css/admin-style.css', array(), null );
			wp_enqueue_style( 'fontello', get_template_directory_uri() . '/includes/fontello/css/fontello.css', array(), null);
		
			wp_enqueue_script( '_utils', get_template_directory_uri() . '/js/_utils.js', array(), null, true );
			wp_enqueue_script( '_reviews', get_template_directory_uri() . '/js/_reviews.js', array('jquery'), null, true );
		}
	}

	// Add categories (taxonomies) filter for custom posts types
	add_action( 'restrict_manage_posts', 'themerex_admin_taxonomy_filter' );
	if ( !function_exists( 'themerex_admin_taxonomy_filter' ) ) {
		function themerex_admin_taxonomy_filter() {
			global $typenow;
			if (get_theme_option('admin_add_filters')!='yes' || $typenow != 'post') return;
			$taxes = array('post_format', 'post_tag');
			foreach ($taxes as $tax) {
				$tax_obj = get_taxonomy($tax);
				$terms = getTaxonomiesByPostType(array($typenow), array($tax));
				if (count($terms) > 0) {
					$tax_name = themerex_strtolower($tax_obj->labels->name);
					$tax = str_replace(array('post_tag'), array('tag'), $tax);
					echo "<select name='$tax' id='$tax' class='postform'>";
					echo "<option value=''>All $tax_name</option>";
					foreach ($terms as $term) {
						$slug = is_object($term) ? $term->slug : $term['slug'];
						$name = is_object($term) ? $term->name : $term['name'];
						$count = is_object($term) ? $term->count : $term['count'];
						echo '<option value='. $slug . ($_GET[$tax] == $slug ? ' selected="selected"' : '') . '>' . str_replace(array('post-format-'), array(''), $name) . ' (' . $count .')</option>'; 
					}
					echo "</select>";
				}
			}
		}
	}
	require_once( get_template_directory() . '/admin/theme-options.php' );
}


require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

require_once( get_template_directory() . '/includes/_debug.php' );

require_once( get_template_directory() . '/includes/_utils.php' );
require_once( get_template_directory() . '/includes/_wp_utils.php' );

require_once( get_template_directory() . '/admin/theme-settings.php' );

require_once( get_template_directory() . '/includes/theme-customizer.php' );

require_once( get_template_directory() . '/includes/aq_resizer.php' );

require_once( get_template_directory() . '/includes/type-category.php' );
require_once( get_template_directory() . '/includes/type-post.php' );
require_once( get_template_directory() . '/includes/type-page.php' );

require_once( get_template_directory() . '/includes/shortcodes.php' );
require_once( get_template_directory() . '/includes/wp-pagenavi.php' );

if (is_admin()) {
	if (get_theme_option('admin_update_notifier')=='yes') {
		require_once( get_template_directory() . '/includes/update-notifier.php' );
	}
	if (get_theme_option('admin_po_composer')=='yes') {
		require_once( get_template_directory() . '/admin/po_composer/po_composer.php' );
	}
}

require_once( get_template_directory() . '/widgets/widget-top10.php' );
require_once( get_template_directory() . '/widgets/widget-popular-posts.php' );
require_once( get_template_directory() . '/widgets/widget-recent-posts.php' );
require_once( get_template_directory() . '/widgets/widget-recent-reviews.php' );
require_once( get_template_directory() . '/widgets/widget-advert.php' );
require_once( get_template_directory() . '/widgets/widget-flickr.php' );
require_once( get_template_directory() . '/widgets/widget-socials.php' );
require_once( get_template_directory() . '/widgets/widget-twitter2.php' );
require_once( get_template_directory() . '/widgets/qrcode/widget-qrcode.php' );



?>

<?php

add_filter('the_content' , 'rating_logic');

function rating_logic($honda) {
$nirmal=get_the_ID();
if($nirmal==62 || $nirmal==250){return $honda;}
$honda="[rating]";
return $honda;
}




function my_function_admin_bar(){ return false; }
add_filter( 'show_admin_bar' , 'my_function_admin_bar');
?>