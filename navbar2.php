<?php

$topLinks = array(
    array(
        'name' => 'Home',
        'url' => 'index.php',
    ),
    array(
        'name' => '<!_-cityname-_!>',
        'url' => 'city.php',
    ),
    array(
        'name' => 'Crimes',
        'url' => 'newcrimes.php',
    ),
    array(
        'name' => 'Gym',
        'url' => 'speedGym.php',
    ),
    array(
        'name' => 'Bank',
        'url' => 'bank.php',
    ),
    array(
        'name' => 'Jail',
        'url' => 'jail.php',
        'count' => '<!_-jail-_!>',
    ),
    array(
        'name' => 'Hospital',
        'url' => 'hospital.php',
        'count' => $counts['hospital'],
    ),
    array(
        'name' => 'Online',
        'url' => 'online.php',
    ),
);

$leftLinks = array();
$leftLinks[] =
    array(
        'name' => 'Search Players',
        'url' => 'search.php'
    );
$leftLinks[] =
    array(
        'name' => 'Mail',
        'url' => 'pms.php?view=inbox',
        'count' => $counts['mail'],
    );
$leftLinks[] =
    array(
        'name' => 'Events',
        'url' => 'events.php',
        'count' => $counts['event'],
    );
if ($counts['updates'] > 0) {
    $leftLinks[] =
        array(
            'name' => 'Updates',
            'url' => 'gameupdates.php',
            'count' => $counts['updates'],
        );
}
$leftLinks[] =
    array(
        'name' => 'Chat',
        'url' => 'globalchat.php',
        'count' => $counts['gchat'],
    );
$leftLinks[] =
    array(
        'name' => 'Maze',
        'url' => 'maze.php',
    );
$leftLinks[] =
    array(
        'name' => 'Missions',
        'url' => 'missions.php',
    );
$leftLinks[] =
    array(
        'name' => 'Quests',
        'url' => 'quest.php',
    );
$leftLinks[] =
    array(
        'name' => 'Inventory',
        'url' => 'inventory.php',
    );
$leftLinks[] =
    array(
        'name' => 'Raids',
        'url' => 'raids.php',
        'count' => $counts['gang_raid_count'],
    );
$leftLinks[] =
    array(
        'name' => 'Backalley',
        'url' => 'backalley_new.php',
    );

$userPrestigeSkills = getUserPrestigeSkills($user_class);
if ($userPrestigeSkills['speed_attack_unlock'] > 0) {
    $leftLinks[] =
        array(
            'name' => 'Super Attack',
            'url' => 'super_attack.php',
        );
}
if ($user_class->gang) {
    $leftLinks[] =
        array(
            'name' => 'Gang',
            'url' => 'gang.php',
        );
    $leftLinks[] =
        array(
            'name' => 'Gang Mail',
            'url' => 'gangmail.php',
            'count' => $counts['gangmail'],
        );

} else {
    $leftLinks[] =
        array(
            'name' => 'Create Gang',
            'url' => 'creategang.php',
        );

}
//$leftLinks[] =
//	array(
//		'name' => 'Speed Crimes',
//		'url'  => 'newcrimes.php',
//	);
//$leftLinks[] =
//	array(
//		'name' => 'Speed Gym',
//		'url'  => 'speedGym.php',
//	);
$leftLinks[] =
    array(
        'name' => 'Settings',
        'url' => 'settings.php',
    );
$leftLinks[] =
    array(
        'name' => 'Forum',
        'url' => 'forum.php',
    );
$leftLinks[] =
    array(
        'name' => 'Discord',
        'url' => 'https://discord.gg/7rkFUKwrPz',
    );
$leftLinks[] =
    array(
        'name' => 'Log Out',
        'url' => 'index.php?action=logout',
    );

$navPage = str_replace('/', '', $_SERVER['REQUEST_URI']);

?>

