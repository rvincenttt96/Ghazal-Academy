<?php
/**
 * Map Liara HTTP errors to a short Persian message for quiz and supervisor AI.
 */

function ghazal_liara_http_error($code, $decoded) {
    $msg = '';
    if (is_array($decoded)) {
        $err = $decoded['error'] ?? null;
        if (is_array($err)) {
            $msg = (string)($err['message'] ?? '');
        } elseif (is_string($err)) {
            $msg = $err;
        }
        if ($msg === '') {
            $msg = (string)($decoded['message'] ?? '');
        }
    }
    $low = strtolower($msg);
    if ((int)$code === 402 || strpos($low, 'insufficient') !== false) {
        return 'اعتبار لیارا کافی نیست.';
    }
    if (
        strpos($low, 'not active') !== false
        || strpos($low, 'not available') !== false
        || strpos($low, 'frozen') !== false
        || strpos($low, 'workspace is not') !== false
    ) {
        return 'فضای کاری لیارا فریز شده. از کنسول لیارا روی AI Workspace گزینه Unfreeze را بزنید و اگر موجودی کافی نیست کیف پول را شارژ کنید.';
    }
    if ($msg !== '') {
        return $msg;
    }
    return 'خطای لیارا (' . (int)$code . ')';
}
