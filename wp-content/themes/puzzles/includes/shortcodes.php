<?php
/**
 * Theme Shortcodes Functions
*/


/* ==================================================================================================
   ==                                       ADMIN SETUP                                            ==
   ================================================================================================== */

add_filter('the_excerpt', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');		// Enable shortcodes in widgets

// Clear paragraph tags around shortcodes
add_filter('the_content', 'shortcode_empty_paragraph_fix');
function shortcode_empty_paragraph_fix($content) {   
	$content = str_replace(
		array('<p>[', '<br />[', '<br/>[', '<br>[', ']</p>', ']<br />', ']<br/>', ']<br>', "]\n</p>", "]\n<br />", "]\n<br/>", "]\n<br>"),
		array('[',    '[',       '[',      '[',     ']',     ']',       ']',      ']',     ']',       ']',         ']',        ']'),
		$content
	);
	return $content;
}

// Show shortcodes list in admin editor
add_action('media_buttons','add_sc_select', 11);
function add_sc_select(){

	$shortcodes_list = '<select id="sc_select"><option value="">&nbsp;'.__('*Select Shortcode*', 'themerex').'&nbsp;</option>';

	$shortcodes_list .= '<option value="'
		. "[accordion initial='1']"
		. "<br />[accordion_item title='".__('Title', 'themerex')."']".__('Item inner text here', 'themerex')."[/accordion_item]"
		. "<br />[accordion_item title='".__('Title', 'themerex')."']".__('Item inner text here', 'themerex')."[/accordion_item]"
		. "<br />[accordion_item title='".__('Title', 'themerex')."']".__('Item inner text here', 'themerex')."[/accordion_item]"
		. "<br />[/accordion]<br />"
		. '">'.__('Accordion', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[audio url='' controls='1' width='100%' height='30']"
		. '">'.__('Audio', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[blogger ids='' cat='' orderby='date' order='desc' count='3' descr='200' readmore='1' rating='0' style='regular' border='0' dir='horizontal']"
		. '">'.__('Blogger', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[button style='regular' color='' size='medium' link='#']".__('Button caption', 'themerex')."[/button]"
		. '">'.__('Button', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[columns count='2']"
		. "<br />[column_item]".__('Item inner text here', 'themerex')."[/column_item]"
		. "<br />[column_item]".__('Item inner text here', 'themerex')."[/column_item]"
		. "<br />[/columns]<br />"
		. '">'.__('Columns', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		."[contact_form title='".__('Contact Form', 'themerex')."' description='']"
		.'">'.__('Contact form', 'themerex').'</option>';	
	$shortcodes_list .= '<option value="'
		. "[dropcaps style='1']".__('Dropcaps paragraph text here', 'themerex')."[/dropcaps]"
		. '">'.__('Dropcaps', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		."[googlemap address='' latlng='' zoom='16' style='' width='100%' height='240']"
		.'">'.__('Google Map', 'themerex').'</option>';	
	$shortcodes_list .= '<option value="'."[hide selector='']"
		.'">'.__('Hide block', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[highlight color='white' backcolor='#ff0000']".__('Highlighted text here', 'themerex')."[/highlight]"
		. '">'.__('Highlight', 'themerex').'</option>';
	$shortcodes_list .= '<option value="' 
		. "[image src='' width='190' height='145' title='' align='left']" 
		. '">'.__('Image', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[infobox style='regular' static='1']".__('Highlight text here', 'themerex')."[/infobox]"
		. '">'.__('Infobox', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[line style='solid' top='10' bottom='10' width='100%' height='1' color='']"
		. '">'.__('Line', 'themerex').'</option>';
	$shortcodes_list .= '<option value="' 
		. "[list style='regular']"
		. "<br />[list_item]".__('List Item inner text here', 'themerex')."[/list_item]"
		. "<br />[list_item]".__('List Item inner text here', 'themerex')."[/list_item]"
		. "<br />[list_item]".__('List Item inner text here', 'themerex')."[/list_item]"
		. "<br />[/list]<br />"
		. '">'.__('List', 'themerex').'</option>';
	$shortcodes_list .= '<option value="' 
		. "[puzzles]"
		. "<br />[blogger style='puzzles' cat='' orderby='date' order='desc' count='3' descr='200']"
		. "<br />[/puzzles]" 
		. '">'.__('Puzzles', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[quote style='1' cite='' title='']".__('Quoted text here', 'themerex')."[/quote]"
		. '">'.__('Quote', 'themerex').'</option>';
	$shortcodes_list .= '<option value="' 
		. "[section style='']".__('Section inner text here', 'themerex')."[/section]" 
		. '">'.__('Section', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[skills]"
		. "<br />[skills_item title='".__('Title', 'themerex')."' level='50%' color='#ff5555']"
		. "<br />[skills_item title='".__('Title', 'themerex')."' level='50%' color='#ff5555']"
		. "<br />[skills_item title='".__('Title', 'themerex')."' level='50%' color='#ff5555']"
		. "<br />[/skills]<br />"
		. '">'.__('Skills', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[slider engine='flex' cat='' count='5' width='100%' height='250']"
		. '">'.__('Slider', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[table]<br />"
		. __('Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/', 'themerex')
		. "<br />[/table]<br />"
		. '">'.__('Table', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[tabs tab_names='Tab 1|Tab 2|Tab 3' style='1' initial='1']"
		. "<br />[tab]".__('Tab inner text here', 'themerex')."[/tab]"
		. "<br />[tab]".__('Tab inner text here', 'themerex')."[/tab]"
		. "<br />[tab]".__('Tab inner text here', 'themerex')."[/tab]"
		. "<br />[/tabs]<br />"
		. '">'.__('Tabs', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[team style='normal']"
		. "<br />[team_item user='".__('User name', 'themerex')."']"
		. "<br />[team_item user='".__('User name', 'themerex')."']"
		. "<br />[team_item user='".__('User name', 'themerex')."']"
		. "<br />[/team]<br />"
		. '">'.__('Team', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[testimonials user='' email='' name='' position='' photo='']Testimonials text[/testimonials]"
		. '">'.__('Testimonials', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[title type='1' style='regular']".__('Title text here', 'themerex')."[/title]"
		. '">'.__('Title', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[toggles initial='1']"
		. "<br />[toggles_item title='".__('Title', 'themerex')."']".__('Item inner text here', 'themerex')."[/toggles_item]"
		. "<br />[toggles_item title='".__('Title', 'themerex')."']".__('Item inner text here', 'themerex')."[/toggles_item]"
		. "<br />[toggles_item title='".__('Title', 'themerex')."']".__('Item inner text here', 'themerex')."[/toggles_item]"
		. "<br />[/toggles]<br />"
		. '">'.__('Toggles', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[tooltip title='Tooltip title']".__('Marked text here', 'themerex')."[/tooltip]"
		. '">'.__('Tooltip', 'themerex').'</option>';
	$shortcodes_list .= '<option value="'
		. "[video url='' width='480' height='270']"
		. '">'.__('Video', 'themerex').'</option>';
	$shortcodes_list .= '</select>';
	echo $shortcodes_list;
}



// Shortcodes list select handler
add_action('admin_head', 'button_js');
function button_js() {
	echo '
	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#sc_select").change(function() {
				send_to_editor(jQuery("#sc_select :selected").val());
				jQuery(this).get(0).options[0].selected = true;
        		return false;
			});
		});
	</script>';
}	






/* ==================================================================================================
   ==                                       USERS SHORTCODES                                       ==
   ================================================================================================== */



// ---------------------------------- [accordion] ---------------------------------------


add_shortcode('accordion', 'sc_accordion');

/*
[accordion id="unique_id" type="1|2" initial="1 - num_elements"]
	[accordion_item title="Et adipiscing integer, scelerisque pid"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta, odio arcu vut natoque dolor ut, enim etiam vut augue. Ac augue amet quis integer ut dictumst? Elit, augue vut egestas! Tristique phasellus cursus egestas a nec a! Sociis et? Augue velit natoque, amet, augue. Vel eu diam, facilisis arcu.[/accordion_item]
	[accordion_item title="A pulvinar ut, parturient enim porta ut sed"]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in. Magna eros hac montes, et velit. Odio aliquam phasellus enim platea amet. Turpis dictumst ultrices, rhoncus aenean pulvinar? Mus sed rhoncus et cras egestas, non etiam a? Montes? Ac aliquam in nec nisi amet eros! Facilisis! Scelerisque in.[/accordion_item]
	[accordion_item title="Duis sociis, elit odio dapibus nec"]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim. Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna.[/accordion_item]
	[accordion_item title="Nec purus, cras tincidunt rhoncus"]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna. Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim.[/accordion_item]
[/accordion]
*/
$THEMEREX_sc_accordion_counter = 0;
function sc_accordion($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"initial" => "1",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	global $THEMEREX_sc_accordion_counter;
	$THEMEREX_sc_accordion_counter = 0;
	$initial = max(0, (int) $initial);
	wp_enqueue_script('jquery-ui-accordion', false, array('jquery','jquery-ui-core'), null, true);
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_accordion"'.($s!='' ? ' style="'.$s.'"' : '') .'>'
			. do_shortcode($content)
		. '</div>'
		. '<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery(\'div' . ($id ? '#' . $id : '') . '.sc_accordion\').accordion({
					header: "h5",
					collapsible: true,
					heightStyle: "content",
					active: ' . ($initial > 0 ? $initial-1 : 'false') . '
				});
			});
		</script>		
	';
}


add_shortcode('accordion_item', 'sc_accordion_item');

//[accordion_item]
function sc_accordion_item($atts, $content=null) {
	extract(shortcode_atts( array(
		"id" => "",
		"title" => ""
	), $atts));
	global $THEMEREX_sc_accordion_counter;
	$THEMEREX_sc_accordion_counter++;
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_accordion_item' . ($THEMEREX_sc_accordion_counter % 2 == 1 ? ' odd' : ' even') . ($THEMEREX_sc_accordion_counter == 1 ? ' first' : '') . '">'
				. '<h5 class="sc_accordion_title"><a href="#"><span class="sc_accordion_icon"></span>'	. $title . '</a></h5>'
				. '<div class="sc_accordion_content">'
					. do_shortcode($content) 
				. '</div>'
			. '</div>';
}

// ---------------------------------- [/accordion] ---------------------------------------



// ---------------------------------- [audio] ---------------------------------------

add_shortcode("audio", "sc_audio");
						
//[audio id="unique_id" url="http://webglogic.com/audio/AirReview-Landmarks-02-ChasingCorporate.mp3" controls="0|1"]

function sc_audio($atts, $content = null) {
	extract(shortcode_atts(array(
		"id" => "",
		"src" => '',
		"url" => '',
		"controls" => "",
		"autoplay" => "",
		"width" => '100%',
		"height" => '30',
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	if ($src=='' && $url=='' && isset($atts[0])) {
		$src = $atts[0];
	}
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	return '<audio' . ($id ? ' id="' . $id . '"' : '') . ' src="' . ($src!='' ? $src : $url) . '" class="sc_audio" ' . ($controls ? ' controls="controls"' : '') . ($autoplay>0 && is_single() ? ' autoplay="autoplay"' : '') . ' width="' . $width . '" height="' . $height .'"'.($s!='' ? ' style="'.$s.'"' : '').'></audio>';
}
// ---------------------------------- [/audio] ---------------------------------------





// ---------------------------------- [blogger] ---------------------------------------

add_shortcode('blogger', 'sc_blogger');

/*
[blogger id="unique_id" ids="comma_separated_list" cat="category_id" orderby="date|views|comments" order="asc|desc" count="5" descr="0" dir="horizontal|vertical" style="regular|date|image_large|image_medium|image_small|bubble_left|bubble_top|accordion|puzzles" border="0"]
*/
$THEMEREX_sc_blogger_counter = 0;
function sc_blogger($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "regular",
		"bubble_color" => "",
		"ids" => "",
		"cat" => "",
		"count" => "3",
		"offset" => "",
		"orderby" => "date",
		"order" => "desc",
		"descr" => "0",
		"readmore" => "0",
		"dir" => "horizontal",
		"border" => "0",
		"rating" => "1",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	
	global $THEMEREX_sc_blogger_counter, $post;
	$THEMEREX_sc_blogger_counter = 0;

	if (!in_array($style, array('regular','date','image_large','image_medium','image_small','bubble_left','bubble_top','accordion','puzzles','underline')))
		$style='regular';	
	if (!empty($ids)) {
		$posts = explode(',', str_replace(' ', '', $ids));
		$count = count($posts);
	}
	if ($style=='accordion')
		$dir = 'vertical';
	
	$output = $style=='puzzles' ? '' : 
		'<div' . ($id ? ' id="' . $id . '"' : '') 
			. ' class="sc_blogger'
				. ' sc_blogger_' . ($dir=='vertical' ? 'vertical' : 'horizontal')
				. ' style_' . $style
				. ($style=='accordion' ? ' sc_accordion' : '')
				. ($dir!='vertical' && $style!='puzzles' ? ' sc_columns sc_columns_count_'.$count : '')
				. '"'
			. ($s!='' ? ' style="'.$s.'"' : '')
		. '>';

	$counters = get_theme_option("blog_counters");

	$args = array(
		'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
		'posts_per_page' => $count,
		'ignore_sticky_posts' => 1,
		'order' => $order=='asc' ? 'asc' : 'desc',
		'orderby' => 'date',
	);

	if ($offset > 0 && empty($ids)) {
		$args['offset'] = $offset;
	}

	$args = addSortOrderInQuery($args, $orderby, $order);
	$args = addPostsAndCatsInQuery($args, $ids, $cat);
	
	$query = new WP_Query( $args );

	while ( $query->have_posts() ) { 
		$query->the_post();
		$post_id = get_the_ID();
		$post_protected = post_password_required();
		$post_format = get_post_format();
		$post_link = get_permalink();
		$post_comments_link = $counters=='comments' ? get_comments_link( $post_id ) : $post_link;
		$post_comments = get_comments_number();
		$post_views = getPostViews($post_id);
		$post_date = prepareDateForTranslation(get_the_date());
		$post_date_sql = get_the_date('Y-m-d H:i:s');
		$post_date_diff = getDateOrDifference($post_date_sql);
		$post_icon = getPostFormatIcon($post_format);
		$post_author = get_the_author();
		$post_author_id = get_the_author_meta('ID');
		$post_author_url = get_author_posts_url($post_author_id, '');
		$post_title_tag = $dir=='vertical' ? 'h3' : 'h4';
		$post_thumb_w = $post_thumb_h = 0;
		if (themerex_strpos($style, 'bubble')!==false) {
			$post_custom_options = get_post_meta($post_id, 'post_custom_options', true);
			$post_icon = isset($post_custom_options['page_icon']) ? $post_custom_options['page_icon'] : $post_icon;
			$post_title_tag = 'h2';
		} else if ($style=='image_small')
			$post_thumb = getResizedImageTag($post_id, 120, 80);
		else if ($style=='image_medium')
			$post_thumb = getResizedImageTag($post_id, $post_thumb_w = 279, $post_thumb_h = 186);
		else if ($style=='image_large')
			$post_thumb = getResizedImageTag($post_id, $post_thumb_w = 466, $post_thumb_h = 310);
		else if ($style=='puzzles')
			$post_thumb = getResizedImageTag($post_id, $post_thumb_w = 310, $post_thumb_h = 310);
		//$post_attachment = wp_get_attachment_url(get_post_thumbnail_id($post_id));
				
		if (get_theme_option('preserve_decoration')=='no') {
			// -------------- Old way to get title, excerpt and content -----------------------
			$post_title = get_the_title();
			$post_content_full = $post->post_content;						//get_the_content() not used, because it trim content up to <!-- more --> in each case!
			$post_content_prepared = strip_shortcodes($post_content_full);	//do_shortcode($post_content_full); //do_shortcode() make a recursion if insert shortcode [blogger] in the posts!!!! 
			$post_excerpt = in_array($post_format, array('quote', 'link')) && $style=='puzzles'
				? $post_content_prepared 
				: ($descr > 0 ? strip_tags(strip_shortcodes(getPostDescription($descr, $readmore ? '' : '...'))) : '');
		} else {
			// ----------------- New way to get title, excerpt and content -----------------------
			$post_title = $post_title_plain = get_the_title();
			global $more;
			$old_more = $more;
			$more = -1;
			$post_content_full = get_the_content();
			$post_content_prepared = do_shortcode($post_content_full);
			$more = $old_more;
			$post_content = get_the_content(null);
			$post_excerpt = has_excerpt() || $post_protected ? get_the_excerpt() : '';
			if (empty($post_excerpt)) {
				if (($more_pos = themerex_strpos($post_content_full, '<span id="more-'))!==false) {
					$post_excerpt = themerex_substr($post_content_full, 0, $more_pos);
				} else {
					$post_excerpt = in_array($post_format, array('quote', 'link')) ? $post_content : get_the_excerpt();
				}
			}
			$post_excerpt = str_replace('[&hellip;]', '', $post_excerpt);
			if (!in_array($post_format, array('quote', 'link')) && $descr > 0 && themerex_strlen($post_excerpt) > $descr) {
				$post_excerpt = getShortString(strip_tags(do_shortcode($post_excerpt)), $descr, $readmore ? '' : '...');
			}
			$post_excerpt .=  !in_array($post_format, array('quote', 'link')) && $readmore ? '&nbsp;<a href="' . $post_link . '" class="readmore">'.($readmore==1 ? '&raquo;' : $readmore).'</a>' : '';
			//$post_content = apply_filters('the_content', $post_content);
			// ------------------  /New way to get title, excerpt and content -----------------------
		}
		$post_excerpt = apply_filters(in_array($post_format, array('quote', 'link')) ? 'the_content' : 'the_excerpt', force_balance_tags($post_excerpt));

		// Extract gallery, video and audio from full post content
		$post_gallery = $post_video = $post_audio = $post_url = $post_url_target = '';
		if (in_array($style, array('image_medium', 'image_large', 'puzzles'))) {
			if ($post_format == 'gallery') {
				$post_gallery =  buildGalleryTag(getPostGallery($post_content_full, $post_id), $post_thumb_w, $post_thumb_h);
			} else if ($post_format == 'video') {
				$post_video = getPostVideo($post_content_full, false);
				if ($post_video=='') {
					$src = getVideoPlayerURL(getPostVideo($post_content_full, true), $post_thumb!='');
					if ($src) $post_video = substituteVideo('<video src="'.$src.'">', $post_thumb_w, $post_thumb_h);
				}
				if ($post_video!='' && get_custom_option('substitute_video')=='yes') {
					$src = getVideoPlayerURL(getPostVideo($post_video), $post_thumb!='');
					if ($src) $post_video = substituteVideo('<video src="'.$src.'">', $post_thumb_w, $post_thumb_h);
				}
			} else if ($post_format == 'audio') {
				$post_audio = getPostAudio($post_content_full, false);
				if ($post_audio=='') {
					$src = getPostAudio($post_content_full, true);
					if ($src) $post_audio = substituteAudio('<audio src="'.$src.'">');
				}
				if ($post_audio!='' && get_custom_option('substitute_audio')=='yes') {
					$src = getPostAudio($post_audio);
					if ($src) $post_audio = substituteAudio('<audio src="'.$src.'">');
				}
			} else if ($post_format == 'image' && !$post_thumb) {
				if (($src = getPostImage($post_content_full))!='')
					$post_thumb = getResizedImageTag($src, $post_thumb_w, $post_thumb_h);
			} else if ($post_format == 'link') {
				$post_url_data = getPostLink($post_content_full, false);
				$post_url = $post_url_data['url'];
				$post_url_target = $post_url_data['target'];
			}
		}
		// Get all post's categories
		$post_categories = getCategoriesByPostId($post_id);
		$post_categories_str = '';
		$post_accent_color = '';
		$post_accent_category = '';
		$ex_cats = explode(',', get_theme_option('exclude_cats'));
		for ($i = 0; $i < count($post_categories); $i++) {
			if (in_array($post_categories[$i]['term_id'], $ex_cats)) continue;
			if ($post_accent_category=='') {
				if (get_theme_option('close_category')=='parental') {
					$parent_cat_id = 0;//(int) get_custom_option('category_id');
					$parent_cat = getParentCategory($post_categories[$i]['term_id'], $parent_cat_id);
					if ($parent_cat) {
						$post_accent_category = $parent_cat['name'];
						if ($post_accent_color=='') $post_accent_color = getCategoryInheritedProperty($parent_cat['term_id'], 'theme_accent_color');
					}
				} else {
					$post_accent_category = $post_categories[$i]['name'];
					if ($post_accent_color=='') $post_accent_color = getCategoryInheritedProperty($post_categories[$i]['term_id'], 'theme_accent_color');
				}
			}
			$post_categories_str .= '<a class="cat_link" href="' . $post_categories[$i]['link'] . '">'
				. $post_categories[$i]['name'] 
				. ($i < count($post_categories)-1 ? ',' : '')
				. '</a> ';
		}
		if ($post_accent_category=='' && count($post_categories)>0) {
			$post_accent_category = $post_categories[0]['name'];
			if ($post_accent_color=='') $post_accent_color = getCategoryInheritedProperty($post_categories[0]['term_id'], 'theme_accent_color');
		}		
		if ($style=='puzzles') {
			$post_hover_bg  = get_custom_option('puzzles_post_bg', null, $post_id);
			$post_hover_pos = get_custom_option('puzzles_post_position', null, $post_id);
		}
		
		// Prepare reviews block
		$reviewsBlock = '';
		$avg_author = 0;
		if ( $rating > 0 && get_custom_option('show_reviews', null, $post_id)=='yes' ) {
			$avg_author = marksToDisplay(get_post_meta($post_id, 'reviews_avg'.(get_theme_option('reviews_first')=='author' ? '' : '2'), true));
			if ($avg_author > 0) {
				$reviewsBlock .= '<div class="reviews_summary blog_reviews' . ($style=='puzzles' ? ' theme_puzzles' : '') . '"' . ($style=='puzzles' && $post_hover_bg!='' && $post_hover_bg!='default' ? ' style="background-color:'.$post_hover_bg.';"' : '') . '>'
					. '<div class="criteria_summary criteria_row">' . getReviewsSummaryStars($avg_author) . '</div>'
					. '</div>';
			}
		}
		
		// Start output
		$THEMEREX_sc_blogger_counter++;
		$output .= '<div class="sc_blogger_item'
				. ($style == 'puzzles' ? ' sc_blogger_item_puzzles' : '')
				. ($style == 'date' ? ' sc_blogger_item_date' : '')
				. ($style == 'accordion' ? ' sc_accordion_item' : '')
				. ($dir!='vertical' && $style!='puzzles' ? ' sc_column_item sc_column_item_'.$THEMEREX_sc_blogger_counter : '')
				. ($border == 1 ? ' sc_blogger_item_bordered' : '')
				. ($THEMEREX_sc_blogger_counter % 2 == 1 ? ' odd' : ' even') 
				. ($THEMEREX_sc_blogger_counter == 1 ? ' first' : '') 
				. '">
			';
		if ($style == 'puzzles' ) {
			$show_content_block = !in_array($post_format, array('link', 'image')) || !$post_thumb;
			$puzzles_style = get_custom_option('puzzles_style');
			$no_thumb = in_array($post_format, array('quote', 'link', 'image')) || (!$post_thumb && (!$post_gallery || $post_protected));
			$output .= '<div class="post_thumb image_wrapper post_format_'.$post_format.' '.($no_thumb ? 'no_thumb' : $post_hover_pos) .'"' . ($post_video && !$post_protected ? ' data-video="'.htmlspecialchars($post_video).'"' : '').'>';
			if ($post_thumb && ($post_format!='gallery' || !$post_gallery || get_custom_option('gallery_instead_image')=='no')) {								// If post have thumbnail - show it
				if ($post_format=='link' && $post_url!='')
					$output .= '<a href="'.$post_url.'"'.($post_url_target ? ' target="'.$post_url_target.'"' : '').'>'.$post_thumb.'</a>';
				else if ($post_link!='')
					$output .= '<a href="'.$post_link.'">'.$post_thumb.'</a>';
				else
					$output .= $post_thumb; 
				if ($post_format == 'video' && $post_video && !$post_protected)
					$output .= '<a href="#" class="post_video_play icon-play"></a>';
			} else if ($post_gallery && !$post_protected) {	// If post have gallery - show it
				if ($post_link!='')
					$output .= '<a href="'.$post_link.'">'.$post_gallery.'</a>';
				else
					$output .= $post_gallery;
			} else if ($post_video && !$post_protected) {	// If post have video - show it
				$output .= $post_video; 
				$show_content_block = false;
			}
			$output .= '<span class="post_format theme_accent_bg '.$post_icon.'"'.(themerex_substr($post_accent_color, 0, 1)=='#' ? ' style="background-color: '.$post_accent_color.'"' : '').'></span>';
			if ($puzzles_style=='heavy') {
				if ($post_accent_category!='') { 
					$output .= '<span class="post_category theme_accent_bg"'.(themerex_substr($post_accent_color, 0, 1)=='#' ? ' style="background-color: '.$post_accent_color.'"' : '').'>'.$post_accent_category.'</span>';
				}
			} else {
				if ($show_content_block) {
					$output .= '<div class="post_content_light">'
						. ($post_accent_category!='' ? '<span class="post_category theme_accent_bg"' . (themerex_substr($post_accent_color, 0, 1)=='#' ? ' style="background-color: '.$post_accent_color.'"' : '')  . '>' . $post_accent_category . '</span><br>' : '')
						. '<h2 class="post_subtitle theme_puzzles"' . ($post_hover_bg!='' && $post_hover_bg!='default' ? ' style="background-color:'.$post_hover_bg.';"' : '') . '><a href="' . $post_link . '">' . $post_title . '</a></h2><br>'
						. $reviewsBlock
						. '</div>';
				}
			}
			if ($show_content_block) {
				$output .= '<div class="post_content_wrapper theme_puzzles"'.($post_hover_bg!='' && $post_hover_bg!='default' ? ' style="background-color:'.$post_hover_bg.';"' : '').'>';
				if (!in_array($post_format, array('quote', 'aside'))) { 
					$output .= '<h2 class="post_subtitle"><a href="'.$post_link.'">'.$post_title.'</a></h2>';
					$output .= $reviewsBlock;
				}
				$output .= '<div class="post_descr">'.$post_excerpt.'</div>'
					. '<div class="post_content_padding theme_puzzles"';
				if ($post_hover_bg!='' && $post_hover_bg!='default') {
					$rgb = Hex2RGB($post_hover_bg);
					$post_hover_ie = str_replace('#', '', $post_hover_bg);
					$output .= " style=\"
						background: -moz-linear-gradient(top,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%, rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%, rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
						background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0)), color-stop(1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01)), color-stop(50%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1)));
						background: -webkit-linear-gradient(top,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
						background: -o-linear-gradient(top,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
						background: -ms-linear-gradient(top,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
						background: linear-gradient(to bottom,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
						filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00{$post_hover_ie}', endColorstr='#$post_hover_ie',GradientType=0 );
						\""; 
				}
				$output .= '></div></div>';
			}
			if (!$post_protected) {
				if ( $post_audio )							// If post have audio - show it
					$output .= $post_audio;
			}
			$output .= '</div>';
		} else {
			$title = '<' . $post_title_tag 
				. ' class="sc_blogger_title sc_title entry-title'
					. ($style=='accordion' ? ' sc_accordion_title' : '')
					. ($style=='underline' ? ' sc_title_underline' : '')
					. (themerex_strpos($style, 'bubble')!==false ? ' sc_title_bubble sc_title_'.$style : '')
					. '">'
					. '<a href="' . ($style=='accordion' ? '#' : $post_link) . '">'
						. (themerex_substr($style, 0, 6)=='bubble' 
							? '<span class="sc_title_bubble_icon '.($post_icon!='' ? ' '.$post_icon : '').'"'.($bubble_color!='' ? ' style="background-color:'.$bubble_color.'"' : '').'></span>' 
							: '')
						. ($style=='accordion' ? '<span class="sc_accordion_icon"></span>' : '')
						. $post_title 
						. ($style=='accordion' ? '' : $reviewsBlock)
					. '</a>'
				. '</' . $post_title_tag . '>';
			if ($style == 'date' ) {
				$output .= '<div class="date_area">'
						.'<div class="date_month">' . prepareDateForTranslation(date('M', strtotime($post_date_sql))) . '</div>'
						.'<div class="date_day">' . date('d', strtotime($post_date)) . '</div>'
					.'</div>';
			} else if (themerex_strpos($style, 'image')!==false) {
				$output .= ($style == 'image_small' ? '<div class="title_area">'.$title.'</div>' : ($post_thumb ? '<div class="sc_blogger_image image_wrapper">' . ($post_link!='' ? '<a href="'.$post_link.'">' : '') . $post_thumb . ($post_link!='' ? '</a>' : '') . '</div>' : '')) 
					.'<div class="post_wrapper">'
						.'<div class="post_info theme_info">'
							.__('Posted', 'themerex').' <span class="post_date theme_text">'.$post_date_diff.'</span> '
							.'<span class="post_author">'.__('by', 'themerex').' <a href="'.$post_author_url.'" class="post_author">'.$post_author.'</a></span>'
							.'<br />'
							.($post_categories_str!='' 
								? '<span class="post_cats">'.__('in', 'themerex').' '.$post_categories_str.'</span>'
								: '')
							.($counters=='none' ? '' : 
								'<span class="post_comments"><a href="'.$post_comments_link.'"><span class="comments_icon theme_info icon-'.($orderby=='comments' || $counters=='comments' ? 'chat-1' : 'eye').'"></span><span class="comments_number">'.($orderby=='comments' || $counters=='comments' ? $post_comments : $post_views).'</span></a></span>')
						.'</div>'
						.($style == 'image_small' ? '' : '<div class="title_area">');
			}
			$output .= $style == 'image_small' ? '' : $title;
			if (themerex_strpos($style, 'image')!==false ) {
				$output .= $style == 'image_small' ? ($post_thumb ? '<div class="sc_blogger_image image_wrapper">' . ($post_link!='' ? '<a href="'.$post_link.'">' : '') . $post_thumb . ($post_link!='' ? '</a>' : '') . '</div>' : '') : '</div>';
			}
			if ($descr > 0) {
				$output .= '<div class="sc_blogger_content' . ($style=='accordion' ? ' sc_accordion_content' : '') . '">'.$post_excerpt
					//. ($readmore ? '&nbsp;<a href="' . $post_link . '" class="readmore">'.($readmore==1 ? '&raquo;' : $readmore).'</a>' : '')
					. '</div>';
			}
			if (themerex_strpos($style, 'image')!==false ) {
				$output .= '</div>';
			}
		}
		$output .= '</div>';
	}
	wp_reset_postdata();

	if ($style!=='puzzles')
		$output .= '</div>';
	if ($style=='accordion') {
		wp_enqueue_script('jquery-ui-accordion', false, array('jquery','jquery-ui-core'), null, true);
		$output .= '<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(\'div' . ($id ? '#' . $id : '') . '.sc_blogger.sc_accordion\').accordion({
						header: "h3",
						collapsible: true,
						heightStyle: "content"
					});
				});
			</script>';
	}
	if ($border == 1) {
		$output .= '<script type="text/javascript">
				jQuery(document).ready(function() {
					var maxHeight = 0;
					for (var i=0; i<2; i++) {
						jQuery(\'.sc_blogger_item_bordered\').each(function(){
							if (i > 0) {
								if (maxHeight>0) jQuery(this).height(maxHeight);
							} else if (jQuery(this).height() > maxHeight)
								maxHeight = jQuery(this).height();
						});
					}
				});
			</script>';
	}
	return $output;
}
// ---------------------------------- [/blogger] ---------------------------------------



// ---------------------------------- [button] ---------------------------------------


add_shortcode('button', 'sc_button');

/*
[button id="unique_id" style="regular|grey|red|green|blue" size="small|medium|large" link='#' target='']Button caption[/button]
*/
function sc_button($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "regular",
		"size" => "medium",
		"color" => "",
		"link" => "",
		"target" => "",
		"align" => "",
		"rel" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		.($color !== '' ? 'background-color:' . $color . ';' : '');
	return empty($link) ? '' : '<a href="' . $link . '"' . (!empty($target) ? ' target="' . $target . '"' : '') . (!empty($rel) ? ' rel="' . $rel . '"' : '') . ($id ? ' id="' . $id . '"' : '') . ' class="sc_button sc_button_style_' . $style . ($style=='regular' ? ' theme_button' : '') . ' sc_button_size_' . $size . ($align ? ' align'.$align : '') . '"'.($s!='' ? ' style="'.$s.'"' : '').'>' . do_shortcode($content) . '</a>';
}

// ---------------------------------- [/button] ---------------------------------------




// ---------------------------------- [columns] ---------------------------------------


add_shortcode('columns', 'sc_columns');

/*
[columns id="unique_id" count="number"]
	[column_item id="unique_id" span="2 - number_columns"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta, odio arcu vut natoque dolor ut, enim etiam vut augue. Ac augue amet quis integer ut dictumst? Elit, augue vut egestas! Tristique phasellus cursus egestas a nec a! Sociis et? Augue velit natoque, amet, augue. Vel eu diam, facilisis arcu.[/column_item]
	[column_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in. Magna eros hac montes, et velit. Odio aliquam phasellus enim platea amet. Turpis dictumst ultrices, rhoncus aenean pulvinar? Mus sed rhoncus et cras egestas, non etiam a? Montes? Ac aliquam in nec nisi amet eros! Facilisis! Scelerisque in.[/column_item]
	[column_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim. Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna.[/column_item]
	[column_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna. Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim.[/column_item]
[/columns]
*/
$THEMEREX_sc_columns_counter = 0;
$THEMEREX_sc_columns_after_span2 = $THEMEREX_sc_columns_after_span3 = $THEMEREX_sc_columns_after_span4 = false;
function sc_columns($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"count" => "2",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	global $THEMEREX_sc_columns_counter, $THEMEREX_sc_columns_after_span2, $THEMEREX_sc_columns_after_span3, $THEMEREX_sc_columns_after_span4;
	$THEMEREX_sc_columns_counter = 1;
	$THEMEREX_sc_columns_after_span2 = $THEMEREX_sc_columns_after_span3 = $THEMEREX_sc_columns_after_span4 = false;
	$count = max(1, min(5, (int) $count));
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_columns sc_columns_count_' . $count . '"'.($s!='' ? ' style="'.$s.'"' : '').'>' . do_shortcode($content).'</div>';
}


add_shortcode('column_item', 'sc_column_item');

//[column_item]
function sc_column_item($atts, $content=null) {
	extract(shortcode_atts( array(
		"id" => "",
		"span" => "1"
	), $atts));
	global $THEMEREX_sc_columns_counter, $THEMEREX_sc_columns_after_span2, $THEMEREX_sc_columns_after_span3, $THEMEREX_sc_columns_after_span4;
	$span = max(1, min(4, (int) $span));
	$output = '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_column_item sc_column_item_'.$THEMEREX_sc_columns_counter 
					. ($THEMEREX_sc_columns_counter % 2 == 1 ? ' odd' : ' even') 
					. ($THEMEREX_sc_columns_counter == 1 ? ' first' : '') 
					. ($span > 1 ? ' span_'.$span : '') 
					. ($THEMEREX_sc_columns_after_span2 ? ' after_span_2' : '') 
					. ($THEMEREX_sc_columns_after_span3 ? ' after_span_3' : '') 
					. ($THEMEREX_sc_columns_after_span4 ? ' after_span_4' : '') 
					. '">' . do_shortcode($content) . '</div>';
	$THEMEREX_sc_columns_counter += $span;
	$THEMEREX_sc_columns_after_span2 = $span==2;
	$THEMEREX_sc_columns_after_span3 = $span==3;
	$THEMEREX_sc_columns_after_span4 = $span==4;
	return $output;
}

// ---------------------------------- [/columns] ---------------------------------------





// ---------------------------------- [Contact form] ---------------------------------------

add_shortcode("contact_form", "sc_contact_form");

//[contact_form id="unique_id" title="Contact Form" description="Mauris aliquam habitasse magna a arcu eu mus sociis? Enim nunc? Integer facilisis, et eu dictumst, adipiscing tempor ultricies, lundium urna lacus quis."]
function sc_contact_form($atts, $content = null) {
	extract(shortcode_atts(array(
		"id" => "",
		"title" => "",
		"description" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	wp_enqueue_script( 'contact_form', get_template_directory_uri().'/js/_contact_form.js', array('jquery'), null, true );
	global $THEMEREX_ajax_nonce, $THEMEREX_ajax_url;
	return '<div ' . ($id ? ' id="' . $id . '"' : '') . 'class="sc_contact_form"'.($s!='' ? ' style="'.$s.'"' : '') .'>'
				. ($title ? '<h4 class="title">' . $title . '</h4>' : '')
				. ($description ? '<div class="description">' . $description . '</div>' : '')
				. '<form' . ($id ? ' id="' . $id . '"' : '') . ' method="post" action="' . $THEMEREX_ajax_url . '">'
					.'<div class="field field_name"><input type="text" id="sc_contact_form_username" name="username" placeholder="' . __('Your Name*', 'themerex') . '"></div>'
					.'<div class="field field_email"><input type="text" id="sc_contact_form_email" name="email" placeholder="' . __('Your Email*', 'themerex') . '"></div>'
					.'<div class="field field_message"><textarea id="sc_contact_form_message" name="message" placeholder="' . __('Your Message*', 'themerex') . '"></textarea></div>'
					.'<div class="sc_contact_form_button"><a href="#" class="sc_contact_form_submit theme_button"><span>' . __('Send', 'themerex') . '</span></a></div>'
					.'<div class="result sc_infobox"></div>'
				.'</form>'
				.'<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery(".sc_contact_form .sc_contact_form_submit").click(function(e){
							userSubmitForm(jQuery(this).parents("form"), "' . $THEMEREX_ajax_url . '", "' . $THEMEREX_ajax_nonce . '");
							e.preventDefault();
							return false;
						});
					});
				</script>'
			.'</div>';
}
// ---------------------------------- [/Contact form] ---------------------------------------



						


// ---------------------------------- [dropcaps] ---------------------------------------

add_shortcode('dropcaps', 'sc_dropcaps');

//[dropcaps id="unique_id" style="1-3"]paragraph text[/dropcaps]
function sc_dropcaps($atts, $content=null){
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "1"
    ), $atts));
	$style = min(3, max(1, $style));
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_dropcaps sc_dropcaps_style_' . $style . '">' 
			. do_shortcode('<span class="sc_dropcap">' . themerex_substr($content, 0, 1) . '</span>' . themerex_substr($content, 1))
		. '</div>';
}
// ---------------------------------- [/dropcaps] ---------------------------------------





// ---------------------------------- [Google maps] ---------------------------------------

add_shortcode("googlemap", "sc_google_map");

//[googlemap id="unique_id" address="your_address" width="width_in_pixels_or_percent" height="height_in_pixels"]
function sc_google_map($atts, $content = null) {
	extract(shortcode_atts(array(
		"id" => "sc_googlemap",
		"width" => "100%",
		"height" => "240",
		"address" => "",
		"latlng" => "",
		"zoom" => 16,
		"style" => '',
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$ed = themerex_substr($width, -1)=='%' ? '%' : 'px';
	if ((int) $width < 100 && $ed != '%') $width='100%';
	if ((int) $height < 50) $height='100';
	$width = (int) str_replace('%', '', $width);
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		.($width >= 0 ? 'width:' . $width . $ed . ';' : '')
		.($height >= 0 ? 'height:' . $height . 'px;' : '');

	wp_enqueue_script( 'googlemap', 'http://maps.google.com/maps/api/js?sensor=false', array(), null, true );
	wp_enqueue_script( 'googlemap_init', get_template_directory_uri().'/js/_googlemap_init.js', array(), null, true );
	return '<script type="text/javascript">
	    	jQuery(document).ready(function(){
				googlemap_init(jQuery("#' . $id . '").get(0), {address: "' . $address . '", latlng: "' . $latlng . '", zoom: '.$zoom.', style: "'.$style.'"});
	    	});
		</script>'
		.'<div id="' . $id . '" class="sc_googlemap"'.($s!='' ? ' style="'.$s.'"' : '') .'></div>';
}
// ---------------------------------- [/Google maps] ---------------------------------------





// ---------------------------------- [hide] ---------------------------------------


add_shortcode('hide', 'sc_hide');

/*
[hide selector="unique_id"]
*/
function sc_hide($atts, $content=null){	
    extract(shortcode_atts(array(
		"selector" => ""
    ), $atts));
	$selector = trim(chop($selector));
	return $selector == '' ? '' : 
		'<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("' . $selector . '").hide();
			});
		</script>';
}
// ---------------------------------- [/hide] ---------------------------------------





// ---------------------------------- [highlight] ---------------------------------------


add_shortcode('highlight', 'sc_highlight');

/*
[highlight id="unique_id" color="fore_color's_name_or_#rrggbb" backcolor="back_color's_name_or_#rrggbb" style="custom_style"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/highlight]
*/
function sc_highlight($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"color" => "",
		"backcolor" => "",
		"style" => ""
    ), $atts));
	$s = ($color != '' ? 'color:' . $color . ';' : '')
		.($backcolor != '' ? 'background-color:' . $backcolor . ';' : '')
		.($style != '' ? $style : '');
	return '<span' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_highlight"'.($s!='' ? ' style="'.$s.'"' : '').'>' . do_shortcode($content) . '</span>';
}
// ---------------------------------- [/highlight] ---------------------------------------





// ---------------------------------- [image] ---------------------------------------


add_shortcode('image', 'sc_image');

/*
[image id="unique_id" src="image_url" width="width_in_pixels" height="height_in_pixels" title="image's_title" align="left|right"]
*/
function sc_image($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"src" => "",
		"title" => "",
		"align" => "",
		"width" => "-1",
		"height" => "-1"
    ), $atts));
	$s = ($width > 0 ? 'width:' . $width . 'px;' : '')
		.($height > 0 ? 'height:' . $height . 'px;' : '')
		.($align != '' ? 'float:' . $align . ';' : '');
	return '<figure' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_image ' . ($align ? ' sc_image_align_' . $align : '') . '"'.($s!='' ? ' style="'.$s.'"' : '').'>'
				.'<img src="' . $src . '" border="0" alt="" />'.(trim($title) ? '<figcaption><span>' . $title . '</span></figcaption>' : '') 
			. '</figure>';
}

// ---------------------------------- [/image] ---------------------------------------






// ---------------------------------- [infobox] ---------------------------------------


add_shortcode('infobox', 'sc_infobox');

/*
[infobox id="unique_id" style="regular|info|success|error|result" static="0|1"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/infobox]
*/
function sc_infobox($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "regular",
		"static" => "1",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '');
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_infobox sc_infobox_style_' . $style . ($static==0 ? ' sc_infobox_closeable' : '') . '"'.($s!='' ? ' style="'.$s.'"' : '').'>'
			. do_shortcode($content) 
			. '</div>';
}

