<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/contact.php';
require_once __DIR__ . '/../config/mail.php';

function getSendMailLastError(): string
{
    return (string) ($GLOBALS['send_mail_last_error'] ?? '');
}

function sendMailNoteFailure(string $method, string $detail): void
{
    $GLOBALS['send_mail_last_error'] = $method . ': ' . $detail;
    if (filter_var(getenv('MAIL_DEBUG') ?: '0', FILTER_VALIDATE_BOOLEAN)) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        @file_put_contents(
            $logDir . '/contact-mail.log',
            date('c') . ' ' . $GLOBALS['send_mail_last_error'] . "\n",
            FILE_APPEND
        );
    }
}

/**
 * Trimite email (formular contact). Încearcă mai multe metode (Plesk / shared hosting).
 */
function sendSiteMail(string $to, string $subject, string $body, string $replyTo): bool
{
    $GLOBALS['send_mail_last_error'] = '';
    $from = CONTACT_FORM_FROM_EMAIL;
    $fromName = 'Alina Bradu';
    $cfg = getMailConfig();

    $attempts = [];
    $labels = [];

    if (!empty($cfg['use_smtp']) && $cfg['host'] !== '') {
        $labels[] = 'SMTP ' . $cfg['host'] . ':' . $cfg['port'] . ' (' . $cfg['encryption'] . ')';
        $attempts[] = static fn () => sendSiteMailSmtp($to, $subject, $body, $replyTo, $from, $fromName, $cfg);
        if ($cfg['encryption'] === 'tls' && (int) $cfg['port'] === 465) {
            $sslCfg = $cfg;
            $sslCfg['encryption'] = 'ssl';
            $labels[] = 'SMTP SSL :465';
            $attempts[] = static fn () => sendSiteMailSmtp($to, $subject, $body, $replyTo, $from, $fromName, $sslCfg);
        }
        if ($cfg['encryption'] === 'tls' && (int) $cfg['port'] === 587) {
            $sslCfg = $cfg;
            $sslCfg['port'] = 465;
            $sslCfg['encryption'] = 'ssl';
            $labels[] = 'SMTP SSL :465 (fallback)';
            $attempts[] = static fn () => sendSiteMailSmtp($to, $subject, $body, $replyTo, $from, $fromName, $sslCfg);
        }
    }

    if (!empty($cfg['try_localhost'])) {
        $labels[] = 'SMTP localhost:25';
        $localhostCfg = [
            'host' => 'localhost',
            'port' => 25,
            'user' => '',
            'pass' => '',
            'encryption' => '',
        ];
        $attempts[] = static fn () => sendSiteMailSmtp($to, $subject, $body, $replyTo, $from, $fromName, $localhostCfg);
    }

    $labels[] = 'PHP mail() From=' . $from;
    $attempts[] = static fn () => sendSiteMailPhpMail($to, $subject, $body, $replyTo, $from, $fromName, $from);
    $labels[] = 'PHP mail() envelope=' . CONTACT_FORM_TO_EMAIL;
    $attempts[] = static fn () => sendSiteMailPhpMail($to, $subject, $body, $replyTo, $from, $fromName, CONTACT_FORM_TO_EMAIL);

    foreach ($attempts as $i => $attempt) {
        if ($attempt()) {
            return true;
        }
        if (!isset($labels[$i])) {
            continue;
        }
        sendMailNoteFailure($labels[$i], getSendMailLastError() !== '' ? getSendMailLastError() : 'eșec necunoscut');
    }

    if (getSendMailLastError() === '') {
        sendMailNoteFailure('sendSiteMail', 'toate metodele au returnat false');
    }

    return false;
}

function sendSiteMailPhpMail(
    string $to,
    string $subject,
    string $body,
    string $replyTo,
    string $from,
    string $fromName,
    string $envelopeFrom
): bool {
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $fromName . ' <' . $from . '>',
        'Reply-To: ' . $replyTo,
    ];

    $previous = ini_get('sendmail_from');
    ini_set('sendmail_from', $envelopeFrom);
    $params = '-f' . $envelopeFrom;
    $ok = @mail($to, $encodedSubject, $body, implode("\r\n", $headers), $params);
    if ($previous !== false) {
        ini_set('sendmail_from', (string) $previous);
    }
    if (!$ok) {
        sendMailNoteFailure('PHP mail()', 'mail() a returnat false pentru envelope ' . $envelopeFrom);
    }
    return $ok;
}

