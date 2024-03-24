<?php
include 'header.php';

if ($user_class->firstlogin1 == 0) {
    $db->query("UPDATE grpgusers SET firstlogin1 = 1 WHERE id = ?");
    $db->execute(array($user_class->id));
    Send_Event2($user_class->id, "Is the latest thug on the streets.", $user_class->id);
    Send_Event1($user_class->id, "Is the latest thug on the streets.", $user_class->id);
    Send_Event($user_class->id, "Welcome To TheMafiaLife!<br>To get you started we are giving you:<br>&bull; 3 RM Days<br>&bull; $100,000 Cash<br>&bull; 1,000 Points<br>Before you get started please read the <b><a href='gamerules.php'>Game Rules</a></b>", $user_class->id);
}

// Function to generate a table row
function generateRow($label1, $data1, $label2, $data2) {
    return "<tr>
                <th>{$label1}:</th>
                <td>{$data1}</td>
                <th>{$label2}:</th>
                <td>{$data2}</td>
            </tr>";
}

// Function to generate section
function generateSection($title, $tableContent) {
    return "
    <div class='content-section'>
        <div class='contenthead'>{$title}</div>
        <table class='content-table'>
            {$tableContent}
        </table>
    </div>";
}

// Generating content sections
$generalInfo = generateRow("Name", "<a href='profiles.php?id={$user_class->id}'>{$user_class->formattedname}</a>", "HP", prettynum($user_class->formattedhp))
    . generateRow("Level", $user_class->level . (($user_class->level >= 400) ? " <a href='pres.php'><span class='notify'>[Prestige]</span></a>" : ""), "Energy", prettynum($user_class->formattedenergy))
    . generateRow("Money", "$" . prettynum($user_class->money), "Awake", prettynum($user_class->formattedawake))
    . generateRow("Bank", "$" . prettynum($user_class->bank), "Nerve", prettynum($user_class->formattednerve))
    . generateRow("EXP", prettynum($user_class->formattedexp), "Work EXP", prettynum($user_class->workexp))
    . generateRow("RM Days", prettynum($user_class->rmdays), "Activity Points", "<a href='spendactivity.php'>" . prettynum($user_class->apoints) . "</a>");

$statInfo = generateRow("Strength", prettynum($user_class->strength) . " [Ranked: " . getRank("$user_class->id", "strength") . "]", "Defense", prettynum($user_class->defense) . " [Ranked: " . getRank("$user_class->id", "defense") . "]")
    . generateRow("Speed", prettynum($user_class->speed) . " [Ranked: " . getRank("$user_class->id", "speed") . "]", "Total", prettynum($user_class->totalattrib) . " [Ranked: " . getRank("$user_class->id", "total") . "]");

// Similarly, you can generate other sections...

// Outputting the content
echo generateSection("General Information", $generalInfo);
echo generateSection("Stat Information", $statInfo);
// ... Output other sections similarly

include "footer.php";
?>