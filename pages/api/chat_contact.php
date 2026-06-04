<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../models/ChatContactModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$payload = [];
if (is_string($raw) && $raw !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }
}
if ($payload === [] && $_POST !== []) {
    $payload = $_POST;
}

try {
    $recorded = ChatContactModel::record([
        'channel' => $payload['channel'] ?? '',
        'source' => $payload['source'] ?? 'unknown',
        'page_path' => $payload['page_path'] ?? (currentUrlPath() ?: '/'),
        'product_id' => $payload['product_id'] ?? null,
        'product_slug' => $payload['product_slug'] ?? null,
        'product_name' => $payload['product_name'] ?? null,
        'message_preview' => $payload['message_preview'] ?? null,
        'ip_address' => clientIpAddress(),
        'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 512),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code($recorded ? 204 : 422);
if (!$recorded) {
    echo json_encode(['ok' => false], JSON_UNESCAPED_UNICODE);
}
