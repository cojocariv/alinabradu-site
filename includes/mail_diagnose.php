<?php
declare(strict_types=1);

require_once __DIR__ . '/send_mail.php';

/** @return list<array{label:string,status:string,detail:string}> */
function runMailDiagnosis(bool $trySendTest = false): array
{
    $rows = [];
    $cfg = getMailConfig();

    $rows[] = row('Versiune PHP', 'ok', PHP_VERSION);
    $rows[] = row(
        'Fișiere config',
        is_readable(__DIR__ . '/../config/mail.php') ? 'ok' : 'fail',
        'mail.php ' . (is_readable(__DIR__ . '/../config/mail.local.php') ? '+ mail.local.php' : '(fără mail.local.php)')
    );
    $rows[] = row('CONTACT_FORM_FROM_EMAIL', 'ok', CONTACT_FORM_FROM_EMAIL);
    $rows[] = row('CONTACT_FORM_TO_EMAIL', 'ok', CONTACT_FORM_TO_EMAIL);
    $rows[] = row(
        'SMTP_PASSWORD',
        SMTP_PASSWORD !== '' ? 'ok' : 'fail',
        SMTP_PASSWORD !== '' ? 'setat (' . strlen(SMTP_PASSWORD) . ' caractere)' : 'LIPSEȘTE — setează în Plesk (PHP) sau config/mail.local.php'
    );
    $rows[] = row('SMTP activ (use_smtp)', $cfg['use_smtp'] ? 'ok' : 'warn', $cfg['use_smtp'] ? 'da' : 'nu — fără parolă SMTP nu se folosește autentificarea');
    $rows[] = row('SMTP_HOST', 'ok', $cfg['host'] . ':' . $cfg['port'] . ' (' . $cfg['encryption'] . ', timeout ' . $cfg['timeout'] . 's)');
    $rows[] = row('SMTP_USERNAME', 'ok', $cfg['user']);

    $rows[] = diagnoseSmtpSocket($cfg);

    if ($trySendTest && $cfg['use_smtp']) {
        $rows[] = diagnoseSmtpAuth($cfg);
    }

    if ($trySendTest) {
        $testSubject = '[Test] Formular contact ' . date('Y-m-d H:i:s');
        $testBody = "Email de test generat de mail-diagnose.\n";
        $ok = sendSiteMail(CONTACT_FORM_TO_EMAIL, $testSubject, $testBody, CONTACT_FORM_FROM_EMAIL);
        $rows[] = row(
            'Trimitere test completă',
            $ok ? 'ok' : 'fail',
            $ok ? 'sendSiteMail() = succes' : (getSendMailLastError() ?: 'toate metodele au eșuat')
        );
    }

    $logFile = __DIR__ . '/../logs/contact-mail.log';
    if (is_readable($logFile)) {
        $tail = array_slice(file($logFile, FILE_IGNORE_NEW_LINES) ?: [], -8);
        $rows[] = row('Jurnal logs/contact-mail.log', 'ok', $tail === [] ? '(gol)' : implode("\n", $tail));
    } elseif (filter_var(getenv('MAIL_DEBUG') ?: '0', FILTER_VALIDATE_BOOLEAN)) {
        $rows[] = row('MAIL_DEBUG', 'warn', 'activ, dar jurnalul nu există încă');
    }

    return $rows;
}

/** @param array{host:string,port:int,user:string,pass:string,encryption:string,timeout:int} $cfg */
function diagnoseSmtpSocket(array $cfg): array
{
    $host = $cfg['host'];
    $port = (int) $cfg['port'];
    $enc = strtolower((string) $cfg['encryption']);
    if ($enc === 'ssl') {
        $remote = 'ssl://' . $host . ':' . $port;
    } else {
        $remote = 'tcp://' . $host . ':' . $port;
    }

    $errno = 0;
    $errstr = '';
    $timeout = max(5, (int) $cfg['timeout']);
    $fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        return row('Conexiune TCP ' . $remote, 'fail', "errno {$errno}: {$errstr}");
    }

    stream_set_timeout($fp, $timeout);
    $banner = fgets($fp, 515) ?: '';
    fclose($fp);

    return row('Conexiune TCP ' . $remote, 'ok', trim($banner));
}

/** @param array{host:string,port:int,user:string,pass:string,encryption:string,timeout:int} $cfg */
function diagnoseSmtpAuth(array $cfg): array
{
    $ok = sendSiteMailSmtp(
        CONTACT_FORM_TO_EMAIL,
        '[Diagnose] AUTH test',
        'Test autentificare SMTP.',
        CONTACT_FORM_FROM_EMAIL,
        CONTACT_FORM_FROM_EMAIL,
        'Alina Bradu',
        $cfg
    );
    return row(
        'SMTP autentificare + DATA (scurt)',
        $ok ? 'ok' : 'fail',
        $ok ? 'conectare și trimitere reușite' : (getSendMailLastError() ?: 'eșec la AUTH sau DATA')
    );
}

/** @return array{label:string,status:string,detail:string} */
function row(string $label, string $status, string $detail): array
{
    return ['label' => $label, 'status' => $status, 'detail' => $detail];
}
