<?php
declare(strict_types=1);

/**
 * Parolă pentru pagina de gestionare poze (produs.php / produs.html).
 *
 * Parolă implicită: poze-admin — schimb-o imediat în producție.
 * Generează hash nou:
 *   php -r "echo password_hash('parola_ta', PASSWORD_DEFAULT);"
 *
 * Poți seta și variabila de mediu STORAGE_MANAGER_PASSWORD_HASH (hash bcrypt).
 */
if (!defined('STORAGE_MANAGER_PASSWORD_HASH')) {
    $envHash = getenv('STORAGE_MANAGER_PASSWORD_HASH');
    if (is_string($envHash) && $envHash !== '') {
        define('STORAGE_MANAGER_PASSWORD_HASH', $envHash);
    } else {
        define('STORAGE_MANAGER_PASSWORD_HASH', '$2y$10$kylOyWOgb5UCIb2LCJRLLOp63a44fsuUsomXA9Ts0MLLzzdN9wVVe');
    }
}
