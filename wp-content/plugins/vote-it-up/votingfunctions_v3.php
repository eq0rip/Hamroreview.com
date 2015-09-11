<?php
/*
This script is designed to be run from wordpress. It will work if placed in the root directory of a wordpress install.
Revision: 6

-Uses database v3
-Now uses sql to count votes instead of php for faster script exectution
-Technical limit on guest votes now raised. Technical limit is now based on maximum size of the database.
-Fixes wrong vote counts as a result of deleting votes
-Excludes private, drafts, and pages from appearing in widget
-Offset feature enhanced
-Fixes duplicate voting issues arising from caching

This file is not compatible with database v1. Please use importdb.php to upgrade your database from v1 to v3.

*/

//Variables
global $wpdb;
$voteiu_databasetable = $wpdb->prefix."voteiu_data";

//Functions SetPost, SetUser are removed as they are no longer needed.

//Returns the vote count
//Format: ''= Overall vote count, 'array'= Array[total,vote,sink,guestvote,guestsink], 'percent'= Percentage of positive votes out of total
function GetVotes($post_ID, $format = '') {
	global $wpdb, $voteiu_databasetable;
	
	$p_ID = $wpdb->escape($post_ID); //prevents SQL injection

	//Associative array format: [0]=post,[1]=votes,[2]=sinks,[3]=guestvotes,[4]=guestsinks,[5]=offset
	$query = "SELECT post, count(distinct (case when type='vote' then vote else null end)), count(distinct (case when type='sink' then vote else null end)), count(distinct (case when type='guestvote' then vote else null end)), count(distinct (case when type='guestsink' then vote else null end)), sum(case when type='offset' then vote else 0 end) FROM `wp_voteiu_data` WHERE post='".$p_ID."' GROUP BY post";
	$querydata = $votes = $wpdb->get_row($query, ARRAY_N, 0);
	//Use round to convert '' to 0;
	$votes = round($querydata[1]);
	$sinks = round($querydata[2]);
	$guestvotes = round($querydata[3]);
	$guestsinks = round($querydata[4]);
	$offset = round($querydata[5]);
	$offset = $offset + get_option('voteiu_initialoffset');

	//The mathematics
	switch ($format) {
		case "percent":
			//Make $votecount into a percent
			$totalcount = $votes + $sinks + $guestvotes + $guestsinks + $offset;
			$forcount = $votes + $guestvotes + $offset;
			$againstcount = $sinks + $guestsinks; //unused

			if ($totalcount > 0) {
				$votecount = number_format(100*($forcount / $totalcount), 0) . "%";
			} else {
				return "0%";
			}
			return $votecount;
			//Uncomment this line below if you want to test
			//return count($votes) . " " . count($sinks) . " " . count($guestvotes) . " " . count($guestsinks) . " " . count($uservotes) . " " . count($usersinks) . " " . get_option('voteiu_initialoffset') . " " . $p_ID;
			break;
		case "array":
			$votecount = $votes - $sinks + $guestvotes - $guestsinks + $offset;
			$votearray = array("total" => $votecount, "votes" => $votes, "sinks" => $sinks, "guestvotes" => $guestvotes, "guestsinks" => $guestsinks, "offset" => $offset);
			return $votearray;
			break;
		default:
			//Normal vote count
			$votecount = $votes - $sinks + $guestvotes - $guestsinks + $offset;
			return $votecount;
			break;
	}
}

//Returns Votes as a percentage
function GetVotesPercent($post_ID) {
	return GetVotes($post_ID, "percent");
}

//Returns positive vote count, considering offset
function GetPostVotes($post_ID) {
	$votearray = GetVotes($post_ID, "array");
	return $votearray['votes'] + $votearray['guestvotes'] + $votearray['offset'];
}

//Returns negative vote count
function GetPostSinks($post_ID) {
	$votearray = GetVotes($post_ID, "array");
	return $votearray['sinks'] + $votearray['guestsinks'];
}

//Returns vote count for bar theme
function GetBarVotes($post_ID, $limiter = 40) {
	$max_displayed_votes = $limiter;
	$vote_threshold = 30;

	$votes = GetVotes($post_ID);
	$votemax = $max_displayed_votes;
	$votebreak =  30; //votes at which bar changes color
	$bar[0] = 0; //The length of the bar
	$bar[1] = 0; //The state of the bar
	if ($votes > $votemax && $votes > -1) {
		$bar[0] = $votemax;
	} else {
		if ($votes > -1) {
			$bar[0] = $votes;
		} else {
			$bar[0] = 0;
		}
	}
	if ($votes > $votebreak) {
		$bar[1] = 1;
	}
	return $bar;
}

