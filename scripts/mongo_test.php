<?php
require __DIR__ . '/../vendor/autoload.php';

// Try to get from environment first, otherwise parse .env
$user = getenv('DB_USERNAME') ?: '';
$pass = getenv('DB_PASSWORD') ?: '';
$host = getenv('DB_HOST') ?: '';
$dbName = getenv('DB_DATABASE') ?: '';

if (empty($user) || empty($host) || empty($dbName)) {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (!str_contains($line, '=')) continue;
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k); $v = trim($v);
            $v = trim($v, "\"'");
            if ($k === 'DB_USERNAME' && empty($user)) $user = $v;
            if ($k === 'DB_PASSWORD' && empty($pass)) $pass = $v;
            if ($k === 'DB_HOST' && empty($host)) $host = $v;
            if ($k === 'DB_DATABASE' && empty($dbName)) $dbName = $v;
        }
    }
}

if (empty($dbName)) $dbName = 'test';

$dsn = "mongodb+srv://" . $user . ":" . rawurlencode($pass) . "@" . $host . "/" . $dbName . "?retryWrites=true&w=majority";
echo "Using DSN: $dsn\n";

try {
    $client = new MongoDB\Client($dsn, [], ['connectTimeoutMS' => 5000, 'serverSelectionTimeoutMS' => 5000]);
    $db = $client->selectDatabase($dbName);
    $cols = $db->listCollections();
    echo "Collections: ";
    foreach ($cols as $c) { echo $c->getName() . ' '; }
    echo PHP_EOL;
    echo "Connected OK\n";
} catch (Throwable $e) {
    echo "Exception (" . get_class($e) . "): " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
