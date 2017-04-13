<?php
$currentUser = get_current_user_id();

if (is_user_logged_in() && $currentUser) {
 
if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "edit_post" && isset($_POST['postid'])) {
    $post_to_edit = array();
    $post_to_edit = get_post($_POST['postid']);

	$title = $_POST['title'];
	$description = $_POST['description'];
	$date = $_POST['date'];
	$user_name = $_POST['user_name']; 
	$user_phone = $_POST['user_phone'];
	$user_email = $_POST['user_email'];
	$ib_price = $_POST['ib_price'];
	$currency_id = $_POST['currency_id'];
	/*
	$ib_more_img_2 = $_POST['ib_more_img_2'];
	$ib_more_img_3 = $_POST['ib_more_img_3'];
	$ib_more_img_4 = $_POST['ib_more_img_4'];
	$ib_more_img_5 = $_POST['ib_more_img_5'];
	$ib_more_img_6 = $_POST['ib_more_img_6'];
	$ib_more_img_7 = $_POST['ib_more_img_7'];
	$ib_more_img_8 = $_POST['ib_more_img_8'];
	$ib_more_img_9 = $_POST['ib_more_img_9'];
	$ib_more_img_10 = $_POST['ib_more_img_10'];
	$ib_more_img_11 = $_POST['ib_more_img_11'];
	*/

    $post_to_edit->post_title = $title;
    $post_to_edit->post_content = $description;
	$post_to_edit->post_date = $date;

    $pid = wp_update_post($post_to_edit);

    wp_set_post_terms($pid, array($_POST['cat']),'category',false);
	wp_set_post_terms($pid, array($_POST['ib_regions']),'ib_regions',false);
    wp_set_post_terms($pid, array($_POST['post_tags']),'post_tag',false);

	update_post_meta($pid, 'user_name', $user_name);
	update_post_meta($pid, 'user_phone', $user_phone);
	update_post_meta($pid, 'user_email', $user_email);
	update_post_meta($pid, 'ib_price', $ib_price);
	update_post_meta($pid, 'currency_id', $currency_id);
	/*
	update_post_meta($pid, 'ib_more_img_2', $ib_more_img_2);
	update_post_meta($pid, 'ib_more_img_3', $ib_more_img_3);
	update_post_meta($pid, 'ib_more_img_4', $ib_more_img_4);
	update_post_meta($pid, 'ib_more_img_5', $ib_more_img_5);
	update_post_meta($pid, 'ib_more_img_6', $ib_more_img_6);
	update_post_meta($pid, 'ib_more_img_7', $ib_more_img_7);
	update_post_meta($pid, 'ib_more_img_8', $ib_more_img_8);
	update_post_meta($pid, 'ib_more_img_9', $ib_more_img_9);
	update_post_meta($pid, 'ib_more_img_10', $ib_more_img_10);
	update_post_meta($pid, 'ib_more_img_11', $ib_more_img_11);
	*/

	//wp_redirect(get_permalink($post_to_edit->ID));
	error_reporting(0);
	echo '<div class="alert alert-success" role="alert">Ваше объявление было обновлено!
	<br/>Вернуться на главную <a href="';
	echo bloginfo(url);
	echo '">страницу</a>.</div><style>#edit_post{display:none;}</style>';

	if (!function_exists('wp_generate_attachment_metadata')){
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');
    }

    if ($_FILES) {
        foreach ($_FILES as $file => $array) {
			
			/*
            if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                return "upload error : " . $_FILES[$file]['error'];
            }
			*/
			
            $attach_id = media_handle_upload( $file, $pid );

			if ($_FILES[$file]['error'] === 0) {
			
				if ($file == 'ib_adv_photo') {
					update_post_meta($pid, '_thumbnail_id', $attach_id);
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
						update_post_meta($pid, $img, $attach_id);
					}
				}
			}
        }
    }

	wp_redirect(home_url());
}
  
