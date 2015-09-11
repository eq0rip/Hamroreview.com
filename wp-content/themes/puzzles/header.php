<?php
/**
 * The Header for our theme.
 *
 * @package puzzles
 */

// AJAX Queries settings
global $THEMEREX_ajax_nonce, $THEMEREX_ajax_url;
$THEMEREX_ajax_nonce = wp_create_nonce('ajax_nonce');
$THEMEREX_ajax_url = admin_url('admin-ajax.php');

// Theme custom settings from current post and category
global $THEMEREX_cat_options, $THEMEREX_post_options, $THEMEREX_custom_options, $THEMEREX_shop_options, $wp_query;
// Current post & category custom options
$THEMEREX_post_options = $THEMEREX_cat_options = $THEMEREX_custom_options = $THEMEREX_shop_options = array();
if (is_woocommerce_page() && ($page_id=get_option('woocommerce_shop_page_id'))>0)
	$THEMEREX_shop_options = get_post_meta($page_id, 'post_custom_options', true);
if (is_category()) {
	$cat = (int) get_query_var( 'cat' );
	if (empty($cat)) $cat = get_query_var( 'category_name' );
	$THEMEREX_cat_options = getCategoryInheritedProperties($cat);
} else if ((is_day() || is_month() || is_year()) && ($page_id=getTemplatePageId('archive'))>0) {
	$THEMEREX_post_options = get_post_meta($page_id, 'post_custom_options', true);
} else if (is_search() && ($page_id=getTemplatePageId('search'))>0) {
	$THEMEREX_post_options = get_post_meta($page_id, 'post_custom_options', true);
} else if (is_404() && ($page_id=getTemplatePageId('404'))>0) {
	$THEMEREX_post_options = get_post_meta($page_id, 'post_custom_options', true);
} else if (function_exists('is_bbpress') && is_bbpress() && ($page_id=getTemplatePageId('bbpress'))>0) {
	$THEMEREX_post_options = get_post_meta($page_id, 'post_custom_options', true);
} else if (function_exists('is_buddypress') && is_buddypress() && ($page_id=getTemplatePageId('buddypress'))>0) {
	$THEMEREX_post_options = get_post_meta($page_id, 'post_custom_options', true);
} else if (is_single() || is_page() || is_singular() || $wp_query->is_posts_page==1) {
	// Current post custom options
	$page_id = is_single() || is_page() ? get_the_ID() : (isset($wp_query->queried_object_id) ? $wp_query->queried_object_id : getTemplatePageId('template-blog'));
	$THEMEREX_post_options = get_post_meta($page_id, 'post_custom_options', true);
	$THEMEREX_cat_options = getCategoriesInheritedProperties(getCategoriesByPostId($page_id));
}

