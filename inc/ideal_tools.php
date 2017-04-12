<?php

class idealTools
{

    const BLR = 1;
    const RUR = 2;
    const EUR = 3;
    const USD = 4;


    /**
     * @param $num
     * @return array|mixed
     */
    public static function return_currency_array($num = 'full')
    {
        $currArray = array(
                        self::BLR => array('curname' => 'BLR', 'cursign' => 'Br',),
                        self::RUR => array('curname' => 'RUR', 'cursign' => 'P',),
                        self::EUR => array('curname' => 'EUR', 'cursign' => '€',),
                        self::USD => array('curname' => 'USD', 'cursign' => '$',),
        );

        if ($num > 0) {
            $r = $currArray[$num];
        } else if ($num === 'full') {
            $r = $currArray;
        } else {
            $r = array(
                  'curname' => '-',
                  'cursign' => '-',
                 );
        }

        return $r;
        
    }

    /**
     * @return array
     */
    public static function return_base_cats_array() {
        $catsArray = array(
            'Все для дома и быта',
            'Дача, сад, огород',
            'Для детей',
            'Животные',
            'Красота, здоровье',
            'Недвижимость',
            'Одежда, обувь',
            'Производство, техника',
            'Работа, образование',
            'Развлечение, отдых',
            'Строительство, ремонт',
            'Транспорт',
            'Услуги, бизнес, партнерство',
        );

        return $catsArray;
    }

    /**
     * @return array
     */
    public static function return_base_regions_array() {
        $regionsArray = array(
            'Могилевская область' => array('Славгород', 'Хотимск', 'Чаусы', 'Чериков', 'Шклов', 'Осиповичи', 'Мстиславль',
                                        'Могилев', 'Круглое', 'Кричев', 'Краснополье', 'Костюковичи', 'Кличев', 'Климовичи',
                                        'Кировск', 'Дрибин', 'Горки', 'Глуск', 'Быхов', 'Бобруйск', 'Белыничи',
            ),
            'Минская область' => array('Молодечно', 'Мядель', 'Несвиж', 'Слуцк', 'Смолевичи', 'Солигорск', 'Старые Дороги',
                                        'Столбцы', 'Узда', 'Червень', 'Минск', 'Марьина Горка', 'Любань', 'Логойск', 'Крупки',
                                        'Копыль', 'Клецк', 'Жодино', 'Дзержинск', 'Воложин', 'Вилейка', 'Борисов', 'Березино',
            ),
            'Гродненская область' => array('Щучин', 'Сморгонь', 'Слоним', 'Свислочь', 'Ошмяны', 'Островец', 'Новогрудок',
                                        'Мосты', 'Лида', 'Кореличи', 'Ивье', 'Зельва', 'Дятлово', 'Гродно', 'Вороново', 
                                        'Волковыск', 'Берестовица',
            ),
            'Гомельская область' => array('Чечерск', 'Хойники', 'Светлогорск', 'Рогачев', 'Речица', 'Петриков', 'Октябрьский',
                                        'Наровля', 'Мозырь', 'Лоев', 'Лельчицы', 'Корма', 'Калинковичи', 'Жлобин', 'Житковичи',
                                        'Ельск', 'Добруш', 'Гомель', 'Ветка', 'Буда-Кошелево', 'Брагин',
            ),
            'Витебская область' => array('Шумилино', 'Шарковщина', 'Чашники', 'Ушачи', 'Толочин', 'Сенно', 'Россоны',
                                        'Поставы', 'Полоцк', 'Орша', 'Миоры', 'Лиозно', 'Лепель', 'Дубровно', 'Докшицы',
                                        'Городок', 'Глубокое', 'Витебск', 'Верхнедвинск', 'Браслав', 'Бешенковичи',
            ),
            'Брестская область' => array('Столин', 'Пружаны', 'Пинск', 'Малорита', 'Ляховичи', 'Лунинец', 'Кобрин',
                                        'Каменец', 'Ивацевичи', 'Иваново', 'Жабинка', 'Дрогичин', 'Ганцевичи', 'Брест',
                                        'Береза', 'Барановичи',
            ),
        );

        return $regionsArray;
    }

