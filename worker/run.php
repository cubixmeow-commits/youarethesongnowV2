#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Generation worker entrypoint for Hostinger Cron Jobs / local use.
 * Schedule: every minute. Exits after one bounded unit of work.
 */

require dirname(__DIR__) . '/app/bootstrap.php';

use Yatsn\Generation\GenerationJobService;
use Yatsn\Support\Config;
use Yatsn\Support\Migrator;

Config::ensureDirectories();
Migrator::migrate();

$result = GenerationJobService::processNext();
$line = sprintf("[%s] %s\n", gmdate('c'), json_encode($result, JSON_UNESCAPED_SLASHES));
file_put_contents(Config::get('paths.log') . '/worker.log', $line, FILE_APPEND | LOCK_EX);
echo $line;
