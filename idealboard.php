<?php
/**
 * Plugin Name: Идеальная доска
 * Description: Идеальная доска объявлений для регионов и городов
 * Version: 1.3.3
 * Author: UPPING
 * Author URI: http://upping.biz/
 *
 * @package Plugin Ideal board
 * @subpackage Plugins
 */

defined('ABSPATH') or die('1.3');

define('IB_PLUGIN_NAME', plugin_basename(__FILE__));
define('IB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IB_PLUGIN_URL', plugins_url('', __FILE__));
define('IB_PLUGIN_INCLUDES_DIR', IB_PLUGIN_DIR.'/inc/');
define('IB_PLUGIN_INCLUDES_URL', IB_PLUGIN_URL.'/inc/');

include_once(IB_PLUGIN_DIR . 'handler.php');
include_once(IB_PLUGIN_INCLUDES_DIR . 'ideal_tools.php');
include_once(IB_PLUGIN_INCLUDES_DIR . 'ib-widget-categories.php');
include_once(IB_PLUGIN_INCLUDES_DIR . 'invisible-captcha/invisible_captcha.php');

/**
 * LOGIN
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) === true) {
    if ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $_SERVER['HTTPS'] = 'on';
    }
}

$pamlOptions = get_option('paml_options');
define('PAML_PLUGIN_DIR', plugin_dir_path(__FILE__).'/inc/pa-modal-login');
define('PAML_PLUGIN_URL', plugins_url('', __FILE__).'/inc/pa-modal-login');
define('PAML_PLUGIN_INCLUDES_DIR', PAML_PLUGIN_DIR.'/includes/');
define('PAML_PLUGIN_INCLUDES_URL', PAML_PLUGIN_URL.'/includes/');
define('PAML_PLUGIN_ASSETS_DIR', PAML_PLUGIN_DIR.'/assets/');
define('PAML_PLUGIN_ASSETS_URL', PAML_PLUGIN_URL.'/assets/');

function paml_load_textdomain() {
    load_plugin_textdomain('pressapps', false, dirname(plugin_basename(__FILE__)).'/lang/');
}
add_action('plugins_loaded', 'paml_load_textdomain');

require_once PAML_PLUGIN_INCLUDES_DIR. 'modal-login-class.php';
require_once PAML_PLUGIN_INCLUDES_DIR . 'widget/modal-login-widget.php';
if ( is_admin() ) {
    require_once PAML_PLUGIN_INCLUDES_DIR . 'admin.php';
}
function add_modal_login_link( $login_text = 'Login', $logout_text = 'Logout', $show_admin = false ) {
    global $paml_class;

    if ( isset( $paml_class ) ) {
        echo $paml_class->modal_login_btn( $login_text, $logout_text, $show_admin );
    } else {
        echo __( 'Error: Modal Login class failed to load', 'pressapps' );
    }
}
function modal_login( $params = array() ) {
    $params_str = '';
    foreach( $params as $parameter => $value ) {
        if( $value ) {
            $params_str .= sprintf( ' %s="%s"', $parameter, $value);
        }
    }
    echo do_shortcode( "[modal_login $params_str]" );
}
if ( class_exists( 'PAML_Class' ) ) {
    $paml_class = new PAML_Class;
}




if ( !class_exists( 'IdealBoard' ) ) {

    class IdealBoard
    {

        public function __construct()
        {
            register_activation_hook( IB_PLUGIN_NAME, array(&$this, 'plugin_activate') );
            register_deactivation_hook( IB_PLUGIN_NAME, array(&$this, 'plugin_deactivate') );

            add_action( 'init', array(&$this, 'register_post_taxonomies'), 0 );

            add_filter( 'manage_edit-post_columns', array(&$this, 'set_custom_posts_columns') );
            add_action( 'manage_post_posts_custom_column' , array(&$this, 'custom_posts_column'), 10, 2 );

            add_action( 'admin_menu', array(&$this, 'remove_admin_menu_items') );

            if ( is_admin() ) {

                //add_filter( 'wp_dropdown_cats', '__return_false' );

                add_action('add_meta_boxes', array(&$this, 'my_extra_fields'), 1);
                add_action('save_post', array(&$this, 'my_extra_fields_update'), 0);
                add_action('admin_init', array(&$this, 'category_custom_fields'), 1);
                add_filter( 'plugin_action_links_' . IB_PLUGIN_NAME, array(&$this, 'my_plugin_action_links') );
            }

            add_action( 'restrict_manage_posts', array(&$this, 'true_taxonomy_filter') );

            add_shortcode( 'profile_shcode', array(&$this, 'profile_short_code') );
            add_shortcode( 'advrt_shcode', array(&$this, 'adv_short_code') );
			add_shortcode( 'advrt_edit_shcode', array(&$this, 'adv_edit_short_code') );
			add_shortcode( 'last_advert_shcode', array(&$this, 'last_advert_short_code') );

            if ( !is_admin() ) {
                add_action( 'wp_print_scripts', array(&$this, 'site_load_scripts') );
                add_action( 'wp_print_styles', array(&$this, 'site_load_styles') );
            } else {
                add_action( 'wp_print_scripts', array(&$this, 'admin_load_scripts') );
            }

            add_filter( 'the_content', array(&$this, 'custom_content_after_post') );
        }


        /**
         * @return bool
         */
        public function plugin_activate()
        {
            update_option('users_can_register', 1);
            update_option('nav_menu_options', 1);
            $this->register_post_taxonomies();
            flush_rewrite_rules();
            $this->create_pages();
            $this->create_base_categories();
            $this->create_base_regions();
            flush_rewrite_rules();
            return true;
        }

        /**
         * @return bool
         */
        public function plugin_deactivate()
        {
            wp_update_term(1, 'category', array('name' => 'Без рубрики',));
            $this->cleanDbAfterDeactivatePlugin();
            flush_rewrite_rules();
            return true;
        }

        /**
         * @return bool
         */
        public static function uninstall()
        {
            delete_option('ib_cats');
            delete_option('ib_pages');
            delete_option('ib_cats_regions');
            return true;
        }

        /**
         *
         */
        public function register_post_taxonomies()
        {
            $labels_regions = array(
                'name' => 'Регион',
                'singular_name' => 'Регион',
                'search_items' =>  'Поиск региона',
                'popular_items' => 'Популярные регионы',
                'all_items' => 'Все регионы',
                'parent_item' => 'Родительский регион',
                'parent_item_colon' => 'Родительский регион:',
                'edit_item' => 'Редактировать регион',
                'update_item' => 'Обновить регион',
                'add_new_item' => 'Добавить новый регион',
                'new_item_name' => 'Новое название региона',
                'separate_items_with_commas' => 'Отдельные регионы через запятую',
                'add_or_remove_items' => 'Добавить или удалить регионы',
                'choose_from_most_used' => 'Выбрать из популярных регионов',
                'menu_name' => 'Регионы',
            );

            register_taxonomy('ib_regions', array('post'), array(
                'hierarchical' => true,
                'labels' => $labels_regions,
                'public' => true,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'region' ),
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
            ));