if( 'POST' == "" && empty( $_POST['action'] ) &&  $_POST['action'] == "" ) {
	error_reporting(0);
	wp_redirect( home_url(), 301 );
	exit;
};

$post_to_edit = get_post($_POST['postid']);
$terms = get_the_terms($post_to_edit->ID, 'category');
$post_tag = strip_tags( get_the_term_list( $post_to_edit->ID, 'post_tag', '', ', ', '' ) );
 
$term_name = strip_tags( get_the_term_list( $post_to_edit->ID, 'category', '', ', ', '' ) );
$term_obj = get_term_by('name', $term_name, 'category');
$term_id = $term_obj->term_id ;

$term_name2 = strip_tags( get_the_term_list( $post_to_edit->ID, 'ib_regions', '', ', ', '' ) );
$term_obj2 = get_term_by('name', $term_name2, 'ib_regions');
$term_id2 = $term_obj2->term_id;

$args = array(
    'selected'        => $term_id,
    'name'            => 'cat',
    'class'           => 'form-control',
    'tab_index'       => 10,
    'depth'           => 2,
    'hierarchical'    => 1,
    'taxonomy'        => 'category',
    'hide_empty'      => false
);

$regions_args = array(
    'selected'        => $term_id2,
    'name'            => 'ib_regions',
    'class'           => 'form-control',
    'tab_index'       => 10,
    'depth'           => 2,
    'hierarchical'    => 1,
    'taxonomy'        => 'ib_regions',
    'hide_empty'      => false
);

$check_edit_page = get_page_by_path( 'change_post' );
$edit_page_id = get_the_id($check_edit_page);