// Reject old browsers support
global $THEMEREX_jreject;
$THEMEREX_jreject = false;
if (!isset($_COOKIE['jreject'])) {
	wp_enqueue_style(  'jquery_reject-style',  get_template_directory_uri() . '/js/jreject/css/jquery.reject.css', array(), null );
	wp_enqueue_script( 'jquery_reject', get_template_directory_uri() . '/js/jreject/jquery.reject.js', array('jquery'), null, true );
	setcookie('jreject', 1, 0, '/');
	$THEMEREX_jreject = true;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <?php if (($favicon = get_theme_option('favicon'))) { ?>
		<link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>" />
    <?php
	}
	?>
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php 
	$class = $style = '';
	if (($body_style=get_custom_option('body_style'))=='boxed') {
		$customizer = get_theme_option('show_theme_customizer') == 'yes';
		if ($customizer && ($img = (int) getValueGPC('bg_image', 0)) > 0)
			$class = 'bg_image_'.$img;
		else if ($customizer && ($img = (int) getValueGPC('bg_pattern', 0)) > 0)
			$class = 'bg_pattern_'.$img;
		else if ($customizer && ($img = getValueGPC('bg_color', '')) != '')
			$style = 'background-color: '.$img.';';
		else {
			if (($img = get_custom_option('bg_custom_image')) != '')
				$style = 'background: url('.$img.') ' . str_replace('_', ' ', get_custom_option('bg_custom_image_position')) . ' no-repeat fixed;';
			else if (($img = get_custom_option('bg_custom_pattern')) != '')
				$style = 'background: url('.$img.') 0 0 repeat fixed;';
			else if (($img = get_custom_option('bg_image')) > 0)
				$class = 'bg_image_'.$img;
			else if (($img = get_custom_option('bg_pattern')) > 0)
				$class = 'bg_pattern_'.$img;
			if (($img = get_custom_option('bg_color')) != '')
				$style .= 'background-color: '.$img.';';
		}
	}
	body_class(
		'theme_' . get_custom_option('blog_theme')
		. ' puzzles_' . get_custom_option('puzzles_style')
		. ' reviews_' . max(5, (int) get_custom_option('reviews_max_level'))
		. (get_custom_option('puzzles_animations')=='yes' ? ' puzzles_animations' : '')
		. ' ' . $body_style
		. ($class!='' ? ' ' . $class : '')
	);
	if ($style!='') echo ' style="'.$style.'"';
	?>
>

	<?php do_action( 'before' ); ?>

	<?php 
	global $THEMEREX_sidemenu;
	$THEMEREX_sidemenu = wp_nav_menu(array(
		'menu'              => '',
		'container'         => '',
		'container_class'   => '',
		'container_id'      => '',
		'items_wrap'      	=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'menu_class'        => '',
		'menu_id'           => 'sidemenu',
		'echo'              => false,
		'fallback_cb'       => '',
		'before'            => '',
		'after'             => '',
		'link_before'       => '',
		'link_after'        => '',
		'depth'             => 11,
		'theme_location'    => 'sidemenu'
	));
	
	if ($THEMEREX_sidemenu) { 
	?>
		<nav id="sidemenu_area" class="sidemenu_area theme_accent_bg" role="navigation">
			<a href="#" id="sidemenu_link" class="icon-menu theme_accent_bg"></a>
			<div id="sidemenu_scroller_wrapper">
				<div id="sidemenu_scroller">
					<?php echo $THEMEREX_sidemenu; ?>
				</div>
			</div>
		</nav>
	<?php } ?>

	<!--[if lt IE 9]>
	<?php echo do_shortcode("[infobox style='error']<div style=\"text-align:center;\">".__("It looks like you're using an old version of Internet Explorer. For the best WordPress experience, please <a href=\"http://microsoft.com\" style=\"color:#191919\">update your browser</a> or learn how to <a href=\"http://browsehappy.com\" style=\"color:#222222\">browse happy</a>!", 'themerex')."</div>[/infobox]"); ?>
	<![endif]-->
    <div id="page" class="hfeed site theme_body">
		<?php 
			if (($header_custom_image = get_header_image()) != '') {
				$header_style = ' style = "background: url('.$header_custom_image.');"';
			} else {
				$header_style = '';
			}
		?>
        <header id="header" class="site_header" role="banner" <?php echo $header_style; ?>>
			<div id="header_top">
				<div class="top_line theme_accent_bg"></div>
				<div id="header_top_inner">

					<?php if (get_custom_option('show_login')=='yes') { ?>
						<div id="login_area">


							<?php if( !is_user_logged_in() ) { ?>
								<a href="#" class="link_login icon-login-1 theme_accent_bg" title="<?php _e('Login', 'themerex'); ?>"><button type="button">Normal signin or &#8594;</button> 
<div style="float:right;">
</div>

</a>
								<a href="#" class="link_register icon-key theme_accent_bg" title="<?php _e('Register', 'themerex'); ?>"></a>
							<?php } else { ?>
								<a href="<?php echo wp_logout_url(home_url()); ?>" class="link_logout icon-logout-1 theme_accent_bg" title="<?php _e('Logout', 'themerex'); ?>"></a>
							<?php } ?>
						</div>
					<?php } ?>

					<?php if (get_custom_option('show_breadcrumbs')=='yes') { ?>
						<div id="breadcrumbs_area">
							<?php if (!is_404()) showBreadcrumbs( array('home' => __('Home', 'themerex'), 'truncate_title' => 50 ) ); ?>
						</div>
					<?php } ?>
					
					<?php
					if (($logo_image=get_theme_option('logo_image'))!='') { 
					?>
						<div class="logo logo_image"><a href="<?php echo get_home_url(); ?>"><img src="<?php echo $logo_image; ?>" alt="" /></a></div>
					<?php 
					} else if (($logo_text = get_theme_option('logo_text'))!='') {
						$logo_text = str_replace(array('[', ']'), array('<span class="theme_accent">', '</span>'), $logo_text);
						$logo_slogan = get_theme_option('logo_slogan');
					?>
						<div class="logo logo_text"><a href="<?php echo get_home_url(); ?>"><span class="logo_title theme_header"><?php echo $logo_text; ?></span><span class="logo_slogan theme_info"><?php echo $logo_slogan; ?></span></a></div>
					<?php 
					} 
					?>

					<?php if (get_custom_option('show_ads_block')=='yes' && (($ads_block_content = get_custom_option('ads_block_content'))!='' || ($ads_block_image = get_custom_option('ads_block_image'))!='')) { ?>
						<div id="ads_block_top">
							<?php
							if ($ads_block_content!='')
								echo $ads_block_content;
							else {
								$ads_block_link = get_custom_option('ads_block_link');
								echo ($ads_block_link!='' ? '<a href="'.$ads_block_link.'">' : '')
									. '<img src="'.$ads_block_image.'" />'
									. ($ads_block_link!='' ? '</a>' : '');
							}
							?>
						</div>
					<?php } ?>
					
				</div>
       		</div>
			
			<?php 
			$menu = wp_nav_menu(array(
				'menu'              => '',
				'container'         => '',
				'container_class'   => '',
				'container_id'      => '',
				'items_wrap'      	=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'menu_class'        => '',
				'menu_id'           => 'mainmenu',
				'echo'              => false,
				'fallback_cb'       => '',
				'before'            => '',
				'after'             => '',
				'link_before'       => '',
				'link_after'        => '',
				'depth'             => 11,
				'theme_location'    => 'mainmenu'
			));
			if ($menu) {
				if (($header_custom_image = get_header_image()) != '') {
					$header_style = ' style = "background: url('.$header_custom_image.');"';
				} else {
					$header_style = '';
				}
				?>
				<div id="header_middle_wrapper">
					<div id="header_middle" <?php echo $header_style; ?> <?php echo $body_style=='fullwidth' ? 'class="theme_menu"' : ''; ?>>
						<div id="header_middle_inner">
							<div class="search_form_area theme_body">
								<form class="search_form" action="<?php echo home_url(); ?>" method="get"><input class="field theme_accent_bg search_field" type="search" placeholder="<?php echo sprintf(htmlspecialchars(__('Type your search query and press "Enter" %s', 'themerex')), '&hellip;'); ?>" value="" name="s"></form>
								<a href="#" class="search_close"><span class="icon-cancel-circled"></span></a>
							</div>
							<nav id="mainmenu_area" class="mainmenu_area theme_menu" role="navigation">
								<?php echo $menu; ?>			
								<a href="#" class="search_link"><span class="icon-search"></span></a>
							</nav>
						</div>
					</div>
				</div>
				<div id="header_middle_fixed"></div>
			<?php } ?>
		</header>

        
		<div id="main" class="<?php echo getSidebarClass(get_custom_option('show_sidebar_main')); ?>">
			<?php
			if (get_custom_option('slider_show')=='yes') { 
				$slider_display = $body_style=='fullwidth' ? 'fullscreen' : get_custom_option('slider_display');
				$slider = get_custom_option('slider_engine');
			?>
                <div id="main_slider" class="main_slider_<?php echo $slider_display; ?>">
                    <div id="main_slider_inner">
                        <?php
							if ($slider == 'revo' && is_plugin_active('revslider/revslider.php')) {
								$slider_alias = get_custom_option('slider_alias');
								if (!empty($slider_alias)) putRevSlider($slider_alias);
							} else if ($slider == 'flex') {
								$slider_cat = get_custom_option("slider_category");
								$slider_orderby = get_custom_option("slider_orderby");
								$slider_order = get_custom_option("slider_order");
								$slider_count = $slider_ids = get_custom_option("slider_posts");
								if (themerex_strpos($slider_ids, ',')!==false)
									$slider_count = 0;
								else {
									$slider_ids = '';
									if (empty($slider_count)) $slider_count = 3;
								}
								$slider_info_box = get_custom_option("slider_info_box");
								$slider_info_fixed = get_custom_option("slider_info_fixed");
								if ($slider_count>0 || !empty($slider_ids)) {
									echo do_shortcode('[slider engine="flex" controls="0"' 
										. ($slider_cat ? ' cat="'.$slider_cat.'"' : '') 
										. ($slider_ids ? ' ids="'.$slider_ids.'"' : '') 
										. ($slider_count ? ' count="'.$slider_count.'"' : '') 
										. ($slider_orderby ? ' orderby="'.$slider_orderby.'"' : '') 
										. ($slider_order ? ' order="'.$slider_order.'"' : '') 
										. ' titles="'.($slider_info_box=='yes' ? ($slider_info_fixed=='yes' ? 2 : 1) : 0)  .'"'
										. ']');
								}
							}
                        ?>
                    </div>
                </div>
			<?php } ?>

			<?php 
			if ( get_custom_option('googlemap_show')=='yes' ) { 
				$map_display = get_custom_option('googlemap_display');
				$map_height = get_custom_option('googlemap_height');
				$map_address = get_custom_option('googlemap_address');
				$map_latlng = get_custom_option('googlemap_latlng');
				if (!empty($map_address) || !empty($map_latlng)) {
			?>
					<div id="main_map" class="main_map_<?php echo $map_display; ?>">
						<div id="main_map_inner">
							<?php
							echo do_shortcode("[googlemap ".(!empty($map_latlng) ? "latlng='$map_latlng'" : "address='$map_address'")." zoom='" .get_custom_option('googlemap_zoom') ."' width='100%' height='$map_height']");
							?>
						</div>
					</div>
			<?php
				}
			} 
			?>