//Checks if the user voted
function UserVoted($post_ID, $user_ID) {
	global $wpdb, $voteiu_databasetable;
	
	//prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);
	$u_ID = $wpdb->escape($user_ID);
	
	//Check if vote exists
	$id_raw = $wpdb->get_var("SELECT ID FROM ".$voteiu_databasetable." WHERE vote='".$u_ID."' AND post='".$p_ID."'");
	if ($id_raw != '') {
		//entry exists
		return true;
	} else {
		//entry does not exist
		return false;
	}
}

//Checks if the guest voted (exactly same as UserVoted)
function GuestVoted($post_ID, $user_ID) {
	global $wpdb, $voteiu_databasetable;
	
	//prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);
	$u_ID = $wpdb->escape($user_ID);
	
	//Check if vote exists
	$id_raw = $wpdb->get_var("SELECT ID FROM ".$voteiu_databasetable." WHERE vote='".$u_ID."' AND post='".$p_ID."'");
	if ($id_raw != '') {
		//entry exists
		return true;
	} else {
		//entry does not exist
		return false;
	}
}

//Checks the key to see if it is valid.
function CheckKey($key, $id) {
	global $wpdb;
	$userdata = $wpdb->get_results("SELECT display_name, user_email, user_url, user_registered FROM $wpdb->users WHERE ID = '".$id."'", ARRAY_N);
	$chhash = md5($userdata[0][0].$userdata[0][3]);
	if ($chhash == $key) {
		return true;
	} else {
		return false;
	}
}

//Inserts a vote. Does not check if user has voted or not
function InsertVote($post_ID, $user_ID, $type) {
	global $wpdb, $voteiu_databasetable;
	
	// Prevents SQL injection
	$post_ID = $wpdb->escape($post_ID);
	$user_ID = $wpdb->escape($user_ID);
	$type = $wpdb->escape($type);

	$queryinsert = "INSERT INTO ".$voteiu_databasetable." (post, vote, type) VALUES ('".$post_ID."', '".$user_ID."', '".$type."')";
	$wpdb->query($queryinsert) or die(mysql_error());
}

//Saves the vote of a user to the database. 
function Vote($post_ID, $user_ID, $type = 'vote') {
	global $wpdb, $voteiu_databasetable;
	$result = false;

	// Prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);
	$u_ID = $wpdb->escape($user_ID);

	if (!UserVoted($post_ID, $user_ID)) {
		InsertVote($p_ID, $u_ID, $type);
		$result = true;
	}
	return $result; //returns true if the vote is saved, false if no changes were made
}

//Saves the vote of a guest to the database. 
function GuestVote($post_ID, $type = 'vote') {
	global $wpdb, $voteiu_databasetable;

	$result = false;
	$user_ID = md5($_SERVER['REMOTE_ADDR']);

	//Prevents SQL injection
	$p_ID = $wpdb->escape($post_ID);
	$u_ID = $wpdb->escape($user_ID);

	if (!UserVoted($post_ID, $user_ID)) {
		if ($type == 'vote') {
			InsertVote($p_ID, $u_ID, 'guestvote');
		} else {
			InsertVote($p_ID, $u_ID, 'guestsink');
		}
		$result = true;
	}
	return $result; //returns true if the vote is saved, false if no changes were made
}

//Gets an array of posts with vote count
//Identical to SortVotes() except that it is sorted by post ID in descending order, and has more information on votes
function GetVoteArray($page = 0) {
	global $wpdb, $voteiu_databasetable;
	
	//First page is 0
	$postsperpage = 50;
	$upperlimit = $postsperpage * ($page + 1);
	$lowerlimit = $postsperpage * ($page);
	//Get the posts available for vote editing
	//Use wordpress posts table
	//For posts to be available for vote editing, they must be published posts.
	mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
	mysql_select_db(DB_NAME) or die(mysql_error());
	$posttablecontents = mysql_query("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT ".$lowerlimit.", ".$upperlimit."") or die(mysql_error());
	//$posttablecontents = mysql_query("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'post'") or die(mysql_error());
	$returnarray = array();
	while ($row = mysql_fetch_array($posttablecontents)) {
		$post_id = $row['ID'];
		$vote_array = GetVotes($post_id, "array");
		$returnarray[count($returnarray)] = array($post_id, $vote_array);
	}

	return $returnarray;
}

//Used in options page
function DisplayPageList($page = 0) {
	global $wpdb;
	$query = "SELECT count(*) FROM ".$wpdb->prefix."posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date_gmt DESC";
	$postcount = $wpdb->get_var($query);
	$postsperpage = 50;
	$pagecount = ceil($postcount / $postsperpage);
	?><div class="alignright actions">
	<form action="" method="get">
	<input type="hidden" name="page" value="voteitupeditvotes" />
	Page <select name="pageno"><?php
	
	$i = 0;
	while ($i < $pagecount) {
		if ($page != $i) {
			echo "<option value='".$i."'>".($i + 1)."</option>";
		} else {
			echo "<option value='".$i."' selected='selected'>".($i + 1)."</option>";
		}
		$i++;
	}
	?></select>
	<input type="submit" name="pagesubmit" value="Go" class="button-secondary action" />
	</form>
	</div><?php
}

