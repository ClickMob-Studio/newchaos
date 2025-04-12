<?php
include 'header2.php';

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$lastactive = $redis->get('lastactive_' . $user_class->id);
$timePassedEnough = (time() - $lastactive) > 120; // 2 minutes
if ($timePassedEnough) {
    $db->query("UPDATE grpgusers SET crimes = 'newcrimes', lastactive = unix_timestamp() WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
    $redis->set('lastactive_' . $user_class->id, time());
}
error_reporting(0);

$db->query("SELECT `name`, mission.crimes as crimestarget, missions.crimes as crimesdone FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE `userid` = ? AND `completed` = \"no\" LIMIT 1");
$db->execute(array(
    $user_class->id
));
$activeMission = $db->fetch_row()[0];

$tempItemUse = getItemTempUse($user_class->id);

if ($tempItemUse['ghost_vacuum_time'] > time()) {
    $db->query("SELECT * FROM crimes ORDER BY nerve DESC");
    $db->execute();
} else {
    $db->query("SELECT * FROM crimes WHERE id < 51 ORDER BY nerve DESC");
    $db->execute();
}

$rows = $db->fetch_row();
?>

<style>
    .gold {
        color: gold;
        font-size: 24px;
    }

    .gray {
        color: gray;
        font-size: 24px;
    }
</style>

<div class="max-w-7xl mx-auto mb-2">
    <h1 class="text-5xl text-white">Crimes: <?= $lastactive ?></h1>
</div>

<div class="max-w-7xl mx-auto flex">

    <div class="w-full border border-[#FF9696]/10 bg-black/40 border-6 rounded-lg p-4"></div>

    <?php
    $error = ($user_class->fbitime > 0) ? "You can't do crimes if you're in FBI Jail!" : "";
    $error = ($user_class->jail > 0) ? "You can't do crimes if you're in prison!" : "";
    $error = ($user_class->hospital > 0) ? "You can't do crimes if you're in hospital!" : $error;
    if (!empty($error)) {
        diefun($error);

    }

    if (isset($_GET['ner'])) {
        switch ($_GET['ner']) {
            case 0:
                if ($user_class->nerref != 0)
                    diefun("Nice Try.");
                if ($user_class->points < 250)
                    diefun("You do not have enough points.");
                $user_class->points -= 250;
                $user_class->nerref = 2;
                $db->query("UPDATE grpgusers SET nerref = ?, points = ?, nerreftime = unix_timestamp() WHERE id = ?");
                $db->execute(array(
                    $user_class->nerref,
                    $user_class->points,
                    $user_class->id
                ));
                break;
            case 1:
                if ($user_class->nerref == 0)
                    diefun("Nice Try.");
                $user_class->nerref = 2;
                $db->query("UPDATE grpgusers SET nerref = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->nerref,
                    $user_class->id
                ));
                mysql_query("UPDATE grpgusers SET nerref = $user_class->nerref WHERE id = $user_class->id");
                break;
            case 2:
                if ($user_class->nerref == 0)
                    diefun("Nice Try.");
                $user_class->nerref = 1;
                $db->query("UPDATE grpgusers SET nerref = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->nerref,
                    $user_class->id
                ));
                break;
        }
    }

    ?>
    <table>
        <tbody>
            <tr>
                <td>
                    <div class="flexele floaty" style="margin:3px;">
                        <hr class="my-4 border-black/30" />

                        <div style="flex flex-row">
                            <div id="noti" class="alert alert-info" style="display: none;">
                                <p><img style="display:none;" id="spinner" src="images/ajax-loader.gif" /> <span
                                        class="response-text"></span></p>
                            </div>
                        </div>

                        <?php if ($activeMission) {
                            echo "<div id='missiontext' style='font-size: 1.2em'>Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}</div></center>";
                        } ?>


                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <meta http-equiv='refresh' content='900'>


    <?php
    include 'footer.php';
    ?>