<?php
// This is an API to perform voting and to obtain voting scores

define('WP_USE_THEMES', false);
include_once("../../../wp-blog-header.php");
include_once("votingfunctions.php");

$v_pid = "";
$v_uid = "";
$v_tid = "";
if (isset($_GET['pid'])) {
	$v_pid = wp_kses($_GET['pid'], array());
}
if (isset($_GET['uid'])) {
	$v_uid = wp_kses($_GET['uid'], array());
}
if (isset($_GET['tid'])) {
	$v_tid = wp_kses($_GET['tid'], array());
}

if ($v_pid != '') {
	if ($v_uid != '') {
		if ($v_uid == 0) {
			//Guest voting
			if ($_GET['type'] != 'sink') {
				GuestVote($v_pid,'vote');
			} else {
				GuestVote($v_pid,'sink');
			}
		} else  {
			//Add vote
			if ($_GET['type'] != 'sink') {
				Vote($v_pid,$v_uid,'vote');
			} else {
				Vote($v_pid,$v_uid,'sink');
			}
		}
	} 
	if ($v_tid == 'total') {
		echo GetVotes($v_pid, false);
	} else if ($v_tid == 'percent') {
		//run the math as a percentage not total
		echo GetVotes($v_pid, true);
	} else {
		$barvotes = GetBarVotes($v_pid);
		echo $barvotes[0];
	}
} else {
	echo '0';
}
?>