/**
 * @param array{host:string,port?:int,user?:string,pass?:string,encryption?:string} $cfg
 */
function sendSiteMailSmtp(
    string $to,
    string $subject,
    string $body,
    string $replyTo,
    string $from,
    string $fromName,
    array $cfg
): bool {
    $host = $cfg['host'];
    $port = isset($cfg['port']) && (int) $cfg['port'] > 0 ? (int) $cfg['port'] : 587;
    $enc = isset($cfg['encryption']) ? strtolower((string) $cfg['encryption']) : 'tls';
    if ($enc !== 'ssl' && $enc !== 'tls') {
        $enc = '';
    }

    $remote = ($enc === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $errno = 0;
    $errstr = '';
    $timeout = isset($cfg['timeout']) ? (int) $cfg['timeout'] : 12;
    $fp = @stream_socket_client($remote, $errno, $errstr, max(5, $timeout), STREAM_CLIENT_CONNECT);
    if (!$fp) {
        sendMailNoteFailure('SMTP connect', "{$remote} — errno {$errno}: {$errstr}");
        return false;
    }

    stream_set_timeout($fp, max(5, $timeout));

    if (!smtpExpect($fp, [220], $greet)) {
        sendMailNoteFailure('SMTP', 'banner invalid: ' . $greet);
        fclose($fp);
        return false;
    }

    $ehloHost = 'alinabradu.com';
    if (!smtpCmd($fp, 'EHLO ' . $ehloHost, [250], $ehloErr) && !smtpCmd($fp, 'HELO ' . $ehloHost, [250], $ehloErr)) {
        sendMailNoteFailure('SMTP EHLO', $ehloErr);
        fclose($fp);
        return false;
    }

    if ($enc === 'tls') {
        if (!smtpCmd($fp, 'STARTTLS', [220], $tlsErr)
            || !@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            sendMailNoteFailure('SMTP STARTTLS', $tlsErr ?: 'enable_crypto failed');
            fclose($fp);
            return false;
        }
        if (!smtpCmd($fp, 'EHLO ' . $ehloHost, [250], $ehloErr2)) {
            sendMailNoteFailure('SMTP EHLO after TLS', $ehloErr2);
            fclose($fp);
            return false;
        }
    }

    $user = (string) ($cfg['user'] ?? '');
    $pass = (string) ($cfg['pass'] ?? '');
    if ($user !== '' && $pass !== '') {
        if (!smtpAuth($fp, $user, $pass, $authErr)) {
            sendMailNoteFailure('SMTP AUTH', $authErr);
            fclose($fp);
            return false;
        }
    }

    if (!smtpCmd($fp, 'MAIL FROM:<' . $from . '>', [250, 251], $mailErr)
        || !smtpCmd($fp, 'RCPT TO:<' . $to . '>', [250, 251], $rcptErr)
        || !smtpCmd($fp, 'DATA', [354], $dataErr)) {
        sendMailNoteFailure('SMTP envelope', $mailErr ?: $rcptErr ?: $dataErr);
        fclose($fp);
        return false;
    }

    $message = buildMailMessage($from, $fromName, $to, $replyTo, $subject, $body);
    fwrite($fp, $message);
    if (!smtpExpect($fp, [250], $dataEndErr)) {
        sendMailNoteFailure('SMTP DATA', $dataEndErr);
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
        'Subject: ' . '=?UTF-8?B?' . base64_encode($subject) . '?=',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        '',
    ];

    $normalized = preg_replace("/\r\n|\r|\n/", "\n", $body) ?? $body;
    foreach (explode("\n", $normalized) as $line) {
        if (isset($line[0]) && $line[0] === '.') {
            $line = '.' . $line;
        }
        $lines[] = $line;
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

    $plain = base64_encode("\0{$user}\0{$pass}");
    return smtpCmd($fp, 'AUTH PLAIN ' . $plain, [235], $err);
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
    $code = 0;
    $lines = [];
    do {
        $line = fgets($fp, 515);
        if ($line === false) {
            $err = 'fără răspuns de la server';
            return false;
        }
        $lines[] = trim($line);
        $code = (int) substr($line, 0, 3);
    } while (isset($line[3]) && $line[3] === '-');

    if (!in_array($code, $okCodes, true)) {
        $err = implode(' | ', $lines);
        return false;
    }
    $err = null;
    return true;
}
