<?php
//remove_post_type_support( 'page', 'comments' );
add_post_type_support( 'page', array('excerpt', 'comments') );

$THEMEREX_meta_box_page = array(
	'id' => 'my-meta-box',
	'title' => __('Page Options', 'themerex'),
	'page' => 'page',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
/*
		array( "name" => __('Page custom parameters', 'themerex'),
			"std" => __('In this section you can set up custom parameters for this page', 'themerex'),
			"type" => "info"),
		array( "name" => __('Featured icon',  'themerex'),
			"desc" => __("Select icon to present this page", 'themerex'),
			"id" => "page_icon",
			"std" => "",
			"type" => "icons",
			"options" => getIconsList()),
*/
	)
);
add_action('admin_menu', 'themerex_add_box_page');

// Add meta box
function themerex_add_box_page() {
    global $THEMEREX_meta_box_page;
    
    add_meta_box($THEMEREX_meta_box_page['id'], $THEMEREX_meta_box_page['title'], 'show_meta_box_page', $THEMEREX_meta_box_page['page'], $THEMEREX_meta_box_page['context'], $THEMEREX_meta_box_page['priority']);
}

// Callback function to show fields in meta box
function show_meta_box_page() {
    global $THEMEREX_meta_box_page, $post, $THEMEREX_theme_options;
	
	$custom_options = get_post_meta($post->ID, 'post_custom_options', true);
    
	wp_enqueue_script( '_admin', get_template_directory_uri() . '/js/_admin.js', array('jquery'), null, true );	
	?>
    
    <script type="text/javascript">
		jQuery(document).ready(function() {
			// Remove General & Reviews tabs - not contains override options
			jQuery('.opt_tabs > ul > li#tab_blog_general').remove();
			jQuery('#content_blog_general').eq(0).remove();
			jQuery('.opt_tabs > ul').eq(0).append(jQuery('.opt_tabs > ul > li#tab_general').eq(0));
			jQuery('.opt_tabs > ul > li#tab_general a').eq(0).html('<?php _e('Advertisement', 'themerex'); ?>');
			jQuery('.opt_tabs > ul > li > a').eq(0).trigger('click');
		});
	</script>
    
    <?php
    // Use nonce for verification
    echo '<input type="hidden" name="meta_box_page_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>

	<div id="truethemes_container">

    <?php
	$menu = $output = '';
	$flags = array(
		'group_opened' => false,
		'tabs_opened' => false,
		'heading_opened' => false,
		'heading_as_tabs' => true,
		'radio_as_select' => true,
		'inherit' => true,
		'clear_shortname' => true
		);

	foreach ($THEMEREX_theme_options as $option) { 
		if (!isset($option['override']) || !in_array('page', explode(',', $option['override']))) continue;

		$id = isset($option['id']) ? get_option_name($option['id']) : '';
        $meta = isset($custom_options[$id]) ? $custom_options[$id] : '';

		list($o, $m) = theme_options_show_field($option, $meta, $flags);
		$output .= $o;
		$menu   .= $m;
	}

    foreach ($THEMEREX_meta_box_page['fields'] as $option) {
        // get current post meta data
        $meta = isset($option['id']) && isset($custom_options[$option['id']]) ? $custom_options[$option['id']] : '';

		list($o, $m) = theme_options_show_field($option, $meta, $flags);
		$output .= $o;
		$menu   .= $m;
    }

	if ($flags['group_opened']) {
		$output .= '</div></div>';
	}

	$output .= '</div>';

	echo '<div class="opt_tabs"><ul>'.$menu.'</ul>'.$output;
}

add_action('save_post', 'themerex_save_data_page');

// Save data from meta box
function themerex_save_data_page($post_id) {
    global $THEMEREX_meta_box_page, $THEMEREX_theme_options;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    
    // verify nonce
    if (!isset($_POST['meta_box_page_nonce']) || !wp_verify_nonce($_POST['meta_box_page_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

	$custom_options = array();

	$need_save = false;

    foreach ($THEMEREX_meta_box_page['fields'] as $field) {
		if (!isset($field['id'])) continue;
		if (!isset($_POST[$field['id']])) continue;
		$need_save = true;
        $new = isset($_POST[$field['id']]) ? $_POST[$field['id']] : '';
		$custom_options[$field['id']] = $new ? $new : 'default';
    }

	foreach ($THEMEREX_theme_options as $option) { 
		if (!isset($option['override']) || !in_array('page', explode(',', $option['override']))) continue;
		if (!isset($option['id'])) continue;
		$id = get_option_name($option['id']);
		if (!isset($_POST[$id])) continue;
		$need_save = true;
        $new = isset($_POST[$id]) ? $_POST[$id] : '';
		$custom_options[$id] = $new ? $new : 'default';
	}
	
	if ($need_save) update_post_meta($post_id, 'post_custom_options', $custom_options);
}
?>