<?php

if (get_option('voteiu_dbversion') >= 3000) {
	include('votingfunctions_v3.php');
} else {
	//Compatibility
	include('votingfunctions_v1.php');
}
?>