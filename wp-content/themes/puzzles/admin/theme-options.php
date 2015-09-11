<?php
/*-----------------------------------------------------------------------------------*/
/* Admin Interface
/*-----------------------------------------------------------------------------------*/
add_action('admin_menu', 'theme_options_add_admin');
function theme_options_add_admin() {

    global $query_string;
   
	// In this case menu item "Theme Options" add in root admin menu level
	//$tt_page = add_menu_page(__('Theme Options', 'themerex'), __('Theme Options', 'themerex'), 'edit_theme_options', 'theme_options', 'theme_options_page');

	// In this case menu item "Theme Options" add in admin menu 'Appearance'
	$tt_page = add_theme_page(__('Theme Options', 'themerex'), __('Theme Options', 'themerex'), 'edit_theme_options', 'theme_options', 'theme_options_page');

	/*
	add_action("admin_print_styles-$tt_page", 'theme_options_load_styles');
	add_action("admin_print_scripts-$tt_page", 'theme_options_load_scripts');
	add_action("admin_head-$tt_page", 'theme_options_admin_head');
	*/
} 


/*-----------------------------------------------------------------------------------*/
/* Load required styles for Options Page
/*-----------------------------------------------------------------------------------*/
add_action("admin_print_styles", 'theme_options_load_styles');
function theme_options_load_styles() {
	wp_enqueue_style('theme-options', get_template_directory_uri().'/admin/theme-options.css', array(), null);
	wp_enqueue_style('wp-color-picker', false, array(), null);
	$color = get_user_option('admin_color');
	if ($color == "fresh")
		wp_enqueue_style('theme-options-fresh', get_template_directory_uri().'/admin/theme-options-fresh.css', array(), null);
}


/*-----------------------------------------------------------------------------------*/
/* Load required javascripts for Options Page
/*-----------------------------------------------------------------------------------*/
add_action("admin_print_scripts", 'theme_options_load_scripts');
function theme_options_load_scripts() {
	
	wp_enqueue_media();
	wp_enqueue_script('ajaxupload', get_template_directory_uri().'/admin/js/ajaxupload.js', array('jquery'), null, true);
    
	wp_enqueue_script('jquery-ui-core', false, array('jquery'), null, true);
	wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
	wp_register_script('jquery-input-mask', get_template_directory_uri().'/admin/js/jquery.maskedinput-1.2.2.js', array( 'jquery' ));
	wp_enqueue_script('jquery-input-mask', false, array('jquery'), null, true);
	wp_enqueue_script('wp-color-picker', false, array(), null, true);
}



/*-----------------------------------------------------------------------------------*/
/* Ajax Save Action handler
/*-----------------------------------------------------------------------------------*/

add_action('wp_ajax_theme_options_ajax_post_action', 'theme_options_ajax_callback');
function theme_options_ajax_callback() {
	global $wpdb, $_REQUEST, $THEMEREX_theme_options;
	
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'ajax_nonce' ) )
		die();

	$save_type = $_POST['type'];

	if ($save_type == 'upload'){
		
		$clickedID = $_POST['data']; // Acts as the name
		$filename = $_FILES[$clickedID];
       	$filename['name'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', $filename['name']); 
		
		$override['test_form'] = false;
		$override['action'] = 'wp_handle_upload';    
		$uploaded_file = wp_handle_upload($filename, $override);
		 
		$upload_tracking[] = $clickedID;
		update_option( $clickedID , $uploaded_file['url'] );
				
		if (!empty($uploaded_file['error'])) {
			echo __('Upload Error:', 'themerex') . ' ' . $uploaded_file['error'];
		} else {
			echo $uploaded_file['url']; 
		}

	} else if ($save_type == 'options' OR $save_type == 'reset') {
		if ($save_type == 'options') {
			$data = $_POST['data'];
			parse_str($data, $output);
		}
		//Pull options
		foreach ($THEMEREX_theme_options as $option_array){
			if (isset($option_array['id'])) { // Non - Headings...
				$id = $option_array['id'];
				$type = $option_array['type'];
				if ($save_type == 'options') {
					$multiple = isset($option_array['multiple']) && $option_array['multiple'];
					if ($multiple && is_array($output[$id])) {
						sort($output[$id]);
					}
					$inc = isset($option_array['increment']) && $option_array['increment'] || $type=='socials';
					if ($inc && is_array($output[$id])) {
						foreach($output[$id] as $k=>$v)
							$output[$id][$k] .= '|' . $output[$id.'_numbers'][$k] . ($type=='socials' ? '|' . $output[$id.'_icons'][$k] : '');
					}
					$new_value = isset($output[$id]) ? (($multiple || $inc) && is_array($output[$id]) ? implode(',', $output[$id]) : stripslashes($output[$id])) : ($type == 'checkbox' ? 'false' : '');
					if (isset($option_array['enable']))
						$new_value = $new_value . '|' . (isset($output[$id.'_enable']) ? 1 : 0); 
				} else {
					$new_value = $option_array['std'];
					if (isset($option_array['enable']))
						$new_value = $new_value . '|' . ($option_array['enable'] ? 1 : 0); 
				}
//				if ($type != 'upload_min' || $save_type == 'reset') {
					update_option($id, $new_value);
//				}
			}
		}	
	}
	die();
}



