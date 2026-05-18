<?php
declare(strict_types=1);

/** @return array{use_smtp:bool,host:string,port:int,user:string,pass:string,encryption:string,try_localhost:bool} */
function getMailConfig(): array
{
    $base = [
        'use_smtp' => filter_var(getenv('MAIL_USE_SMTP') ?: '0', FILTER_VALIDATE_BOOLEAN),
        'host' => (string) (getenv('MAIL_SMTP_HOST') ?: ''),
        'port' => (int) (getenv('MAIL_SMTP_PORT') ?: 587),
        'user' => (string) (getenv('MAIL_SMTP_USER') ?: ''),
        'pass' => (string) (getenv('MAIL_SMTP_PASS') ?: ''),
        'encryption' => strtolower((string) (getenv('MAIL_SMTP_ENCRYPTION') ?: 'tls')),
        'try_localhost' => filter_var(getenv('MAIL_TRY_LOCALHOST') ?: '1', FILTER_VALIDATE_BOOLEAN),
    ];

    $localFile = __DIR__ . '/mail.local.php';
    if (is_readable($localFile)) {
        /** @var array<string, mixed> $local */
        $local = require $localFile;
        $base = array_merge($base, $local);
    }

    if (!$base['use_smtp'] && $base['host'] !== '' && $base['user'] !== '' && $base['pass'] !== '') {
        $base['use_smtp'] = true;
    }

    return $base;
}