//Used in options page
function DisplayPostList($page = 0) {
	$a = GetVoteArray($page);
	$i = 0;
	//Begin table
	?>
	<table class="widefat post fixed" id="formtable" style="clear: both;" cellspacing="0">
		<thead>
		<tr>

		<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" name="multiselect[]" onclick="javascript:CheckUncheck()" /></th>
		<th scope="col" id="title" class="manage-column column-title" style="">Post</th>
	<?php /*?>	<th scope="col" id="author" class="manage-column column-author" style="">Author</th><?php */ ?>
		<th scope="col" id="votes" class="manage-column column-categories" style="width: 40%">Votes</th>


		</tr>
		</thead>

		<tfoot>
		<tr>
		<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" name="multiselect[]" onclick="javascript:CheckUncheck()" /></th>
		<th scope="col"  class="manage-column column-title" style="">Post</th>
	<?php /* ?>	<th scope="col"  class="manage-column column-author" style="">Author</th><?php */ ?>

		<th scope="col"  class="manage-column column-categories" style="">Votes</th>

		</tr>
		</tfoot>
		<tbody>
	<?php

	while ($i < count($a)) {
		$postdat = get_post($a[$i][0]);
		if (!empty($postdat)) {
			?><tr id='post-<?php echo $a[$i][0]; ?>' class='alternate author-other status-publish iedit' valign="top">
			<th scope="row" class="check-column"><input type="checkbox" name="post[]" value="<?php echo $a[$i][0]; ?>" /></th>
			<td class="post-title column-title"><strong><a class="row-title" href="<?php echo $postdat->guid; ?>" title="<?php echo $postdat->post_title; ?>"><?php echo $postdat->post_title; ?></a></strong></td>
			<td class="categories column-categories"><?php echo $a[$i][1]["total"]; ?> (Users: <span style="color:#00CC00">+<?php echo $a[$i][1]["votes"]; ?></span>/<span style="color:#CC0000">-<?php echo $a[$i][1]["sinks"]; ?></span>, Guests: <span style="color:#00CC00">+<?php echo $a[$i][1]["guestvotes"]; ?></span>/<span style="color:#CC0000">-<?php echo $a[$i][1]["guestsinks"]; ?></span><?php
			if($a[$i][1]["offset"] != '0') {
				echo ', Offset: ';
				if ($a[$i][1]["offset"] > 0) {
					echo '<span style="color:#00CC00">+'.$a[$i][1]["offset"].'</span>';
				} else {
					echo '<span style="color:#CC0000">'.$a[$i][1]["offset"].'</span>';
				}
			}
			?>)</td></tr><?php
		}
		$i++;
	}

	//End table
	?></tbody>
	</table><?php
}


//Handles the deleting of votes, used to read the POST when the page is submitted
function VoteBulkEdit() {
	$buttonnumber = 0; //Determines which apply button was clicked on. 0 if no button was clicked.
	$action = 'none'; //Determines what should be done
	if (array_key_exists('doaction1', $_POST)) {
		$buttonnumber = 1;
	}
	if (array_key_exists('doaction2', $_POST)) {
		$buttonnumber = 2;
	}
	if ($buttonnumber != 0 && array_key_exists('action'.$buttonnumber, $_POST)) {
		if ($_POST['action'.$buttonnumber] != -1) {
			//Assigns action to be done
			$action = $_POST['action'.$buttonnumber];
		}
	}

	if (!array_key_exists('post', $_POST)) {
		$action = 'none'; //set action to none if there are no posts to modify
	}

	//Begin modifying votes
	if ($action != 'none' && $action != '') {
		ResetVote($_POST['post'], $action);
	}
}

