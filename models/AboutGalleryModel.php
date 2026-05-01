<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class AboutGalleryModel
{
    public static function allActive(): array
    {
        $sql = 'SELECT image_url FROM about_gallery_images WHERE is_active = 1 ORDER BY sort_order ASC, id ASC';
        $rows = getDbConnection()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        return array_values(array_filter(array_map('strval', $rows)));
    }

    public static function allForAdmin(): array
    {
        $sql = 'SELECT * FROM about_gallery_images ORDER BY sort_order ASC, id ASC';
        return getDbConnection()->query($sql)->fetchAll();
    }

    public static function replaceAll(array $urls): void
    {
        $urls = array_values(array_unique(array_filter(array_map('trim', $urls))));
        $pdo = getDbConnection();
        $pdo->beginTransaction();
        try {
            $pdo->exec('DELETE FROM about_gallery_images');
            $stmt = $pdo->prepare('INSERT INTO about_gallery_images (image_url, sort_order, is_active) VALUES (?, ?, 1)');
            foreach ($urls as $idx => $url) {
                $stmt->execute([$url, $idx]);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
