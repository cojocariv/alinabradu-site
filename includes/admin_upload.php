<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

/**
 * Salvează fișiere încărcate în uploads/$folder/ și returnează URL-uri publice.
 *
 * @return list<string>
 */
function adminSaveUploads(?array $files, string $folder = 'products'): array
{
    if ($files === null || !isset($files['error']) || !is_array($files['error'])) {
        return [];
    }

    $folder = trim($folder, "/\\ \t\n\r\0\x0B");
    if ($folder === '' || !preg_match('/^[a-zA-Z0-9_-]+$/', $folder)) {
        return [];
    }

    $dir = __DIR__ . '/../uploads/' . $folder;
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return [];
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $out = [];
    foreach ($files['error'] as $i => $err) {
        if ($err !== UPLOAD_ERR_OK) {
            continue;
        }
        $tmp = $files['tmp_name'][$i] ?? '';
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            continue;
        }
        $type = @mime_content_type($tmp);
        if (!isset($allowed[$type])) {
            continue;
        }
        $ext = $allowed[$type];
        $name = bin2hex(random_bytes(10)) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (move_uploaded_file($tmp, $dest)) {
            $out[] = url('/uploads/' . $folder . '/' . $name);
        }
    }

    return $out;
}

/**
 * @return list<string>
 */
function adminSaveProductUploads(?array $files): array
{
    return adminSaveUploads($files, 'products');
}
