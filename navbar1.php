<?php

$topLinks = array(
	array(
		'name' => 'Home',
		'url'  => 'index.php',
	),
	array(
		'name' => '<!_-cityname-_!>',
		'url'  => 'city.php',
	),
	array(
		'name' => 'Crimes',
		'url'  => 'newcrimes.php',
	),
	array(
		'name' => 'Gym',
		'url'  => 'speedGym.php',
	),
	array(
		'name' => 'Bank',
		'url'  => 'bank.php',
	),
	array(
		'name'  => 'Jail',
		'url'   => 'jail.php',
		'count' => '<!_-jail-_!>',
	),
	array(
		'name'  => 'Hospital',
		'url'   => 'hospital.php',
		'count' => $counts['hospital'],
	),
	array(
		'name' => 'Online',
		'url'  => 'online.php',
	),
);

$leftLinks = array();
$leftLinks[] =
    array(
        'name'  => 'Search Players',
        'url'   => 'search.php'
    );
$leftLinks[] =
    array(
		'name'  => 'Mail',
		'url'   => 'pms.php?view=inbox',
		'count' => $counts['mail'],
	);
$leftLinks[] =
	array(
		'name'  => 'Events',
		'url'   => 'events.php',
		'count' => $counts['event'],
	);
if ($counts['updates'] > 0) {
    $leftLinks[] =
        array(
            'name'  => 'Updates',
            'url'   => 'gameupdates.php',
            'count' => $counts['updates'],
        );
}
$leftLinks[] =
	array(
		'name' => 'Chat',
		'url'  => 'globalchat.php',
		'count' => $counts['gchat'],
	);
$leftLinks[] =
    array(
        'name' => 'Maze',
        'url'  => 'maze.php',
    );
$leftLinks[] =
	array(
		'name' => 'Missions',
		'url'  => 'missions.php',
	);
$leftLinks[] =
	array(
		'name' => 'Inventory',
		'url'  => 'inventory.php',
	);
$leftLinks[] =
	array(
		'name' => 'Raids',
		'url'  => 'raids.php',
		'count'  => $counts['gang_raid_count'],
	);
$leftLinks[] =
	array(
		'name' => 'Backalley',
		'url'  => 'backalley_new.php',
	);

$userPrestigeSkills = getUserPrestigeSkills($user_class);
if ($userPrestigeSkills['speed_attack_unlock'] > 0) {
    $leftLinks[] =
        array(
            'name' => 'Super Attack',
            'url'  => 'super_attack.php',
        );
}
if ($user_class->gang) {
    $leftLinks[] =
        array(
            'name' => 'Gang',
            'url'  => 'gang.php',
        );
		$leftLinks[] =
        array(
            'name' => 'Gang Mail',
            'url'  => 'gangmail.php',
			'count' => $counts['gangmail'],
        );

} else {
    $leftLinks[] =
        array(
            'name' => 'Create Gang',
            'url'  => 'creategang.php',
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
		'url'  => 'settings.php',
	);
$leftLinks[] =
    array(
        'name' => 'Discord',
        'url'  => 'https://discord.gg/7rkFUKwrPz',
    );
$leftLinks[] =
	array(
		'name' => 'Log Out',
		'url'  => 'index.php?action=logout',
	);

$navPage = str_replace( '/', '', $_SERVER['REQUEST_URI'] );

?> 

<nav class="navbar navbar-expand-lg p-0 dcNav dcTopNav">
    <div class="container-fluid scrollNav">
        <a class="navbar-brand" href="index.php">
            <img src="asset/img/logo1.png" alt="Deadly Cartel logo" class="mainLogo mx-5">
            <!-- <img src="asset/halloween.png" alt="Deadly Cartel logo" class="mainLogo mx-5"> -->
       
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars fa-2x"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="dcPanel h-100">
                <div class="text-center dcBannerButtonsContainer voteMobile">
                    <a href="vote.php" class="dcSecondaryButton my-3">Vote for <i class="far fa-gem"></i></a>
                    <a href="refer.php" class="dcSecondaryButton my-3">Refer for <i class="far fa-gem"></i></a>
                    <a href="store.php" class="dcSecondaryButton my-3">Upgrades <i class="fas fa-level-up-alt"></i></a>
                </div>
                <!-- Sever Time: 09/04/2024  10:24:56 -->
            </div>
            <ul class="navbar-nav">
                <?php foreach ($topLinks as $link) : ?>
                    <li class="nav-item">
                        <?php
                            $linkText    = $link['name'] . ($link['count'] ? ' [' . $link['count'] . ']' : '');
                            $linkClasses = $link['url'] === $navPage ? ' active' : '' . ($link['count'] ? ' hasNew' : '');
                        ?>
                        <a class="nav-link px-4 py-lg-5<?php echo $linkClasses; ?>" aria-current="page" href="<?php echo $link['url']; ?>"><?php echo $linkText; ?></a>
                    </li>
                <?php endforeach; ?>

                <?php foreach ($leftLinks as $link) : ?>
                    <li class="nav-item d-lg-none">
                        <?php
                            $linkText    = $link['name'] . ($link['count'] ? ' [' . $link['count'] . ']' : '');
                            $linkClasses = $link['url'] === $navPage ? ' active' : '' . ($link['count'] ? ' hasNew' : '');
                        ?>
                        <a class="nav-link px-4 py-lg-5<?php echo $linkClasses; ?>" aria-current="page" href="<?php echo $link['url']; ?>"><?php echo $linkText; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>