// Resets vote counts
function ResetVote($postids, $action) {
	global $wpdb, $voteiu_databasetable;

	switch ($action) {
		case 'delete': //reset all votes for the post
			$i = 0;
			while ($i < count($postids)) {
				$wpdb->query("DELETE FROM ".$voteiu_databasetable." WHERE `post`=".$wpdb->escape($postids[$i])." ;");
				$i++;
			}
			EditVoteSuccess();
			break;
		case 'deleteuser': //reset all votes for users
			$i = 0;
			while ($i < count($postids)) {
				$wpdb->query("DELETE FROM ".$voteiu_databasetable." WHERE type = 'vote' post=".$wpdb->escape($postids[$i])." ;");
				$wpdb->query("DELETE FROM ".$voteiu_databasetable." WHERE type = 'sink' post=".$wpdb->escape($postids[$i])." ;");
				$i++;
			}
			EditVoteSuccess();
			break;
		case 'deleteguest':
			//reset all votes for guests
			$i = 0;
			while ($i < count($postids)) {
				$wpdb->query("DELETE FROM ".$voteiu_databasetable." WHERE type = 'guestvote' post=".$wpdb->escape($postids[$i])." ;");
				$wpdb->query("DELETE FROM ".$voteiu_databasetable." WHERE type = 'guestsink' post=".$wpdb->escape($postids[$i])." ;");
				$i++;
			}
			EditVoteSuccess();
			break;
	}
}

//Indicates that votes were edited successfully
function EditVoteSuccess() {
	?><div id="message" class="updated fade"><p><strong>Votes edited</strong></p></div><?php
}


//Used to sort votes for widgets
//Excludes pages, private and draft posts
//Returns an array sorted by vote count in descending order.
//Array[#rank] = {[post id], [vote count], [user vote count], [user sink count], [guest vote count], [guest sink count]}
function SortVotes() {
	global $wpdb, $voteiu_databasetable;

	mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
	mysql_select_db(DB_NAME) or die(mysql_error());
	//Set a limit to reduce time taken for script to run
	$upperlimit = get_option('voteiu_limit');
	if ($upperlimit == '') {
		$upperlimit = 100;
	}
	$lowerlimit = 0;

	$postarray = array();
	$votesarray = array();

	//Use wordpress posts table
	//For posts to be available for vote editing, they must be published posts.
	mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
	mysql_select_db(DB_NAME) or die(mysql_error());
	
	//Sorts by date instead of ID for more accurate representation, also only count actual posts that are published
	$posttablecontents = mysql_query("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT ".$lowerlimit.", ".$upperlimit."") or die(mysql_error());

	$returnarray = array();
	while ($row = mysql_fetch_array($posttablecontents)) {
		$post_id = $row['ID'];
		$vote_array = GetVotes($post_id, "array");
		array_push($postarray, array($post_id));
		array_push($votesarray, array(GetVotes($post_id)));
	}
	array_multisort($votesarray, SORT_DESC, $postarray);
	$output = array($postarray, $votesarray);
	return $output;
}


//Displays the widget
function MostVotedAllTime_Widget() {
	$a = SortVotes();
	
	// Begin
	?><div class="votewidget">
	<div class="title">Most Voted</div><?php
	$rows = 0;

	// Display the top few posts
	$i = 0;
	while ($rows < get_option('voteiu_widgetcount')) {
		if ($a[0][$i][0] != '') {
			$postdat = get_post($a[0][$i][0]);
			if (!empty($postdat)) {
				$rows++;

				if (round($rows / 2) == ($rows / 2)) {
					echo '<div class="fore">';
				} else {
					echo '<div class="back">';
				}
				echo '<div class="votecount">'.$a[1][$i][0].' '.Pluralize($a[1][$i][0], 'votes', 'vote').' </div><div><a href="'.$postdat->guid.'" title="'.$postdat->post_title.'">'.$postdat->post_title.'</a></div>';
				echo '</div>';
			}
		}
		if ($i < count($a[0])) {
			$i++;
		} else {
			break; //exit the loop
		}
	}

	// End
	?></div><?php
}

//Displays the widget optimised for sidebar
function MostVotedAllTime_SidebarWidget() {
	$a = SortVotes();
	//Before
	?><div class="votewidget"><?php
	$rows = 0;

	// Display the top few posts
	$i = 0;
	while ($rows < get_option('voteiu_widgetcount')) {
		if ($a[0][$i][0] != '') {
			$postdat = get_post($a[0][$i][0]);
			if (!empty($postdat)) {
				$rows++;
					if (round($rows / 2) == ($rows / 2)) {
						echo '<div class="fore">';
					} else {
						echo '<div class="back">';
					}
					echo '<div class="votecount">'.$a[1][$i][0].'</div><div><a href="'.$postdat->guid.'" title="'.$postdat->post_title.'">'.$postdat->post_title.'</a></div>';
					echo '</div>';
			}
		}
		if ($i < count($a[0])) {
			$i++;
		} else {
			break; //exit the loop
		}
	}

	//End
	?></div><?php
}

//For those particular with English
function Pluralize($number, $plural, $singular) {
	if ($number == 1) {
		return $singular;
	} else {
		return $plural;
	}
}

//Not used yet
function IsExcluded($id) {
	global $excludedid;
	$clean = str_replace("\r", "", $excludedid);
	$excluded = explode("\n", $clean);
}
