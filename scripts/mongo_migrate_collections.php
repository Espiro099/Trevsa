<?php
require __DIR__ . '/../vendor/autoload.php';

$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    echo ".env not found\n"; exit(1);
}
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (!str_contains($line, '=')) continue;
    [$k,$v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
}
$dsn = $env['MONGO_DSN'] ?? null;
$dbName = $env['DB_DATABASE'] ?? null;
if (!$dsn || !$dbName) {
    echo "MONGO_DSN or DB_DATABASE not set in .env\n"; exit(1);
}

$client = new MongoDB\Client($dsn, [], ['serverSelectionTimeoutMS' => 5000]);
$db = $client->selectDatabase($dbName);

// Mappings: source => target
$mappings = [
    'tarifas_trevsas' => 'tarifas_trevsa',
    'transportistas_invs' => 'transportistas_inv',
];

$timestamp = date('Ymd_His');

foreach ($mappings as $src => $dst) {
    echo "\nProcessing mapping: $src -> $dst\n";
    $srcExists = false;
    foreach ($db->listCollections() as $c) { if ($c->getName() === $src) { $srcExists = true; break; } }
    if (!$srcExists) { echo "Source collection $src not found, skipping.\n"; continue; }

    $srcCol = $db->selectCollection($src);
    $dstCol = $db->selectCollection($dst);

    $count = $srcCol->countDocuments();
    echo "Source count: $count\n";

    $backupName = $src . '_backup_' . $timestamp;
    echo "Creating backup collection: $backupName\n";

    // Create backup by copying documents in batches
    $backupCol = $db->selectCollection($backupName);

    $batchSize = 500;
    $cursor = $srcCol->find();
    $batch = [];
    $copied = 0;
    foreach ($cursor as $doc) {
        $batch[] = $doc;
        if (count($batch) >= $batchSize) {
            $backupCol->insertMany($batch, ['ordered' => false]);
            $copied += count($batch);
            $batch = [];
        }
    }
    if (count($batch) > 0) {
        $backupCol->insertMany($batch, ['ordered' => false]);
        $copied += count($batch);
    }
    echo "Copied $copied documents to backup $backupName\n";

    // Now upsert into destination
    echo "Upserting into destination $dst...\n";
    $cursor = $srcCol->find();
    $upserted = 0;
    foreach ($cursor as $doc) {
        // use _id as identifier
        $id = $doc->_id;
        try {
            $dstCol->replaceOne(['_id' => $id], $doc, ['upsert' => true]);
            $upserted++;
        } catch (Exception $e) {
            echo "Upsert error for _id={$id}: " . $e->getMessage() . "\n";
        }
    }
    echo "Upserted $upserted documents into $dst\n";

    // After successful backup+upsert, drop source collection
    echo "Dropping source collection $src...\n";
    $db->dropCollection($src);
    echo "Dropped $src. Backup remains as $backupName.\n";
}

// Additionally, handle empty or obsolete collections to backup and drop
$obsolete = ['transportistas'];
foreach ($obsolete as $colname) {
    $exists = false;
    foreach ($db->listCollections() as $c) { if ($c->getName() === $colname) { $exists = true; break; } }
    if (!$exists) { continue; }
    $count = $db->selectCollection($colname)->countDocuments();
    echo "\nFound obsolete collection $colname with $count docs. Backing up and dropping...\n";
    $backupName = $colname . '_backup_' . $timestamp;
    if ($count === 0) {
        // create empty backup collection
        echo "Collection is empty, creating empty backup $backupName\n";
        $db->createCollection($backupName);
    } else {
        // copy in batches
        $backupCol = $db->selectCollection($backupName);
        $cursor = $db->selectCollection($colname)->find();
        $batch = [];
        $batchSize = 500;
        foreach ($cursor as $doc) {
            $batch[] = $doc;
            if (count($batch) >= $batchSize) {
                $backupCol->insertMany($batch, ['ordered' => false]);
                $batch = [];
            }
        }
        if (count($batch) > 0) {
            $backupCol->insertMany($batch, ['ordered' => false]);
        }
    }
    $db->dropCollection($colname);
    echo "Backed up to $backupName and dropped $colname\n";
}

echo "\nMigration complete. Current collection counts:\n";
// Run counts
$cols = $db->listCollections();
foreach ($cols as $c) {
    $name = $c->getName();
    $cnt = $db->selectCollection($name)->countDocuments();
    echo str_pad($name, 30) . " : " . $cnt . "\n";
}
