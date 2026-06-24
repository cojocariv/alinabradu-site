<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/ip_geo.php';

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

        $geoCity = null;
        $geoCountry = null;
        $geoRegion = null;
        if ($ip !== null) {
            $geo = lookupIpGeo($ip);
            if ($geo !== null) {
                $geoCity = self::truncate($geo['city'], 120) ?: null;
                $geoCountry = self::truncate($geo['country'], 120) ?: null;
                $geoRegion = self::truncate($geo['region'], 120) ?: null;
            }
        }

        $sql = 'INSERT INTO chat_contact_leads
            (channel, source, page_path, product_id, product_slug, product_name, message_preview, ip_address, geo_city, geo_country, geo_region, user_agent)
            VALUES
            (:channel, :source, :page_path, :product_id, :product_slug, :product_name, :message_preview, :ip_address, :geo_city, :geo_country, :geo_region, :user_agent)';

        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':channel', $channel);
        $stmt->bindValue(':source', $source);
        $stmt->bindValue(':page_path', $pagePath);
        $stmt->bindValue(':product_id', $productId, $productId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':product_slug', $productSlug);
        $stmt->bindValue(':product_name', $productName);
        $stmt->bindValue(':message_preview', $messagePreview);
        $stmt->bindValue(':ip_address', $ip);
        $stmt->bindValue(':geo_city', $geoCity);
        $stmt->bindValue(':geo_country', $geoCountry);
        $stmt->bindValue(':geo_region', $geoRegion);
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

    /**
     * Completează oraș/țară pentru înregistrările vechi (un lookup per IP unic).
     *
     * @param list<array<string, mixed>> $leads
     * @return list<array<string, mixed>>
     */
    public static function hydrateGeoForList(array $leads): array
    {
        if ($leads === []) {
            return $leads;
        }

        /** @var array<string, array{city: string, country: string, region: string}|null> $geoByIp */
        $geoByIp = [];
        $pendingIds = [];

        foreach ($leads as $idx => $lead) {
            if (self::leadHasGeo($lead)) {
                continue;
            }

            $ip = trim((string) ($lead['ip_address'] ?? ''));
            if ($ip === '') {
                continue;
            }

            if (!array_key_exists($ip, $geoByIp)) {
                $geoByIp[$ip] = lookupIpGeo($ip);
            }

            $geo = $geoByIp[$ip];
            if ($geo === null) {
                continue;
            }

            $leadId = (int) ($lead['id'] ?? 0);
            if ($leadId > 0) {
                $pendingIds[$leadId] = $geo;
            }

            $leads[$idx]['geo_city'] = $geo['city'];
            $leads[$idx]['geo_country'] = $geo['country'];
            $leads[$idx]['geo_region'] = $geo['region'];
        }

        foreach ($pendingIds as $leadId => $geo) {
            self::updateGeo($leadId, $geo);
        }

        return $leads;
    }

    /** @param array{city: string, country: string, region: string} $geo */
    public static function updateGeo(int $leadId, array $geo): void
    {
        if ($leadId < 1) {
            return;
        }

        $sql = 'UPDATE chat_contact_leads SET geo_city = :city, geo_country = :country, geo_region = :region WHERE id = :id';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':city', self::truncate($geo['city'], 120) ?: null);
        $stmt->bindValue(':country', self::truncate($geo['country'], 120) ?: null);
        $stmt->bindValue(':region', self::truncate($geo['region'], 120) ?: null);
        $stmt->bindValue(':id', $leadId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function countAll(): int
    {
        return (int) getDbConnection()->query('SELECT COUNT(*) FROM chat_contact_leads')->fetchColumn();
    }

    /** @param array<string, mixed> $lead */
    private static function leadHasGeo(array $lead): bool
    {
        return trim((string) ($lead['geo_city'] ?? '')) !== ''
            || trim((string) ($lead['geo_country'] ?? '')) !== '';
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
