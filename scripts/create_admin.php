<?php
require __DIR__ . '/../vendor/autoload.php';

function env($key, $default = null) {
    $v = getenv($key);
    if ($v !== false) return $v;
    $envFile = __DIR__ . '/../.env';
    if (!file_exists($envFile)) return $default;
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $val] = explode('=', $line, 2);
        if (trim($k) === $key) return trim($val, " \t\n\r\0\x0B\"'");
    }
    return $default;
}

$user = env('DB_USERNAME');
$pass = env('DB_PASSWORD');
$host = env('DB_HOST');
$dbName = env('DB_DATABASE', 'test');

if (empty($host)) {
    echo "DB_HOST not configured in .env\n";
    exit(1);
}

$dsn = "mongodb+srv://" . ($user ?? '') . ":" . rawurlencode($pass ?? '') . "@" . $host . "/" . $dbName . "?retryWrites=true&w=majority";

echo "Attempting to connect to MongoDB with DSN: $dsn\n";

for ($i = 1; $i <= 5; $i++) {
    try {
        $client = new MongoDB\Client($dsn, [], ['serverSelectionTimeoutMS' => 5000]);
        $db = $client->selectDatabase($dbName);
        $users = $db->users;

        $admin = [
            'name' => 'Administrador TREVSA',
            'email' => 'admin@trevsa.local',
            'password' => '$2y$12$AmhHFNcUzoi.lea5fEtNzOVXgoNg9ldCECYx/ouJXZ3maxTrMAdkG',
            'role' => 'admin',
            'email_verified_at' => null,
            'remember_token' => null,
            'created_at' => new MongoDB\BSON\UTCDateTime((int)(microtime(true)*1000)),
            'updated_at' => new MongoDB\BSON\UTCDateTime((int)(microtime(true)*1000)),
        ];

        $existing = $users->findOne(['email' => $admin['email']]);
        if ($existing) {
            echo "Admin user already exists, updating...\n";
            $users->updateOne(['_id' => $existing['_id']], ['$set' => $admin]);
        } else {
            $users->insertOne($admin);
            echo "Admin user inserted.\n";
        }
        exit(0);
    } catch (Throwable $e) {
        echo "Attempt $i: Exception " . get_class($e) . " - " . $e->getMessage() . "\n";
        if ($i < 5) {
            echo "Retrying in 3s...\n";
            sleep(3);
            continue;
        }
        echo "Failed to insert admin after 5 attempts.\n";
        exit(1);
    }
}
