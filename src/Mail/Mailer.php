<?php

declare(strict_types=1);

namespace Yatsn\Mail;

use Yatsn\Support\Config;
use Yatsn\Support\Security;

final class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $transport = strtolower((string) Config::get('mail.transport', 'log'));
        $from = (string) Config::get('mail.from_address');
        $fromName = (string) Config::get('mail.from_name');
        $password = (string) Config::get('mail.password');

        $safeBody = self::redactBody($body);

        if ($transport === 'log' || $password === '' || $transport !== 'smtp') {
            $line = sprintf(
                "[%s] MAIL transport=%s to=%s subject=%s from=%s <%s>\n%s\n----\n",
                gmdate('c'),
                $password === '' ? 'log(fallback-missing-password)' : 'log',
                $to,
                $subject,
                $fromName,
                $from,
                $safeBody
            );
            $path = Config::get('paths.log') . '/mail.log';
            file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
            return true;
        }

        return self::sendSmtp($to, $subject, $body, $from, $fromName, $password);
    }

    public static function transportStatus(): array
    {
        $transport = strtolower((string) Config::get('mail.transport', 'log'));
        $password = (string) Config::get('mail.password');
        $active = ($transport === 'smtp' && $password !== '') ? 'smtp' : 'log';
        return [
            'configuredTransport' => $transport,
            'activeTransport' => $active,
            'smtpPasswordPresent' => $password !== '',
            'host' => Config::get('mail.host'),
            'port' => Config::get('mail.port'),
            'encryption' => Config::get('mail.encryption'),
            'fromAddress' => Config::get('mail.from_address'),
        ];
    }

    private static function redactBody(string $body): string
    {
        $body = preg_replace('#(https?://\S+/activate\?token=)(\S+)#i', '$1[redacted]', $body) ?? $body;
        $body = preg_replace('#(https?://\S+/sign-in/complete\?token=)(\S+)#i', '$1[redacted]', $body) ?? $body;
        $body = preg_replace('#(https?://\S+/reset\?token=)(\S+)#i', '$1[redacted]', $body) ?? $body;
        $body = preg_replace('#(https?://\S+/shared/)(\S+)#i', '$1[redacted]', $body) ?? $body;
        $body = preg_replace('#(token=)(\S+)#i', '$1[redacted]', $body) ?? $body;
        return Security::redact($body);
    }

    private static function sendSmtp(
        string $to,
        string $subject,
        string $body,
        string $from,
        string $fromName,
        string $password
    ): bool {
        $host = (string) Config::get('mail.host', 'smtp.hostinger.com');
        $port = (int) Config::get('mail.port', 465);
        $user = (string) Config::get('mail.username', $from);
        $encryption = strtolower((string) Config::get('mail.encryption', 'ssl'));
        $remote = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;

        $fp = @stream_socket_client($remote, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
        if (!$fp) {
            throw new \RuntimeException('smtp_connect_failed');
        }
        stream_set_timeout($fp, 20);

        self::expect($fp, 220);
        self::command($fp, 'EHLO localhost', 250);
        self::command($fp, 'AUTH LOGIN', 334);
        self::command($fp, base64_encode($user), 334);
        self::command($fp, base64_encode($password), 235);
        self::command($fp, 'MAIL FROM:<' . $from . '>', 250);
        self::command($fp, 'RCPT TO:<' . $to . '>', 250);
        self::command($fp, 'DATA', 354);

        $headers = [
            'From: ' . sprintf('%s <%s>', $fromName, $from),
            'To: <' . $to . '>',
            'Reply-To: <' . $from . '>',
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Date: ' . gmdate('D, d M Y H:i:s') . ' +0000',
        ];
        $data = implode("\r\n", $headers) . "\r\n\r\n" . str_replace("\n.", "\n..", $body) . "\r\n.";
        fwrite($fp, $data . "\r\n");
        self::expect($fp, 250);
        self::command($fp, 'QUIT', 221);
        fclose($fp);
        return true;
    }

    /** @param resource $fp */
    private static function command($fp, string $command, int $expectCode): void
    {
        fwrite($fp, $command . "\r\n");
        self::expect($fp, $expectCode);
    }

    /** @param resource $fp */
    private static function expect($fp, int $code): void
    {
        $response = '';
        while (($line = fgets($fp, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        if (!str_starts_with($response, (string) $code)) {
            throw new \RuntimeException('smtp_protocol_error');
        }
    }
}
