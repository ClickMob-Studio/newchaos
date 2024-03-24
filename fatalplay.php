<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>FatalPlay.com - FREE Online Browser Based MMORPG Game</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META content="MSHTML 6.00.2900.2180" name=GENERATOR>
<link rel="stylesheet" href="css/style_inner.css" type="text/css">
<script src="includes/check.js" type="text/javascript"></script>
<script src="includes/jquery-1.7.2.js" ></script>
<script src="includes/jquery-ui-1.8.21.custom.min.js" ></script>
<script src="includes/script.js" ></script>
</head>

<body>
<div id="dhtmltooltip"></div>


<script type="text/javascript">
/***********************************************
* Cool DHTML tooltip script-   Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

var offsetxpoint=-60 //Customize x offset of tooltip
var offsetypoint=20 //Customize y offset of tooltip
var ie=document.all
var ns6=document.getElementById && !document.all
var enabletip=false
if (ie||ns6)
var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : ""

function ietruebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function ddrivetip(thetext, thecolor, thewidth){
if (ns6||ie){
if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px"
if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor
tipobj.innerHTML=thetext
enabletip=true
return false
}
}

function positiontip(e){
if (enabletip){
var curX=(ns6)?e.pageX : event.clientX+ietruebody().scrollLeft;
var curY=(ns6)?e.pageY : event.clientY+ietruebody().scrollTop;
//Find out how close the mouse is to the corner of the window
var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20
var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20

var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000

//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<tipobj.offsetWidth)
//move the horizontal position of the menu to the left by it's width
tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px"
else if (curX<leftedge)
tipobj.style.left="5px"
else
//position the horizontal position of the menu where the mouse is positioned
tipobj.style.left=curX+offsetxpoint+"px"

//same concept with the vertical position
if (bottomedge<tipobj.offsetHeight)
tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px"
else
tipobj.style.top=curY+offsetypoint+"px"
tipobj.style.visibility="visible"
}
}

function hideddrivetip(){
if (ns6||ie){
enabletip=false
tipobj.style.visibility="hidden"
tipobj.style.left="-1000px"
tipobj.style.backgroundColor=''
tipobj.style.width=''
}
}
document.onmousemove=positiontip
</script>



    <div class="container">
        <div class="header">
            <div class="dateregen">
                <div class="datesec">
                    <p>
                        <br />
                    </p>
                </div><!--datesec-->
                <div class="regsec">
                    <p align='right'>
                        10 Aug 2023, 9:45:33am | Day update: 12am | Regen in <span id="regen_cd"></span><script language="javascript" type="text/javascript">countdown('269', 'regen_cd', '%%M%%:%%S%%');</script>
                    </p>
                </div><!--regsec-->
            </div><!--dateregen-->
            <div class="profile">
                <div class="profilesec">
                    <div class="profavtar"><a href='familymembers.php' onmouseover="ddrivetip('&nbsp;<b>Family Members</b>', '#24232E', 95)";  onmouseout="hideddrivetip()"><img src="avatars/0.jpg" width="58" height="63" style="border:1px solid #FFFFFF" /></a></div><!--profavtar-->
                    <div class="profinfo">
                        <div class="profid">
                            <p><a href='editaccount.php'><img src='images/gendermale.png' title='Male' border='0' style='float:left; margin-left: 3px;'></img></a>&nbsp;</p>
                        </div><!--profid-->
                        <div class="profname">
                            <p><b><a title="[Respected Mobster] [3 RM Days Left]" href="profiles.php?id=12783"><font color = "#64b72d">destroyer1995</a></font></b></p>
                        </div><!--profname-->
                        <div class="proflevel">
                            <p>ID: 12783 | Level: 1</p>
                        </div><!--proflevel-->
                    </div><!--profinfo-->
                </div><!--profilesec-->
            </div><!--profile-->
            <div class="pointslab">
                <div class="pointsec">
                    <div class="pointtxt">
                        <p>Points</p>
                    </div><!--pointtxt-->
                    <div class="point">
                        <p><span id="points_container">0</span></p>
                    </div><!--point-->
                    <div class="pointbuttons">
                        <a href = "spendpoints.php" onmouseover="ddrivetip('&nbsp;<b>Spend Points</b>', '#24232E', 75)";  onmouseout="hideddrivetip()"><div class="but1"></div><!--but1--></a>
                        <a href = "sendpoints.php" onmouseover="ddrivetip('&nbsp;<b>Send Points</b>', '#24232E', 67)";  onmouseout="hideddrivetip()"><div class="but2"></div><!--but2--></a>
                    </div><!--pointbuttons-->
                </div><!--pointsec-->
                <div class="creditsec">
                    <div class="credittxt">
                        <p>Credits</p>
                    </div><!--credittxt-->
                    <div class="credit">
                        <p><span id="credits_container">0</span></p>
                    </div><!--credit-->
                    <a href="rmstore.php" onmouseover="ddrivetip('&nbsp;<b>Use Credits</b>', '#24232E', 67)";  onmouseout="hideddrivetip()"><div class="but3"></div><!--but3--></a>
                </div><!--creditsec-->
                <div class="logo">
                
                </div><!--logo-->
                <div class="moneyhandsec">
                    <div class="moneytxt">
                        <p>Money in hand</p>
                    </div><!--moneytxt-->
                    <div class="moneyinhand">
                        <p>$ <span id="money_container">1,000</span></p>
                    </div><!--moneyinhand-->
                    <a href = "bank.php?dep=all" onmouseover="ddrivetip('&nbsp;<b>Deposit All</b>', '#24232E', 64)";  onmouseout="hideddrivetip()"><div class="but4" style="margin-left:-2px;"></div><!--but4--></a>
                    <a href = "sendmoney.php" onmouseover="ddrivetip('&nbsp;<b>Send Money</b>', '#24232E', 70)";  onmouseout="hideddrivetip()"><div class="but2"></div><!--but2--></a>
                </div><!--moneyhandsec-->
                <div class="cashinbanksec">
                    <div class="cashtxt">
                        <p>Your cash in bank</p>
                    </div><!--cashtxt-->
                    <div class="cashinbank">
                        <p>$ <span id="moneybank_container">0</span></p>
                    </div><!--cashinbank-->
                    <a href = "bank.php" onmouseover="ddrivetip('&nbsp;<b>Bank</b>', '#24232E', 34)";  onmouseout="hideddrivetip()"><div class="but5"></div><!--but5--></a>
                </div><!--cashinbanksec-->
            </div><!--pointslab-->
        </div><!--header-->
        <div class="progbarsection">
            <div class="healthsec">
                <div class="healthtxtsec">
                    <div class="txtleft">
                        <p>Health:</p>
                    </div><!--txtleft-->
                    <div class="txtright">
                        <p><span id="hppercentrightside">100%</span></p>
                    </div><!--txtright-->
                </div><!--healthtxtsec-->
                <div class="bar">
                    <img id='hppercent' src="images/bar12.png" onmouseover="ddrivetip('&nbsp;<b>Health</b><BR>50 / 50 [100%]', '#24232E', 95)"; onmouseout="hideddrivetip()" border="1" style="float: left;">
                </div><!--bar-->
            </div><!--healthsec-->
            <div class="energysec">
                <div class="healthtxtsec">
                    <div class="txtleft">
                        <p><a href='spendpoints.php?spend=energy' onmouseover="ddrivetip('&nbsp;<b>Refill Energy</b>', '#24232E', 75)";  onmouseout="hideddrivetip()"><font style="color:#b5b7e5;">Energy:</font></a></p>
                    </div><!--txtleft-->
                    <div class="txtright">
                        <p><span id="energypercentrightside">100%</span></p>
                    </div><!--txtright-->
                </div><!--healthtxtsec-->
                <div class="bar">
                    <img id='energypercent' src="images/bar12.png" onmouseover="ddrivetip('&nbsp;<b>Energy</b><BR>10 / 10 [100%]', '#24232E', 95)"; onmouseout="hideddrivetip()" border="1" style="float: left;">
                </div><!--bar-->
            </div><!--energysec-->
            <div class="expsec">
                <div class="exptxt">
                    <p>Exp: <b><span id="exppercentcenter">0 / 3,927 [0%]</span></b></p>
                </div><!--exptxt-->
                <div class="bar1">
                    <img id='exppercent' src="images/barxp.png" onmouseover="ddrivetip('&nbsp;<b>EXP</b><br />0 / 3,927 [0%]', '#24232E', 95)"; onmouseout="hideddrivetip()" border="1" style="float: left;">
                </div><!--bar1-->
            </div><!--expsec-->
            <div class="awakesec">
                <div class="healthtxtsec">
                    <div class="txtleft">
                        <p>Awake:</p>
                    </div><!--txtleft-->
                    <div class="txtright">
                        <p><span id="awakepercentrightside">100%</span></p>
                    </div><!--txtright-->
                </div><!--healthtxtsec-->
                <div class="bar">
                    <img id='awakepercent' src="images/bar12.png" onmouseover="ddrivetip('&nbsp;<b>Awake</b><BR>100 / 100 [100%]', '#24232E', 95)"; onmouseout="hideddrivetip()" border="1" style="float: left;">
                </div><!--bar-->
            </div><!--awakesec-->
            <div class="nervesec">
                <div class="healthtxtsec">
                    <div class="txtleft">
                        <p><a href='spendpoints.php?spend=nerve' onmouseover="ddrivetip('&nbsp;<b>Refill Nerve</b>', '#24232E', 70)";  onmouseout="hideddrivetip()"><font style="color:#b5b7e5;">Nerve:</font></a></p>
                    </div><!--txtleft-->
                    <div class="txtright">
                        <p><span id="nervepercentrightside">100%</span></p>
                    </div><!--txtright-->
                </div><!--healthtxtsec-->
                <div class="bar">
                    <img id='nervepercent' src="images/bar12.png" onmouseover="ddrivetip('&nbsp;<b>Nerve</b><BR>5 / 5 [100%]', '#24232E', 95)"; onmouseout="hideddrivetip()" border="1" style="float: left;">
                </div><!--bar-->
            </div><!--nervesec-->
        </div><!--progbarsection-->
        <div class="contentsec">
            <div class="contentleftmenu">
                <div class="menutop"></div><!--menutop-->
                <div class="menumid">
                    <div class="menuhead"></div><!--menuhead-->
                    <div class="menuitemsec">
                        <a href="index.php">Home</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="city.php"><b><i><span id="cityname_container" style="font-size: 13px">San Jose</span></i></b></a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="auction_house.php">Auction House[NEW]</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="missions.php">Missions</a>
                    </div><!--menuitemsec-->
                                        <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="dailies.php">Daily Jobs</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="crime.php">Crime</a>
                    </div><!--menuitemsec-->
                                        <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="search.php"><span id="mailbox_container">Search Mobster</span></a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="backalley.php">Back Alley</span></a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="inventory.php">Inventory</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                                        <div class="menuitemsec" style="margin-top:0px;">
                        <a href="city.php">CITYNAME HERE</span></a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="bank.php">Bank</span></a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="gym.php">Gym</span></a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="crime.php">Crimes</span></a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="yourgang.php">Your Gang</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="gangmail.php">Gang Mail</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="forums.php">Forums</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                                                         <div class="menuitemsec" style="margin-top:0px;">
                        <a href="travel.php">Travel</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="refer.php">Refer Mobster</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="vote.php">Vote for TML2</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="shoutbox.php">Shoutbox</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="chat.php">Chat Room [0]</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="support.php">Support Desk</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuhead2"></div><!--menuhead-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="contactlist.php">Contact List</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="iplog.php">IP Log</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="editaccount.php">Edit Account</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="rmstore.php"><font color='yellow'>Upgrade Account</font></a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                    <div class="menuitemsec" style="margin-top:0px;">
                        <a href="index.php?action=logout">Logout</a>
                    </div><!--menuitemsec-->
                    <div class="menudivide">
                    </div><!--menudivide-->
                </div><!--menumid-->
                <div class="menubottom"></div><!--menubottom-->
            </div><!--contentleftmenu-->
            <div class="contentright">
                <div class="smartadtop"></div><!--smartadtop-->
                <div class="admid">
                    <div class="smartadheader">
                    <p>
<p><a href='smartads.php' title='Click here to place a Smart Ad'><b>Ad:</b></a>THIS IS WHERE THE AD WILL GO</a></p>                            
                    </p>
                    </div><!--smartadheader-->
                </div><!--admid-->
                <div class="smartadbottom"></div><!--smartadbottom-->


<script>
    $(document).ready(function(){
        $(".sec_view").click(function(){
            var linkedObj = "#"+$(this).attr("link");
            var open = 0;
            if($(this).hasClass("sec_close")){
                $(linkedObj).show('slow');
                $(this).removeClass("sec_close");
                open = 1;
            }else{
                $(linkedObj).hide('slow');
                $(this).addClass("sec_close");
            }            
            
            $.post("secview.php",{"do":"saveshd","section":$(this).attr("link"),"open":open},function(data){
               ;//alert(data); 
            });
        });      
    });
</script>

<div class="contenthead">Contenthead</div><!--contenthead-->
<div class="contentcontent">
    <p align='center'>
        THIS IS A CONTENT HEAD TEST<br />
            </p>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->
<!--contentcontent--><script src="includes/jquery.js" ></script>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '241563559294323', // App ID
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true,  // parse XFBML
      oauth      : true
    });
    var userid = "12783";
    FB.Event.subscribe('edge.create',
        function(response) {
        $.ajax({
                type: "POST",
                url: "/fbreceive.php",
                data: "data="+response + "---" + userid + "---" + "like",        
                cache: false
            });
        }
    );
  };
  (function(d){
           var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
           if (d.getElementById(id)) {return;}
           js = d.createElement('script'); js.id = id; js.async = true;
           js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=241563559294323";
           ref.parentNode.insertBefore(js, ref);
  }(document));
</script>

<script>
</script>

    </div><!--container-->
            <div class="footer">
                <div class="footerlinks">
                    <a href="citizens.php">2,783 Total Mobsters</a>
                    &nbsp;&nbsp;<span style="color:#FFFFFF;">|</span>&nbsp;&nbsp;
                    <a href="online.php">1 Mobsters Online</a>
                    &nbsp;&nbsp;<span style="color:#FFFFFF;">|</span>&nbsp;&nbsp;
                    <a href="24hour.php">2 Mobsters Online (24 Hours)</a>
                </div><!--footerlinks-->

<script>$(document).ready(function(){    $("#level_container").html( "1" ); });</script>
<script>$(document).ready(function(){    $("#money_container").html( "1,000"); });</script>
<script>$(document).ready(function(){    $("#moneybank_container").html( "0"); });</script>
<script>$(document).ready(function(){    $("#points_container").html( "0 " ); });</script>
<script>$(document).ready(function(){    $("#credits_container").html( "0 " ); });</script>
<script>$(document).ready(function(){    $("#hppercent").attr( "src","images/bar/?100" ); setToolTip( "hppercent", "&nbsp;<b>Health</b><br />50 / 50 [100%]" );});</script>
<script>$(document).ready(function(){    $("#hppercentrightside").html( "100%" ); });</script>
<script>$(document).ready(function(){    $("#energypercent").attr( "src","images/bar/?100" ); setToolTip( "energypercent", "&nbsp;<b>Energy</b><br />10 / 10 [100%]" );});</script>
<script>$(document).ready(function(){    $("#energypercentrightside").html( "100%" ); });</script>
<script>$(document).ready(function(){    $("#awakepercent").attr( "src","images/bar/?100" ); setToolTip( "awakepercent", "&nbsp;<b>Awake</b><br />100 / 100 [100%]" );});</script>
<script>$(document).ready(function(){    $("#awakepercentrightside").html( "100%" ); });</script>
<script>$(document).ready(function(){    $("#nervepercent").attr( "src","images/bar/?100" ); setToolTip( "nervepercent", "&nbsp;<b>Nerve</b><br />5 / 5 [100%]" );});</script>
<script>$(document).ready(function(){    $("#nervepercentrightside").html( "100%" ); });</script>
<script>$(document).ready(function(){    $("#exppercent").attr( "src","images/barxp/?0" ); setToolTip( "exppercent", "&nbsp;<b>EXP</b><br />0 / 3,927 [0%]" );});</script>
<script>$(document).ready(function(){    $("#exppercentcenter").html( "0 / 3,927 [0%]" ); });</script>
  <div class='foot2'>
                        <p>This page was generated in 0.044 seconds</p>
                    </div><!--foot2-->                <div class="copyright">
                    <p>Copyright &copy; <a href="http://www.mob-gamez.com/" target="_blank">Mob-Gamez.com</a>. All Rights Reserved. <a href="http://www.fatalplay.com/tos.php">Terms of Service</a></p>
                </div><!--copyright-->
            </div><!--footer-->
            
        </div><!--contentsec-->

</body>

<!-- Quantcast Tag -->
<script type="text/javascript">
var _qevents = _qevents || [];

(function() {
var elem = document.createElement('script');
elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
elem.async = true;
elem.type = "text/javascript";
var scpt = document.getElementsByTagName('script')[0];
scpt.parentNode.insertBefore(elem, scpt);
})();

_qevents.push({
qacct:"p-9665Jicul1f0U"
});
</script>

<noscript>
<div style="display:none;">
<img src="//pixel.quantserve.com/pixel/p-9665Jicul1f0U.gif" border="0" height="1" width="1" alt="Quantcast"/>
</div>
</noscript>
<!-- End Quantcast tag -->

</html>
