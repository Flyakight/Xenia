<?php
/**
 * Header Template
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<header class="site-header" role="banner">
		<nav class="site-nav" role="navigation" aria-label="<?php esc_attr_e( 'Main Navigation', 'luxe' ); ?>">
			<div class="container">
				<div class="nav-flex">
					
					<!-- Logo -->
					<div class="nav-brand">
						<?php
						if ( has_custom_logo() ) {
							the_custom_logo();
						} else {
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title">
								<?php bloginfo( 'name' ); ?>
							</a>
							<?php
						}
						?>
					</div>

					<!-- Primary Menu -->
					<div class="nav-menu-wrapper">
						<?php
						wp_nav_menu( array(
							'theme_location'  => 'primary',
							'menu_class'      => 'nav-menu',
							'container'       => false,
							'depth'           => 2,
							'fallback_cb'     => 'luxe_menu_fallback',
						) );
						?>
					</div>

					<!-- Mobile Menu Toggle -->
					<button 
						class="nav-toggle" 
						data-mobile-menu-toggle 
						aria-label="<?php esc_attr_e( 'Toggle Menu', 'luxe' ); ?>"
						aria-expanded="false"
					>
						<span class="nav-toggle-line"></span>
						<span class="nav-toggle-line"></span>
						<span class="nav-toggle-line"></span>
					</button>

				</div>
			</div>
		</nav>

		<!-- Mobile Menu -->
		<div class="nav-mobile" data-mobile-menu>
			<?php
			wp_nav_menu( array(
				'theme_location'  => 'primary',
				'menu_class'      => 'nav-mobile-menu',
				'container'       => false,
				'depth'           => 2,
				'fallback_cb'     => 'luxe_menu_fallback',
			) );
			?>
		</div>
	</header>

	<main id="main" class="site-main" role="main">
<?php

/**
 * Menu Fallback
 */
function luxe_menu_fallback() {
	echo wp_kses_post( '<div class="nav-menu"><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Assign Menu', 'luxe' ) . '</a></div>' );
}
