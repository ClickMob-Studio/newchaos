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
		'url'  => 'crime.php',
	),
	array(
		'name' => 'Gym',
		'url'  => 'gym.php',
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

$leftLinks = array(
	array(
		'name'  => 'Mail',
		'url'   => 'pms.php?view=inbox',
		'count' => $counts['mail'],
	),
	array(
		'name'  => 'Events',
		'url'   => 'events.php',
		'count' => $counts['event'],
	),
	array(
		'name' => 'Chat',
		'url'  => 'globalchat.php',
	),
	array(
		'name' => 'Missions',
		'url'  => 'missions.php',
	),
	array(
		'name' => 'Inventory',
		'url'  => 'inventory.php',
	),
	array(
		'name' => 'Raids',
		'url'  => 'raids.php',
	),
	array(
		'name' => 'Backalley',
		'url'  => 'backalley.php',
	),
	array(
		'name' => 'Speed Crimes',
		'url'  => 'newcrimes.php',
	),
	array(
		'name' => 'Speed Gym',
		'url'  => 'speedGym.php',
	),
	array(
		'name' => 'Settings',
		'url'  => 'preferences.php',
	),
	array(
		'name' => 'Log Out',
		'url'  => 'index.php?action=logout',
	),
);

$navPage = str_replace( '/', '', $_SERVER['REQUEST_URI'] );

?>

<nav class="navbar navbar-expand-lg p-0 dcNav dcTopNav">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php"><img src="asset/img/logo1.png" alt="Deadly Cartel logo" class="mainLogo mx-5"></a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			<i class="fas fa-bars fa-2x"></i>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
				<?php foreach ( $topLinks as $link ) : ?>
					<li class="nav-item">
						<?php
							$linkText    = $link['name'] . ( $link['count'] ? ' [' . $link['count'] . ']' : '' );
							$linkClasses = $link['url'] === $navPage ? ' active' : '' . ( $link['count'] ? ' hasNew' : '' );
						?>
						<a class="nav-link px-4 py-lg-5<?php echo $linkClasses; ?>" aria-current="page" href="<?php echo $link['url']; ?>"><?php echo $linkText; ?></a>
					</li>
				<?php endforeach; ?>

				<?php foreach ( $leftLinks as $link ) : ?>
					<li class="nav-item d-lg-none">
						<?php
							$linkText    = $link['name'] . ( $link['count'] ? ' [' . $link['count'] . ']' : '' );
							$linkClasses = $link['url'] === $navPage ? ' active' : '' . ( $link['count'] ? ' hasNew' : '' );
						?>
						<a class="nav-link px-4 py-lg-5<?php echo $linkClasses; ?>" aria-current="page" href="<?php echo $link['url']; ?>"><?php echo $linkText; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</nav>
