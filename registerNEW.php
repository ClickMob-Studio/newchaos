<?php
$metatitle = 'Text-Based Mafia Game - Free Online Multiplayer RPG';
$metadesc = 'TheMafiaLife (TML) is one of the most popular original text-based mafia games today. Fight in the bloodbath, shoot out in live gang wars with your crime family, or gamble your way to the top. Don a thompson or a sledgehammer and play your way to become the most powerful godfather in TheMafiaLife, the best textbased game on the net!';

if (empty($metatitle))
    $metatitle = 'TheMafiaLife';
else
    $metatitle = $metatitle.' | TheMafiaLife';

?>
<?php
session_start();



$string = "1234567890";
$length = 4;
$rand = substr(str_shuffle($string), 0, $length);
$_SESSION['cap'] = $rand;
$reg_css = filemtime('/var/www/html/css/reg.css');
?>
ï»¿<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Register Page</title>
        <?php if (!empty($metadesc)) echo '<meta name="description" content="'.$metadesc.'">';
?>


		<!--<link rel="stylesheet" href="css/newgame/login.css" media="screen" />-->
		 <link rel="stylesheet" href="css/newgame/login.css?<?php echo filemtime('/var/www/html/css/newgame/login.css') ?>" media="screen"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<link rel="stylesheet" href="css/reg.css?" media="screen" />
		<script src='js/reg.js' type='text/javascript'></script>
		<script type="text/javascript">
			var field = document.querySelector('[name="username"]');

			field.addEventListener('keyup', function ( event ) {  
			var userName = field.value;
			userName = userName.replace(/\s/g, '');
			field.value = userName;
			});
		</script>
		<style>
		 #navig{
                display:flex;
                margin-bottom:2rem;
                align-items:center;
                justify-content:center;
                 
                
            }
            .navmenu{
                color:#c68c53;
                text-decoration:none;
                
            }
            
            .btnnav{
                background:#990000;
                align-items:center;
                font-size:22px;
                font-weight:bold;
                padding:.3rem 0 .3rem 0;
                border:none;
                margin-right:2.7rem;
                width:200px;
                border-radius:5px;
            }
            .btnnav:hover{
                background:#4d0000;
            }
		    #logsign{
		        text-decoration:none;
		        color:red;
		    }
		    #logsign:hover{
		           color:skyblue; 
		    }
		    .formflex{
		        margin-top:1.5rem;
		        display:flex;
		    }
		    form{
		        font-size:18px;
		        color:white;
		        
		    }
		    .forminput{
		        font-size:18px;
		        color:black;
		        margin-left:.5rem;
		        width:300px;
		        
		    }  
		    .btnsubmit{
		        padding: .3rem 0 0 .3rem;
		        width:200px;
		        margin-left:50px;
		        
		    }
		    .btnsubmit:hover{
		        background:grey;
		        color:skyblue;
		    }
			.flexBox-title{
				width:200px;
			}
		</style>
	</head>
	<body>
		<div id="outer" class="wrap">
			<div id="header" class="row">
				<div id="logo" style="color:transparent;overflow:hidden; margin-left:21%; margin-bottom:2rem;"></div>
			</div>
				
				<div class="spacer"></div>
				<div id="navig">
                    <button class="btnnav" style="margin-left:3rem;"><a href="login.php" class="navmenu">HOME</a></button>
                    <button class="btnnav"><a href="register.php" class="navmenu">REGISTER</a></button>
                    <button class="btnnav"><a href="contact.php" class="navmenu">CONTACT</a></button>
                </div>
			    
			<div id="main_area" class="row">
				<div class="top row"></div>
				<div class="middle row">
					<div class="pad">
						<div class="left_side" style="">
							<h2>REGISTER HERE</h2>
							<div class="divider"></div>
							<span id="errors"></span>
							<form id="regForm" onSubmit="return validate();" method="post" action="regsub.php">
								<div class="flexCont">
								    <div class="formflex">
									    <div class="flexBox flexBox-title left">Username:</div>
									    <div class="flexBox right"><input class="forminput" type="text" id="username" onBlur="checkUsername(1);" name="username" /></div>
									</div>
									<div class="formflex">
									    <div class="flexBox flexBox-title left">Email:</div>
									    <div class="flexBox right"><input class="forminput" type="text" id="email" onBlur="checkEmail();" name="email" /></div>
									</div>
									<div class="formflex">
									    <div class="flexBox flexBox-title left">Password:</div>
									    <div class="flexBox right"><input class="forminput" type="password" id="pass" onBlur="checkPassword();" name="pass" /></div>
									</div>
									<div class="formflex">
									    <div class="flexBox flexBox-title left">Confirm Password:</div>
									    <div class="flexBox right"><input class="forminput" type="password" id="conpass" onBlur="checkConfPassword();" name="conpass" /></div>
									</div >
									<div class="formflex">
									    <div class="flexBox flexBox-title left">Gender:</div>
									
									    <div class="flexBox right">
									        <input type="radio" id="gender" name="gender" value="Male">
									        <label for ="Male">Male</label>
									        <input type="radio" id="gender" name="gender" value="Female">
									        <label for ="Male">Female</label>
									    </div>
									</div>
									<input type="text" id="tos" name="tos"/>
									<div class="flexBox right formflex"> <img src='cap.php' /></div>
									<div class="formflex">
									    <div class="flexBox flexBox-title left">Captcha:</div>
									    <div class="flexBox right"><input class="forminput" type="text" id="cap" name="cap" /></div>
									</div>
									<div class="flexBox left"></div>
									<input type="hidden" name="referer" value="<?php echo isset($_GET['referer']) ? $_GET['referer'] : 0; ?>" />
									<div class="flexSub formflex" ><input class="forminput btnsubmit" type="submit" value="Register" /></div>
									<div class="formflex" style="margin-bottom:1.5rem;"><span>Existing Player ?<a id="logsign" href="login.php">Login Here!</a></span></div>
								</div>
							</form>
						</div>
						<!--<div class="right_side">-->
						<!--	<div id="login_panel">-->
						<!--		<div class="padding">-->
						<!--			<form name="login" action="login.php" method="post" accept-charset="utf-8">-->
						<!--				<input type="text" class="user_box" name="username" placeholder="Username" />-->
						<!--				<input type="password" class="pass_box" name="password" placeholder="Password" />-->
						<!--				<input type="submit" class="login" value="login now" />-->
						<!--			</form>-->
						<!--			<div class="divider"></div>-->
						<!--			<a href="forgot.php">Forgot Password?</a> || <a href="register.php">New Player?</a>-->
						<!--			<div class="divider"></div>-->
						<!--			<a href="register.php" class="register"><a>-->
						<!--		</div>-->
						<!--	</div>-->
						<!--	<div class="spacer"></div>-->
						<!--</div>-->
						<div class="spacer"></div>
					</div>
				</div>
				<div class="bottom row"></div>
			</div>
		</div>
	</body>
</html>
