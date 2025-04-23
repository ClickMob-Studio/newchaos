<?php

session_start();

include 'nc_header.php';

if ($user_class->firstlogin1 == 0) {
    $stmt = mysql_query("UPDATE grpgusers SET firstlogin1 = 1 WHERE id = " . $user_class->id);
    Send_Event2($user_class->id, "Is the latest thug on the streets.", $user_class->id);
    Send_Event($user_class->id, "<div class='text-white'>Welcome To Chaos City!<br>To get you started we are giving you:</div><div class='fw-bold text-white'>&bull; 3 VIP Days<br>&bull; $100,000 Cash<br>&bull; 1,250 Points</div>", $user_class->id);
}

$forums = getForums();

var_dump($forums);
?>

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

<div class="nav left-0 right-0 flex justify-center items-center gap-x-2 mb-4" id="slider-controls">
    <button class="bg-white/50 w-3 h-3 rounded-full" onClick="goto(0)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(1)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(2)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(3)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(4)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(5)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(6)"></button>
    <button class="bg-white/10 w-3 h-3 rounded-full" onClick="goto(7)"></button>
</div>

<div class="max-w-7xl mx-auto flex flex-col gap-y-4 px-2 md:px-6 lg:px-8">
    <h1 class="pl-4 pt-2 pb-4 text-white text-2xl">Message Board</h1>
</div>

<div class="max-w-7xl mx-auto flex flex-col gap-y-4 px-2 md:px-6 lg:px-8">
    <div class="w-full border border-white/10 bg-black/40 border-4 rounded-lg p-4">
        <div class="flex flex-col">
            <div class="flex h-[64px] items-center">
                <div>
                    <img src="css/images/svgs/Megaphone.svg" class="h-8 w-8 px-4" />
                </div>
                <div>
                    <h2 class="text-white text-2xl">News</h2>
                    <span class="text-[#BBBBBB]">Stay up to date and comment on game news.</span>
                </div>
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
        console.log("[DEBUG] Reached indexChanged event with index: " + info.displayIndex);
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