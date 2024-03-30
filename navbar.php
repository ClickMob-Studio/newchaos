<?php

$topLinks = array(
	array(
		'name' => 'Home',
		'url'  => 'index.php',
	),
	array(
		'name' => 'City',
		'url'  => 'explore.php',
	),
	array(
		'name' => 'Crimes',
		'url'  => 'criminal.php',
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
		'count' => $count['jail'],
	),
	array(
		'name'  => 'Hospital',
		'url'   => 'hospital.php',
		'count' => $counts['hospital'],
	),
	array(
		'name' => 'School',
		'url'  => 'education.php',
	),
	array(
		'name' => 'Online',
		'url'  => 'usersonline.php',
	),
);

$leftLinks = array(
	array(
		'name'  => 'Mail',
		'url'   => 'mailbox.php',
		'count' => $counts['mail'],
	),
	array(
		'name'  => 'Events',
		'url'   => 'events.php',
		'count' => $counts['event'],
	),
	array(
		'name' => 'Inventory',
		'url'  => 'inventory.php',
	),
	array(
		'name' => 'Settings',
		'url'  => 'preferences.php',
	),
	array(
		'name' => 'Log Out',
		'url'  => 'logout.php',
	),
);

$navPage = str_replace( '/', '', $_SERVER['REQUEST_URI'] );

?>

<nav class="navbar navbar-expand-lg p-0 dcNav dcTopNav">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php"><img src="asset/img/logo1.png" alt="" class="mainLogo mx-5"></a>
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
