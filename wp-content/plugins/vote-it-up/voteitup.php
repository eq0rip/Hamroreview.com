<?php
/*
Plugin Name: Vote It Up
Plugin URI: http://www.onfry.com/projects/voteitup/
Description: Vote It Up enables bloggers to add voting functionality to their posts.
Version: 1.2.4
Author: Nicholas Kwan (multippt)
Author URI: http://www.onfry.com/
*/

/*  Copyright 2007  Nicholas Kwan  (email : ready725@gmail.com)
    This plugin is dual licensed under the LGPL and the MIT license.
*/


//Installs plugin options and database
include_once('voteinstall.php');
VoteItUp_InstallOptions();
VoteItUp_dbinstall();
//register_activation_hook( basename(__FILE__), 'VoteItUp_dbinstall'); //doesn't seem to work at times..., but I'll put it here

//External configuration file
@include_once('compat.php');

//Declare paths used by plugin
function VoteItUp_Path() {
	global $voteitupint_path;
	if ($voteitupint_path == '') {
		$dir = dirname(__FILE__);
		$dir = str_replace("\\", "/", $dir); //For Linux
		return $dir;
	} else {
		return $voteitup_path;
	}
}

function VoteItUp_ExtPath() {
	global $voteitup_path;
	if ($voteitup_path == '') {
		$dir = VoteItUp_Path();
		$base = ABSPATH;
		$base = str_replace("\\", "/", $base);
		$edir = str_replace($base, "", $dir);
		$edir = get_bloginfo('url')."/".$edir;
		$edir = str_replace("\\", "/", $edir);
		return $edir;
	} else {
		return $voteitup_path;
	}
}

//Includes other functions of the plugin
include_once(VoteItUp_Path()."/votingfunctions.php");
include_once(VoteItUp_Path()."/skin.php");

//Installs configuration page
include("voteconfig.php");

function VoteItUp_header() {
	$voteiu_skin = get_option('voteiu_skin');
	//If no skin is selected, only include default theme/script to prevent conflicts.
	if ($voteiu_skin == '') {
		?><link rel="stylesheet" href="<?php echo VoteItUp_ExtPath(); ?>/votestyles.css" type="text/css" />
		<script type="text/javascript" src="<?php echo VoteItUp_ExtPath(); ?>/voterajax.js"></script><?php
	} else {
		LoadSkinHeader($voteiu_skin);
	}
	/* These are things always used by voteitup */
	?><link rel="stylesheet" href="<?php echo VoteItUp_ExtPath(); ?>/voteitup.css" type="text/css" />
	<script type="text/javascript" src="<?php echo VoteItUp_ExtPath(); ?>/userregister.js"></script><?php
}

function VoteItUp_footer() {
	if (!get_option('voteiu_allowguests')) {
		?><div class="regcontainer" id="regbox">
		<div class="regcontainerbackground">&nbsp;</div>
		<div class="regpopup">
		<a href="javascript:regclose();" title="Close"><img class="regclosebutton" src="<?php echo VoteItUp_ExtPath(); ?>/closebutton.png" /></a>
		<h3>You need to log in to vote</h3>

		<p>The blog owner requires users to be <a href="<?php echo get_option('siteurl').'/wp-login.php'; ?>" title="Log in">logged in</a> to be able to vote for this post.</p>
		<p>Alternatively, if you do not have an account yet you can <a href="<?php echo get_option('siteurl').'/wp-login.php?action=register'; ?>" title="Register account">create one here</a>.</p>
		</div></div><?php
	}
}

//Displays the widget, theme supported
function MostVotedAllTime($skinname = '', $mode = '') {
	$voteiu_skin = get_option('voteiu_skin');
	$currentskin = $voteiu_skin;
	if ($skinname != '') {
		$currentskin = $skinname;
	}
	$usefallback = false;
	
	if ($currentskin == '' || $currentskin == 'default') {
		$usefallback = true;
	} else if (!LoadSkinWidget($currentskin, $mode)) { // Try to use a theme
		$usefallback = true; // Theme loading failed
	}
	
	// Use the predefined fallback theme
	if ($usefallback) {
		if ($mode == 'sidebar') {
			MostVotedAllTime_SidebarWidget();
		} else {
			MostVotedAllTime_Widget(); //Use default bar
		}
	}
}

