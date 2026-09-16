<?php
/**
 * Footer Template
 */
?>

	</main><!-- #main -->

	<footer class="site-footer" role="contentinfo">
		<div class="container">
			<div class="footer-content">
				
				<!-- Footer Widgets -->
				<div class="footer-widgets">
					<?php
					if ( is_active_sidebar( 'footer-1' ) ) {
						dynamic_sidebar( 'footer-1' );
					}
					if ( is_active_sidebar( 'footer-2' ) ) {
						dynamic_sidebar( 'footer-2' );
					}
					if ( is_active_sidebar( 'footer-3' ) ) {
						dynamic_sidebar( 'footer-3' );
					}
					?>
				</div>

				<!-- Footer Menu -->
				<div class="footer-menu">
					<?php
					wp_nav_menu( array(
						'theme_location'  => 'footer',
						'container'       => false,
						'depth'           => 1,
						'fallback_cb'     => false,
					) );
					?>
				</div>

				<!-- Copyright -->
				<div class="footer-bottom">
					<p class="site-copyright">
						&copy; <?php echo date( 'Y' ); ?> 
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php bloginfo( 'name' ); ?>
						</a>
						<?php esc_html_e( 'All Rights Reserved', 'luxe' ); ?>
					</p>
				</div>

			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
