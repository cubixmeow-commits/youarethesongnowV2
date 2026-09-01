<?php

declare(strict_types=1);

namespace Yatsn\Support;

/**
 * Safe build identity for private Hostinger testing.
 * Prefers the checked-out Git revision (owner sync under /yatsnV2) and falls back
 * to a committed stamp so FTP-only copies still expose a revision.
 * Never includes secrets, env values, paths under var/, or provider details.
 */
final class BuildInfo
{
    /** @return array{commit:?string,commitFull:?string,source:string,privateBuild:bool} */
    public static function summary(): array
    {
        $resolved = self::resolveCommit();
        return [
            'commit' => $resolved['short'],
            'commitFull' => $resolved['full'],
            'source' => $resolved['source'],
            'privateBuild' => self::isPrivateBuild(),
        ];
    }

    /**
     * Public health / UI payload. Omits commit details once external access is enabled.
     *
     * @return array<string,mixed>
     */
    public static function publicSummary(): array
    {
        $summary = self::summary();
        if (!$summary['privateBuild']) {
            return ['privateBuild' => false];
        }
        return $summary;
    }

    public static function isPrivateBuild(): bool
    {
        // Private Development Build 1 keeps external users gated. This is already
        // false on Hostinger and does not require APP_ENV / APP_DEBUG changes.
        return !Config::getBool('gates.allow_external_users');
    }

    public static function allowDiagnostics(): bool
    {
        return Config::getBool('app.debug')
            || (string) Config::get('app.env', 'production') === 'development'
            || self::isPrivateBuild();
    }

    /**
     * Private-development component lab. Owner-only while external users remain gated.
     * Unreachable once ALLOW_EXTERNAL_USERS is enabled.
     */
    public static function allowComponentLab(?array $session): bool
    {
        if (!self::isPrivateBuild()) {
            return false;
        }
        return ($session['role'] ?? '') === 'owner';
    }

    /** @return array{short:?string,full:?string,source:string} */
    private static function resolveCommit(): array
    {
        $fromGit = self::fromGitCheckout();
        if ($fromGit['full'] !== null) {
            return $fromGit;
        }
        return self::fromCommittedStamp();
    }

    /** @return array{short:?string,full:?string,source:string} */
    private static function fromGitCheckout(): array
    {
        $root = Config::root();
        if ($root === '' || !is_dir($root . '/.git')) {
            return ['short' => null, 'full' => null, 'source' => 'unavailable'];
        }

        $gitDir = $root . '/.git';
        $head = @file_get_contents($gitDir . '/HEAD');
        if (!is_string($head)) {
            return ['short' => null, 'full' => null, 'source' => 'unavailable'];
        }
        $head = trim($head);
        $full = null;
        if (str_starts_with($head, 'ref: ')) {
            $ref = trim(substr($head, 5));
            if ($ref !== '' && !str_contains($ref, '..') && is_file($gitDir . '/' . $ref)) {
                $full = trim((string) @file_get_contents($gitDir . '/' . $ref));
            }
            if (($full === null || $full === '') && is_file($gitDir . '/packed-refs')) {
                $packed = (string) @file_get_contents($gitDir . '/packed-refs');
                foreach (preg_split("/\r\n|\n|\r/", $packed) ?: [] as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, '^')) {
                        continue;
                    }
                    if (preg_match('/^([0-9a-f]{40})\s+' . preg_quote($ref, '/') . '$/', $line, $m) === 1) {
                        $full = $m[1];
                        break;
                    }
                }
            }
        } elseif (preg_match('/^[0-9a-f]{40}$/', $head) === 1) {
            $full = $head;
        }

        if (!is_string($full) || preg_match('/^[0-9a-f]{40}$/', $full) !== 1) {
            return ['short' => null, 'full' => null, 'source' => 'unavailable'];
        }

        return [
            'short' => substr($full, 0, 12),
            'full' => $full,
            'source' => 'git',
        ];
    }

    /** @return array{short:?string,full:?string,source:string} */
    private static function fromCommittedStamp(): array
    {
        $stampFile = Config::root() . '/app/build-stamp.php';
        if (!is_file($stampFile)) {
            return ['short' => null, 'full' => null, 'source' => 'unavailable'];
        }
        /** @var mixed $stamp */
        $stamp = require $stampFile;
        if (!is_array($stamp)) {
            return ['short' => null, 'full' => null, 'source' => 'unavailable'];
        }
        $full = strtolower(trim((string) ($stamp['commit'] ?? '')));
        if (preg_match('/^[0-9a-f]{7,40}$/', $full) !== 1) {
            return ['short' => null, 'full' => null, 'source' => 'unavailable'];
        }
        if (strlen($full) < 40) {
            // Short stamp only — still useful for iPhone verification.
            return [
                'short' => substr($full, 0, 12),
                'full' => null,
                'source' => 'stamp',
            ];
        }
        return [
            'short' => substr($full, 0, 12),
            'full' => $full,
            'source' => 'stamp',
        ];
    }
}
