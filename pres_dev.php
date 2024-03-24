<?php
include "header.php";
if ($user_class->admin != 1)
    header('index.php');

$total = $user_class->strength + $user_class->defense + $user_class->speed;
$perc = $total / 100000000;
$perc = ($perc > 100) ? 100 : floor($perc);
$ptperc = $user_class->points / 250;
$ptperc = ($ptperc > 100) ? 100 : floor($ptperc);
$lvlperc = $user_class->level / 4;
$lvlperc = ($lvlperc > 100) ? 100 : floor($lvlperc);

$_stat = ($perc >= 100) ? 'style="color: #44bd32"' : '';

echo '
<style>
    .prestige_container {
        text-align: center;
    }
    .prestige_container h1 {
        text-transform: capitalize;
        font-size: 1.3em;
        margin-bottom: 0px;
    }
    .prestige_container span {
        color: #44bd32;
    }
    .p_levels {
        margin-top: 1em;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }
    .p_level {
        padding: 5px 20px;
        margin: 6px;
        border: 3px black solid;
        background-color: #273c75;
        font-size: 1.25em;
        cursor: pointer;
    }
    .complete {
        background-color: #44bd32;
        color: black;
        cursor: not-allowed;
    }
    .header {
        font-size: 1.3em;
    }
    .p_stat {
        font-size: 1.2em;
    }
</style>

<div class="prestige_container">

    <div class="header">
        <h1>Choose Your Level Sacrifice</h1>
    </div>
    <div class="sub p_levels">
        <div class="header"></div>
        <div class="p_level">400</div>
        <div class="p_level">500</div>
        <div class="p_level complete">600</div>
        <div class="p_level">700</div>
        <div class="p_level">800</div>
        <div class="p_level">900</div>
    </div>

    <div class="sub p_stats">
        <div class="header"><h1>Stats</h1></div>
        <div class="p_stat" ' . $_stat . '>' . number_format($total, 0) . ' / 10,000,000,000 (10 Billion)</div>
    </div>

</div>';

include "footer.php";
?>