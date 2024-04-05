<?php
if($_GET['key'] != 'cron94'){
    die();
  }
$dbHost     = 'localhost';
$dbUsername = 'chaoscit_user';
$dbPassword = '3lrKBlrfMGl2ic14';
$dbName     = 'chaoscit_game';

// Backup directory
$backupDir = '/home/chaoscit/backup';
// Number of days to keep backup files
$fileRetentionDays = 7;

// Set the timezone
date_default_timezone_set('Your/Timezone');

// Generate a filename for the backup file based on the current date and time
$backupFile = $backupDir . '/db-backup-' . date("Y-m-d-H-i-s") . '.sql';

// Command to perform backup
$command = "mysqldump --opt -h $dbHost -u $dbUsername -p$dbPassword $dbName > $backupFile";

// Execute the command
system($command, $output);

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
