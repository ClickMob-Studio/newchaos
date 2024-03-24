<?php
include 'header.php';
$m->set('lastcrimeload.'.$user_class->id, time());
if ($user_class->hospital > 0) {
    echo Message("You can't train at the gym if you are in the hospital.");
    include 'footer.php';
    die();
}
?>
<style>
     @media only screen and (max-width: 750px) {
      .btna {
        white-space: normal; /* Allow text to wrap within buttons */
        word-wrap: break-word; /* Break words that exceed the width */
        max-width: 100px;
    }
}
    </style>
    <?php
if ($printcaptcha != "") {
    echo $printcaptcha;
} else {
    ?>
    <script type="text/javascript">
        function trainrefill(stat) {
            $(".notice").html("<img src='images/ajax-loader.gif?' />");
            $.post("ajax_gym.php", {'amnt': $('#' + stat).val(), 'stat': stat, 'what': 'trainrefill'}, function (callback) {
                var info = callback.split("|");
                $(".notice").html(info[0]);
                $(".points").html(info[1]);
                $("#" + stat + "amnt").html(info[2]);
                if (info[3]) {
                    $("#strength").val(info[3]);
                    $("#defense").val(info[3]);
                    $("#speed").val(info[3]);
                }
            });
        }
        function train(stat) {
            $(".notice").html("<img src='images/ajax-loader.gif?' />");
            $.post("ajax_gym.php", {'amnt': $('#' + stat).val(), 'stat': stat, 'what': 'train'}, function (callback) {
                var info = callback.split("|");
                $(".notice").html(info[0]);
                $("#" + stat + "amnt").html(info[1]);
                $(".genBars").html(info[2]);
                if (info[3]) {
                    $("#strength").val(info[3]);
                    $("#defense").val(info[3]);
                    $("#speed").val(info[3]);
                }
            });
        }
        function refill(att) {
            $(".notice").html("<img src='images/ajax-loader.gif?' />");
            $.post("ajax_gym.php", {'att': att, 'what': 'refill'}, function (callback) {
                var info = callback.split("|");
                $(".notice").html(info[0]);
                $(".points").html(info[1]);
                $(".genBars").html(info[2]);
                if (info[3]) {
                    $("#strength").val(info[3]);
                    $("#defense").val(info[3]);
                    $("#speed").val(info[3]);
                }
            });
        }
    </script>
    <br>
    <style>
        input,button{background:#0e0e0e;color:#FFF;border:#303030 medium solid;text-slign:center}
        .refills{background:#0e0e0e;color:#FFF;border:#303030 medium solid;color:#FFF;padding:3px;}
    </style>

<h3>Your Training</h3>
<hr>

    <br /><span class='notice'></span><br /><br />
    </table>
    <table class="responsive" width="100%" align="center">
        <tr>
            <th align="center" style="padding-bottom: 10px;width:33%;"><b><center>STRENGTH</center></b></th>
            <th align="center" style="padding-bottom: 10px;width:33%;"><b><center>DEFENSE</center></b></th>
            <th align="center" style="padding-bottom: 10px;width:33%;"><b><center>SPEED</center></b></th>
        </tr>
        <tr>
            <td align="center" style="padding-bottom: 10px;"><input id='strength' type='text' name='energy1' size='3' value="<?php echo $user_class->energy ?>" onKeyPress="return numbersonly(this, event)"></td>
            <td align="center" style="padding-bottom: 10px;"><input id='defense' type='text' name='energy2' size='3' value="<?php echo $user_class->energy ?>" onKeyPress="return numbersonly(this, event)"></td>
            <td align="center" style="padding-bottom: 10px;"><input id='speed' type='text' name='energy3' size='' value="<?php echo $user_class->energy ?>" onKeyPress="return numbersonly(this, event)"/></td>
        </tr>
        <tr><td align="center" style="padding-bottom: 10px;"><span id='strengthamnt'><?php
                    echo prettynum($user_class->strength);
                    ?></span> [Ranked: <?php
                echo getRank("$user_class->id", "strength");
                ?>]</td><td align="center" style="padding-bottom: 10px;"><span id='defenseamnt'><?php
                    echo prettynum($user_class->defense);
                    ?></span> [Ranked: <?php
                    echo getRank("$user_class->id", "defense");
                    ?>]</td><td align="center" style="padding-bottom: 10px;"><span id='speedamnt'><?php
                    echo prettynum($user_class->speed);
                    ?></span> [Ranked: <?php
                    echo getRank("$user_class->id", "speed");
                    ?>]</td>
        <tr>
            <td align="center" style="padding-bottom: 10px;"><button onclick="train('strength');">Strength</button></td>
            <td align="center" style="padding-bottom: 10px;"><button onclick="train('defense');">Defense</button></td>
            <td align="center" style="padding-bottom: 10px;"><button onclick="train('speed');">Speed</button></td>
        </tr>
        <tr>
            <td align="center" style="padding-bottom: 10px;"><button class="btna" onclick="trainrefill('strength');">Strength + Refills</button></td>
            <td align="center" style="padding-bottom: 10px;"><button class="btna" onclick="trainrefill('defense');">Defense + Refills</button></td>
            <td align="center" style="padding-bottom: 10px;"><button class="btna" onclick="trainrefill('speed');">Speed + Refills</button></td>
        </tr>
        <tr>
            <td align="center" style="padding-bottom: 10px;" colspan='3'><hr /></td>
        </tr>
        <tr>
            <td align="center" style="padding-bottom: 10px;"><button onclick="refill('energy');">Refill Energy</button></td>
            <td align="center" style="padding-bottom: 10px;"><button onclick="refill('awake');">Refill Awake</button></td>
            <td align="center" style="padding-bottom: 10px;"><button onclick="refill('both');">Refill Both</button></td>
        </tr>
        <tr>
            <td align="center" style="padding-bottom: 10px;" colspan='3'><span style='color:white;font-weight:bold;'>Super Trains: Click and hold on your desired train, then hold <font color=red>[Enter]</font> button for Super fast trains.<br>You can turn auto refills on</font> <font color=red><a href="preferences.php?refills">[Here]</a></font><br>
            <center><span style="color:white;">  Click <a href="gym2.php">[Here]</a> for Mobile Gym use</span></a></center><br>

</tr>
    </table>
    <?php
}
include 'footer.php';
?>