if ($edit_page_id == $post_to_edit->ID) {
	get_header();
	echo 'Вы не можете просматривать данную страницу. Переходим на главную страницу сайта.';
	echo '<style>#edit_post{display:none;}</style><script>
		jQuery(document).ready(function ($) {
			window.location.replace("';
	echo bloginfo(url);
	echo '");
		});
    </script>';
}

?>

<!-- EDIT FORM -->
<form id="edit_post" name="edit_post" method="post" action="" enctype="multipart/form-data">
	<div class="form-group">
		<label for="cat" >Категория</label><br />
		<?php wp_dropdown_categories( $args ); ?>
	</div>
	<div class="form-group">
		<label for="ib_regions" >Регион</label><br />
		<?php wp_dropdown_categories( $regions_args ); ?>
	</div>
	<div class="form-group">
		<label for="title">Заголовок</label><br />
		<input type="text" class="form-control" id="title" value="<?php echo $post_to_edit->post_title; ?>" tabindex="5" name="title" />
	</div>
	<div class="form-group">
		<label for="description">Текст объявления</label><br />
		<textarea id="description" rows="7" name="description"><?php echo $post_to_edit->post_content; ?></textarea>
	</div>
	<div class="form-group">
		<label for="ib_price">Цена</label><br />
		<input type="text" class="form-control" value="<?php echo get_post_meta($post_to_edit->ID,'ib_price', true); ?>" id="ib_price" tabindex="20" name="ib_price" />
		<select name="currency_id" id="currency_id">
		<?php $currs = idealTools::return_currency_array( 'full' ); ?>
		<?php foreach ( $currs as $key => $value ) { ?>
			<option value="<?php echo $key; ?>" <?php if (get_post_meta($post_to_edit->ID,'currency_id', true) == $key) { echo "selected"; } ?>><?php echo $value['curname']; ?></option>
		<?php } ?>
		</select>
	</div>

	<div class="form-group ib_adv_photo">
		<label for="ib_adv_photo">Загрузить изображение</label>
		<div class="file-upload" data-text="Выберите файл">
			<input type="file" name="ib_adv_photo" id="ib_adv_photo" />
		</div>
		<div class="image_current_preview">
			<?php echo get_the_post_thumbnail( $post_to_edit->ID, array( 200, 150 ) ); ?>
		</div>

		<? if (get_post_meta($post_to_edit->ID, 'ib_more_img_2', true) !== '' && is_numeric(get_post_meta($post_to_edit->ID, 'ib_more_img_2', true))) {} else { echo '<div><button class="load-more-img">Загрузить еще</button></div>'; } ?>

		<!--noindex-->
		<div class="image_preview" style="display: none;">
			<img src="" class="image_preview_img" alt="preview image">
			<a href="#" class="remove_from_preview" onclick="return false;">X</a>
		</div>
		<!--/noindex-->
	</div>
	
	<script>
	jQuery( document ).ready( function( $ ){
		
		$('.img-more').each(function(){
			if ($(this).find('.image_current').length>0) {
				$(this).find('.load-more-img').css('display', 'none');
			}
			else {
				$(this).css('display', 'none');
			}
		});

		$('.img-last .load-more-img:last').css('display', 'block');

	});
	</script>

	<? $array_img = array(
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

	foreach ($array_img as $img) { ?>

	<div class="form-group img-more <? echo $img; ?><? $ib_more_img = get_post_meta($post_to_edit->ID, $img, true); if ($ib_more_img !== '' && is_numeric($ib_more_img)) { echo ' img-last'; } ?>">
		<label for="<? echo $img; ?>">Загрузить изображение</label>
		<div class="file-upload" data-text="Выберите файл">
			<input type="file" name="<? echo $img; ?>" id="<? echo $img; ?>" />
		</div>
		<div class="image_current_preview">
			<? $ib_more_img = get_post_meta($post_to_edit->ID, $img, true);	if ($ib_more_img !== '' && is_numeric($ib_more_img)) { ?>
			<img src="<?php echo wp_get_attachment_url(get_post_meta($post_to_edit->ID, $img, true)); ?>" style="height: 150px; margin-top: 15px;" class="image_current" />
			<? } ?>
		</div>
		<div><button class="load-more-img">Загрузить еще</button></div>
		<!--noindex-->
		<div class="image_preview" style="display: none;">
			<img src="" class="image_preview_img" alt="preview image">
			<a href="#" class="remove_from_preview" onclick="return false;">X</a>
		</div>
		<!--/noindex-->
	</div>
	
	<? } ?>

	<div class="form-group">
		<label for="user_name">Имя</label><br />
		<input type="text" class="form-control" value="<?php echo get_post_meta($post_to_edit->ID,'user_name', true); ?>" id="user_name" tabindex="20" name="user_name" />
	</div>
	<div class="form-group">
		<label for="user_phone">Телефон</label><br />
		<input type="text" class="form-control" value="<?php echo get_post_meta($post_to_edit->ID,'user_phone', true); ?>" id="user_phone" tabindex="20" name="user_phone" />
	</div>
	<div class="form-group">
		<label for="user_email">Email</label><br />
		<input type="text" class="form-control" value="<?php echo get_post_meta($post_to_edit->ID,'user_email', true); ?>" id="user_email" tabindex="20" name="user_email" />
	</div>
	<div class="form-group">
		<input type="submit" class="form-control btn btn-primary" value="Обновить" id="submit" name="submit" />
	</div>

	<!-- DONT REMOVE OR CHANGE -->
	<input type="hidden" name="postid" value="<?php echo $post_to_edit->ID; ?>" /> 
	<input type="hidden" name="action" value="edit_post" /> <!-- DONT REMOVE OR CHANGE -->
	<input type="hidden" name="change_cat" value="" /> <!-- DONT REMOVE OR CHANGE -->
	<input type="hidden" name="change_image" value="1" /> <!-- DONT REMOVE OR CHANGE -->
	<?php // wp_nonce_field( 'new-post' ); ?>
</form>
<!-- END OF FORM -->

<?php
} else {
	get_header();
    if (function_exists('modal_login')) {
        echo 'Для добавления объявления необходимо ';
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