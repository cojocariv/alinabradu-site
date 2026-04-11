<?php
declare(strict_types=1);

/**
 * Parolă implicită: admin123 — schimb-o imediat în producție.
 * Generează hash nou: php -r "echo password_hash('parola_ta', PASSWORD_DEFAULT);"
 */
if (!defined('ADMIN_PASSWORD_HASH')) {
    define('ADMIN_PASSWORD_HASH', '$2y$10$bwaXrTR36SvLITTjPXvhR.pPacuqOV/2TqdboVcUN6zFjOE3nGgke');
}
