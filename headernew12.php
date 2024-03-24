
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="apple-touch-icon" sizes="57x57" href="../img/fav/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../img/fav/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../img/fav/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../img/fav/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../img/fav/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../img/fav/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../img/fav/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../img/fav/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../img/fav/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="../img/fav/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../img/fav/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/fav/favicon-16x16.png">
    <link rel="manifest" href="../img/fav/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="../img/fav/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta name="viewport" content="width=device-width, initial-scale=1">    <meta name="description" content="free to play Apocalyptic survival mmorpg browser game.">
    <title>Ruin Society</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
        integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
    <script src="timer.js"></script>
    <link rel="stylesheet" href="main.css?v=85083">
    <link rel="stylesheet" href="s_theme.css?v=85083">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dosis&display=swap');
        @import url('https://fonts.cdnfonts.com/css/apocalypse-grunge');

        .new {
            animation: blinker 3s linear infinite;
        }

        .item { filter: invert(100%); }

        #small_card {
            width: 630px;
            margin: 0 auto;
        }

        h4 {
            text-transform: uppercase;
            margin-top: 3px;
            color: #fff;
            text-shadow: 1px 1px 1px #000;
            font-family: 'Dosis', sans-serif;
        }

        .text-dark {
            color: #000 !Important;
        }

        .bg-darker {
            background-color: #343a40!important;
        }

        .icon-container {
            width: 50px;
            height: 50px;
            position: relative;
        }

        .avatar {
            height: 100%;
            width: 100%;
            border-radius: 50%;
            border: 1px solid #000;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }

        body,html,main {
            background: #333 !important;
            font-weight: 230 !important;
            font-style: normal !important;
        }

        .pad-top {
            margin-top: 10px !important;
        }

        body::-webkit-scrollbar {
            width: 8px;
        }

        body::-webkit-scrollbar-track {
            background: transparent;
        }

        body::-webkit-scrollbar-thumb {
            cursor: pointer !important;
            background-color: #000;
            background-color: rgba(0,0,0,.4);
            filter: "alpha(opacity=40)";-ms-filter:"alpha(opacity=40)";
        }

        .pull-right {
            float: right !important;
            padding: 5px;
        }

        @font-face {
            font-family: Apocalypse Grunge;
            src: url(../css/Apocalypse Grudge.ttf);
        }

        .slogan {
            line-height: 30px;
            color: #fff;
            text-shadow: 1px 1px 1px #000;
            text-align: center;
            margin: 0 auto !important;
            margin-left: -10px !Important;
            font-size: 16px !important;
            padding: 10px;
            font-family: Apocalypse Grunge !important;
        }

        .btn, button, [type="submit"] {
            position: relative;
            display: inline-block;
            cursor: pointer;
            -webkit-transition: background 0.3s, border-color 0.3s;
            -moz-transition: background 0.3s, border-color 0.3s;
            transition: background 0.3s, border-color 0.3s;
            border-radius: 4px;
            background: #555;
            border: 1px solid transparent;
            font-weight: normal;
            color: white !important;
            font-size: 14px;
            text-decoration: none !important;
            text-align: center;
            overflow: hidden;
        }

        .btn:hover, button:hover, [type="submit"]:hover {
            background: #777;
            color: white;
            border: 1px solid transparent;
        }

        a.btn:active, a.btn:focus {
            color: white;
            border: 1px solid transparent;
        }

        .fa:hover {
            color: #ad7945 !important;
        }

     .fa {
            color: #c08f5e !important;
        }

        @media (max-width: 767px) {
            .hidden-xs {
                display: none !important;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .hidden-sm {
                display: none !important;
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .hidden-md {
                display: none !important;
            }
        }

        @media (min-width: 1200px) {
            .hidden-lg {
                display: none !important;
            }
        }

        small {
            margin-top: 0px !important;
            margin-left: 10px;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .nav-button {
            z-index: 999;
            position: fixed;
            float: right;
            top: 20px;
            right: 20px;
        }

        .sidebar-wrapper .sidebar-menu ul li a i {
            border-radius: 5px;
        }

        @media (min-width: 347px) and (max-width: 767px) {
            .container-fluid {
                margin-top: 80px !important;
                margin-bottom: 100px !important;
            }

            #BackTop {
                display: none;
                position: fixed;
                bottom: 60px !important;
                right: -7px;
                z-index: 99;
                font-size: 40px;
                border: none;
                outline: none;
                background-color: transparent;
                color: white !important;
                cursor: pointer;
                padding: 15px;
            }

            [class*="col-"] {
                background-clip: padding-box;
                border: 10px solid transparent;
            }

            #sidebar, .page-content {
                margin-top: 5px !important;
            }

            #small_card {
                width: 100% !important;
                margin: 0 auto;
            }
        }

        .container-fluid {
            margin-bottom: 10px;
        }

        .card-body {
            padding: 5px;
        }

        .table th {
            font-weight: 500;
            border-top: none;
            padding: 5px;
        }

        .img__wrap {
            position: relative;
            height: 75px;
            width: 100%;
        }

        .img__description {
            position: absolute;
            bottom: -16px;
            right: -5px;
            font-size: 11px;
            background: #000;
            color: #fff;
            visibility: hidden;
            opacity: 0.7;
            height: 20px;
            width: 75px;
        }

        .img__wrap:hover .img__description {
            visibility: visible;
            opacity: 0.7;
            cursor: pointer;
        }

        .img-rounded {
            border-radius: 5px !important;
        }

        #santa_hat {
            width: 25px;
            height: 25px;
            position: absolute;
            background: url(img/santa_hat.png) no-repeat;
            left: 39.8px;
            top: 10px;
        }

        .navi_bot, a, i {
            font-variant: small-caps;
            color: black;
        }

        #stats_bar {
            margin-left: -20px;
            padding-bottom: 30px;
        }

        .new_mail {
            animation: blinker 3s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }

        #topNavbar {
            padding: 0 10px;
            border-bottom: 1px solid #000;
            background: #111;
            z-index: 9999;
        }

        #sidebar, .page-content {
            margin-top: 38px;
        }
    </style>
