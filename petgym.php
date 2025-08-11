<?php
include 'header.php';
?>

<div class='box_top'>Pet Gym</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        $pet_class = new Pet($user_class->id);
        if (!isset($pet_class) || !isset($pet_class->id)) {
            diefun("You don't have a pet");
        }

        if ($pet_class->leash == 1) {
            diefun("Your cannot train your pet whilst it is leashed!");
        }

        print "<div class='includepet'>";
        include 'includepet.php';
        print "</div>";
        print "
<center>
    <span class='notice'>Your pet can currently train " . prettynum($pet_class->energy) . " times.</span><br /><br />
    <table width='100%' id='newtables'>
        <tr>
            <th colspan='3'>Pet Gym</th>
        </tr>
        <tr>
            <td width='33%'><input type='text' id='str' size='8' value='$pet_class->energy' onKeyPress='return numbersonly(this, event)' /></td>
            <td width='34%'><input type='text' id='def' size='8' value='$pet_class->energy' onKeyPress='return numbersonly(this, event)' /></td>
            <td width='33%'><input type='text' id='spe' size='8' value='$pet_class->energy' onKeyPress='return numbersonly(this, event)' /></td>
        </tr>
        <tr>
            <td><input type='submit' onclick='train(\"str\");' value='Strength' /></td>
            <td><input type='submit' onclick='train(\"def\");' value='Defense' /></td>
            <td><input type='submit' onclick='train(\"spe\");' value='Speed' /></td>
        </tr>
        <tr>
            <td><input type='submit' onclick='trainrefill(\"str\");' value='Strength + Refills' /></td>
            <td><input type='submit' onclick='trainrefill(\"def\");' value='Defense + Refills' /></td>
            <td><input type='submit' onclick='trainrefill(\"spe\");' value='Speed + Refills' /></td>
        </tr>
        <tr>
            <th><input type='submit' onclick='refill(\"energy\");' value='Refill Energy' /></th>
            <th><input type='submit' onclick='refill(\"awake\");' value='Refill Awake' /></th>
            <th><input type='submit' onclick='refill(\"both\");' value='Refill Both' /></th>
        </tr>
    </table><br />
    <table width='100%' id='newtables'>
        <tr>
            <th colspan='4'>Pet Attributes</td>
        </tr>
        <tr>
            <th width='12.5%'>Strength</th>
            <td width='37.5%' id='stramnt'>" . prettynum($pet_class->strength) . "</td>
            <th width='12.5%'>Defense</th>
            <td width='37.5%' id='defamnt'>" . prettynum($pet_class->defense) . "</td>
        </tr>
        <tr>
            <th>Speed</th>
            <td id='speamnt'>" . prettynum($pet_class->speed) . "</td>
            <th>Total</th>
            <td>" . prettynum($pet_class->totalatri) . "</th>
        </tr>
    </table>
</center>
</td></tr>";
        print <<<TEXT
    <script type="text/javascript">
        function trainrefill(stat) {
            $(".notice").html("<img src='images/ajax-loader.gif?' />");
            $.post("ajax_petgym.php", {'amnt': $('#' + stat).val(), 'stat': stat, 'what': 'trainrefill'}, function (callback) {
                var info = callback.split("|");
                $(".notice").html(info[0]);
                $(".points").html(info[1]);
                $("#" + stat + "amnt").html(info[2]);
                if (info[3]) {
                    $("#str").val(info[3]);
                    $("#def").val(info[3]);
                    $("#spe").val(info[3]);
                }
            });
        }
        function train(stat) {
            $(".notice").html("<img src='images/ajax-loader.gif?' />");
            $.post("ajax_petgym.php", {'amnt': $('#' + stat).val(), 'stat': stat, 'what': 'train'}, function (callback) {
                var info = callback.split("|");
                $(".notice").html(info[0]);
                $("#" + stat + "amnt").html(info[1]);
                if (info[2]) {
                    $("#str").val(info[2]);
                    $("#def").val(info[2]);
                    $("#spe").val(info[2]);
                }
                $(".includepet").html(info[3]);
            });
        }
        function refill(att) {
            $(".notice").html("<img src='images/ajax-loader.gif?' />");
            $.post("ajax_petgym.php", {'att': att, 'what': 'refill'}, function (callback) {
                var info = callback.split("|");
                $(".notice").html(info[0]);
                $(".points").html(info[1]);
                if (info[2]) {
                    $("#str").val(info[2]);
                    $("#def").val(info[2]);
                    $("#spe").val(info[2]);
                }
                $(".includepet").html(info[3]);
            });
        }
    </script>
TEXT;
        include 'footer.php';