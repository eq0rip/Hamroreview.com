<?php
/* ========================= Custom fields for categories ============================== */

// Get category custom fields
function category_custom_fields_get($tax = null) {  
	$t_id = is_object($tax) ? $tax->term_id : $tax; 			// Get the ID of the term you're editing  
	return $t_id ? get_option( "category_term_{$t_id}" ) : false;		// Do the check  
}

// Get category custom fields
function category_custom_fields_set($term_id, $term_meta) {  
	update_option( "category_term_{$term_id}", $term_meta );  
}


// Add the fields to the "category" taxonomy, using our callback function  
add_action( 'category_edit_form_fields', 'category_custom_fields_show', 10, 1 );  
add_action( 'category_add_form_fields', 'category_custom_fields_show', 10, 1 );  
function category_custom_fields_show($tax = null) {  
	global $THEMEREX_theme_options;
	wp_enqueue_script( '_admin', get_template_directory_uri() . '/js/_admin.js', array(), null, true );	
?>  
    <script type="text/javascript">
		jQuery(document).ready(function() {
			// Remove General tab - not contain override options
			jQuery('.opt_tabs > ul > li#tab_blog_general').eq(0).remove();
			jQuery('#content_blog_general').eq(0).remove();
			jQuery('.opt_tabs > ul').eq(0).append(jQuery('.opt_tabs > ul > li#tab_general').eq(0));
			jQuery('.opt_tabs > ul > li#tab_general a').eq(0).html('<?php _e('Advertisement', 'themerex'); ?>');
			jQuery('.opt_tabs > ul > li > a').eq(0).trigger('click');
		});
	</script>

	<table border="0" cellpadding="0" cellspacing="0" class="form-table">
    <tr class="form-field" valign="top">  
	    <td span="2">
			
	<div id="truethemes_container">

	<div class="section section-info ">
		<h3 class="heading"><?php _e('Custom settings for this category (and nested):', 'themerex'); ?></h3>
		<div class="option">
			<div class="controls">
				<div class="info">
					<?php _e('Select parameters for showing posts from this category and all nested categories.', 'themerex'); ?><br />
					<?php _e('Attention: In each nested category you can override this settings.', 'themerex'); ?>
				</div>
			</div>
		</div>
	</div>
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
	$term_meta = category_custom_fields_get($tax);
	foreach ($THEMEREX_theme_options as $option) { 
		if (!isset($option['override']) || !in_array('category', explode(',', $option['override']))) continue;
		$id = isset($option['id']) ? get_option_name($option['id']) : '';
        $meta = isset($term_meta[$id]) ? $term_meta[$id] : '';

		list($o, $m) = theme_options_show_field($option, $meta, $flags);
		$output .= $o;
		$menu   .= $m;
	}

	if ($flags['group_opened']) {
		$output .= '</div></div>';
	}

	$output .= '
		</div>
		</td>
		</tr>
		</table>
	';
	
	echo '<div class="opt_tabs"><ul>'.$menu.'</ul>'.$output;
} 



  
// Save the changes made on the "category" taxonomy, using our callback function  
add_action( 'edited_category', 'category_custom_fields_save', 10, 1 );
add_action( 'created_category', 'category_custom_fields_save', 10, 1 );
function category_custom_fields_save( $term_id=0 ) {  
    global $THEMEREX_theme_options;
	$term_meta = category_custom_fields_get($term_id);
	$need_save = false;
	foreach ($THEMEREX_theme_options as $option) { 
		if (!isset($option['override']) || !in_array('category', explode(',', $option['override']))) continue;
		if (!isset($option['id'])) continue;
		$id = get_option_name($option['id']);
		if (!isset($_POST[$id])) continue;
		$need_save = true;
		$multiple = isset($option['multiple']) && $option['multiple'];
		if ($multiple && is_array($_POST[$id])) {
			sort($_POST[$id]);
		}
		$inc = isset($option['increment']) && $option['increment'] || $option['type']=='socials';
		if ($inc && is_array($_POST[$id])) {
			foreach($_POST[$id] as $k=>$v)
				$_POST[$id][$k] .= '|' . $_POST[$id.'_numbers'][$k] . ($option['type']=='socials' ? '|' . $_POST[$id.'_icons'][$k] : '');
		}
		$new = isset($_POST[$id]) ? (($multiple || $inc) && is_array($_POST[$id]) ? implode(',', $_POST[$id]) : stripslashes($_POST[$id])) : ($option['type'] == 'checkbox' ? 'false' : '');
		if (isset($option['enable']))
			$new = $new . '|' . (isset($_POST[$id.'_enable']) ? 1 : 0); 
		$term_meta[$id] = $new ? $new : 'default';
	}
	//save the option array  
	if ($need_save) category_custom_fields_set($term_id, $term_meta);
}
?>