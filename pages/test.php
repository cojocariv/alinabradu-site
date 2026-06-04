<?php
declare(strict_types=1);

http_response_code(200);
header('Content-Type: text/html; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow');

$htmlPath = __DIR__ . '/../assets/test/existential-clock.html';
if (!is_readable($htmlPath)) {
    http_response_code(500);
    echo 'Fișierul test lipsește.';
    exit;
}

readfile($htmlPath);
