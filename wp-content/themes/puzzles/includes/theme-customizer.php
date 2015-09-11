<?php
// Redefine colors in styles
$THEMEREX_custom_css = "";

function getThemeCustomStyles() {
	global $THEMEREX_custom_css;
	return $THEMEREX_custom_css;
}

function addThemeCustomStyle($style) {
	global $THEMEREX_custom_css;
	$THEMEREX_custom_css .= "
		{$style}
	";
}

function prepareThemeCustomStyles() {
	// Custom font
	$fonts = getThemeFontsList(false);
	$font = get_custom_option('theme_font');
	if (isset($fonts[$font])) {
		addThemeCustomStyle("
			body, button, input, select, textarea {
				font-family: '".$font."', ".$fonts[$font]['family'].";
			}
		");
	}
	$font = get_custom_option('logo_font');
	if (isset($fonts[$font])) {
		addThemeCustomStyle("
			.logo_text .logo_title {
				font-family: '".$font."', ".$fonts[$font]['family'].";
			}
		");
	}
	
	// Custom menu
	if (get_theme_option('menu_colored')=='yes') {
		$menu_name = 'mainmenu';
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
			if (is_object($menu) && $menu) {
				$menu_items = wp_get_nav_menu_items($menu->term_id);
				$menu_styles = '';
				$menu_slider = get_theme_option('menu_slider')=='yes';
				if (count($menu_items) > 0) {
					foreach($menu_items as $k=>$item) {
		//				if ($item->menu_item_parent==0) {
							$cur_accent_color = '';
							if ($item->type=='taxonomy' && $item->object=='category') {
								$cur_accent_color = getCategoryInheritedProperty($item->object_id, 'theme_accent_color');
							}
							if ((empty($cur_accent_color) || $cur_accent_color=='default') && isset($item->classes[0]) && !empty($item->classes[0])) {
								$cur_accent_color = (themerex_substr($item->classes[0], 0, 1)!='#' ? '#' : '').$item->classes[0];
							}
							if (!empty($cur_accent_color) && $cur_accent_color!='default') {
								$menu_styles .= ($item->menu_item_parent==0 ? "#header_middle_inner #mainmenu li.menu-item-{$item->ID}.current-menu-item > a," : '')
									. "
									#header_middle_inner #mainmenu li.menu-item-{$item->ID} > a:hover,
									#header_middle_inner #mainmenu li.menu-item-{$item->ID}.sfHover > a {
										background-color: {$cur_accent_color} !important;
									}
									#header_middle_inner #mainmenu li.menu-item-{$item->ID} ul {
										background-color: {$cur_accent_color} !important;
									}
								";
							}
							if ($menu_slider && $item->menu_item_parent==0) {
								$menu_styles .= "
									#header_middle_inner #mainmenu li.menu-item-{$item->ID}.blob_over:not(.current-menu-item) > a:hover,
									#header_middle_inner #mainmenu li.menu-item-{$item->ID}.blob_over.sfHover > a {
										background-color: transparent !important;
									}
									";
							}
		//				}
					}
				}
				if (!empty($menu_styles)) {
					addThemeCustomStyle($menu_styles);
				}
			}
		}
	}
	
	// Main menu height
	$menu_height = (int) get_theme_option('menu_height');
	if ($menu_height > 20) {
		addThemeCustomStyle("
			#mainmenu > li > a {
				height: {$menu_height}px !important;
				line-height: {$menu_height}px !important;
			}
			#mainmenu > li ul {
				top: {$menu_height}px !important;
			}
			#header_middle {
				min-height: {$menu_height}px !important;
			}
		");
	}
	// Submenu width
	$menu_width = (int) get_theme_option('menu_width');
	if ($menu_width > 50) {
		addThemeCustomStyle("
			#mainmenu > li ul {
				width: {$menu_width}px;
			}
			#mainmenu > li ul li ul {
				left: ".($menu_width+1)."px;
			}
			#mainmenu > li:nth-child(n+6) ul li ul {
				left: -".($menu_width+1)."px;
			}
		");
	}

	// Custom css from theme options
	$css = get_custom_option('custom_css');
	if (!empty($css)) {
		addThemeCustomStyle($css);
	}
	
	return getThemeCustomStyles();
};
?>