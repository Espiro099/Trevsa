<?php
require __DIR__ . '/../vendor/autoload.php';
$envFile = __DIR__ . '/../.env';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (!str_contains($line, '=')) continue;
    [$k,$v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
}
$dsn = $env['MONGO_DSN'] ?? null;
if (!$dsn) {
    echo "MONGO_DSN not set\n"; exit(1);
}
$client = new MongoDB\Client($dsn, [], ['serverSelectionTimeoutMS' => 5000]);
$dbName = $env['DB_DATABASE'] ?? 'logistica_trevsa';
$db = $client->selectDatabase($dbName);
$cols = $db->listCollections();
$results = [];
foreach ($cols as $c) {
    $name = $c->getName();
    $count = $db->selectCollection($name)->countDocuments();
    $results[$name] = $count;
}
ksort($results);
foreach ($results as $k=>$v) {
    echo str_pad($k, 30) . " : " . $v . "\n";
}
