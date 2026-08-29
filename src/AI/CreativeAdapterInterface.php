<?php

declare(strict_types=1);

namespace Yatsn\AI;

interface CreativeAdapterInterface
{
    public function name(): string;

    public function isAvailable(): bool;

    /** @param array<string, mixed> $snapshot */
    public function buildPackage(array $snapshot): array;
}