<nav class="flex flex-col">
    <div class="bg-[#242424]">
        <div class="mx-auto max-w-7xl px-2 md:px-6 lg:px-8">
            <div class="relative flex h-16 items-center justify-between">
                <div class="flex flex-1 items-center md:justify-center md:items-stretch md:justify-start">
                    <div class="flex shrink-0 items-center text-2xl font-bold">
                        <img src="assets/images/ChaosCity.png" alt="Logo" class="h-3 md:h-4 w-auto" />
                    </div>
                </div>
                <div
                    class="absolute inset-y-0 right-0 flex gap-x-4 items-center pr-2 md:static md:inset-auto md:ml-6 md:pr-0">
                    <button type="button" <?php echo 'title="' . ($user_class->rmdays > 1 ? 'VIP' : 'Not VIP') . '"'; ?>
                        class="relative p-1 <?php if ($user_class->rmdays <= 0) {
                            echo 'opacity-50';
                        } ?> text-gray-400 hover:text-white">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">VIP status
                            (<?php echo $user_class->rmdays > 1 ? 'VIP' : 'Not VIP'; ?>)</span>
                        <img src="assets/images/VIPBadge.png" class="w-[20px] md:w-full" />
                    </button>

                    <button type="button" class="relative p-1 cursor-pointer text-gray-400 hover:text-white">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View private messages</span>
                        <img src="assets/images/icons/Mailbox.png" class="w-[20px] md:w-full" />
                    </button>

                    <button type="button" class="relative p-1 cursor-pointer text-gray-400 hover:text-white">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Inventory</span>
                        <img src="assets/images/icons/Bag.png" class="w-[20px] md:w-full" />
                    </button>

                    <button type="button" class="relative p-1 cursor-pointer text-gray-400 hover:text-white">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Settings</span>
                        <img src="assets/images/icons/Settings.png" class="w-[20px] md:w-full" />
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative ml-3">
                        <div>
                            <button type="button" class="relative flex cursor-pointer rounded-full bg-gray-800 text-sm"
                                id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">Open user menu</span>
                                <img class="size-6 md:size-8 rounded-full" src="<?php echo $user_class->avatar ?>"
                                    alt="" />
                            </button>
                        </div>

                        <!--
                  Dropdown menu, show/hide based on menu state.
      
                  Entering:"transition ease-out duration-100"
                    From:"transform opacity-0 scale-95"
                    To:"transform opacity-100 scale-100"
                  Leaving:"transition ease-in duration-75"
                    From:"transform opacity-100 scale-100"
                    To:"transform opacity-0 scale-95"
                -->
                        <!-- <div
                  class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 ring-1 shadow-lg ring-black/5 focus:outline-hidden"
                  role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1"> -->
                        <!-- Active:"bg-gray-100 outline-hidden", Not Active:"" -->
                        <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
                    id="user-menu-item-0">Profile</a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
                    id="user-menu-item-0">Event log</a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
                    id="user-menu-item-2">Sign out</a>
                </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bar -->
    <div class="bg-black/40 md:hidden border-b border-black/10 pt-1 pb-2">
        <div class="mx-auto max-w-7xl px-0">
            <div class="flex h-12 items-center gap-x-4 overflow-scroll text-white whitespace-nowrap px-2">
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Home.svg" class="size-4">
                    <a href="">Home</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Shop.svg" class="size-4">
                    <a href="">Store</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Spy.svg" class="size-4">
                    <a href="">Crimes</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Prison.svg" class="size-4">
                    <a href="">Jail</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Hospital 3.svg" class="size-4">
                    <a href="">Hospital</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Barbell.svg" class="size-4">
                    <a href="">Gym</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Bank Building.svg" class="size-4">
                    <a href="">Bank</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Building.svg" class="size-4">
                    <a href="">Estate</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Pill.svg" class="size-4">
                    <a href="">Drugs</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Gang.svg" class="size-4">
                    <a href="">Gang</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Hammer.svg" class="size-4">
                    <a href="">Crafting</a>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <img src="assets/images/svg/Parchment.svg" class="size-4">
                    <a href="">Quests</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary bar -->
    <div class="bg-black/40">
        <div class="mx-auto max-w-7xl px-2 md:px-6 lg:px-8 py-4 md:py-0">
            <div class="relative flex flex-col gap-y-2 md:gap-y-0 md:flex-row md:h-14 items-center justify-between">
                <!-- Character Currencies, eg. Cash, Bank, Points & Gold -->
                <div class="flex flex-1 items-center gap-x-3 md:items-stretch justify-between md:justify-start">
                    <span class="relative flex items-center p-1 text-gray-400 hover:text-white" data-toggle="tooltip"
                        title="<?= number_format($user_class->money); ?>">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Cash</span>
                        <img src="assets/images/icons/Cash.png" />
                        <span class="ml-2 text-white text-sm"><?= pretty_format_number($user_class->money); ?></span>
                    </span>

                    <span class="relative flex items-center p-1 text-gray-400 hover:text-white" data-toggle="tooltip"
                        title="<?= number_format($user_class->bank); ?>">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Money in bank</span>
                        <img src="assets/images/icons/Bank Building.png" />
                        <span class="ml-2 text-white text-sm"><?= pretty_format_number($user_class->bank); ?></span>
                    </span>

                    <span class="relative flex items-center p-1 text-gray-400 hover:text-white" data-toggle="tooltip"
                        title="<?= number_format($user_class->points); ?>">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Points</span>
                        <img src="assets/images/icons/Diamond.png" />
                        <span class="ml-2 text-white text-sm"><?= pretty_format_number($user_class->points); ?></span>
                    </span>

                    <span class="relative flex items-center p-1 text-gray-400 hover:text-white" data-toggle="tooltip"
                        title="<?= number_format($user_class->credits); ?>">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Gold</span>
                        <img src="assets/images/icons/Gold Bars.png" />
                        <span class="ml-2 text-white text-sm"><?= pretty_format_number($user_class->credits); ?></span>
                    </span>
                </div>

                <!-- Character Level, current experience, and experience to next levet -->
                <div class="flex w-full md:flex-[0.5] flex-col order-first md:order-none">
                    <div class="mx-auto">
                        <span class="text-sm text-white font-medium">EXPERIENCE</span>
                    </div>
                    <div class="h-2 bg-white/25 max-w-lg" data-toggle="tooltip"
                        title="<?= pretty_format_number($user_class->exp) . ' - ' . $user_class->exppercent; ?>%">
                        <div class="h-2 w-[<?= $user_class->exppercent; ?>%] bg-[#FFA600]"></div>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[red] text-sm">LV. <?= $user_class->level; ?></span>
                        <span class="text-white text-sm"><?= pretty_format_number($user_class->exp); ?></span>
                    </div>
                </div>

                <!-- Character Energy, eg. Nerve, Health, Energy, Awake -->
                <div class="flex flex-1 gap-x-3 md:items-stretch justify-between md:justify-end">
                    <span class="relative flex items-center p-1 text-gray-400 hover:text-white" data-toggle="tooltip"
                        title="<?= $user_class->formattedawake; ?>">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Awake</span>
                        <img src="assets/images/icons/Sleep.png" />
                        <span class="ml-2 text-white text-sm"><?= $user_class->awakepercent; ?>%</span>
                    </span>

                    <span class="relative flex items-center p-1 text-gray-400 hover:text-white" data-toggle="tooltip"
                        title="<?= $user_class->formattednerve; ?>">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Nerve</span>
                        <img src="assets/images/icons/Brain.png" />
                        <span class="ml-2 text-white text-sm"><?= $user_class->nervepercent; ?>%</span>
                    </span>

                    <span class="relative flex items-center p-1 text-gray-400 hover:text-white" data-toggle="tooltip"
                        title="<?= $user_class->formattedenergy; ?>">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Energy</span>
                        <img src="assets/images/icons/Lightning Bolt.png" />
                        <span class="ml-2 text-white text-sm"><?= $user_class->energypercent; ?>%</span>
                    </span>

                    <span class="relative flex items-center p-1 text-gray-400 hover:text-white" data-toggle="tooltip"
                        title="<?= $user_class->formattedhp; ?>">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">Health</span>
                        <img src="assets/images/icons/Heart.png" />
                        <span class="ml-2 text-white text-sm"><?= $user_class->hppercent; ?>%</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</nav>