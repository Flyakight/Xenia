<?php
/**
 * 404 Not Found Template
 */
get_header();
?>

<section class="section-404 py-2xl">
	<div class="container text-center">
		<h1 style="font-size: 8rem; opacity: 0.1; line-height: 1;">404</h1>
		<h2 class="mt-0"><?php esc_html_e( 'Page Not Found', 'luxe' ); ?></h2>
		<p class="mb-lg">
			<?php esc_html_e( 'Sorry, the page you\'re looking for doesn\'t exist. Let\'s get you back on track.', 'luxe' ); ?>
		</p>

		<div class="actions-404 mt-xl">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button button--inverted mr-md">
				<?php esc_html_e( 'Back Home', 'luxe' ); ?>
			</a>
			<button onclick="history.back()" class="button">
				<?php esc_html_e( 'Go Back', 'luxe' ); ?>
			</button>
		</div>

		<!-- Recent Posts -->
		<?php
		$recent = new WP_Query( array(
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		if ( $recent->have_posts() ) :
			?>
			<div class="mt-2xl">
				<h3><?php esc_html_e( 'Recent Posts', 'luxe' ); ?></h3>
				<div class="grid-3 mt-lg" data-card-stagger>
					<?php
					while ( $recent->have_posts() ) :
						$recent->the_post();
						?>
						<article class="card">
							<h4><?php the_title(); ?></h4>
							<a href="<?php the_permalink(); ?>" class="button button--gold mt-md">
								<?php esc_html_e( 'Read', 'luxe' ); ?>
							</a>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<?php
		endif;
		?>
	</div>
</section>

<?php get_footer();