// ---------------------------------- [/infobox] ---------------------------------------





// ---------------------------------- [line] ---------------------------------------


add_shortcode('line', 'sc_line');

/*
[line id="unique_id" style="none|solid|dashed|dotted|double|groove|ridge|inset|outset" top="margin_in_pixels" bottom="margin_in_pixels" width="width_in_pixels_or_percent" height="line_thickness_in_pixels" color="line_color's_name_or_#rrggbb"]
*/
function sc_line($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "solid",
		"color" => "",
		"width" => "-1",
		"height" => "-1",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$ed = themerex_substr($width, -1)=='%' ? '%' : 'px';
	$width = (int) str_replace('%', '', $width);
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		.($width >= 0 ? 'width:' . $width . $ed . ';' : '')
		.($height >= 0 ? 'border-top-width:' . $height . 'px;' : '')
		.($style != '' ? 'border-top-style:' . $style . ';' : '')
		.($color != '' ? 'border-top-color:' . $color . ';' : '');
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_line' . ($style != '' ? ' sc_line_style_' . $style : '') . '"'.($s!='' ? ' style="'.$s.'"' : '').'></div>';
}

// ---------------------------------- [/line] ---------------------------------------





// ---------------------------------- [list] ---------------------------------------

add_shortcode('list', 'sc_list');

