<?php
if ($_GET['key'] != 'cron94') {
    die();
}

require_once 'config.php';
include_once 'dbcon.php';
include_once 'database/pdo_class.php';

// Backup directory
$backupDir = '/home/chaoscit/backup';
// Number of days to keep backup files
$fileRetentionDays = 7;

// Set the timezone
date_default_timezone_set('Your/Timezone');

// Generate a filename for the backup file based on the current date and time
$backupFile = $backupDir . '/db-backup-' . date("Y-m-d-H-i-s") . '.sql';

// Tables to get only the structure
$structureOnlyTables = ['events', 'raid_battle_logs', 'cityevents', 'attacklog', 'attlog'];

// Create a temporary file to store the mysqldump commands
$tempFile = tempnam(sys_get_temp_dir(), 'mysqldump');

// Open the temporary file for writing
$tempFileHandle = fopen($tempFile, 'w');

$dbConfig = Config::db();
// Write the command to get the structure only for the specified tables
foreach ($structureOnlyTables as $table) {
    fwrite($tempFileHandle, "mysqldump --opt -h " . $dbConfig->host . " -u " . $dbConfig->username . " -p" . $dbConfig->password . " --no-data " . $dbConfig->database . " $table >> $backupFile\n");
}

// Write the command to get the data for all tables except the specified ones
fwrite($tempFileHandle, "mysqldump --opt -h " . $dbConfig->host . " -u " . $dbConfig->username . " -p" . $dbConfig->password . " --ignore-table=" . $dbConfig->database . "." . implode(" --ignore-table=" . $dbConfig->database . ".", $structureOnlyTables) . " " . $dbConfig->database . " >> $backupFile\n");

// Close the temporary file
fclose($tempFileHandle);

// Execute the commands
system("sh $tempFile", $output);

// Delete the temporary file
unlink($tempFile);

// Delete files older than $fileRetentionDays
if ($handle = opendir($backupDir)) {
    while (false !== ($file = readdir($handle))) {
        $fileLastModified = filemtime($backupDir . '/' . $file);
        // If file older than retention period and is a SQL file, delete it
        if ((time() - $fileLastModified > $fileRetentionDays * 24 * 60 * 60) && preg_match('/\.sql$/', $file)) {
            unlink($backupDir . '/' . $file);
        }
    }
    closedir($handle);
}
?>