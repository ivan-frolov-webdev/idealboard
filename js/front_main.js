jQuery( document ).ready( function( $ ){

	/* GLOBALS */
	var admin_url = '../wp-admin/admin-ajax.php';
	// $('#ib_price').priceFormat({
	//     prefix: '',
	//     centsSeparator: ',',
	//     thousandsSeparator: ' '
	// });
	/**
	 *
	 */
	// $('.ib-price-dd li').on('click', function (e) {
	//     e.stopPropagation();
	//     e.preventDefault();
	//
	//     var p = $(this),
	//         text = p.find('a').text(),
	//         btn = $('.ib-price-btn'),
	//         currency = p.find('a').attr('data-val');
	//
	//     btn.html(text + ' <span class="caret"></span>');
	//     $('#currency_id').val(currency);
	//
	// });

	$('#ib_price_index').on('change', function (e) {
		e.preventDefault();
		e.stopPropagation;

		var p = $(this),
			currency = p.val();

		$('#currency_id').val(currency);
	});

	/* Валидация формы профиля */
    $('#profile_form').validate({
        debug: false,
        rules: {
            ib_user_name: {
                required: true,
                minlength: 3,
                maxlength: 30
            },
            ib_user_phone: {
                required: true,
                minlength: 5,
                maxlength: 20
            }
        },
        messages: {
            ib_user_name: {
                required: 'Обязательное поле, заполните пожалуйста!',
                minlength: 'Минимальная длинна текста - 3 символов',
                maxlength: 'Максимальная длинна текста - 30 символов'
            },
            ib_user_phone: {
                required: 'Обязательное поле, заполните пожалуйста!',
                minlength: 'Минимальная длинна текста - 5 символов',
                maxlength: 'Максимальная длинна текста - 20 символов'
            }
        }
    });

    /* Валидация формы подачи объявления */
    $("#adv_form").validate({
        debug: false,
        rules: {
            ib_title: {
                required: true,
                minlength: 5,
                maxlength: 75
            },
            adv_text: {
                required: true,
                minlength: 50,
                maxlength: 5000
            },
            ib_name: {
                required: true,
                minlength: 3,
                maxlength: 30
            },
            ib_phone: {
                required: true,
                minlength: 5,
                maxlength: 20
            },
            ib_email: {
                required: true,
                email: true
            }
        },

        messages: {
            ib_title: {
                required: 'Обязательное поле, заполните пожалуйста!',
                minlength: 'Минимальная длинна текста - 5 символов',
                maxlength: 'Максимальная длинна текста - 75 символов'
            },
            adv_text: {
                required: 'Обязательное поле, заполните пожалуйста!',
                minlength: 'Минимальная длинна текста - 50 символов',
                maxlength: 'Максимальная длинна текста - 5000 символов'
            },
            ib_name: {
                required: 'Обязательное поле, заполните пожалуйста!',
                minlength: 'Минимальная длинна текста - 3 символов',
                maxlength: 'Максимальная длинна текста - 30 символов'
            },
            ib_phone: {
                required: 'Обязательное поле, заполните пожалуйста!',
                minlength: 'Минимальная длинна текста - 5 символов',
                maxlength: 'Максимальная длинна текста - 20 символов'
            },
            ib_email: {
                required: 'Обязательное поле, заполните пожалуйста!',
                email: 'Введите правильный e-mail'
            }
        },
        invalidHandler: function(event, validator) {
            var errors = validator.numberOfInvalids();
            //console.log(errors);
        },

        submitHandler: function(form) {
            //form.submit();

        }
    });

    /**
     * Форма подачи объявления
     */
    $('#submit_advert_front').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        $('.alert').removeClass('alert-success').removeClass('alert-danger');

        if ($("#adv_form").valid()) {
            var formData = new FormData();

            formData.append('action', 'save_adverts');
            formData.append('user_id', $('#user_id').val());
            formData.append('category', $('#category').val());
            formData.append('ib_regions', $('#ib_regions').val());
            formData.append('ib_title', $('#ib_title').val());
            formData.append('adv_text', $('#adv_text').val());
            formData.append('ib_price', $('#ib_price').val());
            formData.append('currency_id', $('#currency_id').val());
            formData.append('photo', $('#ib_adv_photo')[0].files[0]);
			formData.append('ib_more_img_2', $('#ib_more_img_2')[0].files[0]);
			formData.append('ib_more_img_3', $('#ib_more_img_3')[0].files[0]);
			formData.append('ib_more_img_4', $('#ib_more_img_4')[0].files[0]);
			formData.append('ib_more_img_5', $('#ib_more_img_5')[0].files[0]);
			formData.append('ib_more_img_6', $('#ib_more_img_6')[0].files[0]);
			formData.append('ib_more_img_7', $('#ib_more_img_7')[0].files[0]);
			formData.append('ib_more_img_8', $('#ib_more_img_8')[0].files[0]);
			formData.append('ib_more_img_9', $('#ib_more_img_9')[0].files[0]);
			formData.append('ib_more_img_10', $('#ib_more_img_10')[0].files[0]);
			formData.append('ib_more_img_11', $('#ib_more_img_11')[0].files[0]);
            formData.append('ib_name', $('#ib_name').val());
            formData.append('ib_phone', $('#ib_phone').val());
            formData.append('ib_email', $('#ib_email').val());

            $.ajax({
                type: 'POST',
                url: admin_url,
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,

                success: function (result) {
                    //console.log(result);
                    $('#submit_profile_front').prop('disabled', true);
                    // var target = $("#adv_form");
                    // if (target.length) {
                    //     $('html,body').animate({
                    //         scrollTop: target.offset().top
                    //     }, 'slow');
                    // }

                    if (result == 'success') {
                        $('.alert').addClass('alert-success').text('Сохранено!').fadeIn();

                        setTimeout(function(){
							window.location.replace("/thanks/")
                            //window.location.reload()
                        }, 1000);

                    } else if (result == 'error' || !result) {
                        $('.alert').addClass('alert-danger').text('Ошибка! Объявление не сохранено...').fadeIn();
                    }

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //console.log(arguments);
                }
            });
        }
    });
	
    /**
     *  Форма профиля
     */
     $('#submit_profile_front').prop('disabled', true);

     $('#ib_user_name, #ib_user_phone').on('keyup', function (e) {
         if($(this).val() != '') {
             $('#submit_profile_front').prop('disabled', false);
         }
     });

    $('#submit_profile_front').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        if ($("#profile_form").valid()) {
            var ib_name = $('#ib_user_name').val(),
                ib_phone = $('#ib_user_phone').val(),
                user_id = $('#user_id').val(),
                postData = {
                    'action': 'save_profile',
                    'ib_name': ib_name,
                    'ib_phone': ib_phone,
                    'user_id': user_id
                };

            $.ajax({
                type: 'POST',
                url: admin_url,
                dataType: 'json' ,
                data: postData,

                beforeSend: function (x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType('application/json;charset=UTF-8' );
                    }
                },
                success: function (result) {
                    //console.log(result);
                    $('#submit_profile_front').prop('disabled', true);
                    // var target = $("#profile_form");
                    // if (target.length) {
                    //     $('html,body').animate({
                    //         scrollTop: target.offset().top
                    //     }, 'slow');
                    // }

                    if (result == 'success') {
                        $('.alert').addClass('alert-success').text('Сохранено!').fadeIn();
                    } else if (result == 'error') {
                        $('.alert').addClass('alert-danger').text('Ошибка! Объявление не сохранено...').fadeIn();
                    }

                    setTimeout(function(){
                        window.location.reload()
                    }, 100);

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //console.log(arguments);
                }
            });
        }
    });

    /* CSS Fix For Select */
    if($('#ib_title').length || $('#ib_user_name').length) {
        var inputHeight = $('#ib_title, #ib_user_name').outerHeight();
        $('select').css('height', inputHeight);
    }

    /* Preview Image before uploading */
	
	var images_arr = [
		"ib_adv_photo",
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
	];
	
	jQuery.each( images_arr, function( i, val ) {

		function next(number) {
			var index = images_arr.indexOf(number);
			index++;
			if(index >= images_arr.length)
				index = 0;
			return images_arr[index];
		}

		$("." + val + " button").on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("." + next(val)).css('display', 'block');
			$("." + val + " button").css('display', 'none');
		});

		$("#" + val).on('change', function(){
			var tcl = $("." + val);
			var p = $(this),
				fileUpload = p[0],
				regex = new RegExp("([a-zа-яA-ZА-Я0-9\s_\\.\-:])+(.jpg|.png|.gif)$"),
				photo = p,
				preview = tcl.find('.image_preview_img'),
				imgBlock = tcl.find('.image_preview');

			if (regex.test(fileUpload.value.toLowerCase())) {
				if (typeof (fileUpload.files) != 'undefined') {
					var reader = new FileReader();
					reader.readAsDataURL(fileUpload.files[0]);
					reader.onload = function (e) {
						var image = new Image();
						image.src = e.target.result;
						image.onload = function () {
							var height = this.height;
							var width = this.width;
							if (width < 100) {
								alert('Минимальная ширина изображения 100px');
								hideDelImgPreview (photo, preview, imgBlock);
								return false;
							} else if (width > 1200) {
								alert('Максимальная ширина изображения 1200px');
								hideDelImgPreview (photo, preview, imgBlock);
								return false;
							}
							preview.attr('src', image.src);
							imgBlock.slideDown();
							tcl.find('.image_preview_img').css('display', 'block');
							tcl.find('.image_current_preview').css('display', 'none');
							return true;
						};

					}
				} else {
					alert('Упс, какая-то ошибка - перезагрузите страницу и попробуйте еще раз...');
					hideDelImgPreview (photo, preview, imgBlock);
					return false;
				}
			} else {
				alert('Допустимы изображения в формате: jpg, png, gif');
				hideDelImgPreview (photo, preview, imgBlock);
				return false;
			}
		});
		
	});

    $('#remove_from_preview').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var photo = $('#ib_adv_photo'),
            preview = $('.image_preview_img'),
            imgBlock = $('.image_preview');

        hideDelImgPreview (photo, preview, imgBlock);
    });

});

function hideDelImgPreview (photo, preview, imgBlock) {
	photo.replaceWith(photo.clone(true));
	preview.attr('src', '');
	imgBlock.slideUp();
}