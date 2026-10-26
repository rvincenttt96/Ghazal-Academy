<?php
/**
 * Plugin Name: Ghazal Rename Retired
 * Description: One-shot rename of deleted generated student usernames. Delete after use.
 */
defined('ABSPATH') || exit;
register_activation_hook(__FILE__, static function () {
    $pdo = new PDO('mysql:host=localhost;dbname=ghazali1_panel;charset=utf8mb4', 'ghazali1_panel', 'et9g3Z8xoLQF8lE9', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $n = $pdo->exec("UPDATE users SET username = CONCAT('old', username) WHERE role = 'student' AND is_deleted = 1 AND username REGEXP '^[0-9]{3,8}$' AND CAST(username AS UNSIGNED) >= 5000");
    $acc = new PDO('mysql:host=localhost;dbname=ghazali1_Acc;charset=utf8mb4', 'ghazali1_Acc', 'hMfb7fghsNuKXWDgz4GQ', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $max = $acc->query("SELECT MAX(CAST(studentCode AS UNSIGNED)) AS m FROM students WHERE studentCode REGEXP '^[0-9]{3,4}$' AND CAST(studentCode AS UNSIGNED) < 5000")->fetch();
    update_option('ghazal_rename_retired', ['panel_renamed' => $n, 'acc_max' => $max], false);
});
