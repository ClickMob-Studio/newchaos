<?php
include 'header.php';

$event = getScheduledEvent();

?>

<div class='box_top'>Gym</div>
<div class='box_middle'>
    <div class='pad'>
        <?php


        if ($user_class->hospital > 0) {
            echo Message("You can't train at the gym if you are in the hospital.");
            include 'footer.php';
            die();
        }
        ?>
        <style>
            @media only screen and (max-width: 750px) {
                .btna {
                    black-space: normal;
                    /* Allow text to wrap within buttons */
                    word-wrap: break-word;
                    /* Break words that exceed the width */
                    max-width: 100px;
                }
            }
        </style>
        <style>
            .section-header {
                background: #111;
                border: 1px solid rgba(255, 255, 255, 0.5);
                color: #fff;
                font-family: monospace;
                font-size: 18px;
                margin: 0 0 18px;
                padding: 6px 0;
                text-align: center;
                text-transform: uppercase;
                box-shadow: 0 8px 6px -6px black;
                text-shadow: 0px 1px black;
            }

            .section {
                border: 1px solid rgba(255, 255, 255, 0.5);
                margin: 0 0 25px;
                width: 100%;
                box-shadow: 0 8px 6px -6px black;
            }

            .section-row {
                display: flex;
                margin: 0 -9px;
            }

            .section-row>.section {
                margin: 0 9px 18px;
            }

            .section-title {
                background: #111;
                border-bottom: 1px solid rgba(255, 255, 255, 0.5);
                color: #fff;
                font-family: monospace;
                font-size: 14px;
                margin: 0;
                padding: 6px 0;
                text-align: center;
                text-transform: uppercase;
            }

            .section-items {
                padding: 12px 9px;
                display: flex;
                flex-wrap: wrap;
            }

            .new-shop-item {
                width: 100px;
                margin: 0 auto;
                text-align: center;
                background: #000;
                border: 3px solid #111;
            }

            /* .new-shop-item.has-tooltip:hover::before{
        bottom: 120px;
    }

    .new-shop-item.has-tooltip:hover::after{
        bottom: 100px;
    } */

            .new-shop-item--img {
                background: url('https://chaoscity.co.uk/css/images/empty.jpg') center;
                background-repeat: no-repeat;
                width: 100%;
                min-height: 100px;
                max-width: 250px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .new-shop-item--img h5 {
                margin: 0;
                text-transform: uppercase;
                font-family: monospace;
                font-size: 24px;
                text-shadow: 0px 0px 5px rgb(200, 0, 0);
            }

            @media (max-width: 767px) {
                #pricing-table {
                    width: 100%
                }

                #pricing-table .plan {
                    /* Additional styles to make each plan display on a separate line */
                    margin-bottom: 20px;
                    /* Adjust spacing between plans */
                    width: 100%;
                    float: none;
                }
            }

            .new-shop-item-img-preview {
                background: rgba(0, 0, 0, 0.85);
                width: 120%;
                margin: 0 -10px;
                padding: 3px 0;
                border: 1px solid #fff;
                height: 21px;
                line-height: 21px;
            }

            .new-shop-item--price {
                display: flex;
                justify-content: center;
                padding: 6px;
                border-bottom: 3px solid #111;
            }

            .new-shop-item--price span {
                line-height: 25px;
                margin-left: 12px;
                font-weight: 700;
                font-size: 24px;
            }

            .new-shop-item--buy {
                padding: 6px;
            }
        </style>
        <script type="text/javascript">
            function trainrefill(stat) {
                var isMegaTrainChecked = $('#mega_train').is(':checked') ? 'yes' : 'no';
                $(".notice").css("display", "block"); // Display the notice
                $(".notice").html("<img src='images/ajax-loader.gif?' />");
                $.post("ajax_supergym.php", {
                    'amnt': $('#' + stat).val(),
                    'stat': stat,
                    'what': 'trainrefill',
                    'mega_train': isMegaTrainChecked  // Include the status of the "mega train" checkbox
                }, function (callback) {
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
                var isMegaTrainChecked = $('#mega_train').is(':checked') ? 'yes' : 'no';
                $(".notice").css("display", "block"); // Display the notice
                $(".notice").html("<img src='images/ajax-loader.gif?' />");
                $.post("ajax_supergym.php", {
                    'amnt': $('#' + stat).val(),
                    'stat': stat,
                    'what': 'train',
                    'mega_train': isMegaTrainChecked  // Correctly include the status of the "mega train" checkbox
                }, function (callback) {
                    var info = callback.split("|");
                    $(".notice").html(info[0]);
                    $("#" + stat + "amnt").html(info[1]);
                    $(".genBars").html(info[2]);
                    if (info[3]) {
                        $("#strength").val(info[3]);
                        $("#defense").val(info[3]);
                        $("#speed").val(info[3]);
                        $("#agility").val(info[3]);
                    }
                });
            }
            function refill(att) {
                $(".notice").html("<img src='images/ajax-loader.gif?' />");

                $.post("ajax_supergym.php", { 'att': att, 'what': 'refill' }, function (callback) {
                    var info = callback.split("|");
                    $(".notice").html(info[0]);
                    $(".points").html(info[1]);
                    $(".genBars").html(info[2]);
                    if (info[3]) {
                        $("#strength").val(info[3]);
                        $("#defense").val(info[3]);
                        $("#speed").val(info[3]);
                        $("#agility").val(info[3]);
                    }
                });
            }
        </script>
        <br>
        <style>
            .refills {
                background: #0e0e0e;
                color: #FFF;
                border: #303030 medium solid;
                color: #FFF;
                padding: 3px;
            }
        </style>

        <? if (!empty($event)): ?>
            <div class='dcPanel p-3 mb-4 event-countdown' data-end="<?= $event['end'] ?>"
                style="text-align:center;background-color:#3d00008a">
                <span>Event is on-going, all types of training is
                    multiplied by <?= $event['multiplier'] ?>!</span>
                <br />
                <div style="margin-top:6px;color: #c8c8c8; font-weight: bold;">Event ends in
                    <span class='countdown-text'><?= secondsToTime($event['end'] - time()) ?></span>.
                </div>
            </div>
        <? endif; ?>

        <div class="contenthead floaty">
            <span
                style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"></span>

            <span class='notice' style="display:none;"></span>

            </table>

            <table class="responsive" width="100%" align="center">
                <tr>
                    <th align="center" style="padding-bottom: 10px;width:33%;"><b>
                            <center>STRENGTH</center>
                        </b></th>
                    <th align="center" style="padding-bottom: 10px;width:33%;"><b>
                            <center>DEFENSE</center>
                        </b></th>
                    <th align="center" style="padding-bottom: 10px;width:33%;"><b>
                            <center>SPEED</center>
                        </b></th>
                    <th align="center" style="padding-bottom: 10px;width:33%;"><b>
                            <center>AGILITY</center>
                        </b></th>
                </tr>
                <tr>
                    <td align="center" style="padding-bottom: 10px;"><input id='strength' type='text' name='energy1'
                            value="<?= $user_class->energy ?>" onKeyPress="return numbersonly(this, event)"></td>
                    <td align="center" style="padding-bottom: 10px;"><input id='defense' type='text' name='energy2'
                            value="<?= $user_class->energy ?>" onKeyPress="return numbersonly(this, event)"></td>
                    <td align="center" style="padding-bottom: 10px;"><input id='speed' type='text' name='energy3'
                            value="<?= $user_class->energy ?>" onKeyPress="return numbersonly(this, event)" />
                    </td>
                    <td align="center" style="padding-bottom: 10px;"><input id='agility' type='text' name='energy3'
                            value="<?= $user_class->energy ?>" onKeyPress="return numbersonly(this, event)" />
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding-bottom: 10px;"><span id='strengthamnt'>
                            <?= prettynum($user_class->strength) ?></span> [Ranked:
                        <?= getRank("$user_class->id", "strength") ?>]
                    </td>
                    <td align="center" style="padding-bottom: 10px;"><span
                            id='defenseamnt'><?= prettynum($user_class->defense) ?></span> [Ranked:
                        <?= getRank("$user_class->id", "defense") ?>]
                    </td>
                    <td align="center" style="padding-bottom: 10px;"><span
                            id='speedamnt'><?= prettynum($user_class->speed) ?></span> [Ranked:
                        <?= getRank("$user_class->id", "speed") ?>]
                    </td>
                    <td align="center" style="padding-bottom: 10px;"><span
                            id='agilityamnt'><?= prettynum($user_class->agility) ?></span> [Ranked:
                        <?= getRank("$user_class->id", "agility") ?>]
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding-bottom: 10px;"><button
                            onclick="train('strength');">Strength</button></td>
                    <td align="center" style="padding-bottom: 10px;"><button
                            onclick="train('defense');">Defense</button></td>
                    <td align="center" style="padding-bottom: 10px;"><button onclick="train('speed');">Speed</button>
                    </td>
                    <td align="center" style="padding-bottom: 10px;"><button
                            onclick="train('agility');">Agility</button></td>
                </tr>
            </table>

        </div>
        <?php
        include 'footer.php';
        ?>