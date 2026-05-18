<?php
declare(strict_types=1);

require_once __DIR__ . '/send_mail.php';

/** @return list<array{label:string,status:string,detail:string}> */
function runMailDiagnosis(bool $trySendTest = false): array
{
    $rows = [];
    $rows[] = ['label' => 'De la', 'status' => 'ok', 'detail' => CONTACT_FORM_FROM_EMAIL];
    $rows[] = ['label' => 'Către', 'status' => 'ok', 'detail' => CONTACT_FORM_TO_EMAIL];
    $rows[] = [
        'label' => 'SMTP_PASSWORD',
        'status' => SMTP_PASSWORD !== '' ? 'ok' : 'fail',
        'detail' => SMTP_PASSWORD !== '' ? 'setat' : 'lipsește — config/mail.local.php pe server',
    ];
    $rows[] = [
        'label' => 'SMTP',
        'status' => 'ok',
        'detail' => SMTP_HOST . ':' . SMTP_PORT . ' (' . SMTP_ENCRYPTION . '), user ' . SMTP_USERNAME,
    ];

    $errno = 0;
    $errstr = '';
    $remote = 'ssl://' . SMTP_HOST . ':' . SMTP_PORT;
    $fp = @stream_socket_client($remote, $errno, $errstr, SMTP_TIMEOUT, STREAM_CLIENT_CONNECT);
    $rows[] = [
        'label' => 'Conexiune ' . $remote,
        'status' => $fp ? 'ok' : 'fail',
        'detail' => $fp ? trim((string) fgets($fp, 515)) : "errno {$errno}: {$errstr}",
    ];
    if ($fp) {
        fclose($fp);
    }

    if ($trySendTest && SMTP_PASSWORD !== '') {
        $ok = sendContactFormMail('[Test] Contact', "Test " . date('c') . "\n", CONTACT_FORM_FROM_EMAIL);
        $rows[] = [
            'label' => 'Trimitere test',
            'status' => $ok ? 'ok' : 'fail',
            'detail' => $ok ? 'mesaj trimis' : 'sendContactFormMail() a eșuat',
        ];
    }

    return $rows;
}
