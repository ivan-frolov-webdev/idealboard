<?php

load_textdomain("invisible-captcha", ABSPATH."wp-content/plugins/idealboard2/inc/invisible-captcha/lang/invisible-captcha-ru_RU.mo");

define('inv_capt_spam_action',	'spam', false);
define('inv_capt_spam',			__('No spam, please. This web-site is protected.<br/>Please, enable JavaScript in your browser settings and try again...','invisible-captcha'),  false);
define('inv_capt_en_js',		'<strong><u><font color="red" size="5">' . __('To leave a comment please enable JavaScript in your browser settings!','invisible-captcha') . '</font></u></strong>',  false);

add_action('activate_invisible-captcha/invisible_captcha.php', 'invisible_captcha_activate');
add_action('deactivate_invisible-captcha/invisible_captcha.php', 'invisible_captcha_deactivate');
add_action('admin_menu', 'invisible_captcha_config_page');

add_action('comment_post',	'invisible_captcha_comment_post');
add_action('wp_head',		'invisible_captcha_head');
add_action('get_footer',	'invisible_captcha_draw');
//add_action('wp_footer', 	'invisible_captcha_draw');
add_action('comment_form', 	'invisible_captcha_comment_form');

//=======================================================
//=======================================================
 
function invisible_captcha_comment_form ($id) {
	global $user_ID;
	
	if (!($user_ID)){	
		$options = get_option('invisible_captcha');
		echo '<noscript>' . $options['invisible_captcha_enable_js_message'] . '</noscript>';
	}
}

//=======================================================
//=======================================================

function invisible_captcha_comment_post ($id){
	global $user_ID;
	
	if ($user_ID){
		return $id;
	}
	$cmt = get_comment($id);
	$pip = $cmt -> comment_author_IP;
	
	$options = get_option('invisible_captcha');
	$invisible_captcha_id = $options['invisible_captcha_id'];
	$invisible_captcha_value = $options['invisible_captcha_value'];
	$invisible_captcha_spam_action = $options['invisible_captcha_spam_action'];
	$invisible_captcha_spam_message = $options['invisible_captcha_spam_message'];
	$invisible_captcha_enable_js_message = $options['invisible_captcha_enable_js_message'];
	
	$is_spam_cmt = false;
	
	if (isset($_POST[$invisible_captcha_id])) {
		if($_POST[$invisible_captcha_id] != $invisible_captcha_value){
			$is_spam_cmt = true;
		} 		
	} else {
		$is_spam_cmt = true;
	}
	
	if ($is_spam_cmt) {	
		if ($invisible_captcha_spam_action=='spam') {
			wp_set_comment_status($id, 'spam');
			wp_die($invisible_captcha_spam_message, '', array('response' => 403));
		} else {
			if ($invisible_captcha_spam_action=='delete') {
				wp_set_comment_status($id, 'trash');
				wp_die($invisible_captcha_spam_message, '', array('response' => 403));
			} else {
				wp_set_comment_status($id, 'hold');
			}
		}		
	}
}

//=======================================================
//=======================================================

function invisible_captcha_head(){
?>

<script type="text/javascript">
	function addHandler(object, event, handler) {
		if (typeof object.addEventListener != 'undefined') 
			object.addEventListener(event, handler, false);
		else
			if (typeof object.attachEvent != 'undefined')
				object.attachEvent('on' + event, handler);
			else 
				throw 'Incompatible browser';
	}
<?php
/*
	function removeHandler(object, event, handler) {
		if (typeof object.removeEventListener != 'undefined')
			object.removeEventListener(event, handler, false);
		else 
			if (typeof object.detachEvent != 'undefined')
				object.detachEvent('on' + event, handler);
			else
				throw 'Incompatible browser';
	}
*/
?>
</script>
<?php 
}

//=======================================================
//=======================================================

function invisible_captcha_draw (){

    global $user_ID;
	if ($user_ID){
		return $user_ID;
	}
	
	$options = get_option('invisible_captcha');
	$invisible_captcha_id = $options['invisible_captcha_id'];
	$invisible_captcha_value = $options['invisible_captcha_value'];

$rand_cap = 'c' . generate_random_value();
$rand_but = 'b' . generate_random_value();
$rand_par = 'p' . generate_random_value();
$rand_fun = 'f' . generate_random_value();

echo '<input type="hidden" name="' . $invisible_captcha_id . '" id="' . $invisible_captcha_id . '" />' . "\n";
echo '<script type="text/javascript">' . "\n";
echo 'function ' . $rand_fun . '() {' . "\n";
echo '	var o=document.getElementById("' . $invisible_captcha_id . '");' . "\n";
echo '	o.value="' . $invisible_captcha_value . '";' . "\n";
echo '}' . "\n";
echo 'var ' . $rand_but . ' = document.getElementById("submit");' . "\n";
echo 'if (' . $rand_but . ') {' . "\n";
echo '	var ' . $rand_cap . ' = document.getElementById("' . $invisible_captcha_id . '");' . "\n";
echo '	var ' . $rand_par . ' = ' . $rand_but . '.parentNode;' . "\n";
echo '	' . $rand_par . '.appendChild(' . $rand_cap . ', ' . $rand_but . ');' . "\n";
echo '	addHandler(' . $rand_but . ', "mousedown", ' . $rand_fun . ');' . "\n";
echo '	addHandler(' . $rand_but . ', "keypress", ' . $rand_fun . ');' . "\n";
echo '}' . "\n";
echo '</script>' . "\n";

}

