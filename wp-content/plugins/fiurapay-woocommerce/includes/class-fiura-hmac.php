<?php

if (!defined('ABSPATH')) {
    exit;
}

final class Fiura_Hmac
{
    public static function verify(string $secret, string $raw_body, string $timestamp, string $signature, int $tolerance_sec = 300): bool
    {
        $timestamp = trim($timestamp);
        $signature = strtolower(trim($signature));
        if ($secret === '' || $timestamp === '' || $signature === '' || !ctype_digit($timestamp)) {
            return false;
        }
        $now = time();
        if (abs($now - (int) $timestamp) > $tolerance_sec) {
            return false;
        }
        $expected = hash_hmac('sha256', $timestamp . '.' . $raw_body, $secret);
        return hash_equals($expected, $signature);
    }

    public static function header(string $name): string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (!empty($_SERVER[$key])) {
            return (string) $_SERVER[$key];
        }
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $h => $v) {
                if (strtolower((string) $h) === strtolower($name)) {
                    return (string) $v;
                }
            }
        }
        return '';
    }
}
