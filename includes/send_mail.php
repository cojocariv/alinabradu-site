<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/contact.php';
require_once __DIR__ . '/../config/mail.php';

/**
 * Trimite email (formular contact). Încearcă mai multe metode (Plesk / shared hosting).
 */
function sendSiteMail(string $to, string $subject, string $body, string $replyTo): bool
{
    $from = CONTACT_MAIL_FROM;
    $fromName = 'Alina Bradu';
    $cfg = getMailConfig();

    $attempts = [];

    if (!empty($cfg['use_smtp']) && $cfg['host'] !== '') {
        $attempts[] = static fn () => sendSiteMailSmtp($to, $subject, $body, $replyTo, $from, $fromName, $cfg);
        if ($cfg['encryption'] === 'tls' && (int) $cfg['port'] === 587) {
            $sslCfg = $cfg;
            $sslCfg['port'] = 465;
            $sslCfg['encryption'] = 'ssl';
            $attempts[] = static fn () => sendSiteMailSmtp($to, $subject, $body, $replyTo, $from, $fromName, $sslCfg);
        }
    }

    if (!empty($cfg['try_localhost'])) {
        $localhostCfg = [
            'host' => 'localhost',
            'port' => 25,
            'user' => '',
            'pass' => '',
            'encryption' => '',
        ];
        $attempts[] = static fn () => sendSiteMailSmtp($to, $subject, $body, $replyTo, $from, $fromName, $localhostCfg);
    }

    $attempts[] = static fn () => sendSiteMailPhpMail($to, $subject, $body, $replyTo, $from, $fromName, $from);
    $attempts[] = static fn () => sendSiteMailPhpMail($to, $subject, $body, $replyTo, $from, $fromName, CONTACT_FORM_TO);

    foreach ($attempts as $attempt) {
        if ($attempt()) {
            return true;
        }
    }

    if (filter_var(getenv('MAIL_DEBUG') ?: '0', FILTER_VALIDATE_BOOLEAN)) {
        @file_put_contents(
            __DIR__ . '/../logs/contact-mail.log',
            date('c') . " FAIL to={$to} from={$from}\n",
            FILE_APPEND
        );
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
    $fp = @stream_socket_client($remote, $errno, $errstr, 25, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        return false;
    }

    stream_set_timeout($fp, 25);

    if (!smtpExpect($fp, [220])) {
        fclose($fp);
        return false;
    }

    $ehloHost = 'alinabradu.com';
    if (!smtpCmd($fp, 'EHLO ' . $ehloHost, [250]) && !smtpCmd($fp, 'HELO ' . $ehloHost, [250])) {
        fclose($fp);
        return false;
    }

    if ($enc === 'tls') {
        if (!smtpCmd($fp, 'STARTTLS', [220])
            || !@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($fp);
            return false;
        }
        if (!smtpCmd($fp, 'EHLO ' . $ehloHost, [250])) {
            fclose($fp);
            return false;
        }
    }

    $user = (string) ($cfg['user'] ?? '');
    $pass = (string) ($cfg['pass'] ?? '');
    if ($user !== '' && $pass !== '') {
        if (!smtpAuth($fp, $user, $pass)) {
            fclose($fp);
            return false;
        }
    }

    if (!smtpCmd($fp, 'MAIL FROM:<' . $from . '>', [250, 251])
        || !smtpCmd($fp, 'RCPT TO:<' . $to . '>', [250, 251])
        || !smtpCmd($fp, 'DATA', [354])) {
        fclose($fp);
        return false;
    }

    $message = buildMailMessage($from, $fromName, $to, $replyTo, $subject, $body);
    fwrite($fp, $message);
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
function smtpAuth($fp, string $user, string $pass): bool
{
    if (smtpCmd($fp, 'AUTH LOGIN', [334])) {
        return smtpCmd($fp, base64_encode($user), [334])
            && smtpCmd($fp, base64_encode($pass), [235]);
    }

    $plain = base64_encode("\0{$user}\0{$pass}");
    return smtpCmd($fp, 'AUTH PLAIN ' . $plain, [235]);
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
