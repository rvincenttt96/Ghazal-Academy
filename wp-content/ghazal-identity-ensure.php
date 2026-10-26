<?php
/**
 * Internal endpoint: create/update a WordPress user whose login is the student code.
 * Loaded from wp-load.php so password hashing matches this WordPress install.
 */
$root = dirname(__DIR__);
require_once $root . '/wp-load.php';

header('Content-Type: application/json; charset=utf-8');

$secret = '';
if (defined('GHAZAL_IDENTITY_SECRET')) {
    $secret = (string) GHAZAL_IDENTITY_SECRET;
}
if ($secret === '' && is_file(__DIR__ . '/ghazal-identity-secret.php')) {
    require_once __DIR__ . '/ghazal-identity-secret.php';
    $secret = defined('GHAZAL_IDENTITY_SECRET') ? (string) GHAZAL_IDENTITY_SECRET : '';
}

$given = $_SERVER['HTTP_X_GHAZAL_IDENTITY'] ?? '';
if ($secret === '' || $given === '' || !hash_equals($secret, $given)) {
    status_header(403);
    echo wp_json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}

$GLOBALS['ghazal_identity_syncing'] = true;

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST;
}

$action = (string) ($input['action'] ?? '');
if ($action === 'retire') {
    $codes = $input['codes'] ?? [];
    if (!is_array($codes)) {
        $codes = [];
    }
    $deleted = [];
    foreach ($codes as $raw_code) {
        $code = preg_replace('/\s+/', '', (string) $raw_code);
        if (!preg_match('/^[0-9]{3,8}$/', $code) || (int) $code < 5000) {
            continue;
        }
        $user = get_user_by('login', $code);
        if (!$user) {
            continue;
        }
        if (array_intersect((array) $user->roles, ['administrator', 'editor', 'shop_manager', 'author'])) {
            continue;
        }
        if (function_exists('wp_delete_user')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
            wp_delete_user((int) $user->ID);
            $deleted[] = $code;
        }
    }
    echo wp_json_encode(['ok' => true, 'deleted' => $deleted]);
    exit;
}

$code = preg_replace('/\s+/', '', (string) ($input['code'] ?? ''));
if (!preg_match('/^[0-9]{3,8}$/', $code)) {
    status_header(400);
    echo wp_json_encode(['ok' => false, 'error' => 'invalid code']);
    exit;
}

$previous = preg_replace('/\s+/', '', (string) ($input['previous_code'] ?? ''));
$full_name = sanitize_text_field((string) ($input['full_name'] ?? $code));
$first = sanitize_text_field((string) ($input['first_name'] ?? ''));
$last = sanitize_text_field((string) ($input['last_name'] ?? ''));
$phone = sanitize_text_field((string) ($input['phone'] ?? ''));
$password = (string) ($input['password'] ?? $code);
$reset = !empty($input['reset_password']);

if ($first === '' && $full_name !== '') {
    $parts = preg_split('/\s+/u', $full_name);
    $first = array_shift($parts);
    $last = implode(' ', $parts);
}

$user = get_user_by('login', $code);
if (!$user && $previous !== '' && $previous !== $code) {
    $user = get_user_by('login', $previous);
}
if (!$user) {
    $email_guess = $code . '@students.ghazalify.com';
    $user = get_user_by('email', $email_guess);
}

$created = false;
if (!$user) {
    $email = $code . '@students.ghazalify.com';
    if (email_exists($email)) {
        $email = $code . '.' . wp_generate_password(4, false, false) . '@students.ghazalify.com';
    }
    $user_id = wp_create_user($code, $password, $email);
    if (is_wp_error($user_id)) {
        status_header(500);
        echo wp_json_encode(['ok' => false, 'error' => $user_id->get_error_message()]);
        exit;
    }
    $user = get_user_by('id', $user_id);
    $created = true;
} else {
    $user_id = (int) $user->ID;
    if ($user->user_login !== $code) {
        global $wpdb;
        $wpdb->update($wpdb->users, ['user_login' => $code], ['ID' => $user_id]);
        clean_user_cache($user_id);
        $user = get_user_by('id', $user_id);
    }
    if ($reset || $created) {
        wp_set_password($password, $user_id);
    }
}

$role_user = new WP_User($user_id);
if (!$role_user->exists()) {
    status_header(500);
    echo wp_json_encode(['ok' => false, 'error' => 'user missing after create']);
    exit;
}
if (!in_array('administrator', (array) $role_user->roles, true)
    && !in_array('shop_manager', (array) $role_user->roles, true)
    && !in_array('editor', (array) $role_user->roles, true)) {
    $role_user->set_role('subscriber');
    if (class_exists('WooCommerce')) {
        $role_user->add_role('customer');
    }
}

wp_update_user([
    'ID' => $user_id,
    'display_name' => $full_name,
    'first_name' => $first,
    'nickname' => $code,
    'last_name' => $last,
]);

if ($phone !== '') {
    update_user_meta($user_id, 'phone', $phone);
    update_user_meta($user_id, 'billing_phone', $phone);
}
update_user_meta($user_id, 'ghazal_student_code', $code);

echo wp_json_encode([
    'ok' => true,
    'created' => $created,
    'user_id' => $user_id,
    'user_login' => $code,
]);
