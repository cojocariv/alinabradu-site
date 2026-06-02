<?php
declare(strict_types=1);

/**
 * Fundal video hero (HTML5 — fără iconițe YouTube).
 * Exportă MP4 din YouTube și încarcă-l în Azure (container poze).
 * Opțional: config/home_hero.php poate suprascrie URL-urile pe server.
 *
 * @return array{mp4:string,poster:string}
 */
function homeHeroVideoConfig(): array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $cached = [
        'mp4' => 'https://alinabradupozestorage.blob.core.windows.net/poze/hero-home.mp4',
        'poster' => 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png',
    ];

    $configFile = __DIR__ . '/../config/home_hero.php';
    if (is_file($configFile)) {
        $overrides = require $configFile;
        if (is_array($overrides)) {
            $cached = array_merge($cached, $overrides);
        }
    }

    return $cached;
}
