<?php

declare(strict_types=1);

namespace Yatsn\Support;

final class Audit
{
    public static function record(
        ?int $actorUserId,
        string $action,
        ?string $subjectType = null,
        ?string $subjectId = null,
        ?string $reason = null,
        array $meta = []
    ): void {
        Database::exec(
            'INSERT INTO audit_events (public_id, actor_user_id, action, subject_type, subject_id, reason, meta_json, created_at)
             VALUES (:pid, :actor, :action, :stype, :sid, :reason, :meta, :created)',
            [
                'pid' => opaque_id(),
                'actor' => $actorUserId,
                'action' => $action,
                'stype' => $subjectType,
                'sid' => $subjectId,
                'reason' => $reason,
                'meta' => $meta === [] ? null : json_encode($meta, JSON_THROW_ON_ERROR),
                'created' => now_utc(),
            ]
        );
    }
}
