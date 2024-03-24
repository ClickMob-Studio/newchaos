<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="lang" content="english">
	<meta name="robots" content="All">
	<title>{$set['game_name']} - Free Online Web RPG</title>
	<link href="assets/css/login.css" type="text/css" rel="stylesheet" />
	<link href="assets/css/game.css" type="text/css" rel="stylesheet" />
	<link rel="icon" type="image/png" href="favicon.ico">
	<script>
		var end = new Date('11/11/2016 10:00 AM');
    		var _second = 1000;
    		var _minute = _second * 60;
    		var _hour = _minute * 60;
    		var _day = _hour * 24;
    		var timer;

    		function showRemaining() {
        		var now = new Date();
        		var distance = end - now;
        		if (distance < 0) {
            			clearInterval(timer);
            			document.getElementById('countdown').innerHTML = 'EXPIRED!';
            			return;
        		}
        		var days = Math.floor(distance / _day);
        		var hours = Math.floor((distance % _day) / _hour);
        		var minutes = Math.floor((distance % _hour) / _minute);
        		var seconds = Math.floor((distance % _minute) / _second);

        		document.getElementById('countdown').innerHTML = days + 'days ';
        		document.getElementById('countdown').innerHTML += hours + 'hrs ';
        		document.getElementById('countdown').innerHTML += minutes + 'mins ';
        		document.getElementById('countdown').innerHTML += seconds + 'secs';
    		}
    		timer = setInterval(showRemaining, 1000);
	</script>
</head>
<body>
	<div id="outer" class="wrap">
		<div id="top_bar" class="wrap">
			<div style="float:left;color:#910503;margin-left:5px;">
				<strong>Users Online: {$count}</strong> | 
				<strong>Users Online in last 24 hours: {$daycount}</strong>
			</div>
			<a href="login.php">Homepage</a> <a href="register.php">Create an account</a> <a href="tos.php">Terms of Service</a>	
		</div>
		<div id="main" class="row">
			<div id="header" class="row">
				
			</div>
			<div id="content" class="row">
				<div id="left_side">
					<h2>Welcome to Chaos City!</h2>
					Chaos City is a Massively Multiplayer Online Role Playing Game, MMORPG for short.<br /><br />
					In this game, you play the role of a mobster who is leading a life of crime and deception. 
					Join gangs and team up with your friends to kick ass, or go your own way. 
					The choices are endless.<br /><br />
					<strong>Contact us at <a style='color:black;text-decoration:none;' href="mailto:support@chaoscityrpg.com">support@chaoscityrpg.com</a></strong><br /><br />
					<span style='color:red;font-weight:bold;'>
						Game is closed for now due to a major server crash.<br />
						We have lost all data due to this and are having to start again
						which is sad news but we will have a team working to get the updates back on 
						and try get it back to playabale.<br /><hr width='98%' align='center' />
						We are sorry for anyone who lost there account we will try get that sorted out for you
						once we are back and live.<br /><hr width='98%' align='center' />
						<span style='font-size:19px;'>Our support emial is back live 01/11/2016</span>
					</span>
					<!-- <strong style='color:red;'>Countdown To beta Launch (<small>11/11/2016</small>)</strong><br /> -->
					<!-- <strong><span id="countdown" style="font-size:25px;"></span></strong><br /><br />-->
EOF;
				/*if($set['GameStage'] == 'Alpha' || $set['GameStage'] == 'Alpha/Beta') {
					echo "
					<form method='post' action='betakey.php'>
						<table width='95%' cellspacing='1' style='text-align:center;'>
							<tr><th>Get a Beta Key</th></tr>
							<tr><td>Email: <input type='email' name='betakey' /></td></tr>
							<tr><td><button>Ask For Beta Key</button></td></tr>
						</table>
					</form>";
				}*/
echo <<<EOF
				</div>
EOF;
 
$IP = str_replace(array('/', '\\', '\0'), '', $_SERVER['REMOTE_ADDR']);
if(file_exists('ipbans/' . $IP))
{
    die(
    	"<center><img src = 'images/banned.gif ' /><div style='height: 50px'></div>
    	<span style='color:#FFF;font-size:11px;'>Copyright &copy; {$set['game_owner']}<br />All Rights Reserved.</span></center></body></html>
    ");
}

$year = date('Y');
 
echo<<<EOF

			<div id="right_side">
			<div id="error_area"></div>
				<div id="login_panel">
					<h3>:: Login Panel ::</h3>
					 
					<form method="POST"  action="authenticate.php">
						<input type="text" class="login" value="" name="username" placeholder="Username" />
						<input type="password" class="login" value=""  name="password" placeholder="Password" />
						<input type='hidden' name="verf" value='{$login_csrf}' />
						<input type="submit" id="submit" name="submit" class="log_btn" value="Login" />
					</form>
					<a href="resend.php">Forgot Password?</a>
				</div>
			</div>
			<div class="spacer"></div>
		</div>
EOF;
echo<<<EOF

		<div id="bottom_content" class="row"></div>
	</div>
	<div id="footer" class="row">
		<span>Chaos City</span><br />
		&copy; 2015+ . All Rights Reserved.<br />
		<a href="">Privacy Policy.</a> <a href="">Terms of Services.</a> <a href="">Help Tutorial.</a> <a href="">Staff	</a>
	</div>	
</div>
</body>
</html>