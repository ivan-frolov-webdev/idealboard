<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action( 'wp_ajax_save_adverts', 'save_adverts_callback' );
add_action( 'wp_ajax_nopriv_save_adverts', 'save_adverts_callback' );

//add_action('admin_post_save_adverts', 'save_adverts_callback');
//add_action('admin_post_nopriv_save_adverts', 'save_adverts_callback');

function save_adverts_callback() {

    $user_id = (int)$_POST['user_id'];
    $category = (int)$_POST['category'];
    $region = (int)$_POST['ib_regions'];
    $title = sanitize_text_field ($_POST['ib_title']);
    //$text = sanitize_text_field ($_POST['adv_text']);
	$text = wp_filter_post_kses ($_POST['adv_text']);
    $price = sanitize_text_field ($_POST['ib_price']);
    $currency = (int)$_POST['currency_id'];
    $user_name = sanitize_text_field ($_POST['ib_name']);
    $user_phone = sanitize_text_field ($_POST['ib_phone']);
    $user_email = is_email($_POST['ib_email']);
    //$category = get_term_by( 'id', $category, 'ib_cats' );
    //$category_id = $category->term_id;
    $region = get_term_by( 'id', $region, 'ib_regions' );
    $region_id = $region->term_id;

    $defaults = array(
        'post_status'   => 'publish', //[ 'draft' | 'publish' | 'pending'| 'future' | 'private' ]
        'post_type'     => 'post',
        'post_category' => array($category),
        'post_author'   => $user_id,
        'ping_status'   => get_option('default_ping_status'),
        'comment_status' => 'closed', //[ 'closed' | 'open' ]
        'post_content' => $text,
        'post_title' => $title,
        'post_parent'   => 0,
        'menu_order'    => 0,
        'to_ping'       => '',
        'pinged'        => '',
        'post_password' => '',
        'guid'          => '',
        'post_content_filtered' => '',
        'post_excerpt'  => '',
        'import_id'     => 0
    );

    $post_id = wp_insert_post($defaults);
	$data_sozdania = current_time('mysql');

    //wp_set_object_terms( $post_id, array($category_id), 'ib_cats' );
    wp_set_object_terms( $post_id, array($region_id), 'ib_regions' );

    update_post_meta($post_id, 'user_name', $user_name);
    update_post_meta($post_id, 'user_phone', $user_phone);
    update_post_meta($post_id, 'user_email', $user_email);
    update_post_meta($post_id, 'ib_price', $price);
    update_post_meta($post_id, 'currency_id', $currency);
	update_post_meta($post_id, 'data_sozdania', $data_sozdania);

    if (!function_exists('wp_generate_attachment_metadata')){
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
    }

    if ($_FILES) {
        foreach ($_FILES as $file => $array) {
            if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                return "upload error : " . $_FILES[$file]['error'];
            }
            $attach_id = media_handle_upload( $file, $post_id );

			if ($file == 'photo') {
				update_post_meta($post_id, '_thumbnail_id', $attach_id);
			}
			
			$array_img = array(
				"ib_more_img_2",
				"ib_more_img_3",
				"ib_more_img_4",
				"ib_more_img_5",
				"ib_more_img_6",
				"ib_more_img_7",
				"ib_more_img_8",
				"ib_more_img_9",
				"ib_more_img_10",
				"ib_more_img_11",
			);

			foreach ($array_img as $img) {
				if ($file == $img) {
					update_post_meta($post_id, $img, $attach_id);
				}
			}

        }
    }

	/*
    if ($attach_id > 0){
		//$ib_more_img_2 = $attach_id - 1;
		//update_post_meta($post_id, 'ib_more_img_2', $ib_more_img_2);
        update_post_meta($post_id, '_thumbnail_id', $attach_id);
    }
	*/

    if ($post_id) {
        $res = 'success';
    } else {
        $res = 'error';
    }
    echo json_encode($res);
    wp_die();
}


add_action( 'wp_ajax_save_profile', 'save_profile_callback' );
//add_action( 'wp_ajax_nopriv_save_profile', 'save_profile_callback' );

function save_profile_callback() {
    $user_name = sanitize_text_field ($_POST['ib_name']);
    $user_phone = sanitize_text_field ($_POST['ib_phone']);
    $user_id = (int)$_POST['user_id'];
    
    $un = update_user_meta($user_id, 'user_name', $user_name );
    $up = update_user_meta($user_id, 'user_phone', $user_phone );

    if ($un || $up) {
        $res = 'success';
    } else {
        $res = 'error';
    }
    echo json_encode($res);

    wp_die();
}


function save_cats_mass_clb() {
    $cat = $_POST['cat'];
    $parent = $_POST['parent'];


    $listOfCats = explode( '<br />',  nl2br($_POST['mass_cats']));


    if ( 'category' === $cat ) {
        foreach ( $listOfCats as $listOfCat ) {
            wp_create_category($listOfCat, $parent);
        }
    } else if ( 'ib_regions' === $cat ) {
        foreach ( $listOfCats as $listOfCat ) {

            $cat_defaults = array(
                'cat_ID' => 0,
                'taxonomy' => 'ib_regions',
                'cat_name' => $listOfCat,
                'category_parent' => $parent,
            );

            wp_insert_category($cat_defaults);
        }
    }

    wp_redirect(admin_url('edit-tags.php?taxonomy=' . $cat));
}
//add_action( 'admin_post_nopriv_save_cats_mass', 'save_cats_mass_clb' );
add_action( 'admin_post_save_cats_mass', 'save_cats_mass_clb' );