<?php

final class EitaaUniomService
{
    private string $apiKey;
    private string $baseUrl;
    private int $timeout;

    public function __construct(string $apiKey, string $baseUrl = 'https://api.uniom.ir/bot', int $timeout = 20)
    {
        $this->apiKey = trim($apiKey);
        $this->baseUrl = rtrim(trim($baseUrl), '/');
        $this->timeout = $timeout;
    }

    public function configured(): bool
    {
        return $this->apiKey !== '';
    }

    public function call(string $method, array $payload = [], string $httpMethod = 'POST'): array
    {
        if (!$this->configured()) {
            return ['ok' => false, 'error' => 'Eitaa API key is not configured.'];
        }

        $url = $this->baseUrl . rawurlencode($this->apiKey) . '/' . ltrim($method, '/');
        $ch = curl_init();

        $headers = ['Accept: application/json'];
        if (strtoupper($httpMethod) === 'GET') {
            if ($payload) {
                $url .= '?' . http_build_query($payload);
            }
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            $headers[] = 'Content-Type: application/json; charset=utf-8';
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'GhazaliBookstore/0.3',
        ]);

        $body = curl_exec($ch);
        $curlError = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {
            return ['ok' => false, 'error' => 'Network error: ' . $curlError, 'http_status' => $status];
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            return [
                'ok' => false,
                'error' => 'Invalid JSON response.',
                'http_status' => $status,
                'raw' => function_exists('mb_substr') ? mb_substr((string)$body, 0, 1000, 'UTF-8') : substr((string)$body, 0, 1000),
            ];
        }

        if (!array_key_exists('http_status', $decoded)) {
            $decoded['http_status'] = $status;
        }
        return $decoded;
    }

    public function getMe(): array
    {
        return $this->call('getMe', [], 'GET');
    }

    public function sendMessage(string $chatId, string $text): array
    {
        return $this->call('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    public function setWebhook(string $url): array
    {
        return $this->call('setWebhook', ['url' => $url]);
    }

    public function deleteWebhook(): array
    {
        return $this->call('deleteWebhook', []);
    }
}
