<?php
include 'header2.php';

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

$crimes = $redis->get("all_crimes");
if (empty($crimes)) {
    $db->query("SELECT * FROM crimes ORDER BY nerve DESC");
    $db->execute();
    $crimes = $db->fetch_row();
    $redis->setEx("all_crimes", 7200, json_encode($crimes)); // Cache for 2 hours
} else {
    $crimes = json_decode($crimes, true);
}

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

<div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-y-4 md:gap-x-4">

    <div class="w-full md:min-w-sm md:max-w-sm md:w-sm border border-white/10 bg-black/40 border-6 rounded-lg p-4">
        <h2 class="text-xl text-white">Serial Crimes</h2>
        <div class="mt-4 mb-2">
          <span class="text-white font-medium">Select Crime</span>

          <div class="border-2 border-white/10 rounded-lg mt-1">
            <select class="w-full text-white bg-black/40 text-sm rounded-lg p-2.5 block"
              style="border-right: 12px solid transparent !important;">
              <option value="1">Crack a skull | <span class="text-red">750 Nerve</span></option>
            </select>
          </div>
        </div>

        <div class="mt-4 mb-2">
          <span class="text-white font-medium">Multiplier</span>

          <div class="flex flex-row pt-1 gap-x-1 select-none">
            <div
              class="py-1 w-50 bg-black/40 text-center items-center my-auto text-sm text-white border-2 border-white/50 rounded-lg">
              5
            </div>
            <div
              class="py-1 w-50 bg-black/40 text-center items-center my-auto text-sm text-white/80 border-2 border-white/10 rounded-lg">
              10
            </div>
            <div
              class="py-1 w-50 bg-black/40 text-center items-center my-auto text-sm text-white/80 border-2 border-white/10 rounded-lg">
              25
            </div>
            <div
              class="py-1 w-50 bg-black/40 text-center items-center my-auto text-sm text-white/80 border-2 border-white/10 rounded-lg">
              50
            </div>
            <div
              class="py-1 w-50 bg-black/40 text-center items-center my-auto text-sm text-white/80 border-2 border-white/10 rounded-lg">
              100
            </div>
            <div
              class="py-1 w-50 bg-black/40 text-center items-center my-auto text-sm text-white/80 border-2 border-white/10 rounded-lg">
              500
            </div>
            <div
              class="py-1 w-50 bg-black/40 text-center items-center my-auto text-sm text-white/80 border-2 border-white/10 rounded-lg">
              1k
            </div>
            <div
              class="py-1 w-50 bg-black/40 text-center items-center my-auto text-sm text-white/80 border-2 border-white/10 rounded-lg">
              5k
            </div>
          </div>
        </div>

        <div class="mt-4 flex flex-col gap-1">
          <div>
            <span class="text-[#ABABAB]">Multiplier time: </span><span class="text-white">1 second</span>
          </div>
          <div>
            <span class="text-[#ABABAB]">Money: </span><span class="text-white">293.3M (After gang tax)</span>
          </div>
          <div>
            <span class="text-[#ABABAB]">Experience: </span><span class="text-white">87.4k</span>
          </div>
          <div>
            <span class="text-[#ABABAB]">Chance of jail: </span><span class="text-white">Small</span>
          </div>
          <div>
            <span class="text-[#ABABAB]">Nerve refill</span> <span class="text-white">(x2)</span> <span
              class="ml-2 text-[#ABABAB]">Cost:</span>
            <span class="text-white">50</span>
          </div>
        </div>
      </div>

      <div class="w-full border border-white/10 bg-black/40 border-6 rounded-lg p-4"></div>
</div>

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
                        <hr class="my-4 border-white/10" />

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