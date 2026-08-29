<?php

declare(strict_types=1);

namespace Yatsn\AI;

interface ImageAdapterInterface
{
    public function name(): string;

    public function isAvailable(): bool;

    /**
     * @param array<string, mixed> $package
     * @param array<string, mixed> $snapshot
     * @return array{adapter:string,mime:string,width:int,height:int,bytes:string,costCents:int}
     */
    public function generate(array $package, array $snapshot): array;
}
