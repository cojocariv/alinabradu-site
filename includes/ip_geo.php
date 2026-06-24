<?php
declare(strict_types=1);

/**
 * Geolocalizare aproximativă după IP (oraș, țară).
 * Notă: whois.domaintools.com este orientat spre WHOIS domenii/IP manual;
 * pentru listă automată folosim ipwho.is (date publice de geolocalizare IP).
 */

/** @return array{city: string, country: string, region: string}|null */
function lookupIpGeo(string $ip): ?array
{
    static $cache = [];

    $ip = trim($ip);
    if ($ip === '') {
        return null;
    }

    if (array_key_exists($ip, $cache)) {
        return $cache[$ip];
    }

    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return $cache[$ip] = null;
    }

    $url = 'https://ipwho.is/' . rawurlencode($ip);
    $context = stream_context_create([
        'http' => [
            'timeout' => 4,
            'header' => "Accept: application/json\r\nUser-Agent: AlinaBradu-Site/1.0\r\n",
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $raw = @file_get_contents($url, false, $context);
    if ($raw === false || $raw === '') {
        return $cache[$ip] = null;
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || empty($data['success'])) {
        return $cache[$ip] = null;
    }

    $city = trim((string) ($data['city'] ?? ''));
    $country = trim((string) ($data['country'] ?? ''));
    $region = trim((string) ($data['region'] ?? ''));

    if ($city === '' && $country === '' && $region === '') {
        return $cache[$ip] = null;
    }

    return $cache[$ip] = [
        'city' => $city,
        'country' => $country,
        'region' => $region,
    ];
}

function formatIpGeoLabel(?string $city, ?string $country, ?string $region = null): string
{
    $city = trim((string) $city);
    $country = trim((string) $country);
    $region = trim((string) $region);

    if ($city !== '' && $country !== '') {
        return $city . ', ' . $country;
    }
    if ($city !== '') {
        return $city;
    }
    if ($country !== '') {
        return $country;
    }
    if ($region !== '') {
        return $region;
    }

    return '—';
}

function domainToolsIpLookupUrl(string $ip): string
{
    return 'https://whois.domaintools.com/' . rawurlencode(trim($ip));
}
