<?php defined( 'ABSPATH' ) || exit;

define('KALAMATN_SHKRN_PATH' , SHKRN_LIB.'/kalamatino');
define('KALAMATN_SHKRN_URL' , SHKRN_DIR_URL.'/inc/lib/kalamatino');
define('KALAMATN_SHKRN_INC' , KALAMATN_SHKRN_PATH.'/inc');
define('KALAMATN_SHKRN_CORE' , KALAMATN_SHKRN_PATH.'/inc/core');
define('KALAMATN_SHKRN_TPL' , KALAMATN_SHKRN_PATH.'/inc/templates');
define('KALAMATN_SHKRN_ASSETS' , SHKRN_DIR_URL.'/inc/lib/kalamatino/assets');

///////////////////////////////////////////////////////////////////////////////////

function kalamatino_shkrn_admin_notices() {
    if (isset($_POST['savewords']) && empty($_POST['from-text']) && empty($_POST['to-text'])) {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e( 'Both fields must be filled! if you want to hide a text use SPACE in "to" field', 'shokrino' ); ?></p>
    </div>
    <?php
    }
}
add_action( 'admin_notices', 'kalamatino_shkrn_admin_notices' );

///////////////////////////////////////////////////////////////////////////////////

function Word_replace_text_kalamatino_shkrn($translated_text)
{
    $this_string = get_option('klmtn_from_text','');
    $to_string = get_option('klmtn_to_text','');
    if (!empty($this_string) && !empty($to_string)) {
        $translated_text = str_replace($this_string, $to_string, $translated_text);
    }
    return $translated_text;
}

add_filter('gettext', 'Word_replace_text_kalamatino_shkrn', 9999999);

function Word_replace_content_kalamatino_shkrn($translated_text)
{
    $this_string = get_option('klmtn_from_text','');
    $to_string = get_option('klmtn_to_text','');
    if (!empty($this_string) && !empty($to_string)) {
        $translated_text = str_replace($this_string, $to_string, $translated_text);
    }
    return $translated_text;
}

add_filter('the_content', 'Word_replace_content_kalamatino_shkrn', 9999999);

function Word_replace_widget_kalamatino_shkrn($translated_text)
{
    $this_string = get_option('klmtn_from_text','');
    $to_string = get_option('klmtn_to_text','');
    if (!empty($this_string) && !empty($to_string)) {
        $translated_text = str_replace($this_string, $to_string, $translated_text);
    }
    return $translated_text;
}

add_filter('widget_text', 'Word_replace_widget_kalamatino_shkrn', 9999999);

///////////////////////////////////////////////////////////////////////////////////