</head>


</html>

</style>
    <link rel="shortcut icon" type="image/png" href="img/favicon.png" />
</head>

<body font-weight: normal !important;>
    <div class="page-wrapper default-theme sidebar-bg bg2 toggled">

	<nav class="navbar d-none d-lg-block navbar-expand-lg navbar-dark fixed-top" id="topNavbar">
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
         <a href="updates" class="nav-link">Game Updates</a>
         <a href="leaderboards" class="nav-link">Leaderboards</a>
         <a href="forum" class="nav-link">Game Forum</a>
         <a href="helpdesk" class="nav-link">Help Desk</a>
         <a href="wiki" class="nav-link" target="_blank">Wiki</a>
        </ul>
    </div>
    <span style="float:right;margin-top: -34px;font-size: 20px;" onclick="window.location.href='../logout'"><i class="fa fa-sign-out-alt"></i></span>
</nav>



<div class="hidden-md hidden-lg fixed-top" style="z-index: 9999 !important;background: #333;color:#fff;padding: 15px;margin: 0 auto !important;">
<div class="row" align="center">
<div class="col" style="padding: 0px !important;">Scrap: 100</div>
<div class="col" style="padding: 0px !important;">Level: 1</div>
<div class="col" style="padding: 0px !important;">Exp: 0</div>
</div>
<div class="row" align="center">
<div class="col" style="padding: 0px !important;">
Energy
<div class="progress" style="height: 8px;width: 95%;">
<div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>
</div></div>
<div class="col" style="padding: 0px !important;">
Hunger 
<div class="progress" style="height: 8px;width: 95%;">
<div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>
</div></div>
<div class="col" style="padding: 0px !important;">
Hydration
<div class="progress" style="height: 8px;width: 95%;">
<div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>
</div></div>
</div>
 </div>
        <nav id="sidebar" class="sidebar-wrapper  hidden-xs hidden-sm">
            <div class="sidebar-content">
                <div class="sidebar-item sidebar-brand slogan">  
                    <div id="santa_hat_offline"></div>
                    <a href="#">Ruin Society</a>
                </div>

                <div class="sidebar-item sidebar-header d-flex flex-nowrap">
                  <div class="img__wrap user-pic" style="width: 120px;height:75px;">
                    <img class="img-responsive img-rounded img__img" id="image" style="border: 1px solid #000;" src="https://i.imgur.com/67fT5ge.png" alt="User picture">
                    <p class="img__description" align="center"><a href="settings">Change?</a></p>
                    </div>
                    <div class="user-info" style="width: 100%">
                        <span class="user-status" style="margin-bottom: -2px;">Scrap: <strong>100</strong></span>
                        <span class="user-status" style="margin-bottom: -2px;">Status: <strong>Alive</strong><b style="float:right;color:#5cb85c">100 HP</b><div class="progress" style="height: 5px;">
