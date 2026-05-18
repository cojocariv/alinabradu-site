<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/mail.php';

function sendContactFormMail(string $subject, string $body, string $visitorEmail): bool
{
    $from = CONTACT_FORM_FROM_EMAIL;
    $to = CONTACT_FORM_TO_EMAIL;

    if (SMTP_PASSWORD !== '') {
        $hosts = array_values(array_unique([SMTP_HOST, 'mail.alinabradu.com', 'alinabradu.com']));
        foreach ($hosts as $host) {
            if (sendSmtpMail($to, $subject, $body, $visitorEmail, $from, 'Alina Bradu', $host, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD, SMTP_ENCRYPTION, SMTP_TIMEOUT)) {
                return true;
            }
            if (SMTP_PORT === 465 && sendSmtpMail($to, $subject, $body, $visitorEmail, $from, 'Alina Bradu', $host, 587, SMTP_USERNAME, SMTP_PASSWORD, 'tls', SMTP_TIMEOUT)) {
                return true;
            }
        }
        contactMailLog('SMTP failed for all hosts');
    } else {
        contactMailLog('SMTP_PASSWORD lipsă — adaugă config/mail.local.php');
    }

    if (sendPhpMail($to, $subject, $body, $visitorEmail, $from)) {
        return true;
    }

    contactMailLog('SMTP și mail() au eșuat');
    return false;
}

function sendPhpMail(string $to, string $subject, string $body, string $replyTo, string $from): bool
{
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = implode("\r\n", [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: Alina Bradu <' . $from . '>',
        'Reply-To: ' . $replyTo,
    ]);

    $previous = ini_get('sendmail_from');
    ini_set('sendmail_from', $from);
    $ok = @mail($to, $encodedSubject, $body, $headers, '-f' . $from);
    if ($previous !== false) {
        ini_set('sendmail_from', (string) $previous);
    }
    return $ok;
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

    $remote = ($enc === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $fp = @stream_socket_client($remote, $errno, $errstr, max(5, $timeout), STREAM_CLIENT_CONNECT);
    if (!$fp) {
        contactMailLog("connect {$remote}: {$errno} {$errstr}");
        return false;
    }

    stream_set_timeout($fp, max(5, $timeout));

    if (!smtpExpect($fp, [220], $err)) {
        contactMailLog('banner: ' . $err);
        fclose($fp);
        return false;
    }

    if (!smtpCmd($fp, 'EHLO alinabradu.com', [250], $err) && !smtpCmd($fp, 'HELO alinabradu.com', [250], $err)) {
        contactMailLog('EHLO: ' . $err);
        fclose($fp);
        return false;
    }

    if ($enc === 'tls') {
        if (!smtpCmd($fp, 'STARTTLS', [220], $err)
            || !@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)
            || !smtpCmd($fp, 'EHLO alinabradu.com', [250], $err)) {
            contactMailLog('STARTTLS: ' . $err);
            fclose($fp);
            return false;
        }
    }

    if (!smtpAuth($fp, $user, $pass, $err)) {
        contactMailLog("AUTH {$user}@{$host}: {$err}");
        fclose($fp);
        return false;
    }

    if (!smtpCmd($fp, 'MAIL FROM:<' . $from . '>', [250, 251], $err)
        || !smtpCmd($fp, 'RCPT TO:<' . $to . '>', [250, 251], $err)
        || !smtpCmd($fp, 'DATA', [354], $err)) {
        contactMailLog('envelope: ' . $err);
        fclose($fp);
        return false;
    }

    fwrite($fp, buildMailMessage($from, $fromName, $to, $replyTo, $subject, $body));
    if (!smtpExpect($fp, [250], $err)) {
        contactMailLog('DATA end: ' . $err);
        fclose($fp);
        return false;
    }

    smtpCmd($fp, 'QUIT', [221]);
    fclose($fp);
    return true;
}

function contactMailLog(string $message): void
{
    $dir = __DIR__ . '/../logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    @file_put_contents($dir . '/contact-mail.log', date('c') . ' ' . $message . "\n", FILE_APPEND);
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
function smtpAuth($fp, string $user, string $pass, ?string &$err = null): bool
{
    if (smtpCmd($fp, 'AUTH LOGIN', [334], $err)) {
        if (!smtpCmd($fp, base64_encode($user), [334], $err)) {
            return false;
        }
        return smtpCmd($fp, base64_encode($pass), [235], $err);
    }
    return smtpCmd($fp, 'AUTH PLAIN ' . base64_encode("\0{$user}\0{$pass}"), [235], $err);
}

/** @param resource $fp */
function smtpCmd($fp, string $cmd, array $okCodes, ?string &$err = null): bool
{
    fwrite($fp, $cmd . "\r\n");
    return smtpExpect($fp, $okCodes, $err);
}

/** @param resource $fp */
function smtpExpect($fp, array $okCodes, ?string &$err = null): bool
{
    $lines = [];
    do {
        $line = fgets($fp, 515);
        if ($line === false) {
            $err = 'fără răspuns';
            return false;
        }
        $lines[] = trim($line);
    } while (isset($line[3]) && $line[3] === '-');

    $code = (int) substr($lines[0], 0, 3);
    if (!in_array($code, $okCodes, true)) {
        $err = implode(' | ', $lines);
        return false;
    }
    $err = null;
    return true;
}
