<?php
/**
 * Main Index Template
 */
get_header();
?>

<section class="section-archive">
	<div class="container">
		
		<?php
		if ( is_home() ) {
			?>
			<h1><?php esc_html_e( 'Insights & Stories', 'luxe' ); ?></h1>
			<?php
		} elseif ( is_category() ) {
			?>
			<h1><?php single_cat_title(); ?></h1>
			<?php
		} elseif ( is_tag() ) {
			?>
			<h1><?php single_tag_title(); ?></h1>
			<?php
		} elseif ( is_search() ) {
			?>
			<h1><?php printf( esc_html__( 'Search Results: %s', 'luxe' ), get_search_query() ); ?></h1>
			<?php
		} else {
			?>
			<h1><?php the_archive_title(); ?></h1>
			<?php
		}
		?>

		<?php if ( have_posts() ) : ?>

			<div class="grid-3 mt-xl" data-card-stagger>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="card scroll-fade">
						<?php
						if ( has_post_thumbnail() ) {
							?>
							<div class="image-parallax mb-md">
								<?php the_post_thumbnail( 'luxe-card' ); ?>
							</div>
							<?php
						}
						?>

						<h3><?php the_title(); ?></h3>
						<p class="text-meta">
							<?php echo get_the_date(); ?>
						</p>
						<?php the_excerpt(); ?>

						<a href="<?php the_permalink(); ?>" class="button button--gold mt-md">
							<?php esc_html_e( 'Read More', 'luxe' ); ?>
						</a>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<!-- Pagination -->
			<?php
			the_posts_pagination( array(
				'prev_text' => esc_html__( 'Previous', 'luxe' ),
				'next_text' => esc_html__( 'Next', 'luxe' ),
			) );
			?>

		<?php else : ?>

			<div class="no-posts mt-xl text-center">
				<h2><?php esc_html_e( 'Nothing found', 'luxe' ); ?></h2>
				<p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'luxe' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button mt-md">
					<?php esc_html_e( 'Back Home', 'luxe' ); ?>
				</a>
			</div>

		<?php endif; ?>

	</div>
</section>

<?php get_footer();
