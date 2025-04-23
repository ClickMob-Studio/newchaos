<?php
session_start();

include 'nc_header.php';
?>

<!-- Quick Navigation -->
<div class="max-w-7xl mx-auto mt-2 py-2 select-none hidden sm:flex">
    <div class="px-4 py-2 bg-black/40 rounded-lg flex gap-x-4">
        <div class="flex gap-x-2 items-center">
            <span class="text-md text-white">QUICK NAV</span>
            <img src="assets/images/icons/QuickNav Expanded.png" class="h-[14px] w-[14px]" />
        </div>
        <a href="#" class="text-white"> HOME </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> STORE </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> CRIMES </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> JAIL </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> HOSPITAL </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> GYM </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> BANK </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> ESTATE </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> DRUGS </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> GANG </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> CRAFTING </a>
        <a href="#" class="text-gray-400 hover:text-gray-300"> QUESTS </a>
    </div>
</div>

<!-- Carousel -->
<div class="slider-container">
    <div class="my-slider select-none px-4 md:px-6 lg:px-2">
        <!-- Battlepass -->
        <div class="my-4 mx-auto">
            <div
                class="relative w-[250px] h-[225px] rounded-xl from-[#000D37] to-[#FFC800] bg-gradient-to-br opacity-100">
                <!-- Card Image Background -->
                <div
                    class="absolute rounded-xl w-full h-full bg-[url(/css/images/2025/battlepass.png)] bg-center bg-origin-content bg-no-repeat bg-cover opacity-20">
                </div>

                <!-- Card Content -->
                <div class="absolute w-full h-full rounded-xl pt-24 px-4">
                    <h2 class="text-4xl font-bold text-white uppercase">Battlepass</h2>
                    <p class="text-md font-normal text-white">Check out the seasonal rewards and your BP progress</p>
                </div>
            </div>
        </div>

        <!-- Raids -->
        <div class="my-4 mx-auto">
            <div
                class="relative w-[250px] h-[225px] rounded-xl from-[#000D37] to-[#FF0037] bg-gradient-to-br opacity-100">
                <!-- Card Image Background -->
                <div
                    class="absolute rounded-xl w-full h-full bg-[url(/css/images/2025/raids.png)] bg-center bg-origin-content bg-no-repeat bg-cover opacity-20">
                </div>

                <!-- Card Content -->
                <div class="absolute w-full h-full rounded-xl pt-24 px-4">
                    <h2 class="text-4xl font-bold text-white uppercase">Raids</h2>
                    <p class="text-md font-normal text-white">Climb the ladder by making your way into the nefarious
                        drug
                        market</p>
                </div>
            </div>
        </div>

        <!-- Backalley -->
        <div class="my-4 mx-auto">
            <div
                class="relative w-[250px] h-[225px] rounded-xl from-[#030004] to-[#7F2D9F] bg-gradient-to-br opacity-100">
                <!-- Card Image Background -->
                <div
                    class="absolute rounded-xl w-full h-full bg-[url(/css/images/2025/backalley.png)] bg-center bg-origin-content bg-no-repeat bg-cover opacity-20">
                </div>

                <!-- Card Content -->
                <div class="absolute w-full h-full rounded-xl pt-24 px-4">
                    <h2 class="text-4xl font-bold text-white uppercase">Backalley</h2>
                    <p class="text-md font-normal text-white">A little shadow action, you might find something, maybe
                        even the
                        hospital</p>
                </div>
            </div>
        </div>

        <!-- Storm City -->
        <div class="my-4 mx-auto">
            <div
                class="relative w-[250px] h-[225px] rounded-xl from-[#000D37] to-[#0DFF00] bg-gradient-to-br opacity-100">
                <!-- Card Image Background -->
                <div
                    class="absolute rounded-xl w-full h-full bg-[url(/css/images/2025/city.png)] bg-center bg-origin-content bg-no-repeat bg-cover opacity-20">
                </div>

                <!-- Card Content -->
                <div class="absolute w-full h-full rounded-xl pt-24 px-4">
                    <h2 class="text-4xl font-bold text-white uppercase">Storm City</h2>
                    <p class="text-md font-normal text-white">You can do anything you can think of in city, the fun
                        awaits you
                    </p>
                </div>
            </div>
        </div>

        <!-- Missions -->
        <div class="my-4 mx-auto">
            <div
                class="relative w-[250px] h-[225px] rounded-xl from-[#170000] to-[#007BD3] bg-gradient-to-br opacity-100">
                <!-- Card Image Background -->
                <div
                    class="absolute rounded-xl w-full h-full bg-[url(/css/images/2025/missions.png)] bg-center bg-origin-content bg-no-repeat bg-cover opacity-20">
                </div>

                <!-- Card Content -->
                <div class="absolute w-full h-full rounded-xl pt-24 px-4">
                    <h2 class="text-4xl font-bold text-white uppercase">Missions</h2>
                    <p class="text-md font-normal text-white">Perform your civic duty as a mobster in Storm</p>
                </div>
            </div>
        </div>

        <!-- Drugs -->
        <div class="my-4 mx-auto">
            <div
                class="relative w-[250px] h-[225px] rounded-xl from-[#020002] to-[#9E00D3] bg-gradient-to-br opacity-100">
                <!-- Card Image Background -->
                <div
                    class="absolute rounded-xl w-full h-full bg-[url(/css/images/2025/drugs.png)] bg-center bg-origin-content bg-no-repeat bg-cover opacity-20">
                </div>

                <!-- Card Content -->
                <div class="absolute w-full h-full rounded-xl pt-24 px-4">
                    <h2 class="text-4xl font-bold text-white uppercase">Drugs</h2>
                    <p class="text-md font-normal text-white">Climb the ladder by making your way into the nefarious
                        drug
                        market
                    </p>
                </div>
            </div>
        </div>

        <!-- Crimes -->
        <div class="my-4 mx-auto">
            <div
                class="relative w-[250px] h-[225px] rounded-xl from-[#020002] to-[#FF4E51] bg-gradient-to-br opacity-100">
                <!-- Card Image Background -->
                <div
                    class="absolute rounded-xl w-full h-full bg-[url(/css/images/2025/crimes.png)] bg-center bg-origin-content bg-no-repeat bg-cover opacity-20">
                </div>

                <!-- Card Content -->
                <div class="absolute w-full h-full rounded-xl pt-24 px-4">
                    <h2 class="text-4xl font-bold text-white uppercase">Crimes</h2>
                    <p class="text-md font-normal text-white">Work your way up the ladder and boost your economy by
                        performing
                        crimes</p>
                </div>
            </div>
        </div>

        <!-- Estate -->
        <div class="my-4 mx-auto">
            <div
                class="relative w-[250px] h-[225px] rounded-xl from-[#020002] to-[#4EFFF0] bg-gradient-to-br opacity-100">
                <!-- Card Image Background -->
                <div
                    class="absolute rounded-xl w-full h-full bg-[url(/css/images/2025/crimes.png)] bg-center bg-origin-content bg-no-repeat bg-cover opacity-20">
                </div>

                <!-- Card Content -->
                <div class="absolute w-full h-full rounded-xl pt-24 px-4">
                    <h2 class="text-4xl font-bold text-white uppercase">Estate</h2>
                    <p class="text-md font-normal text-white">Manage your current and future estate</p>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="nav left-0 right-0 flex justify-center items-center gap-x-2 mb-4">
    <button class="bg-white/50 w-3 h-3 rounded-full" onClick="goto(0)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(1)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(2)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(3)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(4)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(5)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(6)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(7)"></button>
