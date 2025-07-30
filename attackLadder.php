<?php
include 'header.php';

$csrf = md5(uniqid(rand(), true));
$_SESSION['csrf'] = $csrf;
?>

<div class='box_top'>Attack Ladder</div>
<div class='box_middle'>
    <div class='pad'>

        <style>
            .info-box {
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
                padding: 20px;
                margin: 20px 0;
                width: 90%;
                margin-left: auto;
                margin-right: auto;
            }

            .ladder-container {
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
                padding: 20px;
                margin: 20px auto;
                width: 90%;
            }

            .ladder-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px;
                border-bottom: 1px solid #2f2f2f;
            }

            .ladder-item:last-child {
                border-bottom: none;
            }

            .ladder-button {
                border: none;
                padding: 5px 10px;
                border-radius: 5px;
                cursor: pointer;
            }


            /* Glow effects */
            @keyframes goldGlow {
                from {
                    box-shadow: 0 0 5px #ffd700;
                }

                to {
                    box-shadow: 0 0 15px #ffd700, 0 0 25px #ffd700, 0 0 35px #ffd700, 0 0 45px #ffd700;
                }
            }

            @keyframes silverGlow {
                from {
                    box-shadow: 0 0 5px #c0c0c0;
                }

                to {
                    box-shadow: 0 0 15px #c0c0c0, 0 0 25px #c0c0c0, 0 0 35px #c0c0c0, 0 0 45px #c0c0c0;
                }
            }

            @keyframes bronzeGlow {
                from {
                    box-shadow: 0 0 5px #cd7f32;
                }

                to {
                    box-shadow: 0 0 15px #cd7f32, 0 0 25px #cd7f32, 0 0 35px #cd7f32, 0 0 45px #cd7f32;
                }
            }

            .gold {
                animation: goldGlow 1.5s infinite alternate;
            }

            .silver {
                animation: silverGlow 1.5s infinite alternate;
            }

            .bronze {
                animation: bronzeGlow 1.5s infinite alternate;
            }

            .info-list {
                list-style-type: none;
                padding-left: 0;
                font-size: 16px;
                line-height: 1.6;
            }

            .info-list li {
                margin-bottom: 10px;
                position: relative;
                padding-left: 25px;
            }

            .info-list li::before {
                content: "�";
                position: absolute;
                left: 0;
                font-size: 20px;
                color: #ffd700;
                /* Gold color for bullets */
            }

            .info-list li:last-child {
                margin-bottom: 0;
            }

            .info-highlight {
                font-weight: bold;
                color: #ffd700;
                /* Gold color for highlight */
            }
        </style>

        <div class="info-box">
            <h2>Attack Ladder</h2>
            <ul class="info-list">
                <li>To get onto the ladder if the ladder is empty you just attack someone successfully.</li>
                <li>If the ladder is full you must attack someone in the ladder to take their position!</li>
                <li>You can move up the ladder by beating those above you.</li>
                <li>You will be rewarded points every hour and sent an event when you place on the attack ladder.</li>
                <li>After 4 hours of attack inactivity you will be kicked off the ladder.</li>
            </ul>
        </div>

        <div class="ladder-container">
            <?php
            $db->query("SELECT * FROM `attackladder` ORDER BY `spot` ASC");
            $db->execute();
            $rows = $db->fetch_row();

            $shown = [];
            foreach ($rows as $row):
                if (!isset($shown[$row['user']])) {
                    $shown[$row['user']] = 1;

                    $text = formatName($row['user']);
                    $reward = ($row['spot'] == 1) ? '150' : '100';
                    $attack = ($user_class->id == $row['user'])
                        ? '-'
                        : "<a class='ladder-button' href='attack.php?attack={$row['user']}&csrf={$csrf}'>Attack</a>";
                    ?>
                    <div class="ladder-item">
                        <span><b><?= ordinal($row['spot']) ?></b></span>
                        <span><?= $text ?></span>
                        <span>Reward: <?= $reward ?> Points</span>
                        <span><?= $attack ?></span>
                    </div>
                    <?php
                }
            endforeach;
            ?>
        </div>

    </div>
</div>

<?php require "footer.php"; ?>