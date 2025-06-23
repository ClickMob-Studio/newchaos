<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

  $fh = fopen($cacheFile, 'w');
  echo $fh;

  fwrite($fh, time() . "\n");
  fwrite($fh, $json);
  fclose($fh);

  return $json;
}

function getJsonData()
{
  $rows = [
    'messagesRight' => [['now()' => date('Y-m-d H:i:s')]]
  ];
  return json_encode($rows);
}

echo getJson('json_homefeed.php');

?>