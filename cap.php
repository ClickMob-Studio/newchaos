<?php

require_once 'includes/functions.php';

start_session_guarded();

$bgcolor = array(33, 33, 33);
$text = array(255, 255, 255);
$distort = rand(80, 120) / 100;
$distort2 = rand(80, 120) / 100;
$f_x = round(75 * $distort);
$f_y = round(25 * $distort);
$s_x = round(175 * $distort2);
$s_y = round(70 * $distort2);
$first = imagecreatetruecolor($f_x, $f_y);
$second = imagecreatetruecolor($s_x, $s_y);
// Allocate a dark red background color
$background = imagecolorallocate($second, 139, 0, 0); // Dark red

// Fill the background with the dark red color
imagefill($second, 0, 0, $background);

$white = imagecolorallocate($first, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
$twhite = imagecolorallocate($first, 255, 255, 255);
$black = imagecolorallocate($first, 0, 0, 0);
$red = imagecolorallocate($first, 255, 0, 0);
$green = imagecolorallocate($first, 0, 128, 0);
$blue = imagecolorallocate($first, 0, 0, 255);
imagefill($first, 0, 0, $white);
$color[0] = $red;
$color[1] = $green;
$color[2] = $blue;
for ($i = 0; $i <= 2; $i++) {
    $points = array(
        10,
        $f_x - 10,
        5,
        $f_y - 5,
        10,
        $f_x - 10,
        5,
        $f_y - 5,
        10,
        $f_x - 10,
        5,
        $f_y - 5,
        10,
        $f_x - 10,
        5,
        $f_y - 5,
        10,
        $f_x - 10,
        5,
        $f_y - 5,
    );
    imagefilledpolygon($first, $points, $red);
}

if (!isset($_SESSION['cap'])) {
    diefun("CAPTCHA not set");
}

imagestring($first, 4, rand(0, (int) ($f_x / 3)), rand(0, (int) ($f_y / 2.5)), $_SESSION['cap'], $twhite);
imagecopyresized($second, $first, 0, 0, 0, 0, $s_x, $s_y, $f_x, $f_y);
imagedestroy($first);
$red = imagecolorallocate($second, 255, 0, 0);
$green = imagecolorallocate($second, 0, 128, 0);
$blue = imagecolorallocate($second, 0, 0, 255);
$RandomPixels = 0;
for ($i = 0; $i < $RandomPixels; $i++) {
    $locx = rand(0, $s_x - 1);
    $locy = rand(0, $s_y - 1);
    imagesetpixel($second, $locx, $locy, $red);
}
for ($i = 0; $i < $RandomPixels; $i++) {
    $locx = rand(0, $s_x - 1);
    $locy = rand(0, $s_y - 1);
    imagesetpixel($second, $locx, $locy, $green);
}
for ($i = 0; $i < $RandomPixels; $i++) {
    $locx = rand(0, $s_x - 1);
    $locy = rand(0, $s_y - 1);
    imagesetpixel($second, $locx, $locy, $blue);
}
$randcolor = imagecolorallocate($second, rand(100, 255), rand(100, 255), rand(100, 255));
for ($i = 0; $i < 5; $i++) {
    imageline($second, rand(0, $s_x), rand(0, $s_y), rand(0, $s_x), rand(0, $s_y), $randcolor);
    $randcolor = imagecolorallocate($second, rand(100, 255), rand(100, 255), rand(100, 255));
}
@header("Content-Type: image/png");
$finished = imagerotate($second, rand(0, 15) - 7.5, $bgcolor[2] * 65536 + $bgcolor[1] * 256 + $bgcolor[0]);
imagedestroy($second);
imagepng($finished);
imagedestroy($finished);
?>