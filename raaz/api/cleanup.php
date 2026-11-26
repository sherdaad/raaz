<?php
// Run via CLI cron every minute: php /path/to/raaz/api/cleanup.php
require __DIR__.'/../config/config.php';
$pdo->exec("DELETE FROM messages WHERE expires_at IS NOT NULL AND expires_at <= NOW()");
$pdo->exec("DELETE FROM statuses WHERE expires_at <= NOW()");
echo "RAAZ cleanup complete\n";
