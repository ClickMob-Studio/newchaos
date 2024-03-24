<?php
include 'header.php';
?>
<style>
    .table > tbody > tr:nth-child(odd){
        background: #000;
    }
</style>
<tr>
<div class='contenthead'>Hunting</div><center><br />
    You are level <font color=green>1.5</font>/100
    <br /> 
    <table class='myTable'>
        <tr>
            <td width='15%' class='contenthead'><b></b></td>
            <td width='50%' class='contenthead'><b></b></td>
            <td width='35%' class='contenthead'><b></b></td>
        </tr>
        <tr>
            <td>Tactics</td><td>Level</td><td></td></tr>
        <tr><td>
                <!--First tooltip-->
                Boost<a href="#" class="tooltip">
                    [<font color=red>?</font>]
                    <span>
                        <img class="callout" src="cssttp/callout_black.gif" />
                        <strong><center>1/250</center></strong><br />
                        <center>Increase Your Stats by 0.5% Per Level.<br /> You are currently receiving a 0.5% Bonus.
                        </center></span>
                </a>
            </td><td> 1/250 </td><td><u>Upgrade</u></td></tr>
        <tr><td><!--First tooltip-->
                Nerve Bonus Level<a href="#" class="tooltip">
                    [<font color=red>?</font>]
                    <span>
                        <img class="callout" src="cssttp/callout_black.gif" />
                        <strong>Information About this shit.</strong><br />
                        Bla bla bla some shit some shit some shit<br /> Some More shit and some other shit.
                    </span>
                </a></td><td> 1/100 </td><td><u>Upgrade</u></td></tr>
        <tr><td><!--First tooltip-->
                Exp Bonus Level<a href="#" class="tooltip">
                    [<font color=red>?</font>]
                    <span>
                        <img class="callout" src="cssttp/callout_black.gif" />
                        <strong>Information About this shit.</strong><br />
                        Bla bla bla some shit some shit some shit<br /> Some More shit and some other shit.
                    </span>
                </a></td><td> 1/100 </td><td><u>Upgrade</u></td></tr>
        </div></div>
        <?php
        include 'footer.php';
        ?>