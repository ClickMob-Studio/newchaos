<?php include("header.php");
error_reporting(E_ERROR);
if (isset($_POST['go'])) {
  if (isset($_POST['textfield'])) {
    $type = "URL";
  } elseif (isset($_POST['textarea'])) {
    $type = "list";
  } else {
    echo Message("No URL/List given!");
  }
  if (isset($type)) {
    if ($type == "URL") {
      echo ("<table border='0'><tr><td class='contentcontent'>");
      $fp = fopen($_POST['textfield'], "r");
      $ips = fgets($fp);
      $ips = explode("\n", $ips);
      $x = count($ips);
      for ($i = 0; $i < $x; $i++) {
        $db->query("SELECT * FROM `grpgusers` WHERE `ip` = ? OR `ip1` = ? OR `ip2` = ? OR `ip3` = ? OR `ip4` = ? OR `ip5` = ?");
        $db->execute([$ips[$i], $ips[$i], $ips[$i], $ips[$i], $ips[$i], $ips[$i]]);
        $results = $db->fetch_row();
        foreach ($results as $worked) {
          echo ($worked['username'] . "<br>");
          $n++;
        }
      }
    } else {
      $ips = explode("\n", $_POST['textarea']);
      $x = count($ips);
      for ($i = 0; $i < $x; $i++) {
        $db->query("SELECT * FROM `grpgusers` WHERE `ip` = ? OR `ip1` = ? OR `ip2` = ? OR `ip3` = ? OR `ip4` = ? OR `ip5` = ?");
        $db->execute([$ips[$i], $ips[$i], $ips[$i], $ips[$i], $ips[$i], $ips[$i]]);
        $results = $db->fetch_row();
        foreach ($results as $worked) {
          echo ($worked['username'] . "<br>");
          $n++;
        }
      }
    }
  }
}
echo ("Number: " . $n . "</tr></td>"); ?>
<table width="100%" border="0">
  <tr>
    <td class="contenthead">IP Checker</td>
  <tr>
    <td class="contentspacer"></td>
  </tr>
  <tr>
    <td class="contentcontent">
      <form id="form1" name="form1" method="post" action="">

        Newline seperated list URL
        <input type="text" name="textfield" id="textfield" />
        <br />
        OR<br />
        Newline seperated list paste<br />
        <textarea name="textarea" id="textarea" cols="45" rows="5"></textarea>
        <br />
        <input type="submit" name="go" id="go" value="Submit" />
      </form>
  </tr>
</table>