$currentPostObject = null;

function DisplayVotesPrepareHook($postObject) {
	$currentPostObject = $postObject;
	return $postObject;
}

function DisplayVotesHook($postContent) {
	echo "<div class=\"votewrapper\">";
	DisplayVotes($postObject->ID);
	echo "</div>";
	return $postContent;
}

//Display the votes
function DisplayVotes($postID, $type = '') {
	global $user_ID, $guest_votes, $vote_text, $use_votetext, $allow_sinks, $voteiu_skin;
	
	$postID = wp_kses($postID, array()); // Sanitize, just in case

	$voteiu_skin = get_option('voteiu_skin');
	$votes = GetVotes($postID);
	$barvotes = GetBarVotes($postID);
	switch ($type) {
		case '': // In the event no theme selected, use the current theme
			if ($voteiu_skin == '') {
				return DisplayVotes($postID, 'bar');
			} else if (!LoadSkin($voteiu_skin)) {
				return DisplayVotes($postID, 'bar');
			}
			break;
		// The following themes below are pre-defined themes in the event there are no other themes found
		case 'bar':
			?><span class="barcontainer"><span class="barfill" id="votecount<?php echo $postID ?>" style="width:<?php echo round($barvotes[0] * 2.5); ?>%;">&nbsp;</span></span><?php 
			if ($user_ID != '') { 
				if (!($user_login == get_the_author_login() && !get_option('voteiu_allowownvote'))) {
					?><span><?php 
					if(!UserVoted($postID,$user_ID)) {
						?><span class="bartext" id="voteid<?php the_ID(); ?>">
						<a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php
						if (get_option('voteiu_sinktext') != '') {
							?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a><?php
						}
						?></span><?php
					} else {
						if (get_option('voteiu_aftervotetext') != '') {
							?><span class="bartext" id="voteid<?php the_ID(); ?>"><?php echo get_option('voteiu_aftervotetext'); ?></span><?php
						}
					}
					?></span><?php
				}
			} else {
				if (get_option('voteiu_allowguests') == 'true') {
					?><span><?php
					if(!GuestVoted($postID,md5($_SERVER['REMOTE_ADDR']))) { ?><span class="bartext" id="voteid<?php the_ID(); ?>">
						<a href="javascript:vote('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_votetext'); ?></a><?php 
						if (get_option('voteiu_sinktext') != '') {
							?><a href="javascript:sink('votecount<?php the_ID(); ?>','voteid<?php the_ID(); ?>','<?php echo get_option('voteiu_aftervotetext'); ?>',<?php the_ID(); ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo get_option('voteiu_sinktext'); ?></a><?php 
						}
						?></span><?php 
					}
					?></span><?php 
				} 
			}
			break;
		case 'ticker':
			?><span class="tickercontainer" id="votes<?php the_ID(); ?>"><?php echo $votes; ?></span><?php 
			if ($user_ID != '') { 
				?><span id="voteid<?php the_ID(); ?>"><?php 
				if(!UserVoted($postID,$user_ID)) {
					?><span class="tickertext"><?php 
					if ($use_votetext == 'true') {
						?><a class="votelink" href="javascript:vote_ticker(<?php echo $postID ?>,<?php echo $postID ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo $vote_text; ?></a><?php 
					} else {
						?><span class="imagecontainer"><?php 
						if ($allow_sinks == 'true') { 
							?><a href="javascript:sink_ticker(<?php echo $postID ?>,<?php echo $postID ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');">
							<img class="votedown" src="<?php echo VoteItUp_ExtPath(); ?>/votedown.png" alt="Vote down" border="0" />
							</a><?php
						}
						?><a href="javascript:vote_ticker(<?php echo $postID ?>,<?php echo $postID ?>,<?php echo $user_ID; ?>,'<?php echo VoteItUp_ExtPath(); ?>');">
						<img class="voteup" src="<?php echo VoteItUp_ExtPath(); ?>/voteup.png" alt="Vote up" border="0" />
						</a>
						</span><?php 
					}
					?></span><?php 
				}
				?></span><?php 
			} else {
				if ($guest_votes == 'true') {
					?><span id="voteid<?php the_ID(); ?>"><?php 
					if(!GuestVoted($postID,md5($_SERVER['REMOTE_ADDR']))) { 
						?><span class="tickertext"><?php 
						if ($use_votetext == 'true') { 
							?><a class="votelink" href="javascript:vote_ticker(<?php echo $postID ?>,<?php echo $postID ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');"><?php echo $vote_text; ?></a></span><?php 
						} else { 
							?><span class="imagecontainer"><?php 
							if ($allow_sinks == 'true') { 
								?><a href="javascript:sink_ticker(<?php echo $postID ?>,<?php echo $postID ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');">
								<img class="votedown" src="<?php echo VoteItUp_ExtPath(); ?>/votedown.png" alt="Vote down" border="0" />
								</a><?php 
							}
							?><a href="javascript:vote_ticker(<?php echo $postID ?>,<?php echo $postID ?>,0,'<?php echo VoteItUp_ExtPath(); ?>');">
							<img class="voteup" src="<?php echo VoteItUp_ExtPath(); ?>/voteup.png" alt="Vote up" border="0" />
							</a>
							</span><?php 
						}
						?></span><?php 
					}
					?></span><?php
				}
			}
			break;
	}
}

