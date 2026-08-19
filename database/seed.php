<?php

/**
 * Seeder Runner Wrapper
 * Usage: php database/seed.php
 */

$argv[1] = 'seed';
require_once __DIR__ . '/migrate.php';