//            $labels_cats = array(
//                'name' => 'Категория',
//                'singular_name' => 'Категория',
//                'search_items' =>  'Поиск категории',
//                'popular_items' => 'Популярные категории',
//                'all_items' => 'Все категории',
//                'parent_item' => 'Родительская категория',
//                'parent_item_colon' => 'Родительская категория:',
//                'edit_item' => 'Редактировать категорию',
//                'update_item' => 'Обновить категорию',
//                'add_new_item' => 'Добавить новую категорию',
//                'new_item_name' => 'Новое название категории',
//                'separate_items_with_commas' => 'Отдельные категории через запятую',
//                'add_or_remove_items' => 'Добавить или удалить категории',
//                'choose_from_most_used' => 'Выбрать из популярных категорий',
//                'menu_name' => 'Категории',
//            );
//
//            register_taxonomy('ib_cats', array('post'), array(
//                'hierarchical' => true,
//                'labels' => $labels_cats,
//                'public' => true,
//                'show_ui' => true,
//                'query_var' => true,
//                'rewrite' => array( 'slug' => 'cat' ),
//                'show_admin_column' => true,
//                'show_in_nav_menus' => true,
//            ));
        }

        /**
         *
         */
        public function remove_admin_menu_items()
        {
            //remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
            //remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
        }

        /**
         * @param $columns
         * @return array
         */
        public function set_custom_posts_columns($columns)
        {
            //unset($columns['categories']);
            unset($columns['tags']);
            unset($columns['author']);
            unset($columns['comments']);

            $my_custom_col = array(
                'bi_content' => 'Содержание',
                'bi_image' => 'Фотография',
                'bi_price' => 'Цена',
                'bi_contacts' => 'Контакты',
            );

            $columns = array_slice( $columns, 0, 2, true ) + $my_custom_col + array_slice( $columns, 2, NULL, true );
            return $columns;

        }

        /**
         * @param $column
         * @param $post_id
         */
        public function custom_posts_column( $column, $post_id )
        {

            switch ( $column ) {
                case 'bi_content' :
                    echo wp_trim_words( get_post_field('post_content', $post_id), 20, '&hellip;' );
                    break;

                case 'bi_image' :
                    echo get_the_post_thumbnail( $post_id, array(70,70) );
                    break;

                case 'bi_price' :
                    $currency = get_post_meta( $post_id, 'currency_id', true);
                    $sum = get_post_meta( $post_id, 'ib_price', true);
                    echo idealTools::return_currency_array($currency)['cursign'] . ' ' . $sum;
                    break;

                case 'bi_contacts' :
                    $author_id = get_post_field ('post_author', $post_id);
                    $author_name = (get_post_meta($post_id, 'user_name' , true )) ?: get_the_author_meta( 'user_nicename' , $author_id );
                    $author_email = get_post_meta( $post_id, 'user_email' , true );
                    $author_phone = get_post_meta($post_id, 'user_phone', true );
                    echo "<b>Автор:</b> {$author_name}, <br> <b>Email:</b> {$author_email}, <br> <b>Тел.:</b> {$author_phone}";;
                    break;
            }
        }

        /**
         *
         */
        public function true_taxonomy_filter()
        {
            global $typenow;

            if( $typenow == 'post' ){
                //$taxes = array('ib_regions', 'ib_cats');
                $taxes = array('ib_regions', 'category');

                foreach ($taxes as $tax) {

                    $current_tax = isset( $_GET[$tax] ) ? $_GET[$tax] : '';
                    $tax_obj = get_taxonomy($tax);
                    $tax_name = mb_strtolower($tax_obj->labels->name);
                    $args = array('taxonomy' =>$tax, 'hide_empty' => false, );
                    $terms = get_terms($args);

                    if(count($terms) > 0) {
                        $html = "<select name='{$tax}' id='{$tax}' class='postform'>";
                        $html .= "<option value=''>Все {$tax_name}</option>";
                        foreach ($terms as $term) {
                            $selected = $current_tax == $term->slug ? ' selected="selected"' : '';
                            $html .= "<option value='{$term->slug}' {$selected}>{$term->name} ({$term->count})</option>";
                        }
                        $html .= "</select>";

                        echo $html;
                    }
                }
            }
        }

        /**
         * Создание базовых страниц и меню при активации плагина
         */
        private function create_pages()
        {

            $pagesArrayForDeleting = array();

            /* Проверка меню на существование */
            $term = get_term_by('slug', 'menu', 'nav_menu')->term_id;

            /* АвтоДобавление Меню */
            if (!$term) {
                $args = array('alias_of' => 'menu', 'description' => '', 'parent' => 0, 'slug' => 'menu');
                $tObj = wp_insert_term('Menu', 'nav_menu', $args);
                $term = $tObj['term_id'];
            }

            $mArgs = array (
                0 => false,
                'nav_menu_locations' =>
                    array (
                        'primary' => $term,
                    ),
            );

            update_option('theme_mods_' . strtolower(wp_get_theme()->get('Name')), $mArgs);

            $m2Args = array (
                0 => false,
                'auto_add' =>
                    array (
                        0 => $term,
                    ),
            );
            update_option('nav_menu_options', $m2Args);


            $args_addAdv = array(
                'post_title'    => 'Добавить объявление',
                'post_content'  => '[advrt_shcode]',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_author'   => 1,
                'ping_status'   => 'closed',
                'comment_status' => 'closed',
                'post_parent'   => 0,
                'menu_order'    => 1,
                'to_ping'       => '',
                'pinged'        => '',
                'post_password' => '',
                'post_name' => 'adverts',
                'guid'          => get_option('siteurl') . '/adverts',
                'post_content_filtered' => '',
                'post_excerpt'  => '',
                'import_id'     => 0
            );

            $pagesArrayForDeleting[] = wp_insert_post($args_addAdv);

			$args_addAdv_edit = array(
				'post_title'    => 'Редактировать объявление',
				'post_content'  => '[advrt_edit_shcode]',
				'post_status'   => 'publish',
				'post_type'     => 'page',
				'post_author'   => 1,
				'ping_status'   => 'closed',
				'comment_status' => 'closed',
				'post_parent'   => 0,
				'menu_order'    => 1,
				'to_ping'       => '',
				'pinged'        => '',
				'post_password' => '',
				'post_name' => 'change_post',
				'guid'          => get_option('siteurl') . '/change_post',
				'post_content_filtered' => '',
				'post_excerpt'  => '',
				'import_id'     => 0
			);

			$pagesArrayForDeleting[] = wp_insert_post($args_addAdv_edit);
			
			$args_addAdv_thanks = array(
                'post_title'    => 'Спасибо',
                'post_content'  => 'Ваше объявление [last_advert_shcode] успешно опубликовано!<br/>Далее Вы можете зайти в свой <a href="/profile/">личный кабинет</a>, или добавить <a href="/adverts/">новое объявление</a>. Так-же рекомендуем Вам регулярно посещать нашу доску объявлений и бесплатно поднимать свои объявления вверх списка.',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_author'   => 1,
                'ping_status'   => 'closed',
                'comment_status' => 'closed',
                'post_parent'   => 0,
                'menu_order'    => 0,
                'to_ping'       => '',
                'pinged'        => '',
                'post_password' => '',
                'post_name' => 'thanks',
                'guid'          => get_option('siteurl') . '/thanks',
                'post_content_filtered' => '',
                'post_excerpt'  => '',
                'import_id'     => 0
            );

            $pagesArrayForDeleting[] = wp_insert_post($args_addAdv_thanks);

            $args_profile = array(
                'post_title'    => 'Личный кабинет',
                'post_content'  => '[profile_shcode]',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_author'   => 1,
                'ping_status'   => 'closed',
                'comment_status' => 'closed',
                'post_parent'   => 0,
                'menu_order'    => 2,
                'to_ping'       => '',
                'pinged'        => '',
                'post_password' => '',
                'post_name' => 'profile',
                'guid'          => get_option('siteurl') . '/profile',
                'post_content_filtered' => '',
                'post_excerpt'  => '',
                'import_id'     => 0
            );

            $pagesArrayForDeleting[] = wp_insert_post($args_profile);

            $args_pa_modal_login = array(
                'post_title'    => 'Войти // Выйти',
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_type'     => 'nav_menu_item',
                'post_author'   => 1,
                'ping_status'   => 'closed',
                'comment_status' => 'closed',
                'post_parent'   => 0,
                'menu_order'    => 3,
                'to_ping'       => '',
                'pinged'        => '',
                'post_password' => '',
                'post_name' => 'login-logout',
                'guid'          => '',
                'post_content_filtered' => '',
                'post_excerpt'  => '',
                'import_id'     => 0
            );
            $custom_link = wp_insert_post($args_pa_modal_login);

            global $wpdb;
            $wpdb->insert(
                $wpdb->prefix . 'term_relationships',
                array('object_id' => $custom_link, 'term_taxonomy_id' => $term ),
                array( '%d', '%d' )
            );

            update_post_meta($custom_link, '_menu_item_type', 'custom');
            update_post_meta($custom_link, '_menu_item_menu_item_parent', '0');
            update_post_meta($custom_link, '_menu_item_object_id', $custom_link);
            update_post_meta($custom_link, '_menu_item_object', 'custom');
            update_post_meta($custom_link, '_menu_item_target', '');
            update_post_meta($custom_link, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}');
            update_post_meta($custom_link, '_menu_item_xfn', '');
            update_post_meta($custom_link, '_menu_item_url', '#pa_modal_login');

            $pagesArrayForDeleting[] = $custom_link;

            update_option('ib_pages', $pagesArrayForDeleting);

            $palm_opts = array (
                'login-redirect-url' => '',
                'logout-redirect-url' => '',
                'userdefine_password' => 'true',
                'modal-theme' => 'wide',
                'modal-labels' => 'placeholders',
                'bkg-color' => '',
                'font-color' => '',
                'link-color' => '#0a0a0a',
                'btn-color' => '',
                'custom-css' => '',
                'reg_email_subject' => '',
                'reg_email_template' => '',
            );
            update_option('paml_options', $palm_opts);

            $m2Args = array (0 => false, 'auto_add' => array ());
            update_option('nav_menu_options', $m2Args);

        }

        /**
         * @return string
         */
        public static function adv_short_code() {

            ob_start();
            require_once( IB_PLUGIN_INCLUDES_DIR . '_html-adverts.php');
            $out = ob_get_contents();
            ob_end_clean();

            return $out;
        }
		
		public static function adv_edit_short_code() {

			ob_start();
			require_once( IB_PLUGIN_INCLUDES_DIR . '_html-adverts-edit.php');
			$out = ob_get_contents();
			ob_end_clean();

			return $out;
		}
		
        public static function last_advert_short_code() {

            ob_start();
            require_once( IB_PLUGIN_INCLUDES_DIR . '_html-last-advert.php');
            $out = ob_get_contents();
            ob_end_clean();

            return $out;
        }

        /**
         * @return string
         */
        public static function profile_short_code() {
            ob_start();
            require_once( IB_PLUGIN_INCLUDES_DIR . '_html-profile.php');
            $out = ob_get_contents();
            ob_end_clean();

            return $out;
        }

        public function site_load_styles()
        {
            //wp_register_style( 'advBootstrapCss', IB_PLUGIN_URL . '/assets/bootstrap-3.3.6/css/bootstrap.min.css');
            //wp_enqueue_style( 'advBootstrapCss' );
            wp_register_style( 'advReviewsFrontCss', IB_PLUGIN_URL . '/css/front_main.css', '', '1.3.3' );
            wp_enqueue_style( 'advReviewsFrontCss' );
        }

        public function site_load_scripts()
        {
            wp_enqueue_script( 'jquery' );
            wp_register_script( 'advBootstrapJs', IB_PLUGIN_URL . '/assets/bootstrap-3.3.6/js/bootstrap.min.js', array('jquery'), false, true );
            wp_enqueue_script( 'advBootstrapJs' );
            wp_register_script( 'jValidation', IB_PLUGIN_URL . '/assets/jquery-validation-1.15.0/jquery.validate.min.js', array('jquery'), false, true );
            wp_enqueue_script( 'jValidation' );
            wp_register_script( 'jValidationAdd', IB_PLUGIN_URL . '/assets/jquery-validation-1.15.0/additional-methods.min.js', array('jquery, jValidation'), false, true );
            wp_enqueue_script( 'jValidationAdd' );
            wp_register_script( 'jPriceFormat', IB_PLUGIN_URL . '/js/jquery.price_format.min.js', array('jquery'), false, true );
            wp_enqueue_script( 'jPriceFormat' );

            //wp_register_script( 'advReviewsFrontJs', IB_PLUGIN_URL . '/js/front_main.min.js', array('advBootstrapJs'), false, true );
			wp_register_script( 'advReviewsFrontJs', IB_PLUGIN_URL . '/js/front_main.js', array('advBootstrapJs'), '1.3.3', true );
            wp_enqueue_script( 'advReviewsFrontJs' );
        }

        public function admin_load_scripts()
        {
            wp_register_script( 'jPriceFormat', IB_PLUGIN_URL . '/js/jquery.price_format.min.js', array('jquery'), false, true );
            wp_enqueue_script( 'jPriceFormat' );
        }

        /**
         * Создание базовых категорий
         */
        private function create_base_categories() {
            $cats = idealTools::return_base_cats_array();
            $catsForDeleting = array();

            foreach ($cats as $cat) {
                if (get_term_by('name', $cat, 'category')) {
                    continue;
                } else {
                    $id_cat = wp_create_category($cat);
                    if ($id_cat > 0) {
                        $catsForDeleting[] = $id_cat;
                    }
                }
            }

            if (get_term(1, 'category')) {
                wp_update_term(1, 'category', array(
                    'name' => 'Разное',
                ));
            }

            update_option('ib_cats', $catsForDeleting);
        }

        /**
         * Создание базовых регионов
         */
        private function create_base_regions() {
            $regionsArrayForDeleting = array();
            $regions = idealTools::return_base_regions_array();

            foreach ( $regions as $gregion => $regions) {
                $cat_defaults = array(
                    'cat_ID' => 0,
                    'taxonomy' => 'ib_regions',
                    'cat_name' => $gregion,
                );
                $gcat_id = wp_insert_category($cat_defaults);
                $regionsArrayForDeleting[] = $gcat_id;

                foreach ( $regions as $region) {
                    $cat_defaults = array(
                        'cat_ID' => 0,
                        'taxonomy' => 'ib_regions',
                        'cat_name' => $region,
                        'category_parent' => $gcat_id,
                    );
                    $cat_id = wp_insert_category($cat_defaults);
                    $regionsArrayForDeleting[] = $cat_id;
                }
            }

            update_option('ib_cats_regions', $regionsArrayForDeleting);

        }

        /**
         * Удаление данных из БД после деактивации плагина
         */
        private function cleanDbAfterDeactivatePlugin() {
            $pages = get_option('ib_pages');
            $cats = get_option('ib_cats');
            $ib_regions = get_option('ib_cats_regions');

            foreach ( $pages as $page ) {
                wp_delete_post($page, true);
            }

            foreach ( $cats as $cat ) {
                wp_delete_term( $cat, 'category', array('default'=>1, 'force_default'=>1) );
            }

            foreach ($ib_regions as $ib_region ) {
                wp_delete_term( $ib_region, 'ib_regions', array('default'=>1, 'force_default'=>1) );
            }
        }

        /**
         * @param $content
         *
         * @return string
         */
        public function custom_content_after_post($content){
            global $post;
            if (is_single()) {
                $content .= '<!--noindex--><!--googleoff: all--><div class="contact-after-post robots-nocontent">
                                <h4>Контакты:</h4>
                                <p><b>Имя:</b> '.get_post_meta($post->ID, 'user_name', true  ).'</p>
                                <p><b>Email:</b> '.get_post_meta($post->ID, 'user_email', true  ).'</p>
                                <p><b>Цена:</b> '.get_post_meta($post->ID, 'ib_price', true  ) . ' <b>' .
                                                idealTools::return_currency_array(get_post_meta($post->ID, 'currency_id', true  ))['curname'].'</b></p>
                                <p><b>Телефон:</b> '.get_post_meta($post->ID, 'user_phone', true  ).'</p>
                            </div><!--googleon: all--><!--/noindex-->';
							
				$array_images = array(
					'ib_more_img_2',
					'ib_more_img_3',
					'ib_more_img_4',
					'ib_more_img_5',
					'ib_more_img_6',
					'ib_more_img_7',
					'ib_more_img_8',
					'ib_more_img_9',
					'ib_more_img_10',
					'ib_more_img_11',
				);
				
				$content .= '<div style="text-align: center;">
				<script>
				jQuery( document ).ready( function( $ ){
					var image = document.getElementsByClassName("open_image");

					for(var i = 0; i < image.length; i++) {
						image[i].addEventListener("click", function(e) {
							 e.preventDefault();
							openpict(this.children[0].getAttribute("src"));
						});
					}

					function openpict(src) {
						var image = new Image();
						image.src = src;
						var width = image.width;
						var height = image.height;
						window.open(src,"Image","width=" + width + ",height=" + height);
					}
				});
				</script>';
				
				foreach ($array_images as $img) {
					$ib_more_img = get_post_meta($post->ID, $img, true);
					if ($ib_more_img !== '' && is_numeric($ib_more_img)) {
						$ib_more_img_url = wp_get_attachment_url(get_post_meta($post->ID, $img, true));
						$content .= '<figure class="entry-thumbnail" style="display: inline-block; padding: 0 3px;"><a href="#" class="open_image" title="Увеличить"><img src="'.$ib_more_img_url.'" alt="'.$post->post_title.'" style="height: 150px;" /></a></figure>';
					}
				}
				
				$content .= '</div>';
							
            }
            return $content;
        }

        public function my_extra_fields() {
            add_meta_box('extra_fields', 'Контакты', array(&$this, 'extra_fields_box_func'), 'post', 'normal', 'high');
        }


        public function extra_fields_box_func( $post ){
        ?>
        <div class="admin-data">
            <?php
                echo '<p>' . idealTools::inputTag('text', 'Имя', 'ib_user_name', 'Ваше имя', get_post_meta($post->ID, 'user_name', true), '') . '</p>';
                echo '<p>' . idealTools::inputTag('text', 'Номер телефона', 'ib_user_phone', 'Номер телефона', get_post_meta($post->ID, 'user_phone', true), '') . '</p>';
                ?>
                <p><div class="form-group">
                    <label for="ib_price">Цена</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="ib_price" name="ib_price" placeholder="Цена" value="<?php echo get_post_meta($post->ID, 'ib_price', true); ?>">
                        <?php $currs = idealTools::return_currency_array( 'full' ); ?>
                        <select name="ib_price_index" id="ib_price_index">
                            <?php foreach ( $currs as $key => $value ) {
                                $currId = get_post_meta($post->ID, 'currency_id', true);
                                $sel = ($key == $currId) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $value['curname']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div></p>
                <?php echo '<p>' . idealTools::inputTag( 'email', 'Email', 'ib_email', 'Email', get_post_meta($post->ID, 'user_email', true), '' ) . '</p>'; ?>
            </div>
            <style>
            /*    .admin-data label {
                    display: block;
                }
                .admin-data input, .admin-data select {
                    width: 100%;
                }*/
			</style>
            <?php
        }

        public function my_extra_fields_update( $post_id ){
            //if ( !wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__) ) return false; // проверка
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; // если это автосохранение
            if ( !current_user_can('edit_post', $post_id) ) return false; // если юзер не имеет право редактировать запись

            $price = (isset($_POST['ib_price'])) ? sanitize_text_field ($_POST['ib_price']) : '';
            $currency = (isset($_POST['ib_price_index'])) ? (int)$_POST['ib_price_index'] : '';
            $user_name = (isset($_POST['ib_user_name'])) ? sanitize_text_field ($_POST['ib_user_name']) : '';
            $user_phone = (isset($_POST['ib_user_phone'])) ? sanitize_text_field ($_POST['ib_user_phone']) : '';
            $user_email = (isset($_POST['ib_email'])) ? is_email($_POST['ib_email']) : '';
			$more_img_2 = (isset($_POST['ib_more_img_2'])) ? sanitize_text_field ($_POST['ib_more_img_2']) : '';
			$more_img_3 = (isset($_POST['ib_more_img_3'])) ? sanitize_text_field ($_POST['ib_more_img_3']) : '';
			$more_img_4 = (isset($_POST['ib_more_img_4'])) ? sanitize_text_field ($_POST['ib_more_img_4']) : '';
			$more_img_5 = (isset($_POST['ib_more_img_5'])) ? sanitize_text_field ($_POST['ib_more_img_5']) : '';
			$more_img_6 = (isset($_POST['ib_more_img_6'])) ? sanitize_text_field ($_POST['ib_more_img_6']) : '';
			$more_img_7 = (isset($_POST['ib_more_img_7'])) ? sanitize_text_field ($_POST['ib_more_img_7']) : '';
			$more_img_8 = (isset($_POST['ib_more_img_8'])) ? sanitize_text_field ($_POST['ib_more_img_8']) : '';
			$more_img_9 = (isset($_POST['ib_more_img_9'])) ? sanitize_text_field ($_POST['ib_more_img_9']) : '';
			$more_img_10 = (isset($_POST['ib_more_img_10'])) ? sanitize_text_field ($_POST['ib_more_img_10']) : '';
			$more_img_11 = (isset($_POST['ib_more_img_11'])) ? sanitize_text_field ($_POST['ib_more_img_11']) : '';

            update_post_meta($post_id, 'user_name', $user_name);
            update_post_meta($post_id, 'user_phone', $user_phone);
            update_post_meta($post_id, 'user_email', $user_email);
			update_post_meta($post_id, 'ib_more_img_2', $more_img_2);
			update_post_meta($post_id, 'ib_more_img_3', $more_img_3);
			update_post_meta($post_id, 'ib_more_img_4', $more_img_4);
			update_post_meta($post_id, 'ib_more_img_5', $more_img_5);
			update_post_meta($post_id, 'ib_more_img_6', $more_img_6);
			update_post_meta($post_id, 'ib_more_img_7', $more_img_7);
			update_post_meta($post_id, 'ib_more_img_8', $more_img_8);
			update_post_meta($post_id, 'ib_more_img_9', $more_img_9);
			update_post_meta($post_id, 'ib_more_img_10', $more_img_10);
			update_post_meta($post_id, 'ib_more_img_11', $more_img_11);
            update_post_meta($post_id, 'ib_price', $price);
            update_post_meta($post_id, 'currency_id', $currency);

            return $post_id;
        }


        public function my_plugin_action_links( $links ) {
           $plugin_data = get_plugin_data(__FILE__);
           $serv_ver = (float)$this->get_plugin_version();
           $curr_ver = (float)$plugin_data['Version'];

           if ($serv_ver > $curr_ver) {
                $links[] = "<a href='#' style='color: red;'>Обновите плагин ({$curr_ver} > {$serv_ver})</a>";
           }
           return $links;
        }

        /**
         * @return mixed
         */
        private function get_plugin_version() {

            $url = 'http://0st.ru/wp-content/plugins/idealboard/idealboard.php';

            if( $curl = curl_init() ) {
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                $out = curl_exec($curl);
                curl_close($curl);
                return (float)$out;
            }
        }


        public function category_custom_fields() {
            add_action('category_pre_add_form', array(&$this, 'category_custom_fields_form'));
            add_action('ib_regions_pre_add_form', array(&$this, 'category_custom_fields_form'));
        }

        public function category_custom_fields_form($tag) {
            ?>
            <div class="form-wrap">
                <div class="form-field term-parent-wrap">
                    <h2>Массовое добавление категорий</h2>
                    <input type="checkbox" id="onoff_masscats"> Вкл / Выкл
                    <p>Включите для массового добавления категорий</p>
                </div>

                <form id="addtag_mass" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display: none;">
                    <input type="hidden" name="action" value="save_cats_mass">
                    <input type="hidden" name="cat" value="<?php echo $tag; ?>">
                    <div class="form-field term-parent-wrap">
                        <label for="parent"><?php _ex( 'Parent', 'term parent' ); ?></label>
                        <?php
                        $dropdown_args = array(
                            'hide_empty'       => 0,
                            'hide_if_empty'    => false,
                            'taxonomy'         => $tag,
                            'name'             => 'parent',
                            'orderby'          => 'name',
                            'hierarchical'     => true,
                            'show_option_none' => __( 'None' ),
                        );

                        $dropdown_args = apply_filters( 'taxonomy_parent_dropdown_args', $dropdown_args, $tag, 'new' );

                        wp_dropdown_categories( $dropdown_args );
                        ?>
                    </div>

                    <div class="form-field term-description-wrap mass-block">
                        <label for="tag-description">Категории</label>
                        <textarea name="mass_cats" id="mass_cats" rows="10" cols="40" data-tag="<?php echo $tag; ?>"></textarea>
                        <p>Каждый термин должен начинаться с новой строки.</p>
                    </div>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="Добавить рубрики">
                    </p>
                </form>


                <script>
                    jQuery(document).ready(function($) {

                        var btnText = $('#submit').val();

                        $('#onoff_masscats').on('change', function(e) {
                            var p = $(this),
                                b = $('#addtag_mass'),
                                c = $('#addtag').parent();

                            if(p.attr('checked')) {
                                b.show();
                                c.hide();
                            } else {
                                b.hide();
                                c.show();
                            }
                        });
                    });
                </script>
            </div>
            <?php
        }


    } //END CLASS
}

register_uninstall_hook( __FILE__, array( IB_PLUGIN_NAME, 'uninstall' ) );

$iboard = new IdealBoard();

// Скрываем верхнюю панель

add_action('init', 'remove_admin_bar');

function remove_admin_bar() {
	if (current_user_can('administrator') == false) {
		show_admin_bar(false);
	}
}