/*
[list id="unique_id" style="regular|check|mark|error"]
	[list_item id="unique_id" title="title_of_element"]Et adipiscing integer.[/list_item]
	[list_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in.[/list_item]
	[list_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer.[/list_item]
	[list_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus.[/list_item]
[/list]
*/
$THEMEREX_sc_list_counter = 0;
function sc_list($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "default",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	global $THEMEREX_sc_list_counter;
	$THEMEREX_sc_list_counter = 0;
	if (trim($style) == '') $style = 'default';
	return '<ul' . ($id ? ' id="' . $id . '"' : '') . ($style!='default' ? ' class="sc_list sc_list_style_' . $style . '"' : '').($s!='' ? ' style="'.$s.'"' : '') . '>'
			. do_shortcode($content) 
			. '</ul>';
}


add_shortcode('list_item', 'sc_list_item');

//[list_item]
function sc_list_item($atts, $content=null) {
	extract(shortcode_atts( array(
		"id" => "",
		"style" => "default",
		"title" => ""
	), $atts));
	global $THEMEREX_sc_list_counter;
	$THEMEREX_sc_list_counter++;
	if (trim($style) == '') $style = 'default';
	return '<li' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_list_item' . ($THEMEREX_sc_list_counter % 2 == 1 ? ' odd' : ' even') . ($THEMEREX_sc_list_counter == 1 ? ' first' : ''). ($style!='default' ? ' sc_list_style_' . $style : '') . '"' . ($title ? ' title="' . $title . '"' : '') . '><span class="sc_list_icon"></span>' . do_shortcode($content).'</li>';
}

