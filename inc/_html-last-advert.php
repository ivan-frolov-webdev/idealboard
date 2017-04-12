<?php
    $currentUser = get_current_user_id();
	
	if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		set_query_var( 'postid1', $_POST['postid'] );
		wp_delete_post( get_query_var( 'postid1'), true );
	};

    if (is_user_logged_in() && $currentUser) {

	// START SHORT CODE
	
	$currentUser = get_current_user_id();
			
	$latest_post = get_posts(array(
		'author'      => $currentUser,
		'orderby'     => 'date',
		'numberposts' => 1,
	));
	
	$latest_post = $latest_post[0];

	echo '<a href="'.$latest_post->guid.'"><strong>'.$latest_post->post_title.'</strong></a>';
	
	/*
	// Отладка
	echo '<pre>';
	print_r(get_post_meta($latest_post->ID));
	echo '</pre>';
	*/
	
	// END
	
    } else {
        if (function_exists('modal_login')) {
            echo 'Для доступа в личный кабинет необходимо ';
            modal_login(array('form' => 'login', 'login_text'  => 'Авторизироваться'));
            echo ' или ';
            modal_login(array('form' => 'register', 'register_text'  => 'Зарегистрироваться'));
        }

        ?>
        <script>
            jQuery(document).ready(function ($) {
                $('a[href="#modal-login"]').click();
            });
        </script>
        <?php
    }
?>