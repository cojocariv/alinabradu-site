<?php
declare(strict_types=1);

/** SAS container „poze” — nu expune acest fișier public; folosit doar după autentificare. */
if (!defined('AZURE_STORAGE_SAS_URL')) {
    $env = getenv('AZURE_STORAGE_SAS_URL');
    if (is_string($env) && $env !== '') {
        define('AZURE_STORAGE_SAS_URL', $env);
    } else {
        define(
            'AZURE_STORAGE_SAS_URL',
            'https://alinabradupozestorage.blob.core.windows.net/poze?sp=racwdl&st=2026-04-28T08:39:31Z&se=2030-04-28T16:54:31Z&sv=2025-11-05&sr=c&sig=d0AXmmbogXdEMlu2%2B0l9vYNYkUp2XoepL%2F3jBcl%2FYYk%3D'
        );
    }
}
