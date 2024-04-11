<?php
/**
The array $leftLinks is previously declared in the file navbar.php.
This is necessary to allow the main menu to include the left menu on mobile devices
 **/
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
