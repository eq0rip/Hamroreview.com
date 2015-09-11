<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. 
 *
 * @package puzzles
 */
?>

<?php
	/*
	 * If the current post is protected by a password and
	 * the visitor has not yet entered the password we will
	 * return early without loading the comments.
	 */
	if ( post_password_required() )
		return;
?>

<div id="comments" class="post_comments">

	<?php 
	if ( have_comments() ) {
	?>
		<div class="post_comments_tree theme_article">
			<div class="subtitle_area">
				<h3 class="post_comments_title theme_subtitle"><?php echo $post_comments = get_comments_number(); ?> <?php echo $post_comments==1 ? __('Comment', 'themerex') : __('Comments', 'themerex'); ?></h3>
			</div>
	
			<ol class="comment-list">
				<?php
					/* Loop through and list the comments. Tell wp_list_comments()
					 * to use vc_theme_comment() to format the comments.
					 */
					wp_list_comments( array( 'callback' => 'single_comment_output') );
				?>
			</ol><!-- .comment-list -->

			<?php
			if ( !comments_open() && get_comments_number()!=0 && post_type_supports( get_post_type(), 'comments' ) ) {
			?>
				<p class="no_comments"><?php _e( 'Comments are closed.', 'themerex' ); ?></p>
			<?php
			}
			?>
		</div>
	<?php 
	}
	?>

	<div class="post_comments_form theme_article">

	<?php
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$comments_args = array(
			// change the title of send button 
			'label_submit'=>__('Submit', 'themerex'),
			// change the title of the reply section
			'title_reply'=>__('Leave a comment', 'themerex'),
			// remove "Logged in as"
			'logged_in_as' => '',
			// remove text before textarea
			'comment_notes_before' => '',
			// remove text after textarea
			'comment_notes_after' => '',
			// redefine your own textarea (the comment body)
			'comment_field' => '<p class="comment-form-comment">'
								. '<label for="comment" class="required">'
								. __('Your Message', 'themerex')
								. '<span class="required"> (' . __('required', 'themerex') . ')</span>'
								. '</label>'
								. '<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>'
								. '</p>',
			'fields' => apply_filters( 'comment_form_default_fields', array(
				'author' => '<p class="comment-form-author">'
						. '<label for="author"' . ( $req ? ' class="required"' : '' ). '>' 
						. __( 'Name', 'themerex' ) 
						. ( $req ? '<span class="required"> (' . __('required', 'themerex') . ')</span>' : '') 
						. '</label>'
						. '<input id="author" name="author" type="text" value="' . esc_attr( isset($commenter['comment_author']) ? $commenter['comment_author'] : '' ) . '" size="30"' . $aria_req . ' />'
						. '</p>',
				'email' => '<p class="comment-form-email">'
						. '<label for="email"' . ( $req ? ' class="required"' : '' ) . '>' 
						. __( 'Email', 'themerex' )  
						. ( $req ? '<span class="required"> (' . __('required', 'themerex') . ')</span>' : '') 
						. '</label>'
						. '<input id="email" name="email" type="text" value="' . esc_attr(  isset($commenter['comment_author_email']) ? $commenter['comment_author_email'] : '' ) . '" size="30"' . $aria_req . ' />'
						. '</p>',
				'url' => '<p class="comment-form-website clearboth">'
						. '<label for="url" class="optional">' 
						. __( 'Website', 'themerex' ) 
						. '<span class="optional"> (' . __('optional', 'themerex') . ')</span>' 
						. '</label>'
						. '<input id="url" name="url" type="text" value="' . esc_attr(  isset($commenter['comment_author_url']) ? $commenter['comment_author_url'] : '' ) . '" size="30"' . $aria_req . ' />'
						. '</p>'
			) )
	);

	comment_form($comments_args);
	?>

	<div class="nav_comments"><?php paginate_comments_links(); ?></div>
	
	</div>

</div><!-- #comments -->
<?php 

function single_comment_output( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
			?>
			<li class="trackback">
				<p><?php _e( 'Trackback:', 'themerex' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'themerex' ), '<span class="edit-link">', '<span>' ); ?></p>
			<?php
			break;
		case 'trackback' :
			?>
			<li class="pingback">
				<p><?php _e( 'Pingback:', 'themerex' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'themerex' ), '<span class="edit-link">', '<span>' ); ?></p>
			<?php
			break;
		default :
			$author_id = $comment->user_id;
			$author_link = get_author_posts_url( $author_id );
			?>
			<li id="comment-<?php comment_ID(); ?>" <?php comment_class('theme_border'); ?>>
				<div class="comment_author_avatar image_wrapper"><?php echo get_avatar( $comment, 60 ); ?></div>
				<h6 class="comment_title theme_subtitle">
					<?php 
					if ($author_id) echo '<a href="' . $author_link . '">';
					comment_author(); 
					if ($author_id) echo '</a>';
					?>
					<span class="comment_date theme_info"><?php echo getDateOrDifference(get_comment_date('Y-m-d H:i:s')); ?></span>
				</h6>
				<?php if ($depth < $args['max_depth']) { ?>
				<span class="comment_reply theme_button"><?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></span>
				<?php } ?>

				<?php if ( $comment->comment_approved == 0 ) { ?>
				<div class="comment_not_approved"><?php _e( 'Your comment is awaiting moderation.', 'themerex' ); ?></div>
				<?php } ?>

				<div class="comment_content">
					<?php 
					comment_text();
					?>
				</div>
            </li>
			<?php
			break;
	endswitch;
}
?>
