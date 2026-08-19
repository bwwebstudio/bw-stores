<?php

/**
 * Migration: Update stores status ENUM to include pending and maintenance
 */

return [
    'up' => "
        ALTER TABLE stores MODIFY COLUMN status ENUM('active', 'pending', 'suspended', 'maintenance', 'inactive') NOT NULL DEFAULT 'active';
    ",
    'down' => "
        ALTER TABLE stores MODIFY COLUMN status ENUM('active', 'suspended', 'maintenance') NOT NULL DEFAULT 'active';
    ",
];
