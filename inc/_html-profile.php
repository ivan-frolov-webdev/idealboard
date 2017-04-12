<?php
    $currentUser = get_current_user_id();
	
	if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		set_query_var( 'postid1', $_POST['postid'] );
		wp_delete_post( get_query_var( 'postid1'), false );
	};

    if (is_user_logged_in() && $currentUser) {
?>
    <?php 
	if(isset($_POST['podnimaem'])){
		date_default_timezone_set('Europe/Minsk');
		set_query_var( 'postid1', $_POST['postid_up'] );
		$update_time = date('Y-m-d H:i:s');
		$choosen_post = array();
		$choosen_post['ID'] = get_query_var('postid1');
		$choosen_post['post_date'] = $update_time;

		// Обновляем данные в БД
		wp_update_post( $choosen_post );
		echo '<div class="alert alert-success" role="alert">Ваше объявление было поднято!</div>';
	}
	?>
    <div class="alert" role="alert" style="display: none;"></div>
    
    <form action="#" method="post" id="profile_form">
    
        <input type="hidden" id="user_id" name="user_id" value="<?php echo $currentUser; ?>">
        <?php
        echo idealTools::inputTag('text', 'Имя', 'ib_user_name', 'Ваше имя', get_user_meta($currentUser, 'user_name', true), '');
        echo idealTools::inputTag('text', 'Номер телефона', 'ib_user_phone', 'Номер телефона', get_user_meta($currentUser, 'user_phone', true), '');
    
        echo idealTools::inputTag('submit', '', 'submit_profile_front', '', 'Сохранить', 'btn btn-primary');
        ?>
    
    </form>
    
        <h2 style="display:block;margin-top:50px;">Мои объявления</h2>
        <div class="all-posts-list">
			<style>
				.post-item img {
					float: left;
					margin: 0 10px 20px 0; 
				}
				.new-edit {
					display: inline-block;
				}
				.submit-img { 
					width: 24px;
					height: 24px;
					border: 0px;
					background-color: transparent !important;
					cursor: pointer;
					outline: 0;
					margin: 0 !important;
					padding: 0 !important;
				}
				.submit-edit {
					background: url("<?php echo IB_PLUGIN_URL . '/img/edit.png'; ?>") !important;
				}
				.submit-up {
					background: url("<?php echo IB_PLUGIN_URL . '/img/up.png'; ?>") !important;
				}
				.delete {
					background: url("<?php echo IB_PLUGIN_URL . '/img/delete.png'; ?>") !important;
				}
				.submit-img:hover{  
					background-position: 0px -24px;
				}
				.submit-img:active{  
					 background-position: 0px -24px;
				}
			</style>
			<script>
			(function($) {
				$(document).ready(function() {
					$(".delete").click(function() {
						var isGood=confirm('Вы уверены, что хотите удалить выбранное объявление?');
						if (isGood) {}
						else { return false; }
					});
				});
			})(jQuery)
			</script>
            <?php
            $query = new WP_Query( 'author=' . $currentUser );
            if ( $query->have_posts()) : while ($query->have_posts()) : $query->the_post();
            ?>
			
            <div class="post-item">
				<?php echo get_the_post_thumbnail( get_the_ID(), array( 150, 150 ) ); ?>
                <div style="float: left; display: inline-block; width: 75%">
					<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
					<small><?php the_time('G:i, j F Y') ?></small>
					<div class="entry"><?php idealTools::print_excerpt(100); ?></div>
					<div>
						<form class="new-edit" action="<?php bloginfo('url'); ?>/change_post/" method="post">
							<input type="hidden" name="postid" value="<?php the_ID(); ?>" />
							<input type="submit" value="" class="submit-img submit-edit" title="Редактировать объявление" />
						</form>
						<form class="new-edit" action="" method="post">
							<input type="hidden" name="postid" value="<?php the_ID(); ?>" />
							<input type="submit" class="delete submit-img" value="" title="Удалить объявление" />
						</form>
						<form class="new-edit" method="post">
							<input type="hidden" name="postid_up" value="<?php the_ID(); ?>" />
							<input type="submit" class="submit-img submit-up" name="podnimaem" value="" title="Поднять объявление">
						</form>
					</div>
				</div>
            </div>
			<div class="clear"></div>
			
            <?php wp_reset_postdata(); endwhile; endif; ?>
        </div>

<?php
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