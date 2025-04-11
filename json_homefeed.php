<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'dbcon.php';
include 'database/pdo_class.php';

function getJson($url)
{
  // cache files are created like cache/abcdef123456...
  $cacheFile = '/var/www/html/cache' . DIRECTORY_SEPARATOR . md5($url);

  if (file_exists($cacheFile)) {
    $fh = fopen($cacheFile, 'r');
    $cacheTime = trim(fgets($fh));
    echo 'cache exists';
    // if data was cached recently, return cached data
    if ($cacheTime > strtotime('-60 minutes')) {
      return fread($fh);
    }

    // else delete cache file
    fclose($fh);
    unlink($cacheFile);
  }

  $json = getJsonData();

  // file_put_contents($file, $json);

  $fh = fopen($cacheFile, 'w');
  echo $fh;

  fwrite($fh, time() . "\n");
  fwrite($fh, $json);
  fclose($fh);

  return $json;
}

function getJsonData()
{

  $setresult = mysql_query('SET @row_number = 0;');
  $result = mysql_query('SELECT now()');
  // $result = mysql_query('SELECT (@row_number:=@row_number + 1) AS secondsago,
// E.id,
// CAST(CONVERT(E.message USING utf8) AS binary) message
// FROM (
// SELECT e.id,
// CASE WHEN `text` LIKE "%attacked you and won%" THEN CONCAT(UF.`username`, " Attacked ", UT.`username`, " and Won")
//   WHEN `text` LIKE "%attacked you and lost%" THEN CONCAT(UF.`username`, " Attacked ", UT.`username`, " and Lost")
//   WHEN `text` LIKE "%You were mugged by%" THEN CONCAT(UT.`username`, " was mugged by ", UF.`username`)
//   WHEN `text` LIKE "%tried to mug you, but failed%" THEN CONCAT(UF.`username`, " tried to mug ", UT.`username`, ", but failed")
//   WHEN `text` LIKE "%You have been busted out of Jail by%" THEN CONCAT(UT.`username`, " was busted out of Jail by ", UF.`username`)
//   WHEN `text` LIKE "%You have been rated%<b>UP%" THEN CONCAT(UT.`username`, " was rated UP")
//   WHEN `text` LIKE "%You have been rated%<b>DOWN%" THEN CONCAT(UT.`username`, " was rated DOWN")
//   WHEN `text` LIKE "%bet%taken%won%" THEN CONCAT(UT.`username`, " won a bet")
//   WHEN `text` LIKE "%bet%taken%lost%" THEN CONCAT(UT.`username`, " lost a bet")
//   WHEN `text` LIKE "%You have just gained a level%" THEN CONCAT(UT.`username`, " just gained a level")
//   WHEN `text` LIKE "%Daily Login Bonus%" THEN CONCAT(UT.`username`, " received a Daily Login Bonus")
//   WHEN `text` LIKE "%You detonated a city bomb%" THEN CONCAT(UT.`username`, " detonated a city bomb")
//   WHEN `text` LIKE "%blew you up%" THEN CONCAT(UT.`username`, " was blown up by ", UF.`username`)
//   ELSE "NADA"
// END `message`
// FROM events e
// JOIN `grpgusers` UT
//    ON UT.`id` = e.`to`
// JOIN `grpgusers` UF
//    ON UF.`id` = e.`extra`
// WHERE TIME_TO_SEC(TIMEDIFF(NOW(), FROM_UNIXTIME(`timesent`))) < 12000
// AND (`text` LIKE "%attacked you and won%"
// OR `text` LIKE "%attacked you and lost%"
// OR `text` LIKE "%You were mugged by%"
// OR `text` LIKE "%tried to mug you, but failed%"
// OR `text` LIKE "%You have been busted out of Jail by%"
// OR `text` LIKE "%You have been rated%<b>UP%"
// OR `text` LIKE "%You have been rated%<b>DOWN%"
// OR `text` LIKE "%bet%taken%won%"
// OR `text` LIKE "%bet%taken%lost%"
// OR `text` LIKE "%You have just gained a level%"
// OR `text` LIKE "%Daily Login Bonus%"
// OR `text` LIKE "%You detonated a city bomb%"
// OR `text` LIKE "%blew you up%")
// GROUP BY e.TO
// ORDER BY RAND()
// LIMIT 10
// ) E;');

  $rows = array();
  $count = 0;
  while ($r = mysql_fetch_assoc($result)) {
    if ($count++ % 2 == 1)
      $rows['messagesLeft'][] = $r;
    else
      $rows['messagesRight'][] = $r;
  }

  // $rows = array_map('utf8_encode', $rows);

  return json_encode($rows);
}

echo getJson('json_homefeed.php');

?>