<?php
declare(strict_types=1);

/**
 * Parolă implicită: admin123 — schimb-o imediat în producție.
 * Generează hash nou: php -r "echo password_hash('parola_ta', PASSWORD_DEFAULT);"
 */
if (!defined('ADMIN_PASSWORD_HASH')) {
    define('ADMIN_PASSWORD_HASH', '$2y$10$5.4X9gRFXsPfZF52U8VpBODqBvA41lqA3brX0rA5mSyooaysVpABm');
}