// ---------------------------------- [/list] ---------------------------------------




// ---------------------------------- [puzzles] ---------------------------------------


add_shortcode('puzzles', 'sc_puzzles');

/*
[puzzles id="unique_id" style="class_name"][blogger style="puzzles" ...][blogger style="puzzles" ...][/puzzles]
*/
function sc_puzzles($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"class" => "",
		"style" => "",
		"top" => "",
		"bottom" => "-1",
		"left" => "",
		"right" => "-1"
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		.'overflow:hidden;'
		.$style;
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_section sc_puzzles' . ($class ? ' '.$class : '') . '"'.($s!='' ? ' style="'.$s.'"' : '').'>'
			. do_shortcode($content) 
			. '</div>';
}
// ---------------------------------- [/puzzles] ---------------------------------------





// ---------------------------------- [quote] ---------------------------------------


add_shortcode('quote', 'sc_quote');

/*
[quote id="unique_id" style="1|2" cite="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/quote]
*/
function sc_quote($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "1",
		"title" => "",
		"cite" => ""
    ), $atts));
	$cite_param = $cite != '' ? ' cite="' . $cite . '"' : '';
	$title = $title=='' ? $cite : $title;
	$style = min(2, max(1, $style));
	return ($style == 1 ? '<blockquote' : '<q' ) . ($id ? ' id="' . $id . '"' : '') . $cite_param . ' class="sc_quote sc_quote_style_' . $style . '"' . '>' . do_shortcode($content) . ($style == 1 ? ($cite!='' ? '<cite><a href="'.$cite.'">'.$title.'</a></cite>' : ($title!='' ? '<cite>'.$title.'</cite>' : '')).'</blockquote>' : '</q>');
}

