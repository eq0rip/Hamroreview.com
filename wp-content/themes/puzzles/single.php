<?php
/**
 * The Template for displaying all single posts.
 *
 * @package puzzles
 */

get_header(); 

$counters = get_theme_option("blog_counters");
$blog_style = 'fullpost'; //get_custom_option('blog_style');
$thumb_size = array(
	'fullpost' => array('w' => get_custom_option('show_sidebar_main')=='fullwidth' ? 1243 : 932, 'h' => null),
);
$thumb_idx = get_custom_option('show_sidebar_main')=='fullwidth' ? 1 : 0;
?>
	<div id="main_inner" class="clearboth blog_style_<?php echo $blog_style; ?>">
		<div id="content" class="content_blog post_single" role="main">

            <?php
			while ( have_posts() ) {
				the_post();

				setPostViews(get_the_ID()); 

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
				$post_thumb = getResizedImageTag($post_id, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h'], null, true, false, true);
				$post_attachment = wp_get_attachment_url(get_post_thumbnail_id($post_id));
				// Reviews parameters
				if ( get_custom_option('show_reviews')=='yes') {
					$avg_author = marksToDisplay(get_post_meta($post_id, 'reviews_avg', true));
					$avg_users  = marksToDisplay(get_post_meta($post_id, 'reviews_avg2', true));
				} else {
					$avg_author = 0;
					$avg_users = 0;
				}
				?>
				<div class="itemscope" itemscope itemtype="http://schema.org/<?php echo $avg_author > 0 || $avg_users > 0 ? 'Review' : 'Article'; ?>">
				<article <?php post_class('theme_article post_format_'.$post_format); ?>>
					<?php 
				$post_title = apply_filters('the_title', getPostTitle());
				$post_content = get_the_content(null, get_custom_option('show_text_before_readmore')!='yes');
				// Substitute WP [gallery] shortcode
				if (get_custom_option('substitute_gallery')=='yes') {
					$post_content = substituteGallery($post_content, $post_id, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h'], 'none', true);
				}
				// Catch output from the_content filters
				$post_content = apply_filters('the_content', $post_content);
				// Substitute <video> tags to <iframe>
				if (get_custom_option('substitute_video')=='yes') {
					$post_content = substituteVideo($post_content, $thumb_size[$blog_style]['w'], $thumb_size[$blog_style]['h']);
				}
				// Substitute <audio> tags with src from soundcloud to <iframe>
				if (get_custom_option('substitute_audio')=='yes') {
					$post_content = substituteAudio($post_content);
				}
				$post_descr   = apply_filters('the_excerpt', getPostDescription());				//get_the_excerpt();
				// Get all post's categories
				$post_categories = getCategoriesByPostId($post_id);
				$post_categories_ids = array();
				$post_categories_str = '';
				if ( get_custom_option('show_post_info')=='yes') {
					$ex_cats = explode(',', get_theme_option('exclude_cats'));
					for ($i = 0; $i < count($post_categories); $i++) {
						if (in_array($post_categories[$i]['term_id'], $ex_cats)) continue;
						$post_categories_ids[] = $post_categories[$i]['term_id'];
						$post_categories_str .= '<a class="cat_link" href="' . $post_categories[$i]['link'] . '">'
							. $post_categories[$i]['name'] 
							. ($i < count($post_categories)-1 ? ',' : '')
							. '</a> ';
					}
				}
				// Get all post's tags
				$post_tags_str = '';
				if ( get_custom_option('show_post_tags')=='yes') {
					if (($post_tags = get_the_tags()) != 0) {
						$tag_number=0;
						foreach ($post_tags as $tag) {
							$tag_number++;
							$post_tags_str .= '<a class="tag_link" href="' . get_tag_link($tag->term_id) . '">' . $tag->name . ($tag_number==count($post_tags) ? '' : ',') . '</a> ';
						}
					}
				}
					
					if (!$post_protected) { 
						// If post have thumbnail - show it
						if ( $post_thumb && get_custom_option('show_featured_image')=='yes') {
						?>
							<div class="post_thumb image_wrapper no_thumb">
								<?php echo $post_thumb; ?>
								<span class="post_format theme_accent_bg <?php echo $post_icon; ?>"></span>
								<?php if (get_custom_option('puzzles_style')=='heavy' && count($post_categories) > 0) { ?>
								<span class="post_category theme_accent_bg"><?php echo $post_categories[0]['name']; ?></span>
								<?php } ?>
							</div>
						<?php
						}
					}
					?>
					
					<div class="post_content<?php echo get_custom_option('without_paddings')=='yes' ? ' without_paddings' : ''; ?>">
						<?php
						// Post info
						if ( get_custom_option('show_post_info')=='yes') {
						?>
							<div class="post_info post_info_top theme_info">
								<?php _e('Posted ', 'themerex'); ?><span class="post_date theme_text date updated" itemprop="datePublished" content="<?php echo get_the_date('Y-m-d'); ?>"><?php echo $post_date; ?></span>
								<!-- <span class="post_info_delimiter theme_border"></span> -->
								<span class="post_author"><?php _e('by ', 'themerex'); ?><span class="vcard author" itemprop="author"><a href="<?php echo $post_author_url; ?>" class="post_author fn" rel="author"><?php echo $post_author; ?></a></span></span>
								<!-- <span class="post_info_delimiter theme_border"></span> -->
								<?php if ($post_categories_str!='') { ?>
								<span class="post_cats"><?php _e('in ', 'themerex'); ?><?php echo $post_categories_str; ?></span>
								<?php } ?>
								<?php if ($counters!='none') { ?>
								<span class="post_comments"><a href="<?php echo $post_comments_link; ?>"><span class="comments_icon theme_info icon-<?php echo $counters=='comments' ? 'chat-1' : 'eye'; ?>"></span><span class="comments_number"><?php echo $counters=='comments' ? $post_comments : $post_views; ?></span></a></span><meta itemprop="interactionCount" content="User<?php echo $counters=='comments' ? 'Comments' : 'PageVisits'; ?>:<?php echo $counters=='comments' ? $post_comments : $post_views; ?>" />
								<?php } ?>
							</div>
						<?php 
						}								

						// Reviews block
						if ($avg_author > 0 || $avg_users > 0) {
							$reviews_first_author = get_theme_option('reviews_first')=='author';
							$reviews_second_hide = get_theme_option('reviews_second')=='hide';
							$use_tabs = !$reviews_second_hide; // && $avg_author > 0 && $avg_users > 0;
							if ($use_tabs) wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
							$maxLevel = max(5, (int) get_custom_option('reviews_max_level'));
							if ($maxLevel == 100) wp_enqueue_script( 'jquery-ui-draggable', false, array('jquery','jquery-ui-core'), null, true );
							?>	
							<div class="post_reviews theme_<?php echo get_custom_option('sidebar_main_theme'); ?>">
								<?php 
								$output = $marks = $users = '';
								if ($use_tabs) {
									$author_tab = '<li><a href="#reviews_author" class="theme_button">'.__('Author', 'themerex').'</a></li>';
									$users_tab = '<li><a href="#reviews_users" class="theme_button">'.__('Users', 'themerex').'</a></li>';
									$output .= '<ul class="tabs theme_field">' . ($reviews_first_author ? $author_tab . $users_tab : $users_tab . $author_tab) . '</ul>';
								}
								$field = array(
									"options" => get_theme_option('reviews_criterias')
								);
								if (count($post_categories) > 0) {
									foreach ($post_categories as $cat) {
										$id = (int) $cat['term_id'];
										$prop = getCategoryInheritedProperty($id, 'reviews_criterias');
										if (!empty($prop) && $prop!='default' && themerex_substr(trim($prop), 0, 1)!='|') {
											$field['options'] = $prop;
											break;
										}
									}
								}
								if ($reviews_first_author || !$reviews_second_hide) {
									$output .= '<div id="reviews_author" class="reviews_tab reviews_author">';
									$field["id"] = "reviews_marks_author";
									$field["descr"] = $post_descr;
									$marks = marksToDisplay(get_custom_option('reviews_marks'));
									$output .= getReviewsMarkup($field, $marks, false, $reviews_first_author);
									$output .= '</div>';
								}
								if (!$reviews_first_author || !$reviews_second_hide) {
									$output .= '<div id="reviews_users" class="reviews_tab reviews_users"'.(!$output ? ' style="display: block;"' : '').'>';
									$marks = marksToDisplay(get_post_meta($post_id, 'reviews_marks2', true));
									$users = get_post_meta($post_id, 'reviews_users', true);
									$field["id"] = "reviews_marks_users";
									$field["descr"] = '<div class="criteria_summary_descr">' 
										. sprintf(__("Summary rating from %s user's marks.", 'themerex'), '<span class="theme_strong">'.$users.'</span>')
										. ' ' 
										.(!isset($_COOKIE['reviews_vote']) || themerex_strpos($_COOKIE['reviews_vote'], ','.$post_id.',')===false
											? __('You can set own marks for this article - just click on stars above and press "Accept".', 'themerex')
											: __('Thanks for your vote!', 'themerex'))
										. '</div>'
										. '<a href="#" class="theme_button reviews_users_accept">' . __('Accept', 'themerex') . '</a>';
									$output .= getReviewsMarkup($field, $marks, false, !$reviews_first_author);
									$output .= '</div>';
								}
								echo $output;
								?>
							</div>
							<script type="text/javascript">
								var reviews_max_level = <?php echo $maxLevel; ?>;
								var reviews_levels = "<?php echo get_theme_option('reviews_criterias_levels'); ?>";
								var reviews_vote = "<?php echo isset($_COOKIE['reviews_vote']) ? $_COOKIE['reviews_vote'] : ''; ?>";
								var allowUserReviews = <?php echo (!$reviews_first_author || !$reviews_second_hide) && (!isset($_COOKIE['reviews_vote']) || themerex_strpos($_COOKIE['reviews_vote'], ','.$post_id.',')===false) && (get_theme_option('reviews_can_vote')=='all' || is_user_logged_in()) ? 'true' : 'false'; ?>;
								jQuery(document).ready(function () {
									<?php if ($use_tabs) { ?>
									jQuery('.post_reviews').tabs();
									<?php } ?>
									initReviews(true);
									
									// Save user's marks
									if (allowUserReviews) {
										jQuery('.reviews_users_accept').click(function(e) {
											var marks = '<?php echo $marks; ?>'.split(',');
											var users = <?php echo max(0, $users); ?>;
											var marks_cnt = 0;
											jQuery('#reviews_users .reviews_data .criteria_row input').each(function (idx) {
												marks[idx] = Math.round(((marks.length>idx && marks[idx]!='' ? parseFloat(marks[idx])*users : 0) + parseFloat(jQuery(this).val()))/(users+1)*10)/10;
												jQuery(this).val(marks[idx]);
												marks_cnt++;
											});
											if (marks.length > marks_cnt)
												marks = marks.splice(marks_cnt, marks.length-marks_cnt)
											users++;

											jQuery.post(THEMEREX_ajax_url, {
												action: 'reviews_users_accept',
												nonce: THEMEREX_ajax_nonce,
												post_id: <?php echo $post_id; ?>,
												marks: marks.join(','),
												users: users
											}).done(function(response) {
												var rez = JSON.parse(response);
												if (rez.error === '') {
													jQuery('.reviews_users .criteria_summary_text').removeClass('show_button').find('.criteria_summary_descr').html('<?php _e('Thanks for your vote! New average rating is:', 'themerex'); ?>');
													allowUserReviews = false;
													jQuery.cookie('reviews_vote', reviews_vote + (reviews_vote.substr(-1)!=',' ? ',' : '') + <?php echo $post_id; ?> + ',', {expires: 365, path: '/'});
													jQuery('#reviews_users .reviews_data .criteria_row input').each(function (idx) {
														jQuery(this).val(marks[idx]);
													});
													jQuery('#reviews_users .reviews_data .criteria_row .criteria_dragger').hide();
													/*
													jQuery('.reviews_users .reviews_data .theme_stars').each(function() {
														setStarsOnMark(jQuery(this), null);
													});
													*/
													setAverageMark('reviews_users');
												} else {
													jQuery('.reviews_users .criteria_summary_text').removeClass('show_button').find('.criteria_summary_descr').html('<?php _e('Error saving your vote! Please, try again later.', 'themerex'); ?>');
												}
											});

											e.preventDefault();
											return false;
										});
									}
								});
							</script>
						<?php
						}
						
						// Post title
						if ( get_custom_option('show_post_title')=='yes' && !in_array($post_format, array('quote', 'aside'))) {
						?>	
							<div class="title_area">
								<h1 itemprop="<?php echo $avg_author > 0 || $avg_users > 0 ? 'itemReviewed' : 'name'; ?>" class="post_title theme_title entry-title"><?php echo $post_title; ?></h1>
							</div>
						<?php
						}
						?>
						
						
						<div itemprop="<?php echo $avg_author > 0 || $avg_users > 0 ? 'reviewBody' : 'articleBody'; ?>" class="post_text_area">
						<?php
						// Post content
						if ($post_protected) { 
							echo $post_descr; 
						} else {
							echo $post_content; 
							wp_link_pages( array( 
								'before' => '<div class="nav_pages_parts"><span class="pages">' . __( 'Pages:', 'themerex' ) . '</span>', 
								'after' => '</div>',
								'link_before' => '<span class="page_num">',
								'link_after' => '</span>'
							) ); 
							// Post tags
							if ( get_custom_option('show_post_tags')=='yes' && $post_tags_str != '') {
							?>
							<div class="post_info post_info_bottom theme_info">
								<span class="post_tags">
									<span class="tags_label"><?php _e('Tags:', 'themerex'); ?></span>
									<?php echo $post_tags_str; ?>
								</span>
							</div>
							<?php 
							}
							// Social sharing
							if ( get_custom_option('show_post_share')=='yes' && (is_single() || (is_page() && !is_home() && !is_front_page() && !$wp_query->is_posts_page))) {
								showShareButtons(array(
									'post_id'    => $post_id,
									'post_link'  => $post_link,
									'post_title' => $post_title,
									'post_descr' => $post_descr,
									'post_thumb' => $post_attachment
								));
							}
						} 
						?>
						</div>
					</div>
				</article>

                <?php 
				if (!$post_protected) {
					//===================================== Post author info =====================================
					if (get_custom_option("show_post_author") == 'yes') {
						$post_author_email = get_the_author_meta('user_email', $post_author_id);
						$post_author_avatar = get_avatar($post_author_email, 50*min(2, max(1, get_theme_option("retina_ready"))));
						$post_author_descr = do_shortcode(nl2br(get_the_author_meta('description', $post_author_id)));
					?>
						<div class="post_author_details theme_article">
							<h3 class="post_author_title theme_subtitle"><?php echo __('About author', 'themerex'); ?></h3>
							<div class="post_author_info vcard author" itemprop="author" itemscope itemtype="http://schema.org/Person">
								<div class="post_author_avatar image_wrapper"><a href="<?php echo $post_author_url; ?>" itemprop="image"><?php echo $post_author_avatar; ?></a></div>
								<h5 class="post_author_name" itemprop="name"><a href="<?php echo $post_author_url; ?>" class="theme_strong fn"><?php echo $post_author; ?></a></h5>
								<div class="post_author_description" itemprop="description"><?php echo $post_author_descr; ?></div>
								<div class="post_author_socials"><?php echo showUserSocialLinks(array('author_id'=>$post_author_id, 'echo'=>false)); ?></div>
							</div>
						</div>
					<?php
					}
					
					//===================================== Related posts =====================================
					if (get_custom_option("show_post_related") == 'yes') {
						$args = array( 
							'numberposts' => get_custom_option('post_related_count'),
							'post_type' => is_page() ? 'page' : 'post', 
							'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
							'post__not_in' => array($post_id) 
						);
						if ($post_categories_str) {
							$args['category__in'] = $post_categories_ids;
						}
						if ($post_format != '' && $post_format != 'standard') {
							$args['tax_query'] = array(
								array(
									'taxonomy' => 'post_format',
									'field' => 'slug',
									'terms' => 'post-format-' . $post_format
								)
							);
						}
						$args = addSortOrderInQuery($args, get_custom_option('post_related_sort'), get_custom_option('post_related_order'));
						$recent_posts = wp_get_recent_posts( $args );
						if (count($recent_posts) > 0) {
						?>
						<div id="related_posts" class="theme_article">
							<div class="subtitle_area">
								<h3 class="post_related_title theme_subtitle"><?php _e('Related posts', 'themerex'); ?></h3>
							</div>
							<?php
							$i=0;
							foreach( $recent_posts as $recent ) {
								$i++;
								$recent['post_format'] = get_post_format($recent['ID']);
								$recent['post_icon'] = getPostFormatIcon($recent['post_format']);
								$recent['post_link'] = get_permalink($recent['ID']);
								$recent['comments_link'] = $counters=='comments' ? get_comments_link( $recent['ID'] ) : $recent['post_link'];
								$recent['post_thumb'] = getResizedImageTag($recent['ID'], 310, 310);
								$recent['attachment'] = wp_get_attachment_url(get_post_thumbnail_id($recent['ID']));
								if ($counters!='none') { 
									$recent['views'] = getPostViews($recent['ID']);
									$recent['comments'] = get_comments_number($recent['ID']);
								}
								$recent['post_protected'] = post_password_required($recent['ID']);
								$recent['post_content_prepared'] = do_shortcode($recent['post_content']);
								$recent['post_descr'] = $recent['post_format']=='quote' 
									? $recent['post_content_prepared'] 
									: (!empty($recent['post_excerpt']) ? $recent['post_excerpt'] : getShortString(strip_tags(strip_shortcodes($recent['post_content'])), 300));
								$recent['post_gallery'] = $recent['post_video'] = $recent['post_audio'] = '';
								if ($recent['post_format'] == 'gallery') {
									$recent['post_gallery'] =  buildGalleryTag(getPostGallery($recent['post_content'], $recent['ID']), 310, 310);
								} else if ($recent['post_format'] == 'video') {
									$recent['post_video'] = getPostVideo($recent['post_content_prepared'], false);
									if ($recent['post_video']=='') {
										$src = getVideoPlayerURL(getPostVideo($recent['post_content_prepared'], true), $recent['post_thumb']!='');
										if ($src) $recent['post_video'] = substituteVideo('<video src="'.$src.'">', 310, 310);							
									}
									if ($recent['post_video']!='' && get_custom_option('substitute_video')=='yes') {
										$src = getVideoPlayerURL(getPostVideo($recent['post_video']), $recent['post_thumb']!='');
										if ($src) $recent['post_video'] = substituteVideo('<video src="'.$src.'">', 310, 310);
									}
								} else if ($recent['post_format'] == 'audio') {
									$recent['post_audio'] = getPostAudio($recent['post_content_prepared'], false);
									if ($recent['post_audio']=='') {
										$src = getPostAudio($recent['post_content_prepared'], true);
										if ($src) $recent['post_audio'] = substituteAudio('<audio src="'.$src.'">');
									} 
									if ($recent['post_audio']!='' && get_custom_option('substitute_audio')=='yes') {
										$src = getPostAudio($recent['post_audio']);
										if ($src) $recent['post_audio'] = substituteAudio('<audio src="'.$src.'">');
									}
								} else if ($recent['post_format'] == 'image' && !$recent['$post_thumb']) {
									if (($src = getPostImage($recent['post_content_prepared']))!='')
										$recent['post_thumb'] = getResizedImageTag($src, 310, 310);
								} else if ($recent['post_format'] == 'link') {
									$post_url_data = getPostLink($recent['post_content_prepared'], false);
									$recent['post_url'] = $post_url_data['url'];
									$recent['post_url_target'] = $post_url_data['target'];
								}
								$recent['categories'] = getCategoriesByPostId($recent['ID']);
								$recent['post_hover_bg']     = get_custom_option('puzzles_post_bg', null, $recent['ID']);
								$recent['post_hover_pos']    = get_custom_option('puzzles_post_position', null, $recent['ID']);
								$recent['post_accent_color'] = get_custom_option('theme_accent_color', null, $recent['ID']);
								$recent['post_accent_category'] = count($recent['categories']) > 0 ? $recent['categories'][0]['name'] : '';
								$show_content_block = !in_array($recent['post_format'], array('link', 'image')) || !$recent['post_thumb'];
								$puzzles_style = get_custom_option('puzzles_style');
								$no_thumb = in_array($recent['post_format'], array('quote', 'link', 'image')) || (!$recent['post_thumb'] && (!$recent['post_gallery'] || $recent['post_protected']));
								?>
								<div class="related_posts_item related_post_item_<?php echo $i; ?> post_format_<?php echo $recent['post_format']; ?> <?php echo ($i % 2 == 0 ? 'even' : 'odd') . ($i==1 ? ' first' : '') . ($i==3+$thumb_idx ? ' last' : ''); ?> post_thumb image_wrapper <?php echo $recent['post_format']=='quote' || (!$recent['post_thumb'] && (!$recent['post_gallery'] || $recent['post_protected'])) ? 'no_thumb' : $recent['post_hover_pos']; ?>"<?php echo $recent['post_video'] && !$recent['post_protected'] ? ' data-video="'.htmlspecialchars($recent['post_video']).'"' : ''; ?>>
									<?php
									if ($recent['post_gallery'] && !$recent['post_protected']) {		// If post have gallery - show it
										if ($recent['post_link']!='')
											echo '<a href="'.$recent['post_link'].'">'.$recent['post_gallery'].'</a>';
										else
											echo $recent['post_gallery'];
									} else if ($recent['post_thumb']) {									// If post have thumbnail - show it
										if ($recent['post_format']=='link' && $recent['post_url']!='')
											echo '<a href="'.$recent['post_url'].'"'.($recent['post_url_target'] ? ' target="'.$recent['post_url_target'].'"' : '').'>'.$recent['post_thumb'].'</a>';
										else if ($recent['post_link']!='')
											echo '<a href="'.$recent['post_link'].'">'.$recent['post_thumb'].'</a>';
										else
											echo $recent['post_thumb']; 
										if ($recent['post_format'] == 'video' && $recent['post_video'] && !$recent['post_protected']) {
											?>
											<a href="#" class="post_video_play icon-play"></a>
											<?php
										}
									} else if ($recent['post_video'] && !$recent['post_protected']) {
										echo $recent['post_video']; 
										$show_content_block = false;
									}
									?>
									<span class="post_format theme_accent_bg <?php echo $recent['post_icon']; ?>"<?php echo themerex_substr($recent['post_accent_color'], 0, 1)=='#' ? ' style="background-color: '.$recent['post_accent_color'].'"' : ''; ?>></span>
									<?php
									$reviewsBlock = '';
									if ( get_custom_option('show_reviews', null, $recent['ID'])=='yes' ) {
										$avg_author = marksToDisplay(get_post_meta($recent['ID'], 'reviews_avg'.(get_theme_option('reviews_first')=='author' ? '' : '2'), true));
										if ($avg_author > 0) {
											$reviewsBlock = '
												<div class="reviews_summary blog_reviews theme_puzzles"' . ($recent['post_hover_bg']!='' && $recent['post_hover_bg']!='default' ? ' style="background-color:'.$recent['post_hover_bg'].';"' : '') . '>
													<div class="criteria_summary criteria_row">
														' . getReviewsSummaryStars($avg_author) . '
													</div>
												</div>
											';
										}
									}

									if ($puzzles_style=='heavy') {
										if ($recent['post_accent_category']!='') { 
									?>
										<span class="post_category theme_accent_bg"<?php echo themerex_substr($recent['post_accent_color'], 0, 1)=='#' ? ' style="background-color: '.$recent['post_accent_color'].'"' : ''; ?>><?php echo $recent['post_accent_category']; ?></span>
									<?php 
										}
									} else {
										if ($show_content_block) {
											?>
											<div class="post_content_light">
												<?php
												if ($recent['post_accent_category']!='') { 
												?>
													<span class="post_category theme_accent_bg"<?php echo themerex_substr($recent['post_accent_color'], 0, 1)=='#' ? ' style="background-color: '.$recent['post_accent_color'].'"' : ''; ?>><?php echo $recent['post_accent_category']; ?></span><br>
												<?php 
												}
												?>
												<h2 class="post_subtitle theme_puzzles"<?php echo $recent['post_hover_bg']!='' && $recent['post_hover_bg']!='default' ? ' style="background-color:'.$recent['post_hover_bg'].';"' : ''; ?>><a href="<?php echo $recent['post_link']; ?>"><?php echo $recent['post_title']; ?></a></h2><br>
												<?php echo $reviewsBlock; ?>
											</div>
											<?php 
										}
									}
									if ($show_content_block) {
									?>
									<div class="post_content_wrapper theme_puzzles"<?php echo $recent['post_hover_bg']!='' && $recent['post_hover_bg']!='default' ? ' style="background-color:'.$recent['post_hover_bg'].';"' : ''; ?>>
										<?php
										if (!in_array($recent['post_format'], array('quote', 'aside'))) { 
										?>
											<h2 class="post_subtitle"><a href="<?php echo $recent['post_link']; ?>"><?php echo $recent['post_title']; ?></a></h2>
											<?php echo $reviewsBlock; ?>
										<?php
										} 
										?>
										<div class="post_descr"><p><?php echo $recent['post_descr']; ?></p></div>
										<div class="post_content_padding theme_puzzles"<?php
											if ($recent['post_hover_bg']!='' && $recent['post_hover_bg']!='default') {
												$rgb = Hex2RGB($recent['post_hover_bg']);
												$post_hover_ie = str_replace('#', '', $recent['post_hover_bg']);
												echo " style=\"
													background: -moz-linear-gradient(top,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%, rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%, rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
													background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0)), color-stop(1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01)), color-stop(50%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1)));
													background: -webkit-linear-gradient(top,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
													background: -o-linear-gradient(top,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
													background: -ms-linear-gradient(top,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
													background: linear-gradient(to bottom,  rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0) 0%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.01) 1%,rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},1) 50%);
													filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00{$post_hover_ie}', endColorstr='#$post_hover_ie',GradientType=0 );
													\""; 
											}
											?>></div>
									</div>
									<?php
									}
									if (!$recent['post_protected']) {
										if ( $recent['post_audio'] )							// If post have audio - show it
											echo $recent['post_audio'];
									}
									?>
								</div>
								<?php
								//if ($i>=3+$thumb_idx) break;
							}
							?>
						</div>
						<?php
						}
					}	// if (blog_show_related_posts)

					//===================================== Comments =====================================
					if (get_custom_option("show_post_comments") == 'yes') {
						if ( comments_open() || get_comments_number() != 0 ) {
							comments_template();
						}
					}
                } // if (!post_password_required())
				?>

				</div><!-- .itemscope -->

			<?php
			}	// end of the loop. 
			?>

		</div><!-- #content -->

		<?php get_sidebar(); ?>

	</div><!-- #main_inner -->

<?php get_footer(); ?>