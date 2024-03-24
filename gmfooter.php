<script>
 $(".genBars").html("<?php genBars(); ?>");
</script>
<table class="top" width="100%">
 <tr>
  <td align="center" valign="bottom">
<br><br>
<a href='citizens.php'><font color="A4ACFF"><?php echo $stats->playerstotal; ?> <font color="A4ACFF">Total Yobsters</a>&nbsp; | &nbsp;
<a href='online.php'><font color="A4ACFF"><?php echo $stats->playersloggedin; ?> <font color="A4ACFF">Yobsters Online</a> &nbsp; | &nbsp;
<a href='24hour.php'><font color="A4ACFF"><?php echo $stats->playersonlineinlastday; ?> <font color="A4ACFF">Yobsters Online (24 Hours)</a> &nbsp; <br /> <br />
Page execution time in seconds: <?php print $time=(microtime(true) - $starttime); ?><br /><br />
&copy; Game Copyright 2015 - YobCity@hotmail.com <br /> <br />
</td>
 </tr>
 </table>
</center>
</body>
</html>