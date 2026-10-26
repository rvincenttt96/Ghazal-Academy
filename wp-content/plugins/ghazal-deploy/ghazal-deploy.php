<?php
/**
 * Plugin Name: Ghazal Deploy Identity
 * Description: One-shot copy of identity files onto the panel. Deactivate after use.
 */
defined('ABSPATH') || exit;

add_action('admin_init', static function () {
    if (!current_user_can('manage_options') || empty($_GET['ghazal_copy'])) {
        return;
    }
    $payload = plugin_dir_path(__FILE__) . 'payload/';
    $domains = dirname(ABSPATH, 2);
    $panel = $domains . '/panel.ghazalify.com/public_html';
    header('Content-Type: text/plain; charset=utf-8');
    echo "ABSPATH=" . ABSPATH . "\n";
    echo "domains=" . $domains . "\n";
    echo "panel=" . $panel . "\n";
    echo "panel_exists=" . (is_dir($panel) ? 'yes' : 'no') . "\n";
    echo "includes_writable=" . (is_writable($panel . '/includes') ? 'yes' : 'no') . "\n";
    foreach ([
        $payload . 'identity-sync.php' => $panel . '/includes/identity-sync.php',
        $payload . 'identity-ensure.php' => $panel . '/identity-ensure.php',
    ] as $src => $dest) {
        echo "src=" . $src . " exists=" . (is_file($src) ? 'yes' : 'no') . "\n";
        $ok = @copy($src, $dest);
        echo "dest=" . $dest . " copy=" . ($ok ? 'ok' : 'fail') . " err=" . json_encode(error_get_last()) . "\n";
    }
    exit;
});