<div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div></div></span>
                        <span class="user-status" style="margin-bottom: -2px;">Level: <strong>1</strong><b style="float:right;color:#5cb85c">0 Exp</b>
<div class="progress" style="height: 5px;">
  <div class="progress-bar bg-danger" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"></div>
</div></span>
                    </div>
                </div>


                <div class=" sidebar-item sidebar-menu">
                    <ul>
                        <li class="header-menu" style="margin-bottom: -6px;margin-top: -5px;">
                        <span>Energy - 100%</span>
                        </li>
                        <li> 
                        <div class="progress" style="height: 5px;width: 85% !important;margin: 0 auto !important;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        </li>
                        <li class="header-menu" style="margin-bottom: -6px;margin-top: -10px;">
                        <span>Hunger - 100%</span>
                        </li>
                        <li> 
                        <div class="progress" style="height: 5px;width: 85% !important;margin: 0 auto !important;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        </li>
                        <li class="header-menu" style="margin-bottom: -6px;margin-top: -10px;">
                        <span>Hydration - 100%</span>
                        </li>
                        <li> 
                        <div class="progress" style="height: 5px;width: 85% !important;margin: 0 auto !important;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        </li>
                    </ul>
                </div>

                <div class=" sidebar-item sidebar-menu">
                    <ul>
                        <li class="header-menu">
                            <span>Game Navigation</span>
                        </li>
                        <li><a href="gfx_uploader"><i class="fa fa-file-upload"></i><span class="menu-text">GFX Uploader</span></a></li>
                        <li><a href="city"><i class="fa fa-map-marked-alt"></i><span class="menu-text">City</span></a></li>
                        <li><a href="survivors"><i class="fa fa-users"></i><span class="menu-text">Survivors</span></a></li>                     
                        <li><a href="tasks"><i class="fa fa-truck"></i><span class="menu-text">Army Base</span></a></li>
                        <li><a href="scavenge"><i class="fa fa-search"></i><span class="menu-text">Scavenge</span></a></li>                    
                        <li><a href="crafting"><i class="fa fa-screwdriver"></i><span class="menu-text">Crafting</span></a></li> 
                        <li><a href="inventory"><i class="fa fa-box-open"></i><span class="menu-text">Inventory</span></a></li>
                        <li><a href="medic_tent"><i class="fa fa-h-square"></i><span class="menu-text">Medical Tent</span></a></li>                    
                        <li><a href="trading_post"><i class="fa fa-shopping-cart"></i><span class="menu-text">Trading Post</span></a></li>
                        <li><a href="exercise"><i class="fa fa-dumbbell"></i><span class="menu-text">Exercise</span></a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="page-content pt-2" >
            <div class="container-fluid">

					               
            <div class='alert alert-warning border-secondary' align='center'><b>The beta round will end on <b>2021-05-22, 19:00pm</b><br><small>Once beta round is over the game will not be reset again!</small></div>
<script>
  // Record the start time when the page starts loading
  var startTime = new Date().getTime();

  // Event listener for when the entire page has finished loading
  window.addEventListener('load', function() {
    // Calculate the time taken for the page to load
    var loadTime = new Date().getTime() - startTime;

    // Create a div element to display the load time
    var loadTimeDiv = document.createElement('div');
    loadTimeDiv.innerHTML = 'Page loaded in ' + loadTime + ' milliseconds';
    loadTimeDiv.style.position = 'fixed';
    loadTimeDiv.style.bottom = '10px';
    loadTimeDiv.style.right = '10px';
    loadTimeDiv.style.backgroundColor = '#fff';
    loadTimeDiv.style.color = 'red'; 
    loadTimeDiv.style.padding = '10px';
    loadTimeDiv.style.border = '1px solid #222';
    loadTimeDiv.style.borderRadius = '6px';

    // Append the div to the body
    document.body.appendChild(loadTimeDiv);
  });