// ---------------------------------- [/quote] ---------------------------------------




// ---------------------------------- [section] ---------------------------------------


add_shortcode('section', 'sc_section');

/*
[section id="unique_id" style="class_name"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/section]
*/
function sc_section($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"class" => "",
		"style" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		.$style;
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_section' . ($class ? ' '.$class : '') . '"'.($s!='' ? ' style="'.$s.'"' : '').'>' 
			. do_shortcode($content) 
			. '</div>';
}
// ---------------------------------- [/section] ---------------------------------------





// ---------------------------------- [skills] ---------------------------------------


add_shortcode('skills', 'sc_skills');

/*
[skills id="unique_id"]
	[skills_item title="Scelerisque pid" level="50%"]
	[skills_item title="Scelerisque pid" level="50%"]
	[skills_item title="Scelerisque pid" level="50%"]
[/skills]
*/
$THEMEREX_sc_skills_counter = 0;
function sc_skills($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	global $THEMEREX_sc_skills_counter;
	$THEMEREX_sc_skills_counter = 0;
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_skills"'.($s!='' ? ' style="'.$s.'"' : '') .'>'
			. do_shortcode($content)
			. '</div>';
}


add_shortcode('skills_item', 'sc_skills_item');

//[skills_item]
function sc_skills_item($atts, $content=null) {
	extract(shortcode_atts( array(
		"id" => "",
		"title" => "",
		"level" => "",
		"color" => ""
	), $atts));
	global $THEMEREX_sc_skills_counter;
	$THEMEREX_sc_skills_counter++;
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_skills_item' . ($THEMEREX_sc_skills_counter % 2 == 1 ? ' odd' : ' even') . ($THEMEREX_sc_skills_counter == 1 ? ' first' : '') . '">'
		. '<div class="sc_skills_progressbar">'
			. '<span class="sc_skills_progress" style="width:' . (themerex_substr($level, -1)=='%' ? $level : $level.'%') . '">'
				. '<span class="sc_skills_caption">' . $title . '</span>'
			. '</span>' 
			. '<span class="sc_skills_level"' . ($color ? ' style="background-color:' . $color . '"' : '') . '>' . $level . '</span>'
		. '</div>'
		. '</div>';
}

