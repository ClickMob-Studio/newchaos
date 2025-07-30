<?php
include 'header.php';
?>
<div class='box_top'>Referral</div>
<div class='box_middle'>
    <div class='pad'>
        <style>
            body {
                background-color: #121212;
                color: #c0c0c0;
                font-family: 'Arial', sans-serif;
            }

            hr {
                display: none;
            }

            .referral-box {
                background: #252525;
                padding: 15px;
                border-radius: 4px;
                margin: 10px auto;
                width: 75%;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }

            .table {
                background-color: #333;
                color: #c0c0c0;
                width: 100%;
                border-collapse: collapse;
                margin: 10px auto;
            }

            .table th,
            .table td {
                padding: 10px;
                border-bottom: 1px solid #444;
            }

            .table th {
                background-color: #222;
                color: #9CDCFE;
            }

            .table tr:hover {
                background-color: #2A2D2E;
            }

            .rewards-milestones {
                background: #333;
                padding: 15px;
                margin: 20px auto;
                border-radius: 4px;
                width: 75%;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }

            .rewards-milestones ul {
                list-style-type: none;
                padding: 0;
            }

            .rewards-milestones li {
                background: #222;
                padding: 10px;
                margin-bottom: 8px;
                border-left: 4px solid #FFD700;
                color: #c0c0c0;
            }

            .discord-logo {
                height: 60px;
                margin-left: 20px;
            }

            @media only screen and (max-width: 768px) {

                h3,
                .referral-box,
                .rewards-milestones {
                    width: 95%;
                }
            }
        </style>
        <script>
            function startCountdown(duration, display) {
                var timer = duration, minutes, seconds;
                setInterval(function () {
                    minutes = parseInt(timer / 60, 10);
                    seconds = parseInt(timer % 60, 10);

                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    display.textContent = minutes + ":" + seconds;

                    if (--timer < 0) {
                        display.textContent = "00:00";
                    }
                }, 1000);
            }

            window.onload = function () {
                var countdownDisplay = document.querySelector('#time');
                startCountdown(countdownSeconds, countdownDisplay);
            };
        </script>


        <div class="floaty" style="margin:3px;">

            <br>

            <div class="referral-system">
                <h4>Referral System</h4>
                <br>
                <div>
                    <h4>Your referral link</h4>
                    <input type="text" value="https://chaoscity.co.uk/home.php?referer=<?php echo $user_class->id; ?>"
                        readonly onclick="this.select();" style="width: 50%">
                </div>
                <div class="rewards">
                    <b>Reward:</b> <span style="color:red;">50 Gold</span> per referral.
                </div>
                <p>
                    Referrals are paid out once the player referred hits level 100. If your referral hasn't been paid,
                    please message an admin.
                </p>
                <p>
                    You also receive a bonus for any credits your referral buys.
                </p>
            </div>

            <div class="rewards-info">
                <h4>Rewards Milestones</h4>
                <ul>
                    <li>
                        <h4>
                            <font color=red>5 Referrals</font> - 150 GOLD
                        </h4>
                    </li>
                    <li>
                        <h4>
                            <font color=red>10 Referrals</font> - 500 GOLD
                        </h4>
                    </li>
                    <li>
                        <h4>
                            <font color=red>15 Referrals</font> - 1,000 GOLD
                        </h4>
                    </li>
                    <li>
                        <h4>
                            <font color=red>25 Referrals</font> - 2,000 GOLD
                        </h4>
                    </li>

                    <!-- Add more milestones as necessary -->
                </ul>
            </div>


            <hr>
            <div class="rewards-info">
                <h4>Your Referrals</h4>
                <table>
                    <tr>
                        <th>Mobster</th>
                        <th>Status</th>
                        <th>Reward [Gold]</th>
                    </tr>
                    <?php
                    // Assuming you have a connection $conn
                    $db->query("SELECT * FROM referrals WHERE referrer = ? ORDER BY id DESC");
                    $db->execute([$user_class->id]);
                    $results = $db->fetch_row();

                    if (empty($results)) {
                        echo '<tr><td colspan="3">You have no referrals</td></tr>';
                    } else {
                        foreach ($results as $row) {
                            $credited = ($row['credited'] == 0) ? "Pending" : "Approved";
                            $points = ($row['credited'] == 0) ? "0" : "100 + 50 Credits";
                            echo '<tr>';
                            echo '<td>' . formatName($row['referred']) . '</td>';
                            echo '<td>' . $credited . '</td>';
                            echo '<td>' . $points . '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </table>
            </div>

            <?php
            include 'footer.php';
            ?>