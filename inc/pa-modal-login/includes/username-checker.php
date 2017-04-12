<?php

require_once('../../../../../../wp-config.php'); 
require_once('../../../../../../wp-includes/wp-db.php'); 


if(isset($_POST["username"])) {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die();
    }

	$username = explode('@', $_POST['username']);  
	$username = $username[0];
	
	$args = 'search='.$username.'*';
	$users_search = get_users($args);


	if (username_exists($username)) {
		foreach ($users_search as $user) {
			if ($user === end($users_search)) {
				$str = $user->user_login;
				$last = substr($str, -1);
				$math = ($last + 1);
				echo substr_replace($str, $math, -1);
			}
		}
		die;
	} else {
		echo $username;
		die;
	}

}

?>