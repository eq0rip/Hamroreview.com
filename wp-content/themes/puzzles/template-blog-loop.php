<?php
/*
 * The main blog loop template
 * 
 * @package puzzles
*/
				$post_number++;
				$post_id = get_the_ID();
				$post_protected = post_password_required();
				$post_format = get_post_format();
				if (empty($post_format)) $post_format = 'standard';
				$post_link = get_permalink();
				$post_comments_link = $counters=='comments' ? get_comments_link( $post_id ) : $post_link;
				$post_date = getDateOrDifference(get_the_date('Y-m-d H:i:s'));
				$post_comments = get_comments_number();
				$post_views = getPostViews($post_id);
				$post_icon = getPostFormatIcon($post_format);
				$post_author = get_the_author();
				$post_author_id = get_the_author_meta('ID');
				$post_author_url = get_author_posts_url($post_author_id, '');
				$post_thumb = getResizedImageTag($post_id, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
				$post_attachment = wp_get_attachment_url(get_post_thumbnail_id($post_id));
				
				// Get post title, content, excerpt and description
				if (get_theme_option('preserve_decoration')=='no') {
					// -------------- Old way to get title, excerpt and content -----------------------
					$post_title = getPostTitle();
					$post_excerpt = trim(chop($post->post_excerpt));	//getPostDescription();
					$post_descr   = in_array($post_format, array('quote', 'link')) ? get_the_content('') : getPostDescription();				//get_the_excerpt();
					$post_content = get_the_content('<span class="readmore">'.__('Read more', 'themerex').'</span>');
					$post_content_full = $post->post_content;			//get_the_content() not used, because it trim content up to <!-- more --> in each case!
					$post_content_prepared = do_shortcode($post_content_full);
				} else {
					// ----------------- New way to get title, excerpt and content -----------------------
					$post_title = get_the_title();
					global $more;
					$old_more = $more;
					$more = -1;
					$post_content_full = get_the_content();
					$post_content_prepared = do_shortcode($post_content_full);
					$more = $old_more;
					$post_content = get_the_content('<span class="readmore">'.__('Read more', 'themerex').'</span>');
					$post_excerpt = has_excerpt() || $post_protected ? get_the_excerpt() : '';
					if (empty($post_excerpt)) {
						if (($more_pos = themerex_strpos($post_content_full, '<span id="more-'))!==false) {
							$post_excerpt = themerex_substr($post_content_full, 0, $more_pos);
						} else {
							$post_excerpt = in_array($post_format, array('quote', 'link')) ? $post_content : get_the_excerpt();
						}
					}
					$post_excerpt = $post_descr = str_replace('[&hellip;]', '', force_balance_tags($post_excerpt));
				}
				// Substitute WP [gallery] shortcode
				if (get_custom_option('substitute_gallery')=='yes') {
					$post_excerpt = substituteGallery($post_excerpt, $post_id, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
					$post_descr   = substituteGallery($post_descr,   $post_id, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
					$post_content = substituteGallery($post_content, $post_id, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h'], 'none', true);
				}
				
				$post_title   = apply_filters('the_title', $post_title);
				$post_descr   = apply_filters(in_array($post_format, array('quote', 'link')) ? 'the_content' : 'the_excerpt', $post_descr);
				$post_excerpt = apply_filters('the_excerpt', $post_excerpt);
				$post_content = apply_filters('the_content', $post_content);
				$post_content = decorateMoreLink(str_replace(']]>', ']]&gt;', $post_content), '', '');

				// Substitute <video> tags to <iframe>
				if (get_custom_option('substitute_video')=='yes') {
					$post_excerpt = substituteVideo($post_excerpt, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
					$post_descr   = substituteVideo($post_descr,   $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
					$post_content = substituteVideo($post_content, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
				}
				// Substitute <audio> tags with src from soundcloud to <iframe>
				if (get_custom_option('substitute_audio')=='yes') {
					$post_excerpt = substituteAudio($post_excerpt);
					$post_descr   = substituteAudio($post_descr);
					$post_content = substituteAudio($post_content);
				}
				// Extract gallery, video and audio from full post content
				$post_gallery = $post_video = $post_audio = $post_url = $post_url_target = '';
				if ($blog_style != 'fullpost') {
					if ($post_format == 'gallery') {
						$post_gallery = buildGalleryTag(getPostGallery($post_content_full, $post_id), $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
					} else if ($post_format == 'video') {
						$post_video = getPostVideo($post_content_prepared, false);
						if ($post_video=='') {
							$src = getVideoPlayerURL(getPostVideo($post_content_prepared, true), $post_thumb!='');
							if ($src) $post_video = substituteVideo('<video src="'.$src.'">', $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);							
						}
						if ($post_video!='' && get_custom_option('substitute_video')=='yes') {
							$src = getVideoPlayerURL(getPostVideo($post_video), $post_thumb!='');
							if ($src) $post_video = substituteVideo('<video src="'.$src.'">', $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
						}
					} else if ($post_format == 'audio') {
						$post_audio = getPostAudio($post_content_prepared, false);
						if ($post_audio=='') {
							$src = getPostAudio($post_content_prepared, true);
							if ($src) $post_audio = substituteAudio('<audio src="'.$src.'">');
						}
						if ($post_audio!='' && get_custom_option('substitute_audio')=='yes') {
							$src = getPostAudio($post_audio);
							if ($src) $post_audio = substituteAudio('<audio src="'.$src.'">');
						}
					}
				}
				if ($post_format == 'image' && !$post_thumb) {
					if (($src = getPostImage($post_content_prepared))!='')
						$post_thumb = getResizedImageTag($src, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
				} else if ($post_format == 'link') {
					$post_url_data = getPostLink($post_content_prepared, false);
					$post_link = $post_url = $post_url_data['url'];
					$post_url_target = $post_url_data['target'];
				}
				// Get all post's categories
				$post_categories = getCategoriesByPostId($post_id);
				$post_categories_str = '';
				$post_accent_color = $parent_cat_id > 0 ? (!isset($post_accent_color) || $post_accent_color=='' ? getCategoryInheritedProperty($parent_cat_id, 'theme_accent_color') : $post_accent_color) : '';
				$post_accent_category = '';
				$ex_cats = explode(',', get_theme_option('exclude_cats'));
				for ($i = 0; $i < count($post_categories); $i++) {
					if (in_array($post_categories[$i]['term_id'], $ex_cats)) continue;
					if ($post_accent_category=='') {
						if (get_theme_option('close_category')=='parental') {
							$parent_cat = getParentCategory($post_categories[$i]['term_id'], $parent_cat_id);
							if ($parent_cat) {
								$post_accent_category = $parent_cat['name'];
								if ($post_accent_color=='' && $parent_cat_id==0) $post_accent_color = getCategoryInheritedProperty($parent_cat['term_id'], 'theme_accent_color');
							}
						} else {
							$post_accent_category = $post_categories[$i]['name'];
							if ($post_accent_color=='' && $parent_cat_id==0) $post_accent_color = getCategoryInheritedProperty($post_categories[$i]['term_id'], 'theme_accent_color');
						}
					}
					$post_categories_str .= '<a class="cat_link" href="' . $post_categories[$i]['link'] . '">'
						. $post_categories[$i]['name'] 
						. ($i < count($post_categories)-1 ? ',' : '')
						. '</a> ';
				}
				if ($post_accent_category=='' && count($post_categories)>0) {
					$post_accent_category = $post_categories[0]['name'];
					if ($post_accent_color=='' && $parent_cat_id==0) $post_accent_color = getCategoryInheritedProperty($post_categories[0]['term_id'], 'theme_accent_color');
				}
				// Get all post's tags
				$post_tags_str = '';
				if (($post_tags = get_the_tags()) != 0) {
					$tag_number=0;
					foreach ($post_tags as $tag) {
						$tag_number++;
						$post_tags_str .= '<a class="tag_link" href="' . get_tag_link($tag->term_id) . '">' . $tag->name . ($tag_number==count($post_tags) ? '' : ',') . '</a> ';
					}
				}
				// Load teplate for current Blog Style
				require(get_template_directory() . '/template-blog-'.$blog_style.'.php');
?>