<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/contact.php';

/**
 * Trimite email text simplu (contact). Încearcă SMTP dacă e configurat, altfel mail() cu envelope -f.
 */
function sendSiteMail(string $to, string $subject, string $body, string $replyTo): bool
{
    $from = CONTACT_MAIL_FROM;
    $fromName = 'Alina Bradu';

    $mailConfig = require __DIR__ . '/../config/mail.php';
    if (!empty($mailConfig['use_smtp']) && $mailConfig['host'] !== '') {
        return sendSiteMailSmtp($to, $subject, $body, $replyTo, $from, $fromName, $mailConfig);
    }

    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $fromName . ' <' . $from . '>',
        'Reply-To: ' . $replyTo,
    ];

    $params = '-f' . $from;
    return @mail($to, $encodedSubject, $body, implode("\r\n", $headers), $params);
}

/** @param array{host:string,port:int,user:string,pass:string,encryption:string} $cfg */
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
    $port = $cfg['port'] > 0 ? $cfg['port'] : 587;
    $enc = $cfg['encryption'] === 'ssl' ? 'ssl' : ($cfg['encryption'] === 'tls' ? 'tls' : '');
    $remote = ($enc === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;

    $errno = 0;
    $errstr = '';
    $fp = @stream_socket_client($remote, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        return false;
    }

    stream_set_timeout($fp, 20);

    if (!smtpExpect($fp, [220])) {
        fclose($fp);
        return false;
    }

    $ehloHost = 'alinabradu.com';
    if (!smtpCmd($fp, 'EHLO ' . $ehloHost, [250])) {
        fclose($fp);
        return false;
    }

    if ($enc === 'tls') {
        if (!smtpCmd($fp, 'STARTTLS', [220]) || !@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($fp);
            return false;
        }
        if (!smtpCmd($fp, 'EHLO ' . $ehloHost, [250])) {
            fclose($fp);
            return false;
        }
    }

    $user = $cfg['user'];
    $pass = $cfg['pass'];
    if ($user !== '') {
        if (!smtpCmd($fp, 'AUTH LOGIN', [334])
            || !smtpCmd($fp, base64_encode($user), [334])
            || !smtpCmd($fp, base64_encode($pass), [235])) {
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

    $data = 'From: ' . $fromName . ' <' . $from . ">\r\n";
    $data .= 'To: <' . $to . ">\r\n";
    $data .= 'Reply-To: ' . $replyTo . "\r\n";
    $data .= 'Subject: ' . '=?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
    $data .= "MIME-Version: 1.0\r\n";
    $data .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $data .= "\r\n";
    $data .= str_replace(["\r\n.", "\n."], ["\r\n..", "\n.."], str_replace("\n", "\r\n", $body));
    $data .= "\r\n.\r\n";

    fwrite($fp, $data);
    if (!smtpExpect($fp, [250])) {
        fclose($fp);
        return false;
    }

    smtpCmd($fp, 'QUIT', [221]);
    fclose($fp);
    return true;
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
    $line = '';
    while (($chunk = fgets($fp, 515)) !== false) {
        $line .= $chunk;
        if (isset($chunk[3]) && $chunk[3] === ' ') {
            break;
        }
    }
    if ($line === '') {
        return false;
    }
    $code = (int) substr($line, 0, 3);
    return in_array($code, $okCodes, true);
}