// ---------------------------------- [/skills] ---------------------------------------






// ---------------------------------- [slider] ---------------------------------------

add_shortcode('slider', 'sc_slider');

/*
[slider id="unique_id" engine="revo|flex" alias="revolution_slider_alias" titles="0|1|2" cat="category_id or slug" count="posts_number" ids="comma_separated_id_list" offset="" width="" height="" align="" top="" bottom=""]
*/
function sc_slider($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"engine" => "flex",
		"links" => "0",
		"controls" => "0",
		"titles" => "0",
		"alias" => "",
		"ids" => "",
		"cat" => "",
		"count" => "0",
		"offset" => "",
		"orderby" => "date",
		"order" => 'desc',
		"width" => "",
		"height" => "",
		"align" => "",
		"border" => "0",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		.(!empty($width) ? 'width:' . $width . (themerex_strpos($width, '%')!==false ? '' : 'px').';' : '')
		.(!empty($height) ? 'height:' . $height . (themerex_strpos($height, '%')!==false ? '' : 'px').';' : '')
		;
	
	$output = '<div' . ($id ? ' id="' . $id . '"' : '') 
			. ' class="sc_slider'
				. ' sc_slider_' . $engine
				. ($controls > 0 ? ' sc_slider_controls' : '')
				. ($align!='' ? ' align'.$align : '')
				. ($border > 0 ? ' sc_slider_border' : '')
				. '"'
			. ($s!='' ? ' style="'.$s.'"' : '')
		. '>';

	if ($engine=='revo') {
		if (is_plugin_active('revslider/revslider.php') && !empty($alias))
			$output .= do_shortcode('[rev_slider '.$alias.']');
		else
			$output = '';
	} else if ($engine=='flex') {
		
		$output .= '<ul class="slides">';

		global $post;

		if (!empty($ids)) {
			$posts = explode(',', $ids);
			$count = count($posts);
		}
	
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => 1,
			'order' => $order=='asc' ? 'asc' : 'desc',
		);

		if ($offset > 0 && empty($ids)) {
			$args['offset'] = $offset;
		}

		$args = addSortOrderInQuery($args, $orderby, $order, true);
		$args = addPostsAndCatsInQuery($args, $ids, $cat);


		$query = new WP_Query( $args );

		while ( $query->have_posts() ) { 
			$query->the_post();
			$post_id = get_the_ID();
			$post_link = get_permalink();
			$post_attachment = wp_get_attachment_url(get_post_thumbnail_id($post_id));
			$post_accent_color = '';
			$post_accent_category = '';
			$post_title = getPostTitle($post_id);
			$output .= '<li style="background-image:url(' . $post_attachment . ')">' . ($links>0 ? '<a href="'.$post_attachment.'" title="'.htmlspecialchars($post_title).'">' : '');
			if ($titles) {
				$post_hover_bg  = get_custom_option('puzzles_post_bg', null, $post_id);
				$post_bg = '';
				if ($post_hover_bg!='' && $post_hover_bg!='default') {
					$rgb = Hex2RGB($post_hover_bg);
					$post_hover_ie = str_replace('#', '', $post_hover_bg);
					$post_bg = "background-color: rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.8);";
				}
				$output .= '<div class="sc_slider_info' . ($titles > 1 ? ' sc_slider_info_fixed' : '') . ' theme_accent_bg"'.($post_bg!='' ? ' style="'.$post_bg.'"' : '').'>';
				$post_descr = getPostDescription();
				if (get_custom_option("slider_info_category")=='yes') { // || empty($cat)) {
					// Get all post's categories
					$post_categories = getCategoriesByPostId($post_id);
					$post_categories_str = '';
					for ($i = 0; $i < count($post_categories); $i++) {
						if ($post_accent_category=='') {
							if (get_theme_option('close_category')=='parental') {
								$parent_cat_id = 0;//(int) get_custom_option('category_id');
								$parent_cat = getParentCategory($post_categories[$i]['term_id'], $parent_cat_id);
								if ($parent_cat) {
									$post_accent_category = $parent_cat['name'];
									if ($post_accent_color=='') $post_accent_color = getCategoryInheritedProperty($parent_cat['term_id'], 'theme_accent_color');
								}
							} else {
								$post_accent_category = $post_categories[$i]['name'];
								if ($post_accent_color=='') $post_accent_color = getCategoryInheritedProperty($post_categories[$i]['term_id'], 'theme_accent_color');
							}
						}
						if ($post_accent_category!='' && $post_accent_color!='') break;
					}
					if ($post_accent_category=='' && count($post_categories)>0) {
						$post_accent_category = $post_categories[0]['name'];
						if ($post_accent_color=='') $post_accent_color = getCategoryInheritedProperty($post_categories[0]['term_id'], 'theme_accent_color');
					}
					if ($post_accent_category!='') {
						$output .= '<div class="sc_slider_category theme_accent_bg"'.(themerex_substr($post_accent_color, 0, 1)=='#' ? ' style="background-color: '.$post_accent_color.'"' : '').'>'.$post_accent_category.'</div>';
					}
				}
				$output_reviews = '';
				if (get_custom_option('show_reviews')=='yes' && get_custom_option('slider_reviews')=='yes') {
					$avg_author = marksToDisplay(get_post_meta($post_id, 'reviews_avg'.((get_theme_option('reviews_first')=='author' && $orderby != 'users_rating') || $orderby == 'author_rating' ? '' : '2'), true));
					if ($avg_author > 0) {
						$output_reviews .= '<div class="sc_slider_reviews reviews_summary blog_reviews' . (get_custom_option("slider_info_category")=='yes' ? ' after_category' : '') . '">'
							. '<div class="criteria_summary criteria_row">' . getReviewsSummaryStars($avg_author) . '</div>'
							. '</div>';
					}
				}
				if (get_custom_option("slider_info_category")=='yes') $output .= $output_reviews;
				$output .= '<h2 class="sc_slider_subtitle"><a href="'.$post_link.'">'.$post_title.'</a></h2>';
				if (get_custom_option("slider_info_category")!='yes') $output .= $output_reviews;
				if (get_custom_option('slider_descriptions')=='yes') {
					$output .= '<div class="sc_slider_descr">'.$post_descr.'</div>';
				}
				$output .= '</div>';
			} else {
				//$output .= '<a href="'. $post_link . '">'.$titles.'</a>';
			}
			$output .= ($links > 0 ? '</a>' : '' ) . '</li>';
		}
		wp_reset_postdata();
	
		$output .= '</ul>';
	
	} else
		$output = '';
	$output .= !empty($output) ? '</div>' : '';
	return $output;
}
// ---------------------------------- [/slider] ---------------------------------------