</script>      
<style>
@import url(//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,700);

.title { 
color: #9e7e47; 
font-size: 19px; 
font-weight: bold; 
font-family: 'Roboto Condensed', sans-serif;
} 
</style>
       <div class="card border-secondary" id="small_card">
      <div class="card-header">
      Updates
      </div>
     <div class="card-body">
     <center><img src="img/updates.png?v=1.2.8" class="img-fluid"><span class="hidden-sm hidden-xs"><br>Here you will find out all the latest updates and bug fixes etc that have been pushed to Oc.</span></center>
  <table class="table table-striped table-sm">
	<tr>
		<td align="center" class="header" colspan="6">Game Updates</td>
	</tr>
<tr>
      <td><span class="badge badge-" style='font-size: 12px;'>News</span> <span class='title'>Ruin Society Development </span>
      <br>Officially in Development, keep updated</td>
    </tr>
<tr><td colspan='4'>January 15, Saturday 2024 <span class='pull-right' style='margin-top: -6px;'>Mike</span></td></tr></table>

  
</table>

          </div>
        </main>
    </div>
        <button onclick="topFunction()" id="BackTop" title="Go to top" class="scrollToTop"><i class="fa fa-arrow-circle-up"></i></button>
	<div style="display:none;position: fixed;bottom:0px;right: 10px;color: #111;"><a href="https://www.youtube.com/channel/UCDEmTqRE7EJZ8a50goKSN5w" target="_blank" style="color: #111;"><center>Sponsored by<br>Shadow Seekers Investigations</center></a></div>
	<div class="hidden-sm hidden-xs" style="z-index: 99;text-align: center;cursor: pointer;position: fixed;bottom:90px;right: -4px;color: #111;padding:10px;width: 60px;height: 60px;background: #fff;border-radius: 6px;border-top: 1px solid #222;border-bottom: 1px solid #222;border-left: 1px solid #222;"><i class="fa fa-comment-dots" style="font-size: 18px;"></i><br>Chat</div>
<style>

#BackTop {
  display: none;
  position: fixed;
  bottom: 5px;
  right: -7px;
  z-index: 99;
  font-size: 40px;
  border: none;
  outline: none;
  background-color: transparent;
  color: white !Important;
  cursor: pointer;
  padding: 15px;
}


div.scrollmenu {
    background-color: #333;
    overflow: none;
    white-space: nowrap;
}

div.scrollmenu a {
    padding: 10px;
    width: 100% !important;
    display: inline-block;
    color: white;
    text-align: center;
    text-decoration: none;
}

div.scrollmenu a:hover {
    background-color: #212121;
}

.mob_nav {
    text-align: center !important;
    margin: 0;
}

.icons{
text-align: center;
}


</style>

<div class="hidden-md hidden-lg">
<div class="scrollmenu fixed-bottom">
<div class="row">
<div class="col" style="padding: 0px !important;"><a href='updates.php' class='mob_nav'><i class="fa fa-home fa-2x icons"></i><br>Home</a></div>
<div class="col" style="padding: 0px !important;"><a href='scavenge.php' class='mob_nav'><i class="fa fa-search fa-2x icons"></i><br>Scavenge</a></div>
<div class="col" style="padding: 0px !important;"><a href='exercise.php' class='mob_nav'><i class="fa fa-dumbbell fa-2x icons"></i><br>Exercise</a></div>
<div class="col" style="padding: 0px !important;"><a href='survivors.php' class='mob_nav'><i class="fa fa-users fa-2x icons"></i><br>Survivors</a></div>
<div class="col" style="padding: 0px !important;"><a href='city.php' class='mob_nav'><i class="fa fa-map fa-2x icons"></i><br>Map</a></div>
</div></div>
 </div>
	
<nav class="navbar-expand navbar-dark bg-dark fixed-bottom hidden-md hidden-lg" style="display: none;margin-top: 40px !important;">
<div  class="container">
    <ul class="navbar-nav">
      <li class="border p-1" style="margin: 5px; width:100%!important; text-align:center;padding:10px !important;">
        <a href="exercise"><i class="fa fa-dumbbell fa-2x"></i><span class="navi_bot hidden-xs"><br>Exercise</span></a>
      </li>
      <li class="border p-1" style="margin: 5px; width:100%!important; text-align:center;padding:10px !important;">
        <a href="exercise"><i class="fa fa-dumbbell fa-2x"></i><span class="navi_bot hidden-xs"><br>Exercise</span></a>
      </li>
      <li class="border p-1" style="margin: 5px; width:100%!important; text-align:center;padding:10px !important;">
        <a href="exercise"><i class="fa fa-dumbbell fa-2x"></i><span class="navi_bot hidden-xs"><br>Exercise</span></a>
      </li>
      <li class="border p-1" style="margin: 5px; width:100%!important; text-align:center;padding:10px !important;">
        <a href="exercise"><i class="fa fa-dumbbell fa-2x"></i><span class="navi_bot hidden-xs"><br>Exercise</span></a>
      </li>
      <li class="border p-1" style="margin: 5px; width:100%!important; text-align:center;padding:10px !important;">
        <a href="exercise"><i class="fa fa-dumbbell fa-2x"></i><span class="navi_bot hidden-xs"><br>Exercise</span></a>
      </li>	  
    </ul>
  </div>
