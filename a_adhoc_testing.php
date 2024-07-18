<?php
include 'header.php';
$m->set('lastcrimeload.'.$user_class->id, time());
?>
    <div class='box_top'>Speed Gym</div>
    <div class='box_middle'>
    <div class='pad'>
    <p><a href="gym.php" class="dcSecondaryButton"> Go to Standard Gym </a></p>
    <?php
    if ($user_class->hospital > 0) {
        echo Message("You can't train at the gym if you are in the hospital.");
        include 'footer.php';
        die();
    }
    if ($printcaptcha != "") {
        echo $printcaptcha;
    } else {
        ?>
        <script>
            var doingtrain = false, what = "";
            function start(statName) {
                what = statName;
                doingtrain = true;
                var isMegaTrain = $('#mega_train').is(':checked') ? 'yes' : 'no';
                var intervalId = setInterval(function() {
                    if (doingtrain && what) {
                        trainrefill(what, isMegaTrain);
                    } else {
                        clearInterval(intervalId);
                        intervalId = null;
                    }
                }, 30);
            }

            function finish() {
                if (doingtrain) {
                    location.reload();
                }
                what = "";
                doingtrain = false;
            }

            function trainrefill(stat, isMegaTrain) {
                $("#noti").html("<img src='images/ajax-loader.gif?' />");
                $.post("ajax_supergym.php", {
                    amnt: <?php echo $user_class->maxenergy ?>,
                    stat: stat,
                    what: "trainrefill",
                    mega_train: isMegaTrain // Pass the mega train status to the server
                }, function(response) {
                    var info = response.split("|");
                    $(".hidden-alert").show();
                    $("#noti").html(info[0]);
                    $(".points").html(info[1]);
                    $("#" + stat + "amnt").html(info[2]);
                    if (info[3]) {
                        $("#strength").val(info[3]);
                        $("#defense").val(info[3]);
                        $("#speed").val(info[3]);
                        $("#agility").val(info[3]);
                    }
                });
            }

            document.onblur = function() { finish(); };
            window.onblur = function() { finish(); };
            document.body.onmouseup = function() { finish(); };
        </script>

        <div style="display:flex;min-height:60px;flex-direction:row;">
            <div class="alert alert-info hidden-alert" style="display: none;">
                <p><div id="noti"></div></p>
            </div>
        </div>

        <?php
        echo "
    <br />
    
<div class='contenthead floaty'>
<span style='margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;'>
    <table id='newtables' class='altcolors' style='width:100%;'>
        <tr>
            <th>STRENGTH</th>
            <th>DEFENSE</th>
            <th>SPEED</th>
            <th>AGILITY</th>
        </tr>
        <tr>
            <td><span id='strengthamnt'>".prettynum($user_class->strength)."</span> [Ranked: ".getRank("$user_class->id", "strength")."]</td>
            <td><span id='defenseamnt'>".prettynum($user_class->defense)."</span> [Ranked: ".getRank("$user_class->id", "defense")."]</td>
            <td><span id='speedamnt'>".prettynum($user_class->speed)."</span> [Ranked: ".getRank("$user_class->id", "speed")."]</td>
            <td><span id='agilityamnt'>".prettynum($user_class->agility)."</span> [Ranked: ".getRank("$user_class->id", "agility")."]</td>
        </tr>
        <tr>
            <td><button onmousedown='start(\"strength\");' onmouseup='finish();' ontouchend='finish();' onmouseleave='finish();' ontouchstart='start(\"strength\");'>Strength + Refills</button></td>
            <td><button onmousedown='start(\"defense\");' onmouseup='finish();' ontouchend='finish();' onmouseleave='finish();' ontouchstart='start(\"defense\");'>Defense + Refills</button></td>
            <td><button onmousedown='start(\"speed\");' onmouseup='finish();' ontouchend='finish();' onmouseleave='finish();' ontouchstart='start(\"speed\");'>Speed + Refills</button></td>
            <td><button onmousedown='start(\"agility\");' onmouseup='finish();' ontouchend='finish();' onmouseleave='finish();' ontouchstart='start(\"agility\");'>Agility + Refills</button></td>
        </tr>
        <tr>
            <td colspan='4'><span style='color:red;font-weight:bold;'>Click and hold down the mouse on the stat + refills button.</span></td>
        </tr>
    </table></div>";
    }
    ?>
    <h1>Daily Stats</h1>
    <p>Here you will find your historical gym stats, these update at rollover</p>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <canvas id="statsChart" width="100" height="100"></canvas>
    <script>
        fetch('ajax_gym_stats.php')
            .then(response => response.json())
        .then(data => {
            const dates = data.map(item => item.record_date);
        const strengths = data.map(item => item.strength);
        const defenses = data.map(item => item.defense);
        const speeds = data.map(item => item.speed);
        const agilitys = data.map(item => item.agility);

        const ctx = document.getElementById('statsChart').getContext('2d');
        const statsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Strength',
                        data: strengths,
                        borderColor: 'red',
                        backgroundColor: 'rgba(255, 0, 0, 0.1)',
                    },
                    {
                        label: 'Defense',
                        data: defenses,
                        borderColor: 'green',
                        backgroundColor: 'rgba(0, 255, 0, 0.1)',
                    },
                    {
                        label: 'Speed',
                        data: speeds,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 0, 255, 0.1)',
                    },
                    {
                        label: 'Agility',
                        data: agilitys,
                        borderColor: 'yellow',
                        backgroundColor: 'rgba(0, 0, 255, 0.1)',
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white' // Set Y-axis tick labels to white
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white' // Set X-axis tick labels to white
                        }
                    }
                },
                legend: {
                    labels: {
                        color: 'white' // Set legend labels to white
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'white' // Ensures text color is white
                        }
                    }
                }
            }
        });
        })
        .catch(error => console.error('Error loading the data: ', error));
    </script>
<?php
include 'footer.php';
?>