<?php
/**
 * Home/Blog Main Template
 */
get_header();
?>

<section class="section-hero-blog py-2xl">
	<div class="container text-center">
		<h1 data-expand-text><?php esc_html_e( 'Insights & Stories', 'luxe' ); ?></h1>
		<p><?php bloginfo( 'description' ); ?></p>
	</div>
</section>

<?php if ( have_posts() ) : ?>

	<section class="section-posts py-2xl">
		<div class="container">
			<div class="grid-3" data-card-stagger>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="card scroll-fade">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="image-parallax mb-md">
								<?php the_post_thumbnail( 'luxe-card' ); ?>
							</div>
						<?php endif; ?>

						<p class="text-meta"><?php echo get_the_date(); ?></p>
						<h3><?php the_title(); ?></h3>
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
			<div class="pagination-wrapper mt-2xl text-center">
				<?php
				the_posts_pagination( array(
					'prev_text' => '← ' . esc_html__( 'Newer', 'luxe' ),
					'next_text' => esc_html__( 'Older', 'luxe' ) . ' →',
					'type'      => 'list',
				) );
				?>
			</div>
		</div>
	</section>

<?php else : ?>

	<section class="section-no-posts py-2xl">
		<div class="container text-center">
			<h2><?php esc_html_e( 'No posts yet', 'luxe' ); ?></h2>
			<p><?php esc_html_e( 'Check back soon for updates.', 'luxe' ); ?></p>
		</div>
	</section>

<?php endif; ?>

<?php get_footer();
