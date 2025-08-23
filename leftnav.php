<?php
/**
The array $leftLinks is previously declared in the file navbar.php.
This is necessary to allow the main menu to include the left menu on mobile devices
 **/
?>
<nav class="navbar navbar-expand-lg p-0 dcNav dcLeftNav">
	<div class="navbar d-block w-100 p-0">
		<ul class="navbar-nav text-center">
			<?php foreach ($leftLinks as $link): ?>
				<li class="nav-item">
					<?php
					$linkText = $link['name'] . (isset($link['count']) && $link['count'] ? ' [' . $link['count'] . ']' : '');
					$linkClasses = $link['url'] === $navPage ? ' active' : '' . ((isset($link['count']) && $link['count'] > 0) ? ' ahasNew' : '');
					?>
					<a class="nav-link px-4 py-3<?php echo $linkClasses; ?>" aria-current="page"
						href="<?php echo $link['url']; ?>"><?php echo $linkText; ?></a>
				</li>
			<?php endforeach; ?>
			<?php if ($user_class->admin > 0): ?>
				<a class="nav-link px-4 py-3" href="admin_panel.php">Admin Panel</a>
			<?php endif; ?>
		</ul>
	</div>
</nav>