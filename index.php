<?php
include 'header.php';

if ($user_class->firstlogin1 == 0) {
    $db->query("UPDATE grpgusers SET firstlogin1 = 1 WHERE id = ?");
    $db->execute([$user_class->id]);
    Send_Event2($user_class->id, "Is the latest thug on the streets.", $user_class->id);
    Send_Event1($user_class->id, "Is the latest thug on the streets.", $user_class->id);
    Send_Event($user_class->id, "<span style='color:white;'>Welcome To Mafia Lords!<br>To get you started we are giving you:</span><br><span style='color:white;font-weight:bold;'>&bull;&nbsp;3 VIP Days<br>&bull;&nbsp;$100,000 Cash<br>&bull;&nbsp;1,250 Points</span>", $user_class->id);
}

function tableRow($label, $value) {
    return "<tr><th width='10%'>$label:</th><td width='30%'>$value</td></tr>";
}

$content = <<<HTML
<div class="contenthead floaty">
    <h1>General Information</h1>
    <table id="newtables" style="width:100%;">
        %s
    </table>
</div>

<div class="contenthead floaty">
    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>Stat Information</h4></span>
    <table id="newtables" style="width:100%;">
        %s
    </table>
</div>

<div class="contenthead floaty">
    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>Modded Stats Information</h4></span>
    <table id="newtables" style="width:100%;">
        %s
    </table>
</div>

<div class="contenthead floaty">
    <h1>Battle Statistics</h1>
    <table id="newtables" style="width:100%;">
        %s
    </table>
</div>

<div class="contenthead floaty">
    <h1>Crime Rankings</h1>
    <table id="newtables" style="width:100%;">
        %s
    </table>
</div>

<div class="contenthead floaty">
    <h1>Bonus Stats</h1>
    <table id="newtables" style="width:100%;">
        %s
    </table>
</div>

<div class="contenthead floaty">
    <h1>EXP Calculator</h1>
    <div class="floaty">
        <div class="flexcont">
            <div class="flexele" style="border-right:thin solid #333;">
                What level are you aiming for? <input type="text" oninput="calcEXP();" id="levelcalc" size="8" />
            </div>
            <div class="flexele">
                <span id="levelrtn">
                    You need %s EXP to get to level %s.
                </span>
            </div>
        </div>
    </div>
</div>

<center>
    <div class="flexcont" style="margin: 0 auto; display: flex; flex-wrap: wrap;">
        <div class="flexele"></div>
        <a href="achievements.php"><div class="flexele floatylinks"><font color=white>[Achievements]</font></div></a>
        <a href="translog.php"><div class="flexele floatylinks"><font color=white>[Transfer Logs]</font></div></a>
        <a href="attacklog.php"><div class="flexele floatylinks"><font color=white>[Attack Log]</font></div></a>
        <a href="defenselog.php"><div class="flexele floatylinks"><font color=white>[Defense Log]</font></div></a>
        <a href="muglog.php"><div class="flexele floatylinks"><font color=white>[Mug Log]</font></div></a>
        <a href="spylog.php"><div class="flexele floatylinks"><font color=white>[Spy Log]</font></div></a>
        <div class="flexele"></div>
    </div>
</center>
HTML;

$tables = [
    tableRow('Name', "<a href='profiles.php?id={$user_class->id}'>{$user_class->formattedname}</a>"),
    tableRow('HP', prettynum($user_class->formattedhp)),
    tableRow('Level', $user_class->level . ($user_class->level >= 1000 ? ' <a href="prestige.php"><span class="notify">[Prestige]</span></a>' : '')),
    tableRow('Energy', prettynum($user_class->formattedenergy)),
    tableRow('Money', '$' . prettynum($user_class->money)),
    tableRow('Awake', prettynum($user_class->formattedawake)),
    tableRow('Bank', '$' . prettynum($user_class->bank)),
    tableRow('Nerve', prettynum($user_class->formattednerve)),
    tableRow('EXP', prettynum($user_class->formattedexp)),
    tableRow('Work EXP', prettynum($user_class->workexp)),
    tableRow('RM Days', prettynum($user_class->rmdays)),
    tableRow('Activity Points', "<a href='spendactivity.php'>Activity Points Store [" . prettynum($user_class->apoints) . " Activity Points]</a>"),
];

$content = sprintf($content, ...$tables, prettynum(experience($user_class->level + 1) - $user_class->exp), prettynum($user_class->level + 1));

echo $content;

include "footer.php";
?>
