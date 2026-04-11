<?php
declare(strict_types=1);

/**
 * Parolă implicită: admin123 — schimb-o imediat în producție.
 * Generează hash nou: php -r "echo password_hash('parola_ta', PASSWORD_DEFAULT);"
 */
if (!defined('ADMIN_PASSWORD_HASH')) {
    define('ADMIN_PASSWORD_HASH', '$2y$10$xyXRwOlNob/RlddNCbvLJ.8nWzPq4xfouAIF0VxAZ6fqw.rW79a/u');
}
