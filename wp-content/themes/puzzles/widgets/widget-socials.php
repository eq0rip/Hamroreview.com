<?php
/**
 * Add function to widgets_init that will load our widget.
 */
add_action( 'widgets_init', 'themerex_social_load_widgets' );

/**
 * Register our widget.
 */
function themerex_social_load_widgets() {
	register_widget( 'themerex_social_widget' );
}

/**
 * flickr Widget class.
 */
class themerex_social_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function themerex_social_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_socials', 'description' => __('Show site logo and social links', 'themerex') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'themerex-social-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'themerex-social-widget', __('ThemeREX - Show logo and social links', 'themerex'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$text = isset($instance['text']) ? do_shortcode($instance['text']) : '';
		$logo_image = isset($instance['logo_image']) ? $instance['logo_image'] : '';
		$show_logo = isset($instance['show_logo']) ? (int) $instance['show_logo'] : 1;
		$show_icons = isset($instance['show_icons']) ? (int) $instance['show_icons'] : 1;

		/* Before widget (defined by themes). */			
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ($title) echo $before_title . $title . $after_title;
		
		
		?>
		<div class="widget_inner">
            <?php
				if ($show_logo) {
					if ($logo_image!='' || ($logo_image=get_theme_option('logo_image'))!='') { 
					?>
						<div class="logo logo_image"><a href="<?php echo get_home_url(); ?>"><img src="<?php echo $logo_image; ?>" alt="" /></a></div>
					<?php 
					} else if (($logo_text = get_theme_option('logo_text'))!='') {
						$logo_text = str_replace(array('[', ']'), array('<span class="theme_accent">', '</span>'), $logo_text);
					?>
						<div class="logo logo_text"><a href="<?php echo get_home_url(); ?>"><span class="logo_title theme_header"><?php echo $logo_text; ?></span></a></div>
					<?php 
					} 
				}

				if (!empty($text)) {
					?>
					<div class="logo_descr"><?php echo $text; ?></div>
                    <?php
				}
				
				if ($show_icons) {
					$socials = get_theme_option('social_icons');
					$arr = explode(',', $socials);
					foreach ($arr as $s) {
						if (empty($s)) continue;
						$s = explode('|', $s);
						if (count($s)!=3 || empty($s[0])) continue;
						?><a class="social_icons social_<?php echo $s[2]; ?>" href="<?php echo $s[0]; ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/socials/<?php echo $s[2]; ?>.png" width="32" height="32" /></a><?php 
					}
				}
			?>
		</div>

		<?php
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['text'] = $new_instance['text'];
		$instance['logo_image'] = strip_tags( $new_instance['logo_image'] );
		$instance['show_logo'] = (int) $new_instance['show_logo'];
		$instance['show_icons'] = (int) $new_instance['show_icons'];
	
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => '', 'logo_image'=>'', 'show_logo' => '1', 'show_icons' => '1', 'text'=>'', 'description' => __('Show logo and social icons', 'themerex') );
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		$title = isset($instance['title']) ? $instance['title'] : '';
		$text = isset($instance['text']) ? $instance['text'] : '';
		$show_logo = isset($instance['show_logo']) ? $instance['show_logo'] : 1;
		$show_icons = isset($instance['show_icons']) ? $instance['show_icons'] : 1;
		$logo_image = isset($instance['logo_image']) ? $instance['logo_image'] : '';
		wp_enqueue_script( '_admin', get_template_directory_uri() . '/js/_admin.js', array(), null, true );	
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'themerex'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e('Description:', 'themerex'); ?></label>
			<textarea id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" style="width:100%;"><?php echo htmlspecialchars($instance['text']); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'logo_image' ); ?>"><?php _e('Logo image:<br />(if empty - use logo from Theme Options)', 'themerex'); ?></label>
			<input id="<?php echo $this->get_field_id( 'logo_image' ); ?>" name="<?php echo $this->get_field_name( 'logo_image' ); ?>" value="<?php echo $logo_image; ?>" style="width:100%;" onchange="jQuery(this).siblings('img').get(0).src=this.value;" />
            <?php
			echo show_custom_field(array('id'=>$this->get_field_id( 'logo_media' ), 'type'=>'mediamanager', 'media_field_id'=>$this->get_field_id( 'logo_image' )), null);
			if ($logo_image) {
			?>
	            <br /><br /><img src="<?php echo $logo_image; ?>" border="0" width="220" />
			<?php
			}
			?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_logo'); ?>_1"><?php _e('Show logo:', 'themerex'); ?></label><br />
			<input type="radio" id="<?php echo $this->get_field_id('show_logo'); ?>_1" name="<?php echo $this->get_field_name('show_logo'); ?>" value="1" <?php echo $show_logo==1 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_logo'); ?>_1"><?php _e('Show', 'themerex'); ?></label>
			<input type="radio" id="<?php echo $this->get_field_id('show_logo'); ?>_0" name="<?php echo $this->get_field_name('show_logo'); ?>" value="0" <?php echo $show_logo==0 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_logo'); ?>_0"><?php _e('Hide', 'themerex'); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_icons'); ?>_1"><?php _e('Show social icons:', 'themerex'); ?></label><br />
			<input type="radio" id="<?php echo $this->get_field_id('show_icons'); ?>_1" name="<?php echo $this->get_field_name('show_icons'); ?>" value="1" <?php echo $show_icons==1 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_icons'); ?>_1"><?php _e('Show', 'themerex'); ?></label>
			<input type="radio" id="<?php echo $this->get_field_id('show_icons'); ?>_0" name="<?php echo $this->get_field_name('show_icons'); ?>" value="0" <?php echo $show_icons==0 ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id('show_icons'); ?>_0"><?php _e('Hide', 'themerex'); ?></label>
		</p>

	<?php
	}
}
?>