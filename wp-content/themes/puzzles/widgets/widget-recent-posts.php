<?php
/**
 * Add function to widgets_init that will load our widget.
 */
add_action( 'widgets_init', 'widget_recent_posts_load' );

/**
 * Register our widget.
 */
function widget_recent_posts_load() {
	register_widget('themerex_recent_posts_widget');
}

/**
 * Recent posts Widget class.
 */
class themerex_recent_posts_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function themerex_recent_posts_widget() {
		/* Widget settings. */
		$widget_ops = array('classname' => 'widget_recent_posts', 'description' => __('The recent blog posts (extended)', 'themerex'));

		/* Widget control settings. */
		$control_ops = array('width' => 200, 'height' => 250, 'id_base' => 'themerex-recent-posts-widget');

		/* Create the widget. */
		$this->WP_Widget('themerex-recent-posts-widget', __('ThemeREX - Recent Posts', 'themerex'), $widget_ops, $control_ops);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) {
		extract($args);

		global $wp_query, $post;

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$number = isset($instance['number']) ? (int) $instance['number'] : '';
		$show_date = isset($instance['show_date']) ? (int) $instance['show_date'] : 0;
		$show_image = isset($instance['show_image']) ? (int) $instance['show_image'] : 0;
		$show_author = isset($instance['show_author']) ? (int) $instance['show_author'] : 0;
		$show_counters = isset($instance['show_counters']) ? (int) $instance['show_counters'] : 0;
		$category = isset($instance['category']) ? (int) $instance['category'] : 0;

		$counters = get_theme_option("blog_counters");
		
		$output = '';

		$args = array(
			'numberposts' => $number,
			'offset' => 0,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
			'ignore_sticky_posts' => 1,
			'suppress_filters' => true 
    	);
		if ($category > 0) {
			$args['category'] = $category;
		}
		$ex = get_theme_option('exclude_cats');
		if (!empty($ex)) {
			$args['category__not_in'] = explode(',', $ex);
		}

    	$recent_posts = wp_get_recent_posts($args);
			
		$post_number = 0;
		foreach ($recent_posts as $post) {

			$post_number++;

			$post_id = $post['ID'];
			$post_date = getDateOrDifference($post['post_date']);
			$post_title = $post['post_title'];
			$post_link = get_permalink($post_id);
			
			$output .= '
				<div class="post_item' . ($post_number==1 ? ' first' : '') . '">
			';

			if ($show_image) {
				$post_thumb = getResizedImageTag($post_id, 60, 60);
				if ($post_thumb) {
					$output .= '
							<div class="post_thumb image_wrapper">' . $post_thumb . '</div>
					';
				}
			}
			$output .= '
							<div class="post_wrapper">
								<h5 class="post_title theme_title'.($show_counters==2 ? '' : ' title_padding').'"><a href="' . $post_link . '">' . $post_title . '</a></h5>
			';
			if ($show_date || $show_counters==2 || $show_author) {
				$output .= '
								<div class="post_info theme_info">
				';
				if ($show_date) {
					$output .= '
										<span class="post_date theme_text">' . $post_date . '</span>
					';
				}
				if ($show_author) {
					$post_author_id   = $post['post_author'];
					$post_author_name = get_the_author_meta('display_name', $post_author_id);
					$post_author_url  = get_author_posts_url($post_author_id, '');
					$output .= '
									<span class="post_author">' . __('by', 'themerex') . ' <a href="' . $post_author_url . '">' . $post_author_name . '</a></span>
					';
				}
				if ($show_counters) {
					$post_counters = $counters=='comments' ? get_comments_number($post_id) : getPostViews($post_id);
					$post_counters_link = $counters=='comments' ? get_comments_link( $post_id ) : $post_link;
					if ($show_counters==2 && ($show_date || $show_author)) {
						$output .= '<br />';
					}
					$output .= '
									<span class="post_comments' . ($show_counters==2 ? '_text' : '') . '"><a href="'.$post_counters_link.'">'
									. ($show_counters==2 ? '' : '<span class="comments_icon icon-' . ($counters=='views' ? 'eye' : 'chat-1') . '"></span>')
									. '<span class="post_comments_number">' . $post_counters . '</span>'
									. ($show_counters==2 ? ' '.($counters=='views' ? __('views', 'themerex') : __('comments', 'themerex')) : '')
									.'</a></span>
						';
				}
				$output .= '
								</div>
				';
			}
			$output .= '
					</div>
				</div>
			';
			if ($post_number >= $number) break;
		}
		
		if (!empty($output)) {
	
			/* Before widget (defined by themes). */			
			echo $before_widget;		
			
			/* Display the widget title if one was input (before and after defined by themes). */
			echo $before_title . $title . $after_title;		
	
			echo $output;
			
			/* After widget (defined by themes). */
			echo $after_widget;
		}
	}

	/**
	 * Update the widget settings.
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		/* Strip tags for title and comments count to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (int) $new_instance['show_date'];
		$instance['show_image'] = (int) $new_instance['show_image'];
		$instance['show_author'] = (int) $new_instance['show_author'];
		$instance['show_counters'] = (int) $new_instance['show_counters'];
		$instance['category'] = (int) $new_instance['category'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form($instance) {
 		/* Set up some default widget settings. */
		$defaults = array('title' => '', 'number' => '4', 'show_date' => '1', 'show_image' => '1', 'show_author' => '1', 'show_counters' => '1', 'category'=>'0', 'description' => __('The most popular & commented posts', 'themerex'));
		$instance = wp_parse_args((array) $instance, $defaults); 
		$title = isset($instance['title']) ? $instance['title'] : '';
		$number = isset($instance['number']) ? $instance['number'] : '';
		$show_date = isset($instance['show_date']) ? $instance['show_date'] : '';
		$show_image = isset($instance['show_image']) ? $instance['show_image'] : '';
		$show_author = isset($instance['show_author']) ? $instance['show_author'] : '';
		$show_counters = isset($instance['show_counters']) ? $instance['show_counters'] : '';
		$category = isset($instance['category']) ? $instance['category'] : '0';
		$categories = getCategoriesList(false);
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget title:', 'themerex'); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category:', 'themerex'); ?></label>
			<select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" style="width:100%;">
				<option value="0"><?php _e('-- Any category --', 'themerex'); ?></option> 
			<?php
				foreach ($categories as $cat_id => $cat_name) {
					echo '<option value="'.$cat_id.'"'.($category==$cat_id ? ' selected="selected"' : '').'>'.$cat_name.'</option>';
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number posts to show:', 'themerex'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $number; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('show_image'); ?>_1"><?php _e('Show post image:', 'themerex'); ?></label><br />
			<input type="radio" id="<?php echo $this->get_field_id('show_image'); ?>_1" name="<?php echo $this->get_field_name('show_image'); ?>" value="1" <?php echo $show_image==1 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_image'); ?>_1"><?php _e('Show', 'themerex'); ?></label>
			<input type="radio" id="<?php echo $this->get_field_id('show_image'); ?>_0" name="<?php echo $this->get_field_name('show_image'); ?>" value="0" <?php echo $show_image==0 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_image'); ?>_0"><?php _e('Hide', 'themerex'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('show_author'); ?>_1"><?php _e('Show post author:', 'themerex'); ?></label><br />
			<input type="radio" id="<?php echo $this->get_field_id('show_author'); ?>_1" name="<?php echo $this->get_field_name('show_author'); ?>" value="1" <?php echo $show_author==1 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_author'); ?>_1"><?php _e('Show', 'themerex'); ?></label>
			<input type="radio" id="<?php echo $this->get_field_id('show_author'); ?>_0" name="<?php echo $this->get_field_name('show_author'); ?>" value="0" <?php echo $show_author==0 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_author'); ?>_0"><?php _e('Hide', 'themerex'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('show_date'); ?>_1"><?php _e('Show post date:', 'themerex'); ?></label><br />
			<input type="radio" id="<?php echo $this->get_field_id('show_date'); ?>_1" name="<?php echo $this->get_field_name('show_date'); ?>" value="1" <?php echo $show_date==1 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_date'); ?>_1"><?php _e('Show', 'themerex'); ?></label>
			<input type="radio" id="<?php echo $this->get_field_id('show_date'); ?>_0" name="<?php echo $this->get_field_name('show_date'); ?>" value="0" <?php echo $show_date==0 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_date'); ?>_0"><?php _e('Hide', 'themerex'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('show_counters'); ?>_1"><?php _e('Show post counters:', 'themerex'); ?></label><br />
			<input type="radio" id="<?php echo $this->get_field_id('show_counters'); ?>_2" name="<?php echo $this->get_field_name('show_counters'); ?>" value="2" <?php echo $show_counters==2 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_counters'); ?>_2"><?php _e('As text', 'themerex'); ?></label>
			<input type="radio" id="<?php echo $this->get_field_id('show_counters'); ?>_1" name="<?php echo $this->get_field_name('show_counters'); ?>" value="1" <?php echo $show_counters==1 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_counters'); ?>_1"><?php _e('As icon', 'themerex'); ?></label>
			<input type="radio" id="<?php echo $this->get_field_id('show_counters'); ?>_0" name="<?php echo $this->get_field_name('show_counters'); ?>" value="0" <?php echo $show_counters==0 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_counters'); ?>_0"><?php _e('Hide', 'themerex'); ?></label>
		</p>

	<?php
	}
}

?>