/*-----------------------------------------------------------------------------------*/
/* Prepare javascripts for Options Page
/*-----------------------------------------------------------------------------------*/
add_action("admin_head", 'theme_options_admin_head');
function theme_options_admin_head() { 
	$THEMEREX_ajax_nonce = wp_create_nonce('ajax_nonce');
	$THEMEREX_ajax_url = admin_url('admin-ajax.php');
?>
    
	<script type="text/javascript" language="javascript">
        jQuery(document).ready(function(){
	
			var flip = 0;
					
			jQuery('#expand_options').click(function(){
				if(flip == 0){
					flip = 1;
					jQuery('#truethemes_container #of-nav').hide();
					jQuery('#truethemes_container #content').width(755);
					jQuery('#truethemes_container .group').add('#truethemes_container .group h2').show();
					jQuery(this).text('[-]');
				} else {
					flip = 0;
					jQuery('#truethemes_container #of-nav').show();
					jQuery('#truethemes_container #content').width(579);
					jQuery('#truethemes_container .group').add('#truethemes_container .group h2').hide();
					jQuery('#truethemes_container .group:first').show();
					jQuery('#truethemes_container #of-nav li').removeClass('current');
					jQuery('#truethemes_container #of-nav li:first').addClass('current');
					jQuery(this).text('[+]');
				}
			});
				
			jQuery('.group').hide();
			jQuery('.group:first').fadeIn();
					
			jQuery('.group .collapsed').each(function(){
				jQuery(this).find('input:checked').parent().parent().parent().nextAll().each( 
					function(){
						if (jQuery(this).hasClass('last')) {
							jQuery(this).removeClass('hidden');
							return false;
						}
						jQuery(this).filter('.hidden').removeClass('hidden');
					});
			});
						
			jQuery('.group .collapsed input:checkbox').click(unhideHidden);
			
			function unhideHidden(){
				if (jQuery(this).attr('checked')) {
					jQuery(this).parent().parent().parent().nextAll().removeClass('hidden');
				}
				else {
					jQuery(this).parent().parent().parent().nextAll().each( 
						function(){
							if (jQuery(this).filter('.last').length) {
								jQuery(this).addClass('hidden');
								return false;
							}
							jQuery(this).addClass('hidden');
						});
						
				}
			}
							
				
			//Save all options
			jQuery('#ofform #button-save').click(function(e){
					function newValues() {
					  var serializedValues = jQuery("#ofform").serialize();
					  return serializedValues;
					}
					//jQuery(":checkbox, :radio").click(newValues);
					//jQuery("select").change(newValues);
					jQuery('.ajax-loading-img').fadeIn();
					var serializedReturn = newValues();
	
					var THEMEREX_ajax_url = '<?php echo $THEMEREX_ajax_url; ?>';
				
					 //var data = {data : serializedReturn};
					var data = {
						<?php if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'theme_options') { ?>
						type: 'options',
						<?php } ?>
						action: 'theme_options_ajax_post_action',
						nonce: '<?php echo $THEMEREX_ajax_nonce; ?>',
						data: serializedReturn
					};
					
					jQuery.post(THEMEREX_ajax_url, data, function(response) {
						var success = jQuery('#of-popup-save');
						var loading = jQuery('.ajax-loading-img');
						loading.fadeOut();  
						success.fadeIn();
						window.setTimeout(function(){
						   success.fadeOut(); 
						}, 2000);
					});
					
					e.preventDefault();
					return false;
			});   	 	
	
			//Reset options
			jQuery('#ofform #button-reset').click(function(e){
					jQuery('.ajax-loading-img').fadeIn();
					
					if (!confirm('<?php _e('You really want reset all options to default values ?', 'themerex'); ?>')) return;
					 
					var THEMEREX_ajax_url = '<?php echo $THEMEREX_ajax_url; ?>';
				
					 //var data = {data: serializedReturn};
					var data = {
						<?php if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'theme_options') { ?>
						type: 'reset',
						<?php } ?>
						action: 'theme_options_ajax_post_action',
						nonce: '<?php echo $THEMEREX_ajax_nonce; ?>'
					};
					
					jQuery.post(THEMEREX_ajax_url, data, function(response) {
						var success = jQuery('#of-popup-reset');
						var loading = jQuery('.ajax-loading-img');
						loading.fadeOut();  
						success.fadeIn();
						window.setTimeout(function(){
						   success.fadeOut(); 
						   window.location.href = (pos=window.location.href.indexOf('&rnd='))>0 ? window.location.href.substring(0, pos) : window.location.href + '&rnd=' + Math.random();
						}, 2000);
					});

					e.preventDefault();
					return false;
			});
	
		
			jQuery('#of-nav li:first').addClass('current');

			var i = 0;
			jQuery('#of-nav li a').attr('id', function() {
			   i++;
			   return 'item'+i;
			});

			jQuery('#of-nav li a').click(function(evt){
		
				jQuery('#of-nav li').removeClass('current');
				jQuery(this).parent().addClass('current');
				
				var clicked_group = jQuery(this).attr('href');
 
				jQuery('.group').hide();
				
				jQuery(clicked_group).fadeIn();

				evt.preventDefault();
			});

			if('<?php if(isset($_REQUEST['reset'])) { echo $_REQUEST['reset']; } else { echo 'false';} ?>' == 'true'){
				
				var reset_popup = jQuery('#of-popup-reset');
				reset_popup.fadeIn();
				window.setTimeout(function(){
					   reset_popup.fadeOut();                        
					}, 2000);
					//alert(response);
				
			}

			//Update Message popup
			jQuery.fn.center = function () {
				this.animate({"top":( jQuery(window).height() - this.height() - 200 ) / 2+jQuery(window).scrollTop() + "px"},100);
				this.css("left", 250 );
				return this;
			}

			jQuery('#of-popup-save').center();
			jQuery('#of-popup-reset').center();
			jQuery(window).scroll(function() { 
				jQuery('#of-popup-save').center();
				jQuery('#of-popup-reset').center();
			});


           	// Tabs and group toggles
			jQuery('.opt_group .opt_content.closed').hide();
            jQuery('.opt_group h3.toggle').click(function(){
            	jQuery(this).next().toggleClass('closed').slideToggle();
            });
            jQuery('.opt_tabs').tabs();
	
			
			// Color Selector
			jQuery('.colorSelector').wpColorPicker();
			jQuery('.wp-picker-clear').css('width', '80px');
			
			// Social pictogramm selector
			jQuery('.section').on('click', '.of-socials-button', function(e){
				jQuery(this).siblings('.of-socials-icons').toggle();
				e.preventDefault();
				return false;
			});

			// Images selector
			jQuery('.section').on('click', '.of-radio-img-img', function(e){
				var parent = jQuery(this).parent().parent();
				parent.find('input[type="hidden"]').val(jQuery(this).data('icon'));
				parent.find('.of-radio-img-img').removeClass('of-radio-img-selected');
				jQuery(this).addClass('of-radio-img-selected');
				if (parent.siblings('.of-socials-button').length > 0) {
					parent.siblings('.of-socials-button').find('img').attr('src', jQuery(this).attr('src'));
					parent.hide();
				}
				e.preventDefault();
				return false;
			});

			// Icons selector
			jQuery('.section').on('click', '.of-radio-icon-icon', function(e){
				var parent = jQuery(this).parent().parent();
				parent.find('input[type="hidden"]').val(jQuery(this).data('icon'));
				parent.find('.of-radio-icon-icon').removeClass('of-radio-icon-selected');
				jQuery(this).addClass('of-radio-icon-selected');
				if (parent.siblings('.of-socials-button').length > 0) {
					parent.siblings('.of-socials-button').find('img').attr('src', jQuery(this).attr('src'));
					parent.hide();
				}
				e.preventDefault();
				return false;
			});

			// WP Media manager	
			var media_frame = null;
			jQuery('.image_media_button').on('click', function(e){
				var clickedObject = jQuery(this);
				var clickedID = jQuery(this).attr('id');	
				if ( media_frame ) {
					media_frame.open();
					return;
				}
				var multi = jQuery( this ).data('multiple')===true;
				media_frame = wp.media.frames.media_frame = wp.media({
					// Multiple choise
					multiple:	multi ? 'add' : false,
					// Set the title of the modal.
					title: jQuery( this ).data('choose'),
					// Tell the modal to show only images.
					library: {
						type: 'image'
					},
					// Customize the submit button.
					button: {
						// Set the text of the button.
						text: jQuery( this ).data('update'),
						// Tell the button to close the modal
						close: true
					}
				});
				media_frame.on( 'select', function(e) {
					var field = jQuery("#"+clickedObject.data('linked-field')).eq(0);
					var attachment = '';
					if (multi) {
						media_frame.state().get('selection').map( function( att ) {
							attachment += (attachment ? "\n" : "") + att.toJSON().url;
						});
						var val = field.val();
						attachment = val + (val ? "\n" : '') + attachment;
					} else {
						attachment = media_frame.state().get('selection').first().toJSON().url;
						var buildReturn = '';
						if ((pos = attachment.lastIndexOf('.'))>=0) {
							var ext = attachment.substr(pos+1);
							buildReturn = '<a class="of-uploaded-image" target="_blank" href="' + attachment + '">';
							if ('jpg,png,gif'.indexOf(ext)>=0) {
								buildReturn += '<img class="hide of-option-image" id="image_'+clickedID+'" src="'+attachment+'" alt="" />';
							} else {
								buildReturn += '<span id="image_'+clickedID+'">'+attachment.substr(attachment.lastIndexOf('/')+1)+'</span>';
							}
							buildReturn += '</a>';
						}
						jQuery("#image_" + clickedID).remove();	
						if (buildReturn!='') {
							clickedObject.parent().after(buildReturn);
						}
						jQuery('img#image_'+clickedID).fadeIn();
					}
					clickedObject.next('span').fadeIn();
					clickedObject.parent().prev('input').val(attachment);
				});
				media_frame.open();
				e.preventDefault();
				return false;
			});
			
			//AJAX Upload
			jQuery('.image_upload_button').each(function(){
				var clickedObject = jQuery(this);
				var clickedID = jQuery(this).attr('id');	
				new AjaxUpload(clickedID, {
					  action: '<?php echo $THEMEREX_ajax_url; ?>',
					  name: clickedID, // File upload name
					  data: { // Additional data to send
							action: 'theme_options_ajax_post_action',
							nonce: '<?php echo $THEMEREX_ajax_nonce; ?>',
							type: 'upload',
							data: clickedID 
							},
					  autoSubmit: true, // Submit file after selection
					  responseType: false,
					  onChange: function(file, extension){},
					  onSubmit: function(file, extension){
							clickedObject.text('<?php _e('Uploading', 'themerex'); ?>'); // change button text, when user selects file	
							this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
							interval = window.setInterval(function(){
								var text = clickedObject.text();
								if (text.length < 13){	clickedObject.text(text + '.'); }
								else { clickedObject.text('<?php _e('Uploading', 'themerex'); ?>'); } 
							}, 200);
					  },
					  onComplete: function(file, response) {
					   
						window.clearInterval(interval);
						clickedObject.text('<?php _e('Upload Image', 'themerex'); ?>');	
						this.enable(); // enable upload button
						
						// If there was an error
						if(response.search('<?php _e('Upload Error', 'themerex'); ?>') > -1){
							var buildReturn = '<span class="upload-error">' + response + '</span>';
							jQuery(".upload-error").remove();
							clickedObject.parent().after(buildReturn);
						
						} else {
							var buildReturn = '';
							
							if ((pos = response.lastIndexOf('.'))>=0) {
								var ext = response.substr(pos+1);
								buildReturn = '<a class="of-uploaded-image" target="_blank" href="' + response + '">';
								if ('jpg,png,gif'.indexOf(ext)>=0) {
									buildReturn += '<img class="hide of-option-image" id="image_'+clickedID+'" src="'+response+'" alt="" />';
								} else {
									buildReturn += '<span id="image_'+clickedID+'">'+response.substr(response.lastIndexOf('/')+1)+'</span>';
								}
								buildReturn += '</a>';
							}
							jQuery(".upload-error").remove();
							jQuery("#image_" + clickedID).remove();	
							if (buildReturn!='') {
								clickedObject.parent().after(buildReturn);
							}
							jQuery('img#image_'+clickedID).fadeIn();
							clickedObject.next('span').fadeIn();
							clickedObject.parent().prev('input').val(response);
						}
					}
				});
			});
				
			//AJAX Remove (clear option value)
			jQuery('.image_reset_button').click(function(e){
					var clickedObject = jQuery(this);
					clickedObject.parent().siblings('a.of-uploaded-image').fadeOut(500, function() { jQuery(this).remove(); });
					clickedObject.parent().prev('input').val('');
					clickedObject.fadeOut();
					e.preventDefault();
					return false;
			});
			
			
			// Increment buttons
			jQuery('#truethemes_container').on('click', '.of-increment-add', function(e) {
				var inc_area = jQuery(this).siblings('.of-increment-area').eq(0);
				var inc_item = null;
				var max_num = 0;
				inc_area.find('.of-increment-item').each(function() {
					var cur_item = jQuery(this);
					if (inc_item == null) 
						inc_item = cur_item;
					var num = Number(cur_item.find('input[type="hidden"]').eq(0).val());
					if (num > max_num)
						max_num = num;
				});
				var clonedObj = inc_item.clone();
				clonedObj.find('input[type="text"],textarea').val('');
				clonedObj.find('input[type="hidden"]').val(max_num+1);
				inc_area.append(clonedObj);
				e.preventDefault();
				return false;
			});
			jQuery('#truethemes_container').on('click', '.of-increment-del', function(e) {
				if (jQuery(this).parents('.of-increment-item').parent().find('.of-increment-item').length>1)
					jQuery(this).parents('.of-increment-item').eq(0).remove();
				else
					alert("<?php _e("You can't delete last item! To disable it - just clear value in field.", 'themerex'); ?>");
				e.preventDefault();
				return false;
			});
		});
	</script>
