<?php
/**
 * Plugin Name: WP Advanced Support Ticket
 * Plugin URI: https://zhaket.com/web/wp-advanced-support-ticket
 * Description: This is a powerful plugin to build and maintain better customer relationships.
 * Version: 13.19.0
 * Author: S.Reza Salehi
 * Author URI: http://sreza-salehi.ir
 * Text Domain: wpast
 * Domain Path: /languages/
 */

defined('ABSPATH') || exit('No Access!');

if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

$plugin_data = get_plugin_data(__FILE__, true, false);
define('WPAST_BASENAME', plugin_basename(__FILE__));
define('WPAST_NAME', $plugin_data['Name']);
define('WPAST_VER', $plugin_data['Version']);
define('WPAST_URI', $plugin_data['PluginURI']);
define('WPAST_AUTHOR_URI', $plugin_data['AuthorURI']);
define('WPAST_AUTHOR_NAME', $plugin_data['AuthorName']);

require(trailingslashit(plugin_dir_path(__FILE__)) . 'core.php');