//=======================================================
//=======================================================

function invisible_captcha_activate() {
	$options = get_option('invisible_captcha');
	$options['invisible_captcha_id'] = generate_random_value();
	$options['invisible_captcha_value'] = generate_random_value();
	if (!isset($options['invisible_captcha_spam_action'])) $options['invisible_captcha_spam_action'] = inv_capt_spam_action;
	if (!isset($options['invisible_captcha_spam_message'])) $options['invisible_captcha_spam_message'] = inv_capt_spam;
	if (!isset($options['invisible_captcha_enable_js_message'])) $options['invisible_captcha_enable_js_message'] = inv_capt_en_js;
	update_option('invisible_captcha', $options);
}

//=======================================================
//=======================================================

function invisible_captcha_deactivate() {
	//delete_option('invisible_captcha');
}


//=======================================================
//=======================================================

function invisible_captcha_config_page() {
	global $wpdb;
	if ( function_exists('add_submenu_page') )
		add_submenu_page('options-general.php', __('Invisible Captcha','invisible-captcha'), __('Invisible Captcha','invisible-captcha'), 'edit_pages', __FILE__, 'invisible_captcha_conf');
}

//=======================================================
//=======================================================

function invisible_captcha_conf() {
	$options = get_option('invisible_captcha');
	if (!isset($options['invisible_captcha_id'])) $options['invisible_captcha_id'] = generate_random_value();
	if (!isset($options['invisible_captcha_value'])) $options['invisible_captcha_value'] = generate_random_value();
	if (!isset($options['invisible_captcha_spam_action'])) $options['invisible_captcha_spam_action'] = inv_capt_spam_action;
	if (!isset($options['invisible_captcha_spam_message'])) $options['invisible_captcha_spam_message'] = inv_capt_spam;
	if (!isset($options['invisible_captcha_enable_js_message'])) $options['invisible_captcha_enable_js_message'] = inv_capt_en_js;
	
	$updated = false;
	$restored = false;
	if ( isset($_POST['submit']) ) {
		//check_admin_referer();
		
		if (isset($_POST['restore_defaults'])&&($_POST['restore_defaults']='1')) {
			$invisible_captcha_id = generate_random_value();
			$invisible_captcha_value = generate_random_value();
			$invisible_captcha_spam_action = inv_capt_spam_action;
			$invisible_captcha_spam_message = inv_capt_spam;
			$invisible_captcha_enable_js_message = inv_capt_en_js;
			$restored = true;
		} else {

			if (isset($_POST['invisible_captcha_id'])) {
				$invisible_captcha_id = $_POST['invisible_captcha_id'];
			} else {
				$invisible_captcha_id = generate_random_value();
			}

			if (isset($_POST['invisible_captcha_value'])) {
				$invisible_captcha_value = $_POST['invisible_captcha_value'];
			} else {
				$invisible_captcha_value = generate_random_value();
			}


			if (isset($_POST['invisible_captcha_spam_action'])) {
				$invisible_captcha_spam_action = $_POST['invisible_captcha_spam_action'];
			} else {
				$invisible_captcha_spam_action = inv_capt_spam_action;
			}

			if (isset($_POST['invisible_captcha_spam_message'])) {
				$invisible_captcha_spam_message = stripslashes($_POST['invisible_captcha_spam_message']);
			} else {
				$invisible_captcha_spam_message = inv_capt_spam;
			}
			
			
			
			if (isset($_POST['invisible_captcha_enable_js_message'])) {
				$invisible_captcha_enable_js_message = stripslashes($_POST['invisible_captcha_enable_js_message']);
			} else {
				$invisible_captcha_enable_js_message = inv_capt_en_js;
			}
			
			//echo $_POST['invisible_captcha_enable_js_message'] . '<br/>'. $invisible_captcha_enable_js_message . '<br/>';


			
		}
		$options['invisible_captcha_id'] = $invisible_captcha_id;
		$options['invisible_captcha_value'] = $invisible_captcha_value;
		$options['invisible_captcha_spam_action'] = $invisible_captcha_spam_action;
		$options['invisible_captcha_spam_message'] = $invisible_captcha_spam_message;
		$options['invisible_captcha_enable_js_message'] = $invisible_captcha_enable_js_message;
				

		
		
		update_option('invisible_captcha', $options);
		$updated = true;
	}	
	
?>

<div class="wrap">
<?php
if ($updated) {
	echo "<div id='message' class='updated fade'><p>";
	_e('Configuration updated.','invisible-captcha');
	if ($restored) {
		_e(' Restored default settings.','invisible-captcha');
	}
	echo "</p></div>";
}
?>
<h3><?php _e('Invisible Captcha Configuration','invisible-captcha'); ?></h3>
<div style="float: right; width: 300px">
	<p><?php _e('Smart invisible captcha for wordpress comments. Visitors of your website don\'t need to enter letters and numbers. This small plugin automatically protects your website from spam in comments..','invisible-captcha')?></p>
</div>
<form action="" method="post" id="invisible-captcha-conf">
<p><label for="invisible_captcha_id"><strong><?php _e('ID of the hidden field','invisible-captcha'); ?>:</strong></label><br/>
<input id="invisible_captcha_id" name="invisible_captcha_id" type="text" size="30" maxlength="100" value="<?php echo $options['invisible_captcha_id']; ?>" />
<input type="button" value="<?php _e('Generate random value','invisible-captcha')?>" onclick="RandomValue('invisible_captcha_id')"/>
</p>

<p><label for="invisible_captcha_value"><strong><?php _e('Value of the hidden field','invisible-captcha'); ?>:</strong></label><br/>
<input id="invisible_captcha_value" name="invisible_captcha_value" type="text" size="30" maxlength="100" value="<?php echo $options['invisible_captcha_value']; ?>" />
<input type="button" value="<?php _e('Generate random value','invisible-captcha')?>" onclick="RandomValue('invisible_captcha_value')"/>
</p>

<p><label for="invisible_captcha_spam_action"><strong><?php _e('Action to perform if comment is identified as spam','invisible-captcha'); ?>:</strong></label><br/>
	<input type="radio" name="invisible_captcha_spam_action" value="spam"<?php if ($options['invisible_captcha_spam_action']=='spam') echo ' checked';?>><?php _e('Mark as Spam','invisible-captcha'); ?><Br>
	<input type="radio" name="invisible_captcha_spam_action" value="hold"<?php if ($options['invisible_captcha_spam_action']=='hold') echo ' checked';?>><?php _e('Comment Moderation','invisible-captcha'); ?><Br>
	<input type="radio" name="invisible_captcha_spam_action" value="delete"<?php if ($options['invisible_captcha_spam_action']=='delete') echo ' checked';?>><?php _e('Delete Comment','invisible-captcha'); ?><Br>
</p>
	

<h3><?php _e('Displayed messages','invisible-captcha')?></h3>

<?php $invisible_captcha_spam_message = htmlspecialchars($options['invisible_captcha_spam_message']); ?>
<p><label for="invisible_captcha_spam_message"><strong><?php _e('When spam comment has been blocked','invisible-captcha'); ?>:</strong></label><br/>
<input id="invisible_captcha_spam_message" name="invisible_captcha_spam_message" type="text" size="65" maxlength="200" value="<?php echo $invisible_captcha_spam_message; ?>" /></p>

<?php $invisible_captcha_enable_js_message = htmlspecialchars($options['invisible_captcha_enable_js_message']); ?>
<p><label for="invisible_captcha_enable_js_message"><strong><?php _e('If JavaScript is disabled in commentator\'s browser','invisible-captcha'); ?>:</strong></label><br/>
<input id="invisible_captcha_enable_js_message" name="invisible_captcha_enable_js_message" type="text" size="65" maxlength="200" value="<?php echo $invisible_captcha_enable_js_message; ?>" /></p>




<p class="submit" style="text-align: left"><input type="submit" name="submit" value="<?php _e('Save','invisible-captcha'); ?>" /></p>
</form>
<form action="" method="post" id="invisible-captcha-default">
<input id="restore_defaults" name="restore_defaults" type="hidden" value="1" />
<p class="submit" style="text-align: left"><input type="submit" name="submit" value="<?php _e('Restore Defaults','invisible-captcha'); ?>" /></p>
</form>
</div>

<script type="text/javascript">
	Chars = new Array ()   
	for (j = 65; j <=122; j++){
		if ((j < 91) || (j > 96)) {
			Chars[Chars.length] = String.fromCharCode(j);
		}
	}
function GenerateRandomPassword(){   
	PassLength = 10 + Math.round(5 * Math.random());
	Pass = ''
	for (i = 0; i < PassLength; i++){
		ChIndex = Math.round(Chars.length * Math.random()) - 1;
		if ((Chars[ChIndex]) && (Pass.length < 15)) {
			Pass += Chars[ChIndex];   
		}
	}
	return Pass;   
}
function RandomValue(id){
	document.getElementById(id).value=GenerateRandomPassword();
	}
</script>
<?php
}

//=======================================================
//=======================================================

function generate_random_value(){
	return wp_generate_password( 12, false);
	}


?>