<?php 
}



/*-----------------------------------------------------------------------------------*/
/* Build the Options Page
/*-----------------------------------------------------------------------------------*/
function theme_options_page(){
?>
<div class="wrap" id="truethemes_container">
	<div id="of-popup-save" class="of-save-popup">
		<div class="of-save-save"><?php _e('Options Updated', 'themerex'); ?></div>
	</div>
	<div id="of-popup-reset" class="of-save-popup">
		<div class="of-save-reset"><?php _e('Options Reset', 'themerex'); ?></div>
	</div>
	<form action="" enctype="multipart/form-data" id="ofform">
		<div id="header">
			<div class="logo">
				<h2><?php _e('Theme Options', 'themerex'); ?></h2>
			</div>
			<div class="icon-option"> </div>
			<div class="clear"></div>
		</div>
		<?php $option_page = theme_options_render(); ?>
		<div id="main">
			<div id="of-nav">
				<ul>
					<?php echo $option_page[1]; ?>
				</ul>
			</div>
			<div id="content"><?php echo $option_page[0]; ?></div>
			<div class="clear"></div>
		</div>
		<div class="save_bar_top">
			<img style="display:none;" src="<?php echo get_template_directory_uri() ?>/admin/images/wpspin_light.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="<?php _e('Working...', 'themerex'); ?>" />
			<input type="button" id="button-save" value="<?php _e('Save All Options', 'themerex'); ?>" class="button-primary" />
			<span class="submit-footer-reset">
			<input type="button" id="button-reset" value="<?php _e('Reset Options', 'themerex'); ?>" class="button submit-button reset-button" onclick="return confirm('<?php _e('CAUTION: Any and all settings will be lost! Click OK to reset.', 'themerex'); ?>');" />
			</span>
		</div>
	</form>
<?php  if (!empty($update_message)) echo $update_message; ?>
<div style="clear:both;"></div>
</div>
<!--wrap-->
<?php
}



