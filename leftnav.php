<?php
/**
The array $leftLinks is previously declared in the file navbar.php.
This is necessary to allow the main menu to include the left menu on mobile devices
 **/
	if($user_class->id == 1){
?>
<div class="row mx-auto my-3 mainContent">
    <div class="d-none d-lg-block col-2 dcLeftNavContainer p-0">
        <nav class="navbar navbar-expand-lg p-0 dcNav dcLeftNav">
            <div class="navbar d-block w-100 p-0">
                <ul class="navbar-nav text-center">
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="refer_leaderboard.php">Referral Competition</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="search.php">Search Players</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 hasNew" aria-current="page" href="pms.php?view=inbox">Mail [0]</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 hasNew" aria-current="page" href="events.php">Events [2]</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="globalchat.php">Chat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="maze.php">Maze</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="missions.php">Missions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="inventory.php">Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="raids.php">Raids</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="backalley.php">Backalley</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="gang.php">Gang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="gangmail.php">Gang Mail</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="preferences.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="https://discord.gg/7rkFUKwrPz">Discord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3" aria-current="page" href="index.php?action=logout">Log Out</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
	<?php 
	}else{
		?>


<nav class="navbar navbar-expand-lg p-0 dcNav dcLeftNav">
	<div class="navbar d-block w-100 p-0">
		<ul class="navbar-nav text-center">
			<?php foreach ( $leftLinks as $link ) : ?>
				<li class="nav-item">
					<?php
						$linkText = $link['name'] . ( $link['count'] ? ' [' . $link['count'] . ']' : '' );
						$linkClasses = $link['url'] === $navPage ? ' active' : '' . ( $link['count'] ? ' hasNew' : '' );
					?>
					<a class="nav-link px-4 py-3<?php echo $linkClasses; ?>" aria-current="page" href="<?php echo $link['url']; ?>"><?php echo $linkText; ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>
<?php 
	}