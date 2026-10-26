<?php defined( 'ABSPATH' ) || exit;
/**
 * Plugin Name: ShokrinoDevOptions Framework
 * Plugin URI: https://shokrino.com/
 * Author: Shokrino Team
 * Author URI: https://shokrino.com/
 * Version: 1.0.0
 * Text Domain: sdo
 * Domain Path: /languages
 * Description: Professional Tool to develop your WordPress theme and plugin easier
 * @package   SDO Framework - WordPress Options
 * @link      https://shokrino.com
 * @copyright 2023 Shokrino
 */
define('SDOPATH' , defined( 'ABSPATH' ));
defined( 'SDOPATH' ) || exit;
define('SDO_PATH' , SHKRN_LIB.'/sdo-framework/');
define('SDO_URL' , SHKRN_DIR_URL.'/inc/lib/sdo-framework/');
define('SDO_INC' , SDO_PATH.'inc/');
define('SDO_CLSS' , SDO_PATH.'inc/classes/');
define('SDO_TMPL' , SDO_PATH.'inc/templates/');
define('SDO_ASSETS' , SDO_URL.'assets/');
define('SDO_NAME', SHKRN_THEME_NAME);
define('SDO_VERSION', SHKRN_THEME_VERSION);
$current_theme = wp_get_theme()->get( 'Name' );
define('SDO_TEXTDOMAIN', 'sdo');
define('SDO_CURRENT_THEME', $current_theme);
define('SDO_CURRENT_PHP',substr(phpversion(), 0, strpos(phpversion(), '.', strpos(phpversion(), '.') + 1)));

include_once SDO_INC.'functions.php';
include_once SDO_CLSS.'secure.class.php';
include_once SDO_CLSS.'builder.class.php';
include_once SDO_CLSS.'ajax.class.php';
include_once SDO_CLSS.'core.class.php';

# customize framework
include_once 'config.php';