// ---------------------------------- [table] ---------------------------------------


add_shortcode('table', 'sc_table');

/*
[table id="unique_id" style="regular"]
Table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/
[/table]
*/
function sc_table($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "regular",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	$content = str_replace(
				array('<p><table', 'table></p>', '><br />'),
				array('<table', 'table>', '>'),
				html_entity_decode($content, ENT_COMPAT, 'UTF-8'));
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_table sc_table_style_' . $style . '"'.($s!='' ? ' style="'.$s.'"' : '') .'>' 
			. do_shortcode($content) 
			. '</div>';
}

// ---------------------------------- [/table] ---------------------------------------





// ---------------------------------- [tabs] ---------------------------------------

add_shortcode("tabs", "sc_tabs");

/*
[tabs id="unique_id" tab_names="Planning|Development|Support" style="1|2" initial="1 - num_tabs"]
	[tab]Randomised words which don't look even slightly believable. If you are going to use a passage. You need to be sure there isn't anything embarrassing hidden in the middle of text established fact that a reader will be istracted by the readable content of a page when looking at its layout.[/tab]
	[tab]Fact reader will be distracted by the <a href="#" class="main_link">readable content</a> of a page when. Looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using content here, content here, making it look like readable English will uncover many web sites still in their infancy. Various versions have evolved over. There are many variations of passages of Lorem Ipsum available, but the majority.[/tab]
	[tab]Distracted by the  readable content  of a page when. Looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using content here, content here, making it look like readable English will uncover many web sites still in their infancy. Various versions have  evolved over.  There are many variations of passages of Lorem Ipsum available.[/tab]
[/tabs]
*/
$THEMEREX_sc_tab_counter = 0;
$THEMEREX_sc_tab_id = '';
function sc_tabs($atts, $content = null) {
    extract(shortcode_atts(array(
		"id" => "",
		"tab_names" => "",
		"initial" => "1",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	global $THEMEREX_sc_tab_counter, $THEMEREX_sc_tab_id;
	$THEMEREX_sc_tab_counter = 0;
	$THEMEREX_sc_tab_id = $id;
	$title_chunks = explode("|", $tab_names);
	$initial = max(1, min(count($title_chunks), (int) $initial));
	$tabs_output = '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_tabs"'.($s!='' ? ' style="'.$s.'"' : '') .'>'
					.'<ul class="tabs">';
	$titles_output = '';
	for ($i = 0; $i < count($title_chunks); $i++) {
		$classes = array('tab_names');
		if ($i == 0) $classes[] = 'first';
		else if ($i == count($title_chunks) - 1) $classes[] = 'last';
		$titles_output .= '<li class="'.join(' ', $classes).'"><a href="#'.($THEMEREX_sc_tab_id!='' ? $THEMEREX_sc_tab_id : 'sc_tabs').'_'.($i+1).'" class="theme_button">' . $title_chunks[$i] . '</a></li>';
	}

	wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);

	$tabs_output .= $titles_output
		. '</ul>' 
		. do_shortcode($content) 
		. '<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(\'div' . ($id ? '#' . $id : '') . '.sc_tabs\').tabs({
						active: '.($initial-1).'
					});
				});
			</script>'
		.'</div>';
	return $tabs_output;
}


add_shortcode("tab", "sc_tab");

//[tab id="tab_id"]
function sc_tab($atts, $content = null) {
	global $THEMEREX_sc_tab_counter, $THEMEREX_sc_tab_id;
	$THEMEREX_sc_tab_counter++;
	return '<div id="' . ($THEMEREX_sc_tab_id!='' ? $THEMEREX_sc_tab_id : 'sc_tabs') . '_' . $THEMEREX_sc_tab_counter . '" class="content' . ($THEMEREX_sc_tab_counter % 2 == 1 ? ' odd' : ' even') . ($THEMEREX_sc_tab_counter == 1 ? ' first' : '') . '">' 
			. do_shortcode($content) 
			. '</div>';
}
// ---------------------------------- [/tabs] ---------------------------------------






// ---------------------------------- [team] ---------------------------------------


add_shortcode('team', 'sc_team');

/*
[team id="unique_id" style="normal|big"]
	[team_item user="user_login"]
[/team]
*/
$THEMEREX_sc_team_counter = 0;
function sc_team($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"style" => "normal",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	global $THEMEREX_sc_team_counter;
	$THEMEREX_sc_team_counter = 0;
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_team ' . ($style=='big' ? 'sc_team_big' : 'sc_team_normal') . '"'.($s!='' ? ' style="'.$s.'"' : '') .'>'
			. do_shortcode($content)
			. '</div>';
}


add_shortcode('team_item', 'sc_team_item');

//[team_item]
function sc_team_item($atts, $content=null) {
	extract(shortcode_atts( array(
		"id" => "",
		"user" => ""
	), $atts));
	global $THEMEREX_sc_team_counter;
	$THEMEREX_sc_team_counter++;
	if (($user = get_user_by('login', $user)) != false) {
		$meta = get_user_meta($user->ID);
		return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_team_item sc_team_item_' . $THEMEREX_sc_team_counter . ($THEMEREX_sc_team_counter % 2 == 1 ? ' odd' : ' even') . ($THEMEREX_sc_team_counter == 1 ? ' first' : '') . '">'
				. '<div class="sc_team_item_avatar">' . get_avatar($user->data->user_email, 370) . '</div>'
				. '<h3 class="sc_team_item_title theme_title">' . $user->data->display_name . '</h3>'
				. '<div class="sc_team_item_position theme_info">' . (isset($meta['user_position'][0]) ? $meta['user_position'][0] : '') . '</div>'
				. '<div class="sc_team_item_description">' . (isset($meta['description'][0]) ? nl2br($meta['description'][0]) : '') . '</div>'
				. '<div class="sc_team_item_social">' . showUserSocialLinks(array('author_id'=>$user->ID, 'echo'=>false)) . '</div>'
			. '</div>';
	}
	return '';
}

