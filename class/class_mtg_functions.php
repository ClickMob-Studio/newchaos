<?php
define('IMAGE_URL', 'https://chaoscity.co.uk/img');
class mtg_functions
{
	function format($str, $dec = 0)
	{
		return is_numeric($str) ? number_format($str, $dec) : stripslashes(strip_tags($str, "<p><a><ul><ol><li>"));
	}
	function error($msg)
	{
		echo "<div class='error'><strong>ERROR:</strong><br />", $msg, "<br />
			<a onclick='window.history.go(-1);' style='cursor:pointer;'>Back</a> &middot; <a href='index.php' style='color:#FFF;'>Home</a></div>";
		include(__DIR__ . '/footer.php');
		exit;
	}

	function success($msg)
	{
		echo "<div class='success'><strong>SUCCESS:</strong><br />", $msg, "<br />
			<a onclick='window.history.go(-1);' style='cursor:pointer;'>Back</a> &middot; <a href='index.php' style='color:#FFF;'>Home</a></div>";
	}

	function info($msg)
	{
		echo "<div class='info'><strong>INFORMATION:</strong><br />", $msg, "</div>";
	}

	function warning($msg)
	{
		echo "<div class='warning'><strong>WARNING:</strong><br />", $msg, "</div>";
	}
	function s($num)
	{
		return $num == 1 ? '' : 's';
	}

	function username($id)
	{
		if (!$id)
			return 'Unknown';
		$user = new User($id);
		if (!$user->id)
			return 'Unknown';
		return $user->formattedname;
	}

	function _ip()
	{
		return $_SERVER['REMOTE_ADDR'];
	}

	function time_format($seconds, $mode = "short")
	{
		$names = array('long' => array('year', 'month', 'day', 'hour', 'minute', 'second'), 'short' => array('yr', 'mnth', 'day', 'hr', 'min', 'sec'));

		$seconds = floor($seconds);

		$minutes = intval($seconds / 60);
		$seconds -= ($minutes * 60);

		$hours = intval($minutes / 60);
		$minutes -= ($hours * 60);

		$days = intval($hours / 24);
		$hours -= ($days * 24);

		$months = intval($days / 31);
		$days -= ($months * 31);

		$years = intval($months / 12);
		$months -= ($years * 12);

		$result = array();
		if ($years)
			$result[] = sprintf("%s %s%s", number_format($years), $names[$mode][0], $years == 1 ? "" : "s");
		if ($months)
			$result[] = sprintf("%s %s%s", number_format($months), $names[$mode][1], $months == 1 ? "" : "s");
		if ($days)
			$result[] = sprintf("%s %s%s", number_format($days), $names[$mode][2], $days == 1 ? "" : "s");
		if ($hours)
			$result[] = sprintf("%s %s%s", number_format($hours), $names[$mode][3], $hours == 1 ? "" : "s");
		if ($minutes && count($result) < 2)
			$result[] = sprintf("%s %s%s", number_format($minutes), $names[$mode][4], $minutes == 1 ? "" : "s");
		if (($seconds && count($result) < 2) || !count($result))
			$result[] = sprintf("%s %s%s", number_format($seconds), $names[$mode][5], $seconds == 1 ? "" : "s");

		return implode(", ", $result);
	}

	function sentence_case($str)
	{
		$cap = true;
		$ret = '';
		for ($x = 0; $x < strlen($str); $x++) {
			$letter = substr($str, $x, 1);
			if ($letter == "." || $letter == "!" || $letter == "?") {
				$cap = true;
			} elseif ($letter != " " && $cap == true) {
				$letter = strtoupper($letter);
				$cap = false;
			}
			$ret .= $letter;
		}
		return $ret;
	}

	function stripBBCode($text_to_search)
	{
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
		$replace = '';
		return preg_replace($pattern, $replace, $text_to_search);
	}

	function fuzzehCrypt($pass)
	{
		return crypt($pass, '$6$rounds=5000$awrgwrnuBUIEF89243t89bNFAEb942$');
	}
}
$mtg = new mtg_functions;