/*-----------------------------------------------------------------------------------*/
/* Theme options page renderer
/*-----------------------------------------------------------------------------------*/

function theme_options_render() {
    
	global $THEMEREX_theme_options;

	$menu = '';
	$output = '';
	$flags = array(
		'group_opened' => false,
		'tabs_opened' => false,
		'heading_opened' => false,
		'inherit' => false
		);

	foreach ($THEMEREX_theme_options as $field) {
		list($o, $m) = theme_options_show_field($field, null, $flags);
		$output .= $o;
		$menu   .= $m;
	}

	if ($flags['group_opened']) {
		$output .= '</div></div>';
	}
	
	if ($flags['heading_opened']) {
	    $output .= '</div>';
	}
    return array($output, $menu);
}



/*-----------------------------------------------------------------------------------*/
/* Theme options page show option field
/*-----------------------------------------------------------------------------------*/

function theme_options_show_field($field, $value=null, &$flags) {

	if ($value !== null) $field['val'] = $value;
	if (!isset($field['val']) || $field['val']=='') $field['val'] = 'default';
	if ($flags['inherit'] && isset($field['options']) && is_array($field['options']))
		$field['options'] = themerex_array_merge(array('default'=>'Inherit'), $field['options']);
	if (isset($flags['radio_as_select']) && $flags['radio_as_select'] && $field['type']=='radio')
		$field['type']='select';
	if (isset($flags['clear_shortname']) && $flags['clear_shortname'] && isset($field['id']))
		$field['id'] = get_option_name($field['id']);

	$menu = '';
	$output = '';	
	
	$name_hook = str_replace(" ", "-", preg_replace("[^A-Za-z0-9]", "", themerex_strtolower($field['name'])));
	
	if (isset($flags['heading_as_tabs']) && $flags['heading_as_tabs'] && $field['type']=='heading') {
		$field['type'] = 'group';
		$field['tab'] = $name_hook;
		$field['tabs'] = array($name_hook => $field['name']);
	}
	
	if (in_array($field['type'], array('group', 'groupend', 'heading')) && $flags['group_opened']) {
		$output .= '</div>';
		if (!$flags['tabs_opened']) {
			$output .= '</div>';
		} else if (in_array($field['type'], array('groupend', 'heading'))) {
			$output .= '</div>';
			$flags['tabs_opened'] = false;
		}
		$flags['group_opened'] = false;
	}
	
	if ($field['type'] == 'group') {
		$flags['group_opened'] = true;
		if (isset($field['tabs'])) {
			if (!isset($flags['heading_as_tabs']) || !$flags['heading_as_tabs'])
				$output .= '<div class="opt_tabs"'.(isset($field['std']) ? ' id="'.$field['std'].'"' : '').'><ul>';
			foreach ($field['tabs'] as $id=>$title) {
				if (!isset($flags['heading_as_tabs']) || !$flags['heading_as_tabs'])
					$output .= '<li id="tab_'.$id.'"><a href="#content_'.$id.'">'.$title.'</a></li>';
				else
					$menu .= '<li id="tab_'.$id.'"><a href="#content_'.$id.'">'.$title.'</a></li>';
			}
			if (!isset($flags['heading_as_tabs']) || !$flags['heading_as_tabs'])
				$output .= '</ul>';
			$flags['tabs_opened'] = true;
		}
		if (isset($field['tab'])) {
			$output .= '<div class="opt_group opt_content" id="content_'.$field['tab'].'">';
		} else {
			$output .= '<div class="opt_group'.(isset($field['tabs']) ? ' opt_tabs' : '').'"'.(isset($field['std']) ? ' id="'.$field['std'].'"' : '').'>';
			$output .= '<h3 class="toggle">'.$field['name'].'</h3><div class="opt_content'.(isset($field['closed']) && $field['closed'] ? ' closed' : '').'">';
		}
	}  

	if ( !in_array( $field['type'], array("heading", "hidden", "group", "groupend") ) ) { 
		$class = ''; if(isset( $field['class'] )) { $class = $field['class']; }
		$output .= '<div class="section section-'.str_replace(array('list', 'range'), array('select', 'select'), $field['type']).' '. $class .'">'."\n";
		$output .= '<h3 class="heading">'. $field['name'] .'</h3>'."\n";
		$output .= '<div class="option">'."\n" . '<div class="controls">'."\n";
	} 

	if (isset($field['enable'])) {
		$tmp = explode('|', $field['val']);
		$field['val'] = $tmp[0];
		if (isset($tmp[1]))
			$field['enable'] = $tmp[1];
	}
	

	switch ( $field['type'] ) {
	
	
	
	case 'hidden':
		$output .= '<input class="of-input" name="'. $field['id'] .'" id="'. $field['id'] .'" type="'. $field['type'] .'" value="'. htmlspecialchars($field['val']=='default' ? '' : $field['val']) . '" />';
	break;



	case 'text':
		$inc = isset($field['increment']) && $field['increment'];
		if ($inc) {
			$arr = explode(',', $field['val']);
			$output .= '<div class="of-increment-area">';
		} else
			$arr = array($field['val']);
		for ($i=0; $i<count($arr); $i++) {
			$sb = explode('|',$arr[$i]);
			if (count($sb)==1) $sb[1] = $i+1;
			$output .= ($inc ? '<div class="of-increment-item"><input type="hidden" name="'. $field['id'] . '_numbers[]" value="'.$sb[1].'" />' : '').'<input class="of-input' . ($inc ? ' of-incremented' : '') . '" name="'. $field['id'] . ($inc ? '[]' : '') .'" id="'. $field['id'] .'" type="'. $field['type'] .'" value="'. htmlspecialchars($sb[0]=='default' ? '' : $sb[0]) . '" />' . ($inc ? '<a href="#" class="of-increment-button of-increment-del">-</a></div>' : '');
		}
		if ($inc) {
			$output .= '</div><a href="#" class="of-increment-button of-increment-add">'. __('+ Add item', 'themerex') .'</a>';
		}
	break;
	
		
	
	
	case 'textarea':
		$cols = isset($field['cols']) && $field['cols'] > 10 ? $field['cols'] : '40';
		$rows = isset($field['rows']) && $field['rows'] > 1 ? $field['rows'] : '8';
		$output .= '<textarea class="of-input" name="'. $field['id'] .'" id="'. $field['id'] .'" cols="'. $cols .'" rows="' . $rows . '">' . htmlspecialchars($field['val']=='default' ? '' : $field['val']) . '</textarea>';
	break;
	
	
		
	
	case 'select':
		$multiple = isset($field['multiple']) && $field['multiple'];
		$output .= '<select class="of-input" name="'. $field['id'] .($multiple ? '[]' : '') 
			.'" id="'. $field['id'] .'"' 
			. (isset($field['size']) ? ' size="' . $field['size'] . '"' : '') 
			. ($multiple ? ' multiple="multiple"' : '') . '>';
		foreach ($field['options'] as $k => $option) {
			if (is_array($option)) 
				$title = isset($option['name']) ? $option['name'] : (isset($option['title']) ? $option['title'] : $k);
			else
				$title = $option;
			$output .= '<option'. (($multiple ? in_array($k, explode(',', $field['val'])) : $field['val'] == $k) ? ' selected="selected"' : '') . ' value="' . $k . '">' . htmlspecialchars($title) . '</option>';
		}
		$output .= '</select>';
	break;


	
	
	
	
	
	
	case 'checklist':
		$multiple = isset($field['multiple']) && $field['multiple'];
		$i = 0;
		$id = $field['id'];
		$output .= '<ul class="checklist">';
		foreach ($field['options'] as $k => $val) {
			$i++;
			$output .='<li>';
			$output .='<input type="' . ($multiple ? 'checkbox' : 'radio') . '" value="'.$k.'" name="'.$id.($multiple ? '[]' : '').'" id="'.$id.$i.'"'.(in_array($k, explode(',', $field['val'])) ? ' checked="checked"' : '').'><label for="'.$id.$i.'">'.$val.'</label>';
			$output .='</li>';
		}
		$output .= '</ul>';
	break;
	
	
	
	
	
	case 'list':
		$output .= '<select class="of-input" name="'. $field['id'] .'" id="'. $field['id'] .'">';
		foreach ($field['options'] as $option) {
			$output .= '<option'. ($field['val'] == $option ? ' selected="selected"' : '') . ' value="' . $option . '">' . htmlspecialchars($option) . '</option>';
		}
		$output .= '</select>';
	break;



	
	case 'range':
		$output .= '<select class="of-input" name="'. $field['id'].'" id="'.$field['id'].'">';
		$output .= '<option value="default">Inherit</option>'; 
		for ($i = $field['from']; $i <= $field['to']; $i++) { 
			$output .= '<option value="'. $i .'" ' . ($field['val'] == $i ? ' selected="selected"' : '') . '>'. $i .'</option>'; 
		}
		$output .= '</select>';
	break;
	


	
	
	case 'fontsize':
		$output .= '<select class="of-typography of-typography-size" name="'. $field['id'].'" id="'. $field['id'].'_size">';
		for ($i = 9; $i < 71; $i++) { 
			$output .= '<option value="'. $i .'" ' . ($field['val'] == $i ? ' selected="selected"' : '') . '>'. $i .'px</option>'; 
		}
		$output .= '</select>';
	break;
	
	
	
	
	
	
	
	
	case "radio":
		foreach ($field['options'] as $key => $option) { 
			$output .= '<input class="of-input of-radio" type="radio" name="'. $field['id'] .'" value="'. $key .'" '. ($field['val'] == $key ? ' checked="checked"' : '') .' id="' . $field['id'] . '_' . $key . '" /><label for="' . $field['id'] . '_' . $key . '">' . $option . '</label>' . (isset($field['style']) && $field['style']=='vertical' ? '<br />' : '');
		}
	break;
	
	
	
	
	
	
	
	
	case "checkbox": 
		$output .= '<input type="checkbox" class="checkbox of-input" name="'.  $field['id'] .'" id="'. $field['id'] .'" value="true" ' . ($field['val'] == 'true' ? ' checked="checked"' : '') .' /><label for="' . $field['id'] . '">' . $field['name'] . '</label>';
	break;

	
	
	
	
	
	
	case "upload":
	case "upload_min":
	case "mediamanager":
		$upload = $field['val']=='default' ? '' : $field['val'];
		if (!empty($upload)) {$hide = '';} else { $hide = 'hide';}
		$output .= '<input class="of-input" name="'. $field['id'] .'" id="'.  $field['id'] .'_upload" value="'.  $upload .'" type="' . ( $field['type'] == 'upload_min' ? 'hidden' : 'text') . '" />'
			. '<div class="upload_button_div">'
			. '<span class="button image_'.( $field['type'] == 'mediamanager' ? 'media' : 'upload').'_button" id="'.$field['id'].'"'
			. ( $field['type'] == 'mediamanager' 
				? ' data-choose="' . (isset($field['multiple']) && $field['multiple'] ? __( 'Choose Images', 'themerex') : __( 'Choose Image', 'themerex')) . '"'
					. ' data-update="' . (isset($field['multiple']) && $field['multiple'] ? __( 'Add to Gallery', 'themerex') : __( 'Choose Image', 'themerex')) . '"'
					. '	data-multiple="' . (isset($field['multiple']) && $field['multiple'] ? 'true' : 'false') . '"'
					. ' data-linked-field="' . $field['id'] . '_upload"'
				: '').'>'
			. __('Upload Image', 'themerex')
			. '</span>'
			. '<span class="button image_reset_button '. $hide.'" id="reset_'. $field['id'] .'"' .(empty($upload) ? ' style="display:none;"' : ''). '>' . __('Remove', 'themerex') . '</span>'
			. '</div>' . "\n";
//			. '<div class="clear"></div>' . "\n";
		if (!empty($upload)){
			$output .= '<a class="of-uploaded-image" target="_blank" href="'. $upload . '">';
			$ext = pathinfo($upload, PATHINFO_EXTENSION);
			if (in_array(themerex_strtolower($ext), array('jpg', 'png', 'gif')))
				$output .= '<img class="of-option-image" id="image_'.$field['id'].'" src="'.$upload.'" alt="" />';
			else {
				$fname = pathinfo($upload, PATHINFO_BASENAME);
				$output .= '<span id="image_'.$field['id'].'">'.$fname.'</span>';
			}
			$output .= '</a>';
			$output .= '<div class="clear"></div>' . "\n"; 
			}
	break;






	case "color":
		$output .= '<input class="of-color colorSelector" name="'. $field['id'] .'" id="'. $field['id'] .'" type="text" value="'. ($field['val']=='default' ? '' : $field['val']) .'" />';
	break;   
	
	
	
	
	 
	
	case "images":
		$output .= '<input type="hidden" value="'.$field['val'].'" name="'. $field['id'].'" />';
		$i = 0;
		foreach ($field['options'] as $key => $option) { 
			$i++;
			$selected = '';
			if ($field['val'] == $key || ($i == 1 && $field['val'] == '')) $selected = 'of-radio-img-selected';
			$output .= '<span>';
			if ($key=='default') $option = get_template_directory_uri().'/images/spacer.png';
			$output .= '<img src="'.$option.'" data-icon="'. $key .'" alt="" class="of-radio-img-img '. $selected .'" />';
			$output .= '</span>';
		}
	break; 
	
	
	
	
	
	 
	
	case "icons":
		$i = 0;
		if (isset($field['css']) && $field['css']!='' && file_exists($field['css'])) {
			$field['options'] = parseIconsClasses($field['css']);
		}
		$output .= '</div>';
		$output .= '<input type="hidden" value="'.$field['val'].'" name="'. $field['id'].'" />';
		foreach ($field['options'] as $option) { 
			$i++;
			$selected = '';
			if ($field['val'] == $option || ($i == 1  && $field['val'] == '')) $selected = 'of-radio-icon-selected';
			$output .= '<span>';
			$output .= '<span class="of-radio-icon-icon '. $option . ' ' . $selected .'" data-icon="'.$option.'"></span>';
			$output .= '</span>';
		}
		$output .= '<div>';
	break; 
	
	
	
	case 'socials':
		$arr = explode(',', $field['val']);
		$output .= '<div class="of-increment-area of-socials-area">';
		for ($i=0; $i<count($arr); $i++) {
			$sb = explode('|', $arr[$i]);
			if (count($sb)==1) $sb[1] = $i+1;
			if (count($sb)==2) $sb[2] = '';
			$j=0;
			$icons = '';
			$button = '';
			foreach ($field['options'] as $k=>$option) { 
				$j++;
				$selected = '';
				if ($sb[2] == $k || ($j == 1  && $sb[2] == '')) { $selected = 'of-radio-img-selected'; $button = $option; }
				$icons .= '<span>';
				//$output .= '<span class="of-radio-icon-icon '. $option . ' ' . $selected .'" onClick="document.getElementById(\'of-radio-icon-'. $field['id'] . $j.'\').checked = true;"></span>';
				$icons .= '<img src="'.$option.'" data-icon="' . $k . '" alt="" class="of-radio-img-img '. $selected .'" />';
				$icons .= '</span>';
			}
			$output .= '<div class="of-increment-item"><input type="hidden" name="'. $field['id'] . '_numbers[]" value="'.$sb[1].'" />'
				. '<input class="of-input of-incremented" name="'.$field['id'].'[]" id="'.$field['id'].'" type="text" value="'.htmlspecialchars($sb[0]=='default' ? '' : $sb[0]).'" />' 
				. '<a href="#" class="of-socials-button"><img src="'.$button.'" alt="" /></a>'
				. '<a href="#" class="of-increment-button of-increment-del">-</a>'
				. '<div class="of-socials-icons">'
				. '<input type="hidden" value="'.$sb[2].'" name="'. $field['id'].'_icons[]" />'
				. $icons
				. '</div></div>';
		}
		$output .= '</div><a href="#" class="of-increment-button of-increment-add">'. __('+ Add item', 'themerex') .'</a>';
	break;
	
	
	
	
	
	case "info":
		$default = $field['std'];
		$output .= '<div class="info">'.$default.'</div>';
	break;
	
	

								 
	
	case "heading":
		if ($flags['heading_opened']){
		   $output .= '</div>'."\n";
		}
		$jquery_click_hook = "of-option-".$name_hook;
		$menu .= '<li><a title="'.  $field['name'] .'" href="#'.  $jquery_click_hook  .'">'.  $field['name'] .'</a></li>';
		$output .= '<div class="group" id="'. $jquery_click_hook  .'"><h2>'.$field['name'].'</h2>'."\n";
		$flags['heading_opened'] = true;
	break;
	
	
	
	default:
		if (function_exists('show_custom_field')) {
			$output .= show_custom_field($field, $field['val']);
		}
	} 







	if ( !in_array( $field['type'], array("heading", "hidden", "group", "groupend") ) ) { 
		//$output .= $field['type']!='checklist' ? '<br/>' : '';
		if (!isset($field['desc'])) { $descr = ''; } 
		else { $descr = $field['desc']; } 
		if (isset($field['enable'])) { 
			$output .= '<input type="checkbox" class="checkbox of-enable of-input" name="'.  $field['id'] .'_enable" id="'. $field['id'] .'_enable" value="1" ' . ($field['enable'] == '1' ? ' checked="checked"' : '') .' />';
		} 
		$output .= '</div><div class="explain">'. $descr .'</div>'."\n";
		$output .= '<div class="clear"> </div></div></div>'."\n";
	}

	return array($output, $menu);
}
?>