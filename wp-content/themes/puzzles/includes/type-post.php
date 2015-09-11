<?php
add_post_type_support( 'post', array('excerpt', 'post-formats') );

$THEMEREX_meta_box_post = array(
	'id' => 'post-meta-box',
	'title' => __('Post Options', 'themerex'),
	'page' => 'post',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array( "name" => __('Reviews criterias for this post', 'themerex'),
			"std" => __('In this section you can put your reviews marks', 'themerex'),
			"class" => "reviews_meta",
			"type" => "info"),
		array( "name" => __('Reviews marks',  'themerex'),
			"desc" => __("Marks for this review.", 'themerex'),
			"id" => "reviews_marks",
			"class" => "reviews_meta reviews_tab reviews_users",
			"std" => "",
			"type" => "reviews",
			"options" => get_theme_option('reviews_criterias')),
		array( "name" => __('Show reviews block',  'themerex'),
			"desc" => __("Show reviews block on single post page and average reviews rating after post's title in stream pages", 'themerex'),
			"id" => "show_reviews",
			"class" => "reviews_meta",
			"std" => "default",
			"type" => "radio",
			"style" => "horizontal",
			"options" => getYesNoList(true)),
	)
);

// Add meta box
add_action('admin_menu', 'add_meta_box_post');
function add_meta_box_post() {
    global $THEMEREX_meta_box_post;
    add_meta_box($THEMEREX_meta_box_post['id'], $THEMEREX_meta_box_post['title'], 'show_meta_box_post', $THEMEREX_meta_box_post['page'], $THEMEREX_meta_box_post['context'], $THEMEREX_meta_box_post['priority']);
}

// Callback function to show fields in meta box
function show_meta_box_post() {
    global $THEMEREX_meta_box_post, $post, $THEMEREX_theme_options;
	
	$THEMEREX_ajax_nonce = wp_create_nonce('ajax_nonce');
	$THEMEREX_ajax_url = admin_url('admin-ajax.php');
	
	$maxLevel = max(5, (int) get_theme_option('reviews_max_level'));

	$custom_options = get_post_meta($post->ID, 'post_custom_options', true);
	if (isset($custom_options['reviews_marks'])) 
		$custom_options['reviews_marks'] = marksToDisplay($custom_options['reviews_marks']);

	wp_enqueue_script( '_admin', get_template_directory_uri() . '/js/_admin.js', array('jquery'), null, true );	
	?>
    
    <script type="text/javascript">
		// AJAX fields
		var THEMEREX_ajax_url = "<?php echo $THEMEREX_ajax_url; ?>";
		var THEMEREX_ajax_nonce = "<?php echo $THEMEREX_ajax_nonce; ?>";
		var reviews_criterias = "<?php echo get_theme_option('reviews_criterias'); ?>";
		var reviews_levels = "<?php echo get_theme_option('reviews_criterias_levels'); ?>";
		var reviews_max_level = <?php echo $maxLevel; ?>;
		var allowUserReviews = true;
		jQuery(document).ready(function() {
			// Remove General tab - not contain override options
			jQuery('.opt_tabs > ul > li#tab_blog_general').eq(0).remove();
			jQuery('#content_blog_general').eq(0).remove();
			jQuery('.opt_tabs > ul').eq(0).append(jQuery('.opt_tabs > ul > li#tab_general').eq(0));
			jQuery('.opt_tabs > ul > li#tab_general a').eq(0).html('<?php _e('Advertisement', 'themerex'); ?>');
			jQuery('.opt_tabs > ul > li > a').eq(0).trigger('click');
			// Init post specific meta fields
			initPostReviews();
		});
	</script>
    
    <?php
    // Use nonce for verification
    echo '<input type="hidden" name="meta_box_post_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	?>

	<div id="truethemes_container" class="reviews_<?php echo $maxLevel; ?>">

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
		if (!isset($option['override']) || !in_array('post', explode(',', $option['override']))) continue;

		$id = isset($option['id']) ? get_option_name($option['id']) : '';
        $meta = isset($custom_options[$id]) ? $custom_options[$id] : '';

		list($o, $m) = theme_options_show_field($option, $meta, $flags);
		$output .= $o;
		$menu   .= $m;
	}

    foreach ($THEMEREX_meta_box_post['fields'] as $option) {
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

add_action('save_post', 'save_meta_box_post');


// Save data from meta box
function save_meta_box_post($post_id) {
    global $THEMEREX_meta_box_post, $THEMEREX_theme_options;
    
    // verify nonce
    if (!isset($_POST['meta_box_post_nonce']) || !wp_verify_nonce($_POST['meta_box_post_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } else if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
	$custom_options = array();

	$need_save = false;

    foreach ($THEMEREX_meta_box_post['fields'] as $field) {
		if (!isset($field['id'])) continue;
		if (!isset($_POST[$field['id']])) continue;
		$need_save = true;
        $new = isset($_POST[$field['id']]) ? (is_array($_POST[$field['id']]) ? join(',', $_POST[$field['id']]) : $_POST[$field['id']]) : '';
		$custom_options[$field['id']] = $new ? $new : 'default';
		if ($field['id']=='reviews_marks' && ($avg = getReviewsRatingAverage($new)) > 0) {
			$custom_options[$field['id']] = marksToSave($custom_options[$field['id']]);
			update_post_meta($post_id, 'reviews_avg', marksToSave($avg));
		}
    }

	foreach ($THEMEREX_theme_options as $option) { 
		if (!isset($option['override']) || !in_array('post', explode(',', $option['override']))) continue;
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