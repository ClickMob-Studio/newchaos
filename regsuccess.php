<?php
error_reporting(0);
include 'dbcon.php';
include 'classes.php';
include 'colourgradient.class.php';
?>
<script src="landing/bg.js" type="text/javascript"></script>
<script type="text/javascript">
 if (window.addEventListener) {
  window.addEventListener("resize", function() { alignBg(); }, false);
  window.addEventListener("load", function() { alignBg();getme(); }, false);
 } else if (window.attachEvent) {
  window.attachEvent("onresize",function() { alignBg(); });
  window.attachEvent("onload",function() { alignBg();getme(); });
 } else {
  window.resize = function () { alignBg(); }
  window.onload = function () { alignBg();getme(); }
 }
</script>
<script language="JavaScript">
var usr;
var pw;
var sv;
function getme()
{
usr = document.login.username;
pw = document.login.password;
sv = document.login.save;
if (GetCookie('player') != null)
{
usr.value = GetCookie('username')
pw.value = GetCookie('password')
if (GetCookie('save') == 'true')
{
sv[0].checked = true;
}
}
}
   var codefield;
            // Wait for the document to load, then call the clear function.
            window.addEventListener('load', function() {
                // Get the fields we want to clear.
                codefield = document.getElementById('code');
                
                // Clear the fields.
                codefield.value = '';
                
                // Clear the fields again after a very short period of time, in case the auto-complete is delayed.
                setTimeout(function() { codefield.value = ''; }, 50);
                setTimeout(function() { codefield.value = ''; }, 100);
            }, false);
function saveme()
{
if (usr.value.length != 0 && pw.value.length != 0)
{
if (sv[0].checked)
{
expdate = new Date();
expdate.setTime(expdate.getTime()+(365  24  60  60  1000));
SetCookie('username', usr.value, expdate);
SetCookie('password', pw.value, expdate);
SetCookie('save', 'true', expdate);
}
if (sv[1].checked)
{
DeleteCookie('username');
DeleteCookie('password');
DeleteCookie('save');
}
}
else
{
alert('You must enter your username/password.');
return false;
}
}
</script>
<?php
$stats = new User_Stats("bang");
?>
<html>
<head>
<meta charset="UTF-8">
<title>TheMafiaLife</title>
<meta name="keywords" content="mafia, TheMafiaLife, Thug Style Game, online mmorpg, text rpg, text mmorpg ,free online game, mccodes , mafia games , shooting , weapons ,torn, torn city, torncity, adventure rpg game, rpg multiplayer games, rpg computer games, rpg adventure games, rpg game,rpg, online rpg games, game online rpg, rpg games, rpg pc games, play rpg games online, fantasy rpg game, online adventure role playing game, massively multiplayer, EternalDuel, massive online adventure games, online adventure games, play adventure games, play role playing games, planetarion, java online games, online game browser games, adventure games, the mob online game, a gangsta game, cool gangster games, mafia gangster games, play gangster games, gangster rpg game, online gangster rpg games, gangsters online game, mob game, gangsta rpg games, gangster war game, crime solving game, online crime games, online crime solving games, crime games, online police games, criminal games, crime game, online police game, online crime game, criminal game, crime solving games, crime pc games, internet role playing, massive multiplayer online role playing game, on line role playing games, massive multiplayer online role playing games, role playing websites, play role playing games, role playing sites, computer role playing games, massively multiplayer online role playing games, online role playing game, multiplayer online role playing games, online roleplaying, web browser game, online web games, web game, Online web game, web games, RPG web game, multiplayer web game, web rpg game, browser web game, text web game, textbased web game, mmorpg games, mmorpg game, MMORPG, online text game, text based online games, text based game, online text games, text based online game, text game, a online multiplayer game, all online multiplayer games, best online multiplayer game, best online multiplayer games, cool online multiplayer games, fun multiplayer online game, fun online multiplayer games, good online multiplayer games, list of online multiplayer games, masive multiplayer online games, mass multiplayer online games, massive multi player online games" />
<meta name="copyright" content="Copyright 2021 TheMafiaLife.com" />
     <link rel="stylesheet" type="text/css" href="landing/landing.css">    
    <style>
body {
        background-image: url("landing/background1.jpg");
} 
 
</style>
</head>
<body>
    <img src='landing/footer.png' id='floatFooter' />
    <div id="topContent">
<center> 
<div id='main'>
        <div id="banner">
        </div>
 
  
<div id='mainlogin'>
<table width='100%' border="0" cellpadding="0" cellspacing="0" >
<br>
<!-- Begin Main Content -->
<center><table  width=100%><tr>
<td> <td>
<img src="landing/ld.png">
</td>
<td align='right' valign='middle'>
 <form action='login.php' method='post' id='login' onsubmit='return saveme();'>
<table border='0' cellpadding='0' cellspacing='0'>
<tr><META http-equiv='refresh' content='0;URL=index.php'>
</tr>  
<tr>
    <td align='right'><b>Username:</b></td>
    <td colspan='2'><input type='text' name='username' /></td>
  </tr>
  <tr>
    <td align='right'><b>Password:</b></td>
    <td colspan='2'><input type='password' name='password' /></td>
  </tr>
  <tr>
    <td align='right'><b>Captcha:</b></td>
            <td align='right' colspan='2'><img src="captcha.php" /><input name="captcha" type="text" style="width: 70px;"></td>
    </td>
  </tr>
  <tr>
    <td colspan='3' align='right'>
      <input type='submit' id='submit' value='Login' />
    </td>
  </tr>
</table>
</form>
<a  href="passrecover.php"><font color='white'>Forgotten your password?</a><BR>
<a href='confirmation.php?action=sndconfirm'>Didn't receive confirmation?</a><br />
<a href="Register.php"><img src="landing/reg1.png" onMouseOver="this.src='landing/reg2.png'" onMouseOut="this.src='landing/reg1.png'" /></a>
</td></tr></center>
</table>
<table class='contentcontent' width='100%'><tr><td>
<tr><th width=50%>
Summary
</th>
<th>
Screen Shots</th>
</tr>
<tr>
<td><div  class='about'>
:. Free to play MMORPG<BR><BR>
:. No download required<BR><BR>
:. Work with or against others, <BR>&nbsp;&nbsp;&nbsp; compete to get to the top <BR><BR>
:. Click on the screenshots (right) for bigger image
</td>
<td >
<table>
	<tr>
		<td>
			<img src='landing/explore1.PNG'  width=200 onClick="javascript:window.open( 'images/explore1.PNG', '60', 'left = 20, top = 20, width = 900, height = 600, toolbar = 0, resizable = 0, scrollbars=1' )" />
		</td>
		<td>
			<img src='landing/valore1.PNG'  width=200 onClick="javascript:window.open( 'images/valore1.PNG', '60', 'left = 20, top = 20, width = 900, height = 600, toolbar = 0, resizable = 0, scrollbars=1' )" />
		</td>
	</tr>
	<tr>
		<td>
			<img src='landing/slots1.PNG'  width=200 onClick="javascript:window.open( 'images/slots1.PNG', '60', 'left = 20, top = 20, width = 900, height = 600, toolbar = 0, resizable = 0, scrollbars=1' )" />
		</td>
		<td>
			<img src='landing/drug1.PNG' width=200 onClick="javascript:window.open( 'images/drug1.PNG', '60', 'left = 20, top = 20, width = 900, height = 600, toolbar = 0, resizable = 0, scrollbars=1' )" />
		</td>
	</tr>
</table>
</div></td>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>