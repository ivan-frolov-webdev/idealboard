<?php
$currentUser = get_current_user_id();

if (is_user_logged_in() && $currentUser) {
    ?>
    
    <div class="alert" role="alert" style="display: none;"></div>

    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="adv_form" method="post" enctype="multipart/form-data">

        <input type="hidden" id="user_id" name="user_id" value="<?php echo $currentUser; ?>">
        <input type="hidden" name="action" value="save_adverts"/>
        <?php
            //echo idealTools::selectOptionsTag( 'wp', 'ib_cats', false, 'Категория', '', '' );
            echo idealTools::selectOptionsTag( 'wp', 'category', false, 'Категория', '', '' );
            echo idealTools::selectOptionsTag( 'region', 'ib_regions', false, 'Регион', '', '' );
            echo idealTools::inputTag( 'text', 'Заголовок', 'ib_title', 'Заголовок объявления', '', '' );

            //idealTools::editorBlock('main_adv');
        ?>
        <div class="form-group">
            <label for="adv_text">Текст объявления</label>
            <textarea class="form-control" name="adv_text" id="adv_text" rows="7"></textarea>
        </div>

        <div class="form-group">
            <label for="ib_price">Цена</label>
            <div class="input-group">
                <input type="text" class="form-control" id="ib_price" name="ib_price" placeholder="Цена">
                <?php $currs = idealTools::return_currency_array( 'full' ); ?>
                <select name="ib_price_index" id="ib_price_index">
                <?php foreach ( $currs as $key => $value ) { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value['curname']; ?></option>
                <?php } ?>
                </select>
            </div>
        </div>

        <input type="hidden" id="currency_id" name="currency_id" value="1">

        <div class="form-group ib_adv_photo">
            <label for="ib_adv_photo">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_adv_photo" name="ib_adv_photo">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_2" style="display: none;">
            <label for="ib_more_img_2">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_2" name="ib_more_img_2">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_3" style="display: none;">
            <label for="ib_more_img_3">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_3" name="ib_more_img_3">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_4" style="display: none;">
            <label for="ib_more_img_4">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_4" name="ib_more_img_4">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_5" style="display: none;">
            <label for="ib_more_img_5">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_5" name="ib_more_img_5">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_6" style="display: none;">
            <label for="ib_more_img_6">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_6" name="ib_more_img_6">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_7" style="display: none;">
            <label for="ib_more_img_7">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_7" name="ib_more_img_7">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_8" style="display: none;">
            <label for="ib_more_img_8">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_8" name="ib_more_img_8">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_9" style="display: none;">
            <label for="ib_more_img_9">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_9" name="ib_more_img_9">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_10" style="display: none;">
            <label for="ib_more_img_10">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_10" name="ib_more_img_10">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
				<div><button>Загрузить еще</button></div>
            </div>
            <!--/noindex-->
        </div>
		
		<div class="form-group ib_more_img_11" style="display: none;">
            <label for="ib_more_img_11">Загрузить изображение</label>
            <div class="file-upload" data-text="Выберите файл">
                <input type="file" id="ib_more_img_11" name="ib_more_img_11">
            </div>

            <!--noindex-->
            <div class="image_preview" style="display: none;">
                <img src="" class="image_preview_img" alt="preview image">
                <a href="#" class="remove_from_preview" onclick="return false;">X</a>
            </div>
            <!--/noindex-->
        </div>
		
        <?php
        echo idealTools::inputTag( 'text', 'Имя', 'ib_name', 'Имя', get_user_meta( $currentUser, 'user_name', true ), '' );
        echo idealTools::inputTag( 'text', 'Телефон', 'ib_phone', 'Телефон', get_user_meta( $currentUser, 'user_phone', true ), '' );
        echo idealTools::inputTag( 'email', 'Email', 'ib_email', 'Email', get_userdata( $currentUser )->user_email, '' );

        echo idealTools::inputTag( 'submit', '', 'submit_advert_front', '', 'Сохранить', 'btn btn-primary' );
        ?>

    </form>


    <?php
} else {
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