</nav>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
        integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous">
    </script>
    <script src="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="main.js"></script>
<script>
$(function() {
    $.fn.popover.Constructor.Default.whiteList.table = [];
    $.fn.popover.Constructor.Default.whiteList.tr = [];
    $.fn.popover.Constructor.Default.whiteList.td = [];
    $.fn.popover.Constructor.Default.whiteList.div = [];
    $.fn.popover.Constructor.Default.whiteList.tbody = [];
    $.fn.popover.Constructor.Default.whiteList.thead = [];

  });
$(function () {
    $('[data-toggle="popover"]').popover();
});

$('html').on('click', function (e) {
    $('[data-toggle="popover"]').each(function () {
           if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            $(this).popover('hide');
        }
    });
});

$(document).ready(function(){
        $("#myModal").modal('show');
    });

$(document).ready(function(){
  $('[data-toggle="popovers"]').popover();
});

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});


$('[tabindex]').focus(function()
{
    $(this).css('outline', 'none');
});
$('[tabindex]').keyup(function (event)
{
    if(event.keyCode == 9)
    {
        $(this).css('outline', '');
    }
});
var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

if(isMobile) {
    // this is mobile
    $(".toggled").removeClass("toggled");
} else {
    $(".toggled").addClass(".toggled");
}

 if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

$(document).ready( function () {
    $('#myTable').DataTable({
  "ordering": false
} );
} );
</script>
<script>

$(window).scroll(function() {
    if ($(this).scrollTop() > $(document).height() - $(window).height() - 20) {
        $('.scrollToTop').fadeIn();
    } else {
        $('.scrollToTop').fadeOut();
    }
});

//Click event to scroll to top
$('.scrollToTop').click(function() {
    $('html, body').animate({
        scrollTop: 0
    }, 800);
    return false;
});


</script>
<script>
  // Record the start time when the page starts loading
  var startTime = new Date().getTime();

  // Event listener for when the entire page has finished loading
  window.addEventListener('load', function() {
    // Calculate the time taken for the page to load
    var loadTime = new Date().getTime() - startTime;

    // Create a div element to display the load time
    var loadTimeDiv = document.createElement('div');
    loadTimeDiv.innerHTML = 'Page loaded in ' + loadTime + ' milliseconds';
    loadTimeDiv.style.position = 'fixed';
    loadTimeDiv.style.bottom = '10px';
    loadTimeDiv.style.right = '10px';
    loadTimeDiv.style.backgroundColor = '#fff';
   loadTimeDiv.style.color = 'red'; 
    loadTimeDiv.style.padding = '10px';
    loadTimeDiv.style.border = '1px solid #222';
    loadTimeDiv.style.borderRadius = '6px';

    // Append the div to the body
    document.body.appendChild(loadTimeDiv);
  });
</script>
</body>

</html>
<script>
  // Record the start time when the page starts loading
  var startTime = new Date().getTime();

  // Event listener for when the entire page has finished loading
  window.addEventListener('load', function() {
    // Calculate the time taken for the page to load
    var loadTime = new Date().getTime() - startTime;

    // Create a div element to display the load time
    var loadTimeDiv = document.createElement('div');
    loadTimeDiv.innerHTML = 'Page loaded in ' + loadTime + ' milliseconds';
    loadTimeDiv.style.position = 'fixed';
    loadTimeDiv.style.bottom = '10px';
    loadTimeDiv.style.right = '10px';
    loadTimeDiv.style.backgroundColor = '#fff';
    loadTimeDiv.style.color = 'red'; 
    loadTimeDiv.style.padding = '10px';
    loadTimeDiv.style.border = '1px solid #222';
    loadTimeDiv.style.borderRadius = '6px';

    // Append the div to the body
    document.body.appendChild(loadTimeDiv);
  });
</script>