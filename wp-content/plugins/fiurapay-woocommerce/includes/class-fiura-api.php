<?php

if (!defined('ABSPATH')) {
    exit;
}

final class Fiura_Api
{
    public function __construct(
        private string $api_key,
        private string $base_url = 'https://fiurapay.com',
    ) {
        $this->base_url = rtrim($base_url, '/');
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    public function create_invoice(array $body): array
    {
        return $this->request('POST', '/v1/defiura/invoices', $body);
    }

    /**
     * @return array<string, mixed>
     */
    public function get_invoice(string $id): array
    {
        return $this->request('GET', '/v1/defiura/invoices/' . rawurlencode($id), null);
    }

    /**
     * @param array<string, mixed>|null $body
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, ?array $body): array
    {
        $args = [
            'method'  => $method,
            'timeout' => 30,
            'headers' => [
                'Accept'     => 'application/json',
                'X-Api-Key'  => $this->api_key,
                'User-Agent' => 'FiuraWoo/' . FIURAPAY_WC_VERSION,
            ],
        ];
        if ($body !== null) {
            $args['headers']['Content-Type'] = 'application/json';
            $encoded = wp_json_encode($body);
            if (!is_string($encoded)) {
                throw new RuntimeException('Failed to encode Fiura request');
            }
            $args['body'] = $encoded;
        }

        $res = wp_remote_request($this->base_url . $path, $args);
        if (is_wp_error($res)) {
            throw new RuntimeException($res->get_error_message());
        }
        $code = (int) wp_remote_retrieve_response_code($res);
        $text = (string) wp_remote_retrieve_body($res);
        $json = json_decode($text, true);
        if ($code < 200 || $code >= 300) {
            $msg = '';
            if (is_array($json) && isset($json['message'])) {
                $msg = is_array($json['message'])
                    ? implode(' ', array_map('strval', $json['message']))
                    : (string) $json['message'];
            }
            throw new RuntimeException($msg !== '' ? $msg : ($text !== '' ? $text : ('Fiura HTTP ' . $code)));
        }
        return is_array($json) ? $json : [];
    }
}
