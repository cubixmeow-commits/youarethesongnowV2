<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$database = first_ruck_database();
$trailCount = (int) $database->query('SELECT COUNT(*) FROM trails')->fetchColumn();

fwrite(STDOUT, sprintf("First Ruck is ready. %d demonstration routes loaded.\n", $trailCount));

