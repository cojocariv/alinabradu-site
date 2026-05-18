<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/mail.php';

/**
 * Trimite mesajul formularului: contact@alinabradu.com → admin@alinabradu.com
 * Reply-To = emailul vizitatorului.
 */
function sendContactFormMail(string $subject, string $body, string $visitorEmail): bool
{
    if (SMTP_PASSWORD === '') {
        return false;
    }

    return sendSmtpMail(
        CONTACT_FORM_TO_EMAIL,
        $subject,
        $body,
        $visitorEmail,
        CONTACT_FORM_FROM_EMAIL,
        'Alina Bradu Contact',
        SMTP_HOST,
        SMTP_PORT,
        SMTP_USERNAME,
        SMTP_PASSWORD,
        SMTP_ENCRYPTION,
        SMTP_TIMEOUT
    );
}

function sendSmtpMail(
    string $to,
    string $subject,
    string $body,
    string $replyTo,
    string $from,
    string $fromName,
    string $host,
    int $port,
    string $user,
    string $pass,
    string $encryption,
    int $timeout
): bool {
    $enc = strtolower($encryption);
    if ($port === 465 && $enc === 'tls') {
        $enc = 'ssl';
    }
    if ($enc !== 'ssl' && $enc !== 'tls') {
        $enc = $port === 465 ? 'ssl' : 'tls';
    }

    $remote = ($enc === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $fp = @stream_socket_client($remote, $errno, $errstr, max(5, $timeout), STREAM_CLIENT_CONNECT);
    if (!$fp) {
        return false;
    }

    stream_set_timeout($fp, max(5, $timeout));

    if (!smtpExpect($fp, [220])
        || (!smtpCmd($fp, 'EHLO alinabradu.com', [250]) && !smtpCmd($fp, 'HELO alinabradu.com', [250]))) {
        fclose($fp);
        return false;
    }

    if ($enc === 'tls') {
        if (!smtpCmd($fp, 'STARTTLS', [220])
            || !@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)
            || !smtpCmd($fp, 'EHLO alinabradu.com', [250])) {
            fclose($fp);
            return false;
        }
    }

    if (!smtpAuth($fp, $user, $pass)
        || !smtpCmd($fp, 'MAIL FROM:<' . $from . '>', [250, 251])
        || !smtpCmd($fp, 'RCPT TO:<' . $to . '>', [250, 251])
        || !smtpCmd($fp, 'DATA', [354])) {
        fclose($fp);
        return false;
    }

    fwrite($fp, buildMailMessage($from, $fromName, $to, $replyTo, $subject, $body));
    if (!smtpExpect($fp, [250])) {
        fclose($fp);
        return false;
    }

    smtpCmd($fp, 'QUIT', [221]);
    fclose($fp);
    return true;
}

function buildMailMessage(
    string $from,
    string $fromName,
    string $to,
    string $replyTo,
    string $subject,
    string $body
): string {
    $lines = [
        'From: ' . $fromName . ' <' . $from . '>',
        'To: <' . $to . '>',
        'Reply-To: ' . $replyTo,
        'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        '',
    ];

    $normalized = preg_replace("/\r\n|\r|\n/", "\n", $body) ?? $body;
    foreach (explode("\n", $normalized) as $line) {
        $lines[] = (isset($line[0]) && $line[0] === '.') ? '.' . $line : $line;
    }

    return implode("\r\n", $lines) . "\r\n.\r\n";
}

/** @param resource $fp */
function smtpAuth($fp, string $user, string $pass): bool
{
    if (smtpCmd($fp, 'AUTH LOGIN', [334])) {
        return smtpCmd($fp, base64_encode($user), [334]) && smtpCmd($fp, base64_encode($pass), [235]);
    }
    return smtpCmd($fp, 'AUTH PLAIN ' . base64_encode("\0{$user}\0{$pass}"), [235]);
}

/** @param resource $fp */
function smtpCmd($fp, string $cmd, array $okCodes): bool
{
    fwrite($fp, $cmd . "\r\n");
    return smtpExpect($fp, $okCodes);
}

/** @param resource $fp */
function smtpExpect($fp, array $okCodes): bool
{
    $code = 0;
    do {
        $line = fgets($fp, 515);
        if ($line === false) {
            return false;
        }
        $code = (int) substr($line, 0, 3);
    } while (isset($line[3]) && $line[3] === '-');

    return in_array($code, $okCodes, true);
}