</div>

<div class="max-w-7xl mx-auto flex flex-col gap-y-4">
    <div class="w-full border border-white/10 bg-black/40 border-6 rounded-lg p-4">

        <?php
        if ($user_class->firstlogin1 == 0) {
            $stmt = mysql_query("UPDATE grpgusers SET firstlogin1 = 1 WHERE id = " . $user_class->id);
            Send_Event2($user_class->id, "Is the latest thug on the streets.", $user_class->id);
            Send_Event($user_class->id, "<div class='text-white'>Welcome To Chaos City!<br>To get you started we are giving you:</div><div class='fw-bold text-white'>&bull; 3 VIP Days<br>&bull; $100,000 Cash<br>&bull; 1,250 Points</div>", $user_class->id);
        }
        if ($user_class->level >= 10) {
            $line = mysql_fetch_array(mysql_query("SELECT * FROM referrals WHERE referred = " . $user_class->id . " AND credited = '0'"));
            if (mysql_num_rows($line)) {
                bloodbath('referrals', $line['referrer']);
                mysql_query("UPDATE grpgusers SET credits = credits + 50, points = points + 100, referrals = referrals + 1, refcomp = refcomp + 1, refcount = refcount + 1 WHERE referred = " . $user_class->id);
                mysql_query("UPDATE referrals SET credited = 1 WHERE referred =" . $user_class->id);
                mysql_query("UPDATE referrals SET viewed = 1 WHERE referred = " . $user_class->id);
                Send_Event($line['referrer'], "You have been credited 50 Credits & 100 Points for referring [-_USERID_-]. Keep up the good work!", $line['referred']);
                Send_Event(1, 'USER ID: ' . $line['referred'] . ' referral for ' . $user_class->formattedname . ' payed out');
                Send_Event(2, 'USER ID: ' . $line['referred'] . ' referral for ' . $user_class->formattedname . ' payed out');

            }
        }

        ?>
        <h1 class="text-lg text-white font-medium">General Information</h1>
        <div class="table-container">
            <table id="newtables" class="w-full text-white">
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Name:</th>
                    <td width="30%" class="bg-white/10 px-2">
                        <a href="profiles.php?id=<?= $user_class->id ?>"><?= $user_class->formattedname ?></a>
                    </td>
                    <th width="10%" class="bg-white/5 px-2">HP:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->formattedhp) ?></td>
                </tr>
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Level:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= $user_class->level ?></td>
                    <th width="10%" class="bg-white/5 px-2">Energy:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->formattedenergy) ?></td>
                </tr>
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Money:</th>
                    <td width="30%" class="bg-white/10 px-2">$<?= prettynum($user_class->money) ?></td>
                    <th width="10%" class="bg-white/5 px-2">Awake:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->formattedawake) ?></td>
                </tr>
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Bank:</th>
                    <td width="30%" class="bg-white/10 px-2">$<?= prettynum($user_class->bank) ?></td>
                    <th width="10%" class="bg-white/5 px-2">Nerve:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->formattednerve) ?></td>
                </tr>
                <tr>
                    <th width="10%" class="bg-white/5 px-2">EXP:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->formattedexp) ?></td>
                    <th width="10%" class="bg-white/5 px-2">Work EXP:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->workexp) ?></td>
                </tr>
                <tr>
                    <th width="10%" class="bg-white/5 px-2">RM Days:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->rmdays) ?></td>
                    <th width="10%" class="bg-white/5 px-2">Activity Points:</th>
                    <td width="30%" class="bg-white/10 px-2"><a href="spendactivity.php">Activity Points Store
                            [<?= prettynum($user_class->apoints) ?> Activity Points]</td>
                </tr>
            </table>

            <h1 class="text-lg text-white mt-2 font-medium">Stat Information</h1>
            <table id="newtables" class="w-full text-white">
                <tr>
                    <th width="15%" class="bg-white/5 px-2">Strength:</th>
                    <td class="bg-white/10 px-2"><?= prettynum($user_class->strength) ?></td>
                    <td class="bg-white/10 px-2">[Ranked: <?= getRank("$user_class->id", "strength") ?>]</td>
                    <th width="15%" class="bg-white/5 px-2">Defense:</th>
                    <td class="bg-white/10 px-2"><?= prettynum($user_class->defense) ?></td>
                    <td class="bg-white/10 px-2">[Ranked: <?= getRank("$user_class->id", "defense") ?>]</td>
                </tr>
                <tr>
                    <th width="15%" class="bg-white/5 px-2">Speed:</th>
                    <td class="bg-white/10 px-2"><?= prettynum($user_class->speed) ?></td>
                    <td class="bg-white/10 px-2">[Ranked: <?= getRank("$user_class->id", "speed") ?>]</td>
                    <th width="15%" class="bg-white/5 px-2">Agility:</th>
                    <td class="bg-white/10 px-2"><?= prettynum($user_class->agility) ?></td>
                    <td class="bg-white/10 px-2">[Ranked: <?= getRank("$user_class->id", "agility") ?>]</td>
                </tr>
                <tr>
                    <th width="15%" class="bg-white/5 px-2">Total:</th>
                    <td class="bg-white/10 px-2"><?= prettynum($user_class->totalattrib) ?></td>
                    <td class="bg-white/10 px-2">[Ranked: <?= getRank("$user_class->id", "total") ?>]</td>
                </tr>
            </table>

            <h1 class="text-lg text-white mt-2 font-medium">Modded Stats Information</h1>
            <table id="newtables" class="w-full text-white">
                <tr>
                    <th width="15%" class="bg-white/5 px-2">Modded Strength:</th>
                    <td width="25%" class="bg-white/10 px-2"><?= prettynum($user_class->moddedstrength) ?></td>
                    <th width="15%" class="bg-white/5 px-2">Modded Defense:</th>
                    <td width="25%" class="bg-white/10 px-2"><?= prettynum($user_class->moddeddefense) ?></td>
                </tr>
                <tr>
                    <th width="15%" class="bg-white/5 px-2">Modded Speed:</th>
                    <td width="25%" class="bg-white/10 px-2"><?= prettynum($user_class->moddedspeed) ?></td>
                    <th width="15%" class="bg-white/5 px-2">Modded Agility:</th>
                    <td width="25%" class="bg-white/10 px-2"><?= prettynum($user_class->moddedagility) ?></td>
                </tr>
                <th width="15%" class="bg-white/5 px-2">Modded Total:</th>
                <td width="25%" class="bg-white/10 px-2"><?= prettynum($user_class->moddedtotalattrib) ?></td>
                <th width="15%" class="bg-white/5 px-2"></th>
                <td width="25%" class="bg-white/10 px-2"></td>
            </table>

            <h1 class="text-lg text-white mt-2 font-medium">Battle Statistics</h1>
            <table id="newtables" class="w-full text-white">
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Won:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->battlewon) ?></td>
                    <th width="10%" class="bg-white/5 px-2">Lost:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->battlelost) ?></td>
                </tr>
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Total:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->battletotal) ?></td>
                    <th width="10%" class="bg-white/5 px-2">Money Gain:</th>
                    <td width="30%" class="bg-white/10 px-2">$<?= prettynum($user_class->battlemoney) ?></td>
                </tr>
            </table>

            <h1 class="text-lg text-white mt-2 font-medium">Crime Rankings</h1>
            <table id="newtables" class="w-full text-white">
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Succeeded:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->crimesucceeded) ?></td>
                    <th width="10%" class="bg-white/5 px-2">Failed:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->crimefailed) ?></td>
                </tr>
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Total:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->crimetotal) ?></td>
                    <th width="10%" class="bg-white/5 px-2">Money Gain:</th>
                    <td width="30%" class="bg-white/10 px-2">$<?= prettynum($user_class->crimemoney) ?></td>
                </tr>
            </table>

            <h1 class="text-lg text-white mt-2 font-medium">Bonus Stats</h1>
            <table id="newtables" class="w-full text-white">
                <tr>
                    <th width="10%" class="bg-white/5 px-2">Total Tax Paid:</th>
                    <td width="30%" class="bg-white/10 px-2">$<?= prettynum($user_class->totaltax) ?></td>
                    <th width="10%" class="bg-white/5 px-2">???:</th>
                    <td width="30%" class="bg-white/10 px-2"><?= prettynum($user_class->crimefailed) ?></td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="achievements.php"
                        class="p-2 text-white/80 hover:text-white bg-secondary m-2">[Achievements]</a>
                    <a href="translog.php" class="p-2 text-white/80 hover:text-white bg-secondary m-2">[Transfer
                        Logs]</a>
                    <a href="attackv2_logs.php" class="p-2 text-white/80 hover:text-white bg-secondary m-2">[Attack Log
                        NEW]</a>
                    <a href="attacklog.php" class="p-2 text-white/80 hover:text-white bg-secondary m-2">[Attack Log]</a>
                    <a href="defenselog.php" class="p-2 text-white/80 hover:text-white bg-secondary m-2">[Defense
                        Log]</a>
                    <a href="muglog.php" class="p-2 text-white/80 hover:text-white bg-secondary m-2">[Mug Log]</a>
                    <a href="spylog.php" class="p-2 text-white/80 hover:text-white bg-secondary m-2">[Spy Log]</a>
                </div>
            </div>
        </div>

    </div>

    <div class="w-full border border-white/10 bg-black/40 border-6 rounded-lg p-4 text-white">
        <h1 class="text-lg font-medium">EXP Calculator</h1>
        <div class="d-flex">
            <div class="flex flex-col p-2">
                What level are you aiming for?
                <input type="text" oninput="calcEXP();" id="levelcalc" size="8"
                    class="bg-white/10 border min-w-md max-w-md border-gray rounded-lg mt-1" />
            </div>
            <div class="p-2">
                <span id="levelrtn">
                    You need <?= prettynum(experience($user_class->level + 1) - $user_class->exp); ?>
                    EXP to get to level <?= prettynum($user_class->level + 1); ?>.
                </span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script>
<script>
    var slider = tns({
        container: '.my-slider',
        items: 8,
        touch: true,
        mouseDrag: true,
        fixedWidth: 250,
        gutter: 24,
        controls: false,
        nav: true,
        navPosition: 'bottom',
        navAsThumbnails: true,
        responsive: {
            640: {
                items: 4,
            },
            700: {
                items: 4,
            },
            900: {
                items: 8,
            }
        }
    });

    slider.events.on('indexChanged', function (info) {
        updateSliderControls(info.displayIndex - 1);
    });

    function goto(index) {
        updateSliderControls(index);
        slider.goTo(index);
    }

    function updateSliderControls(index) {
        var info = slider.getInfo();

        var sliderControls = document.querySelectorAll('#slider-controls button');
        sliderControls.forEach(function (button, i) {
            if (i === index) {
                button.classList.remove('bg-white/10');
                button.classList.add('bg-white/50');
            } else {
                button.classList.remove('bg-white/50');
                button.classList.add('bg-white/10');
            }
        });
    }

    var onIndexChanged = function () {
        updateSliderControls();
    }
</script>

<?php include "nc_footer.php"; ?>