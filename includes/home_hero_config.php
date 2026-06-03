<?php
declare(strict_types=1);

/**
 * Fundal video hero (HTML5). La fiecare încărcare de pagină se alege aleator un MP4.
 * Pentru pornire rapidă: MP4 max ~8–15 MB, 720p, H.264, cu „fast start” (moov la început).
 * CapCut: export 720p; apoi ffmpeg -i in.mp4 -c copy -movflags +faststart out.mp4
 * Opțional: config/home_hero.php poate suprascrie lista pe server.
 *
 * @return array{mp4:string,poster:string}
 */
function homeHeroVideoConfig(): array
{
    static $base = null;
    if ($base === null) {
        $base = [
            'videos' => [
                'https://alinabradupozestorage.blob.core.windows.net/poze/amrita.mp4',
                'https://alinabradupozestorage.blob.core.windows.net/poze/spirit.mp4',
            ],
            'poster' => 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png',
        ];

        $configFile = __DIR__ . '/../config/home_hero.php';
        if (is_file($configFile)) {
            $overrides = require $configFile;
            if (is_array($overrides)) {
                if (isset($overrides['videos']) && is_array($overrides['videos'])) {
                    $base['videos'] = $overrides['videos'];
                    unset($overrides['videos']);
                }
                $base = array_merge($base, $overrides);
            }
        }

        $base['videos'] = array_values(array_filter(
            $base['videos'],
            static fn($url) => is_string($url) && $url !== ''
        ));
    }

    $videos = $base['videos'];
    $mp4 = $videos !== [] ? $videos[array_rand($videos)] : '';

    return [
        'mp4' => $mp4,
        'poster' => (string) ($base['poster'] ?? ''),
    ];
}
