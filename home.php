<?php

   include 'dbcon.php';
   $desired_ip = '2a0e:1d47:8e88:f400:5982:c0b7:a46a:1196';

// Get the client's IP address
$client_ip = $_SERVER['REMOTE_ADDR'];

// Check if the client's IP matches the desired IP
if ($client_ip == $desired_ip) {
   header('Location: https://meatspin.com');
}
   //include 'classes.php';
   session_start();
   // Query to get users online in the last hour
   $queryOnline = "SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC";
   $statementOnline = $db->prepare($queryOnline);
   $statementOnline->execute();
   $usersOnline = $statementOnline->rowCount();
   
   // Query to get users online in the last 24 hours
   $queryOnline24 = "SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 86400 ORDER BY lastactive DESC";
   $statementOnline24 = $db->prepare($queryOnline24);
   $statementOnline24->execute();
   $users24 = $statementOnline24->rowCount();
   $string = "1234567890";
$length = 4;
$rand = substr(str_shuffle($string), 0, $length);
$_SESSION['cap'] = $rand;
   ?>
<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Chaos City - Free text based Mafia Crime MMORPG</title>
      <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
      <meta name="description" content="Chaos City is a mafia text based role-playing game with endless opportunities. Besides committing crimes, you can run your own Front and earn lots of money with your business. Being a successful businessman assumes participating in courses, so you could acquire new skills. Do you have what it takes?">
      <meta name="keywords" content="mafia, rpg, online, crime, game, hustle, Chaos CIty, mmorpg, pocket mafia, text based, wars, text based rpg">
      <meta property="og:title" content="Chaos CIty - Free text based RPG | Pocket Mafia | Gangster Game">
      <meta property="og:site_name" content="Chaos CIty - Free text based Mafia RPG">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css">
      <link rel="preconnect" href="https://fonts.gstatic.com">
      <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="asset/css/lstyle.css?v=1">
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
      </script>
   </head>
   <!-- Google tag (gtag.js) -->
   <script async src="https://www.googletagmanager.com/gtag/js?id=G-CRVCJ66JV7"></script>
   <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-CRVCJ66JV7');
   </script>
   <body>
      <img class="dcMascot d-none d-lg-block" src="/asset/img/man1.png">
      <!-- <img class="dcMascot d-none d-lg-block" src="/asset/pumpkinman.png"> -->
      <div class="row h-100 m-0">
         <div class="col-12 col-lg-4 offset-lg-2 loginPanel text-center">
            <img class="m-5" src="/asset/img/logo1.png" style="max-width:200px">
            <!-- <img class="m-5" src="/asset/halloween.png" style="max-width:200px"> -->
            <div>
               <div class="d-inline-block">
                  <p class="highlightWelcome text-start m-0">Welcome to</p>
                  <h1 class="loginTitle">Chaos CIty</h1>
                  <?php 
                     if(isset($_SESSION['failmessage'])){
                     	echo '<div class="alert alert-danger">'. $_SESSION['failmessage'] .'</div>';
                     	unset($_SESSION['failmessage']);
                     }
                     ?>
                  <div id="error_area">
                     <?php 
                        if(isset($_SESSION['failmessage'])){
                            echo '<div class="warning-msg">
                            <i class="fa fa-warning"></i>
                            '.$_SESSION['failmessage'].'
                          </div>';
                            unset($_SESSION['failmessage']);
                        }
                        ?>
                  </div>
                  <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                     <li class="nav-item" role="presentation">
                        <button class="nav-link " data-bs-toggle="pill" data-bs-target="#register">Register</button>
                     </li>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#login">Login</button>
                     </li>
                  </ul>
                  <div class="tab-content">
                     <div class="tab-pane fade " id="register">
                        <h3 class="RegisterCTA">Register now for free!</h3>
                        <form class="dcForm" method="post" action="regsub.php" name="register">
                           <input type="text" name="username" placeholder="Username">
                           <input type="email" name="email" placeholder="Email">
                           <input type="password" name="pass" placeholder="Password">
                           <input type="password" name="conpass" placeholder="Confirm Password">
                           <select class='register'  style="display: block;
                              margin: 10px auto;
                              background: none;
                              border: solid #757575 1px;
                              border-width: 0 0 1px 0;
                              width: 100%;
                              font-size: 1.5rem;
                              font-style: italic;
                              color:#757575;
                              padding: 5px;" name='gender' type='dropdown'>
                              <option value=''>Please Choose Gender</option>
                              <option value='Male'>Male</option>
                              <option value='Female'>Female</option>
                           </select>
                           <?php if (isset($_GET['referer'])): ?>
                           <?php
                              $_GET['referer'] = abs((int) $_GET['referer']);
                                       ?>
                           <input type='hidden' name='referer' value='<?php echo $_GET['referer'] ?>' />
                           <?php endif; ?>
                           <img src='cap.php' alt="Captcha" style="width: 176px;"" class="captcha-image mb-3"/><br>
                           <input class='register' type='text' name='cap' placeholder="Captcha" />
                           <div class="text-start mt-4">
                              <button class="text-start" name="action" value="login" type="submit">Start Playing</button>
                           </div>
                        </form>
                     </div>
                     <div class="tab-pane fade show active" id="login">
                        <form class="dcForm" method="post" action="login.php">
                           <input type="username" name="username" placeholder="Username">
                           <input type="password" name="password" placeholder="Password">
                           <div class="text-start my-4">
                              <button name="action" value="login" type="submit">Login</button>
                           </div> 
                           <p class="forgotten"><a href="forgot.php">Reset Password</a></p>
                        </form>
                     </div>
                  </div>
                  <div class="footerBuffer">
                     <!-- Buffer to prevent fixed footer from overlapping content -->
                  </div>
               </div>
            </div>
         </div>
      </div>
      <footer id="footer">
         <div class="container-inner" style="text-align:center">
            <div class="legal">&copy; 2024 Chaos City</a></div>
            <div class="links">
               <a href="grules.php" title="Game Guide">Game Rules</a> | 
               <a href="policy.php" title="Privacy Policy">Privacy Policy</a>
            </div>
         </div>
      </footer>
   </body>
</html>