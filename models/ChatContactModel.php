<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class ChatContactModel
{
    private const ALLOWED_CHANNELS = ['whatsapp', 'viber'];

    /** @param array<string, mixed> $data */
    public static function record(array $data): bool
    {
        $channel = strtolower(trim((string) ($data['channel'] ?? '')));
        if (!in_array($channel, self::ALLOWED_CHANNELS, true)) {
            return false;
        }

        $source = self::truncate(trim((string) ($data['source'] ?? 'unknown')), 64) ?: 'unknown';
        $pagePath = self::truncate(trim((string) ($data['page_path'] ?? '')), 500);

        $productId = isset($data['product_id']) && $data['product_id'] !== '' && $data['product_id'] !== null
            ? max(0, (int) $data['product_id'])
            : null;
        if ($productId === 0) {
            $productId = null;
        }

        $productSlug = self::truncate(trim((string) ($data['product_slug'] ?? '')), 220);
        $productSlug = $productSlug !== '' ? $productSlug : null;

        $productName = self::truncate(trim((string) ($data['product_name'] ?? '')), 200);
        $productName = $productName !== '' ? $productName : null;

        $messagePreview = self::truncate(trim((string) ($data['message_preview'] ?? '')), 500);
        $messagePreview = $messagePreview !== '' ? $messagePreview : null;

        $ip = self::truncate(trim((string) ($data['ip_address'] ?? '')), 45);
        $ip = $ip !== '' ? $ip : null;

        $userAgent = self::truncate(trim((string) ($data['user_agent'] ?? '')), 512);
        $userAgent = $userAgent !== '' ? $userAgent : null;

        $sql = 'INSERT INTO chat_contact_leads
            (channel, source, page_path, product_id, product_slug, product_name, message_preview, ip_address, user_agent)
            VALUES
            (:channel, :source, :page_path, :product_id, :product_slug, :product_name, :message_preview, :ip_address, :user_agent)';

        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':channel', $channel);
        $stmt->bindValue(':source', $source);
        $stmt->bindValue(':page_path', $pagePath);
        $stmt->bindValue(':product_id', $productId, $productId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':product_slug', $productSlug);
        $stmt->bindValue(':product_name', $productName);
        $stmt->bindValue(':message_preview', $messagePreview);
        $stmt->bindValue(':ip_address', $ip);
        $stmt->bindValue(':user_agent', $userAgent);
        $stmt->execute();

        return true;
    }

    /** @return list<array<string, mixed>> */
    public static function listRecent(int $limit = 200, int $offset = 0): array
    {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);

        $sql = 'SELECT * FROM chat_contact_leads ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        return (int) getDbConnection()->query('SELECT COUNT(*) FROM chat_contact_leads')->fetchColumn();
    }

    private static function truncate(string $value, int $max): string
    {
        if ($max <= 0 || $value === '') {
            return $value;
        }
        if (mb_strlen($value) <= $max) {
            return $value;
        }

        return mb_substr($value, 0, $max);
    }
}
