<?php
/**
 * Plugin Name: Ghazal Identity
 * Description: Site logins for students stay on WordPress only. Book buyers are not created in the teaching panel.
 * Version: 1.1.0
 */
defined('ABSPATH') || exit;

// Intentionally no user_register hook into the panel.
// A customer who only buys a book must not receive a file number or panel account.
