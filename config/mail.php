<?php
declare(strict_types=1);

/**
 * Trimitere email (formular contact).
 * Pe Plesk: creează mailbox admin@alinabradu.com, apoi setează SMTP mai jos sau în variabile de mediu.
 *
 * MAIL_USE_SMTP=1
 * MAIL_SMTP_HOST=mail.alinabradu.com
 * MAIL_SMTP_PORT=587
 * MAIL_SMTP_USER=admin@alinabradu.com
 * MAIL_SMTP_PASS=***
 * MAIL_SMTP_ENCRYPTION=tls
 */
return [
    'use_smtp' => filter_var(getenv('MAIL_USE_SMTP') ?: '0', FILTER_VALIDATE_BOOLEAN),
    'host' => (string) (getenv('MAIL_SMTP_HOST') ?: ''),
    'port' => (int) (getenv('MAIL_SMTP_PORT') ?: 587),
    'user' => (string) (getenv('MAIL_SMTP_USER') ?: ''),
    'pass' => (string) (getenv('MAIL_SMTP_PASS') ?: ''),
    'encryption' => strtolower((string) (getenv('MAIL_SMTP_ENCRYPTION') ?: 'tls')),
];
