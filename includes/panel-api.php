<?php

function panel_hook_token(): string
{
    $hookFile = dirname(__DIR__) . '/config/panel-hook.php';
    $hook = is_file($hookFile) ? require $hookFile : [];
    return is_array($hook) ? (string)($hook['token'] ?? '') : '';
}

function require_panel_hook(): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    $expected = panel_hook_token();
    $given = (string)($_SERVER['HTTP_X_GHAZAL_BOOKS'] ?? '');
    if ($expected === '' || $given === '' || !hash_equals($expected, $given)) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'forbidden'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function panel_json_body(): array
{
    $raw = file_get_contents('php://input');
    $input = json_decode((string)$raw, true);
    if (is_array($input)) {
        return $input;
    }
    return is_array($_POST) ? $_POST : [];
}