    /**
     * RETURNS HTML SELECT-TAG
     *
     * @param $flag - [classic, wp]
     * @param $options - select options or others parameters
     * @param bool $is_multiple - multiple or no
     * @return string - html-tag
     */
    public static function selectOptionsTag($flag, $options, $is_multiple = false, $label = '', $name = '')
    {
        $html = '';

        if ($flag == 'classic' && $options && is_array($options)) {

            $multiple = ($is_multiple) ? 'multiple' : '';
            
            $html = '<div class="form-group">';
            $html .= '<label for="' . $name . '">' . $label . '</label>';
            $html .= '<select ' . $multiple . ' class="form-control">';

            foreach ($options as $index => $value) {
                $html .= '<option value="' . $index . '">' . $value . '</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
            
        } else if ($flag == 'wp' && $options) {
			
			// Определение ID категории последнего объявления пользователя
			
			$currentUser = get_current_user_id();
			
			$latest_post = get_posts(array(
				'author'      => $currentUser,
				'orderby'     => 'date',
				'numberposts' => 1,
			));
			
			$latest_post = $latest_post[0];

			$cat_poisk = get_the_category($latest_post->ID);
			$cat_id = $cat_poisk[0]->term_id;
			
			// END

            $args = array(
                'show_option_all'    => '',
                'show_option_none'   => '',
                'orderby'            => 'ID',
                'order'              => 'ASC',
                'show_last_update'   => 0,
                'show_count'         => 0,
                'hide_empty'         => 0,
                'child_of'           => 0,
                'exclude'            => '',
                'echo'               => 0,
                'selected'           => $cat_id,
                'hierarchical'       => 1,
                'name'               => $options,
                'id'                 => $options,
                'class'              => 'form-control',
                'depth'              => 0,
                'tab_index'          => 0,
                'taxonomy'           => $options,
                'hide_if_empty'      => false,
                'value_field'        => 'term_id', // значение value e option
            );

            $html = '<div class="form-group">';
            $html .= '<label for="' . $options . '">' . $label . '</label>';
            $html .= wp_dropdown_categories( $args );
            $html .= '</div>';

        }
		
		else if ($flag == 'region' && $options) {
			
			// Определение ID региона последнего объявления пользователя
			
			$currentUser = get_current_user_id();
			
			$latest_post = get_posts(array(
				'author'      => $currentUser,
				'orderby'     => 'date',
				'numberposts' => 1,
			));
			
			$latest_post = $latest_post[0];
			
			$region_poisk = get_the_terms( $latest_post->ID, 'ib_regions' );
			$region_poisk = $region_poisk[0];
			$region_id = $region_poisk->term_id;
			
			// END

            $args = array(
                'show_option_all'    => '',
                'show_option_none'   => '',
                'orderby'            => 'ID',
                'order'              => 'ASC',
                'show_last_update'   => 0,
                'show_count'         => 0,
                'hide_empty'         => 0,
                'child_of'           => 0,
                'exclude'            => '',
                'echo'               => 0,
                'selected'           => $region_id,
                'hierarchical'       => 1,
                'name'               => $options,
                'id'                 => $options,
                'class'              => 'form-control',
                'depth'              => 0,
                'tab_index'          => 0,
                'taxonomy'           => $options,
                'hide_if_empty'      => false,
                'value_field'        => 'term_id',
            );

            $html = '<div class="form-group">';
            $html .= '<label for="' . $options . '">' . $label . '</label>';
            $html .= wp_dropdown_categories( $args );
            $html .= '</div>';

        }

        return $html;
    }


    /**
     * RETURNS HTML INPUT-TAG
     * 
     * @param string $type
     * @param string $label
     * @param string $name
     * @param string $placeholder
     * @return string
     */
    public static function inputTag($type = 'text', $label = '', $name = '', $placeholder = '', $value = '', $class = '')
    {
        $html = '<div class="form-group">';
        $html .= '<label for="' . $name . '">' . $label . '</label>';
        $html .= '<input type="' . $type . '" class="form-control ' . $class . '" id="' . $name . '" 
                                              value="' . $value . '" name="' . $name . '" placeholder="' . $placeholder . '">';
        $html .= '</div>';

            
        return $html;
    }
    
    public static function editorBlock($ta_name)
    {
        $settings = array(
            'wpautop' => 1,
            'media_buttons' => 0,
            'textarea_name' => $ta_name, //нужно указывать!
            'textarea_rows' => 20,
            'tabindex'      => null,
            'editor_css'    => '',
            'editor_class'  => '',
            'teeny'         => 0,
            'dfw'           => 0,
            'tinymce'       => 1,
            'quicktags'     => 0,
            'drag_drop_upload' => false
        );

        wp_editor('', 'main_adv', $settings);
    }


    /**
     * @param $length - Максимальная длина цитаты. Длина задается в символах
     * <?php print_excerpt(50); ?>
     */
    public static function print_excerpt($length)
    {
        global $post;
        $text = $post->post_excerpt;
        if ( '' == $text ) {
            $text = get_the_content('');
            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]>', $text);
        }
        $text = strip_shortcodes($text);
        $text = strip_tags($text); // используйте' $text = strip_tags($text,'<p><a>'); ' если хотите оставить некоторые теги

        $text = substr($text,0,$length);
        $excerpt = strrpos($text, '.') ? substr($text, 0, strrpos($text, '.') + 1) : false;
        if( $excerpt ) {
            echo apply_filters('the_excerpt',$excerpt);
        } else {
            echo apply_filters('the_excerpt',$text);
        }
    }


}