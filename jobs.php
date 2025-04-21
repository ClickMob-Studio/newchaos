<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'header.php';
?>
<style>
    .card {
        background: none;
    }

    .table {
        color: #fff;
    }

    .btn-primary {
        margin: 10px;
        padding: 10px;
        width: auto;
        border: solid var(--colorHighlight) 1px !important;
        text-transform: uppercase;
        background: #000000c4;
    }
</style>
<h1 class="text-center mt-4">Jobs</h1>
<div class="container">
    <p class="d-none d-sm-block d-md-none">Scroll to see the rest of the information</p>
    <div class="card">
        <div class="card-body">
            <?php
            if ($user_class->fbitime > 0) {
                diefun("You can't do work if you're in FBI Jail!");
            }

            $db->query("SELECT * FROM jobinfo WHERE userid = ?");
            $db->execute(array($user_class->id));
            if (!$db->num_rows()) {
                $db->query("INSERT INTO jobinfo (userid, dailyClockins, lastClockin, addedPercent) VALUES (?, 0, 0, 0)");
                $db->execute(array($user_class->id));
                $jobinfo = ['userid' => $user_class->id, 'dailyClockins' => 0, 'lastClockin' => 0, 'addedPercent' => 0];
            } else {
                $jobinfo = $db->fetch_row(true);
            }
            if (isset($_GET['clockin'])) {
                if ($jobinfo['lastClockin'] > time() - 3600) {
                    diefun("You have already clocked in less than an hour ago.");
                }
                if ($user_class->dailyClockins >= 8) {
                    diefun("You have already clocked in 8 times today.");
                }
                $jobinfo['lastClockin'] = time();
                $jobinfo['dailyClockins']++;
                $db->query("UPDATE jobinfo SET lastClockin = ?, dailyClockins = ? WHERE userid = ?");
                $db->execute(array(
                    $jobinfo['lastClockin'],
                    $jobinfo['dailyClockins'],
                    $user_class->id
                ));
                $db->query("SELECT money FROM jobs WHERE id = ?");
                $db->execute(array(
                    $user_class->job
                ));
                $pay = $db->fetch_single();
                $pay *= (1 + ($jobinfo['addedPercent'] / 100));
                $db->query("SELECT points FROM jobs WHERE id = ?");
                $db->execute(array(
                    $user_class->job
                ));
                $pay2 = $db->fetch_single();

                $user_class->money += $pay;
                $user_class->points += $pay2;
                $db->query("UPDATE grpgusers SET money = ?, points = ?, dailyClockins = dailyClockins + 1, jobcis = jobcis + 1, jobMoney = jobMoney + ? WHERE id = ?");
                $db->execute(array(
                    $user_class->money,
                    $user_class->points,
                    $pay,
                    $user_class->id
                ));
            }
            if (isset($_GET['action']) and $_GET['action'] == "quit") {
                $user_class->job = 0;
                $db->query("UPDATE grpgusers SET job = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->job,
                    $user_class->id
                ));
            }
            if (isset($_GET['take'])) {
                $db->query("SELECT * FROM jobs WHERE id = ?");
                $db->execute(array(
                    $_GET['take']
                ));
                if (!$db->num_rows())
                    diefun("This job is not available.");
                $row = $db->fetch_row(true);
                if (($row['level'] > $user_class->level) || ($row['prestige'] > $user_class->prestige))
                    diefun("You don't have the needed level to take on this job.<br />");
                $user_class->job = $_GET['take'];
                $db->query("UPDATE grpgusers SET job = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->job,
                    $user_class->id
                ));
                $db->query("UPDATE jobinfo SET lastClockin = 0, dailyClockins = 0 WHERE userid = ?");
                $db->execute(array(
                    $user_class->id
                ));
            }
            if ($user_class->job != 0) {
                $db->query("SELECT * FROM jobs WHERE id = ?");
                $db->execute(array(
                    $user_class->job
                ));
                $row = $db->fetch_row(true);
                echo '<div class="p-4 rounded text-center">';
                echo 'You are currently a ' . $row['name'] . '<br />';
                echo 'You make <span class="text-success">' . prettynum($row['money'] * (1 + ($jobinfo['addedPercent'] / 100)), 1) . '</span> & ' . prettynum($row['points']) . ' Points Per Hour<br />';
                echo '<br />';
                echo 'You last clocked in <span class="text-danger">', ($jobinfo['lastClockin'] == 0) ? 'never' : date('h:i:s a', $jobinfo['lastClockin']), '</span>.<br />';
                echo 'You clocked in <span class="text-danger">' . $user_class->dailyClockins . '</span> time', ($user_class->dailyClockins == 1) ? '' : 's', ' today.<br />';
                echo '<br />';
                echo '<a href="jobs.php?clockin" class="btn btn-primary">Clockin</a>';
                echo '</div>';
            }
            ?>
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">Job</th>
                            <th>Requirement</th>
                            <th colspan="2">Hourly Payment</th>
                            <th rowspan="2">Apply</th>
                        </tr>
                        <tr>
                            <th>Level</th>
                            <th>Cash</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $db->query("SELECT * FROM jobs ORDER BY money ASC");
                        $db->execute();
                        $rows = $db->fetch_row();
                        foreach ($rows as $row) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . prettynum($row['level']) . '</td>';
                            echo '<td>' . prettynum($row['money'], 1) . '</td>';
                            echo '<td>' . prettynum($row['points']) . '</td>';
                            echo '<td>', ($row['id'] > $user_class->job) ? '<a href="jobs.php?take=' . $row['id'] . '" class="btn btn-primary">Take Job</a>' : '', '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>