// ---------------------------------- [/team] ---------------------------------------






// ---------------------------------- [testimonials] ---------------------------------------


add_shortcode('testimonials', 'sc_testimonials');

/*
[testimonials id="unique_id" user="user_login" style="callout|flat"]Testimonials text[/testimonials]
or
[testimonials id="unique_id" email="" name="" position="" photo="photo_url"]Testimonials text[/testimonials]
*/
function sc_testimonials($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"user" => "",
		"name" => "",
		"position" => "",
		"photo" => "",
		"nophoto" => "0",
		"email" => "",
		"style" => "flat",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	if (!empty($user) && ($author = get_user_by('login', $user)) != false) {
		$meta = get_user_meta($author->ID);
		$name = $author->data->display_name;
		$position = $meta['user_position'][0];
		$photo = get_avatar($author->data->user_email, 45);
	} else
		$photo = getResizedImageTag($photo, 45, 45);
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_testimonials sc_testimonials_style_'.$style.'"' . ($s!='' ? ' style="'.$s.'"' : '') . '>'
				. '<div class="sc_testimonials_content">' . ($style=='callout' ? '<span class="icon-quote"></span>' : '') . do_shortcode($content) . '</div>'
				. '<div class="sc_testimonials_extra"><div class="sc_testimonials_extra_inner"></div></div>'
				. '<div class="sc_testimonials_user">'
					. ($nophoto==0 ? '<div class="sc_testimonials_avatar image_wrapper">' . $photo . '</div>' : '')
					. '<h4 class="sc_testimonials_title">' . $name . '</h4>'
					. '<div class="sc_testimonials_position">' . $position . '</div>'
				. '</div>'
			. '</div>';
}

// ---------------------------------- [/testimonials] ---------------------------------------





// ---------------------------------- [title] ---------------------------------------


add_shortcode('title', 'sc_title');

/*
[title id="unique_id" style='regular|bubble_left|bubble_top|icon_left|icon_top' icon='' type="1-6"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/title]
*/
function sc_title($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"type" => "1",
		"style" => "regular",
		"icon" => "",
		"bubble_color" => "",
		"weight" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		//.(themerex_substr($style, 0, 4)=='icon' ? 'background-image:url('.get_template_directory_uri().'/images/icons/'.$icon.'.png);' : '')
		.($weight ? 'font-weight:' . $weight .';' : '');
	$type = min(6, max(1, $type));
	$icon_file = $icon!='' && file_exists(get_template_directory().'/images/icons/'.$icon.'.png');
	return '<h' . $type . ($id ? ' id="' . $id . '"' : '')
		. ($style=='underline' 
			? ' class="sc_title_underline"' 
			: (themerex_strpos($style, 'bubble')!==false ? ' class="sc_title_bubble sc_title_'.$style.'"' : (themerex_strpos($style, 'icon')!==false ? ' class="sc_title_icon sc_title_'.$style.'"' : '')))
		. ($s!='' ? ' style="'.$s.'"' : '')
		. '>'
		. (themerex_substr($style, 0, 6)=='bubble' 
			? '<span class="sc_title_bubble_icon '.($icon!='' && !$icon_file ? ' icon-'.$icon : '').'"'.($bubble_color!='' ? ' style="background-color:'.$bubble_color.'"' : '').'>'
				.($icon_file ? '<img src="'.get_template_directory_uri().'/images/icons/'.$icon.'.png" />' : '').'</span>' 
			: (themerex_substr($style, 0, 4)=='icon' ? '<img src="'.get_template_directory_uri().'/images/icons/'.$icon.'.png" />'.($style=='icon_top' ? '<br />' : '') : ''))
		. do_shortcode($content) 
		. '</h' . $type . '>';
}

// ---------------------------------- [/title] ---------------------------------------






// ---------------------------------- [toggles] ---------------------------------------


add_shortcode('toggles', 'sc_toggles');

/*
[toggles id="unique_id" initial="1 - num_elements"]
	[toggles_item title="Et adipiscing integer, scelerisque pid"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta, odio arcu vut natoque dolor ut, enim etiam vut augue. Ac augue amet quis integer ut dictumst? Elit, augue vut egestas! Tristique phasellus cursus egestas a nec a! Sociis et? Augue velit natoque, amet, augue. Vel eu diam, facilisis arcu.[/toggles_item]
	[toggles_item title="A pulvinar ut, parturient enim porta ut sed"]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in. Magna eros hac montes, et velit. Odio aliquam phasellus enim platea amet. Turpis dictumst ultrices, rhoncus aenean pulvinar? Mus sed rhoncus et cras egestas, non etiam a? Montes? Ac aliquam in nec nisi amet eros! Facilisis! Scelerisque in.[/toggles_item]
	[toggles_item title="Duis sociis, elit odio dapibus nec"]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim. Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna.[/toggles_item]
	[toggles_item title="Nec purus, cras tincidunt rhoncus"]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna. Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim.[/toggles_item]
[/toggles]
*/
$THEMEREX_sc_toggle_counter = 0;
function sc_toggles($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"initial" => "0",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	global $THEMEREX_sc_toggle_counter;
	$THEMEREX_sc_toggle_counter = 0;
	$initial = max(0, (int) $initial);
	wp_enqueue_script('jquery-effects-slide', false, array('jquery','jquery-ui-core'), null, true);
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_toggles"'.($s!='' ? ' style="'.$s.'"' : '') .'>'
			. do_shortcode($content)
			. '</div>';
}


add_shortcode('toggles_item', 'sc_toggles_item');

//[toggles_item]
function sc_toggles_item($atts, $content=null) {
	extract(shortcode_atts( array(
		"id" => "",
		"title" => "",
		"open" => "0"
	), $atts));
	global $THEMEREX_sc_toggle_counter;
	$THEMEREX_sc_toggle_counter++;
	return '<div' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_toggles_item' . ($THEMEREX_sc_toggle_counter % 2 == 1 ? ' odd' : ' even') . ($THEMEREX_sc_toggle_counter == 1 ? ' first' : '') . '">'
				. '<h5 class="sc_toggles_title'.($open==1 ? ' ui-state-active' : '').'"><a href="#"><span class="sc_toggles_icon"></span>' . $title . '</a></h5>'
				. '<div class="sc_toggles_content"'.($open==1 ? ' style="display:block;"' : '').'>' 
				. do_shortcode($content) 
				. '</div>'
			. '</div>';
}

// ---------------------------------- [/toggles] ---------------------------------------





// ---------------------------------- [tooltip] ---------------------------------------


add_shortcode('tooltip', 'sc_tooltip');

/*
[tooltip id="unique_id" title="Tooltip text here"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/tooltip]
*/
function sc_tooltip($atts, $content=null){	
    extract(shortcode_atts(array(
		"id" => "",
		"title" => ""
    ), $atts));
	return '<span' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_tooltip_parent">' . do_shortcode($content) . '<span class="sc_tooltip">' . $title . '</span></span>';
}
// ---------------------------------- [/tooltip] ---------------------------------------

						


// ---------------------------------- [video] ---------------------------------------

add_shortcode("video", "sc_video");

//[video id="unique_id" url="http://player.vimeo.com/video/20245032?title=0&amp;byline=0&amp;portrait=0" width="" height=""]
function sc_video($atts, $content = null) {
	extract(shortcode_atts(array(
		"id" => "",
		"url" => '',
		"src" => '',
		"autoplay" => '',
		"width" => '790',
		"height" => '391',
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts));
	if ($src=='' && $url=='' && isset($atts[0])) {
		$src = $atts[0];
	}
	$s = ($top !== '' ? 'margin-top:' . $top . 'px;' : '')
		.($bottom !== '' ? 'margin-bottom:' . $bottom . 'px;' : '')
		.($left !== '' ? 'margin-left:' . $left . 'px;' : '')
		.($right !== '' ? 'margin-right:' . $right . 'px;' : '')
		;
	$url = getVideoPlayerURL($src!='' ? $src : $url);
	return '<video' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_video" src="' . $url . '" width="' . $width . '" height="' . $height . '"' . ($autoplay>0 && is_single() ? ' autoplay="autoplay"' : '') . ($s!='' ? ' style="'.$s.'"' : '').' controls="controls"></video>';
/*
	return '<iframe' . ($id ? ' id="' . $id . '"' : '') . ' class="sc_video" src="' . $url . '" width="' . $width . '" height="' . $height . '"'.($s!='' ? ' style="'.$s.'"' : '').' frameborder="0" webkitAllowFullScreen="webkitAllowFullScreen" mozallowfullscreen="mozallowfullscreen" allowFullScreen="allowFullScreen"></iframe>';
*/
}
// ---------------------------------- [/video] ---------------------------------------

?>