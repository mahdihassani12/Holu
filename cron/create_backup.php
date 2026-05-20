<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

require_once __DIR__ . '/../lib/_db.php';

$backupDir = __DIR__ . '/../backups';
$projectDir = realpath(__DIR__ . '/..');

if (!is_dir($backupDir)) {
    die('Backups folder not found');
}

if (!is_writable($backupDir)) {
    die('Backups folder is not writable');
}

/**
 * Save backup record.
 * It reconnects to DB each time to avoid "MySQL server has gone away".
 */
function saveBackupRecord($type, $filename, $path)
{
    require __DIR__ . '/../lib/_db.php';

    $size = file_exists($path) ? filesize($path) : 0;

    $stmt = $db->prepare("
        INSERT INTO backups (type, filename, path, size, status, created_at)
        VALUES (:type, :filename, :path, :size, 'completed', NOW())
    ");

    $stmt->execute([
        ':type' => $type,
        ':filename' => $filename,
        ':path' => $path,
        ':size' => $size
    ]);
}

/**
 * 1. Database backup
 */
$dbFilename = 'database_' . date('Y-m-d_H-i-s') . '.sql';
$dbPath = $backupDir . '/' . $dbFilename;

$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

$sql = "-- Database Backup\n";
$sql .= "-- Created at: " . date('Y-m-d H:i:s') . "\n\n";
$sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    $createTable = $db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);

    $sql .= "\n\nDROP TABLE IF EXISTS `$table`;\n";
    $sql .= $createTable['Create Table'] . ";\n\n";

    $rows = $db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $values = array_map(function ($value) use ($db) {
            return $value === null ? "NULL" : $db->quote($value);
        }, array_values($row));

        $sql .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
    }
}

$sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents($dbPath, $sql);

saveBackupRecord('database', $dbFilename, $dbPath);

/**
 * 2. Files backup
 */
$filesFilename = 'files_' . date('Y-m-d_H-i-s') . '.zip';
$filesPath = $backupDir . '/' . $filesFilename;

$zip = new ZipArchive();

if ($zip->open($filesPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die('Could not create files backup ZIP');
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($projectDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$backupRealPath = realpath($backupDir);

foreach ($files as $file) {
    if (!$file->isFile()) {
        continue;
    }

    $filePath = $file->getRealPath();

    // Do not backup the backups folder itself
    if ($backupRealPath && strpos($filePath, $backupRealPath) === 0) {
        continue;
    }

    // Do not backup cron backup script itself if needed? Keep it included for now.

    $relativePath = substr($filePath, strlen($projectDir) + 1);
    $zip->addFile($filePath, $relativePath);
}

$zip->close();

saveBackupRecord('files', $filesFilename, $filesPath);

echo 'Database and files backup created successfully';