/* Widget examples can be found in widget.php of wp-includes.*/
function widget_MostVotedAllTime_init() {
	if (function_exists('register_sidebar_widget')) {
		function widget_MostVotedAllTime($args) {
			$options = get_option("widget_MostVotedAllTime");
			if ($options['title'] != '') {
				$title = $options['title'];
			} else {
				$title = 'Most Voted Posts';
			}
			extract($args);
			echo $before_widget;
			echo $before_title.$title.$after_title;
			MostVotedAllTime('', 'sidebar'); 
			echo $after_widget;
		}
		register_sidebar_widget('Most Voted Posts', 'widget_MostVotedAllTime');
		//$widget_ops = array('classname' => 'widget_MostVotedAllTime', 'description' => __( "Displays the most voted up posts") );
		//@wp_register_sidebar_widget('widget_MostVotedAllTime', __('Most Voted Posts'), 'widget_MostVotedAllTime', $widget_ops);

		function widget_MostVotedAllTime_Control() {
			$options = $newoptions = get_option("widget_MostVotedAllTime");
			if (isset($_POST['widget_MostVotedAllTime_title'])) {
				$newoptions['title'] = strip_tags(stripslashes($_POST['widget_MostVotedAllTime_title']));
			}
			if ($options != $newoptions ) {
				$options = $newoptions;
				update_option('widget_MostVotedAllTime', $options);
			}
			$title = attribute_escape($options['title']);
			?>
			<p>
				<label for="widget_MostVotedAllTime_title">Title: </label>
				<input type="text" class="widefat" id="widget_MostVotedAllTime_title" name="widget_MostVotedAllTime_title" value="<?php echo $title; ?>" />
				<input type="hidden" id="voteitup-submit" name="voteitup-submit" value="1" />
			</p>
			<?php
		}
		register_widget_control('Most Voted Posts', 'widget_MostVotedAllTime_Control', 0, 0 );
	}
}

//Runs the plugin
add_action('wp_head', 'VoteItUp_header');
add_action('get_footer', 'VoteItUp_footer');
add_action('admin_menu', 'VoteItUp_options');
add_action('init', 'widget_MostVotedAllTime_init');

$autoAddVotingCode = false;
if (get_option('voteiu_autoinsert') == "1") {
	$autoAddVotingCode = true;
}
if ($autoAddVotingCode) {
	add_action('the_post', 'DisplayVotesPrepareHook');
	add_action('the_content', 'DisplayVotesHook');
}

?>