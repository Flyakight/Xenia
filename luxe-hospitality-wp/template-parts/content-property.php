<?php
/**
 * Property Content Template
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'property-single' ); ?>>

	<!-- HERO -->
	<?php if ( has_post_thumbnail() ) : ?>
		<section class="hero">
			<?php the_post_thumbnail( 'luxe-full', array( 'class' => 'hero__background' ) ); ?>
			<div class="hero__content">
				<h1 data-expand-text><?php the_title(); ?></h1>
				<?php
				$property_type = get_the_terms( get_the_ID(), 'property_type' );
				if ( ! empty( $property_type ) ) {
					?>
					<p class="text-meta">
						<?php echo esc_html( $property_type[0]->name ); ?>
					</p>
					<?php
				}
				?>
			</div>
		</section>
	<?php endif; ?>

	<!-- CONTENT -->
	<section class="section-content py-2xl">
		<div class="container container-narrow">
			<div class="content-body scroll-fade">
				<?php the_content(); ?>
			</div>
		</div>
	</section>

	<!-- GALLERY -->
	<?php
	$gallery = get_field( 'gallery', get_the_ID() );
	if ( $gallery && ! empty( $gallery ) ) :
		?>
		<section class="section-gallery py-2xl">
			<div class="container">
				<h2 class="text-center mb-xl"><?php esc_html_e( 'Gallery', 'luxe' ); ?></h2>
				<div class="gallery-grid">
					<?php
					foreach ( $gallery as $image ) :
						$alt = get_post_meta( $image['ID'], '_wp_attachment_image_alt', true );
						?>
						<div class="gallery-item image-parallax scroll-fade">
							<img 
								src="<?php echo esc_url( $image['url'] ); ?>" 
								alt="<?php echo esc_attr( $alt ); ?>"
								loading="lazy"
								decoding="async"
							/>
						</div>
						<?php
					endforeach;
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
	?>

	<!-- AMENITIES -->
	<?php
	$amenities = get_field( 'amenities', get_the_ID() );
	if ( $amenities && ! empty( $amenities ) ) :
		?>
		<section class="section-amenities py-2xl">
			<div class="container">
				<h2><?php esc_html_e( 'Amenities', 'luxe' ); ?></h2>
				<div class="grid-3 mt-xl">
					<?php
					foreach ( $amenities as $amenity ) :
						?>
						<div class="card scroll-fade">
							<?php if ( ! empty( $amenity['icon'] ) ) : ?>
								<div class="amenity-icon mb-md">
									<?php echo wp_kses_post( $amenity['icon'] ); ?>
								</div>
							<?php endif; ?>
							<h4><?php echo esc_html( $amenity['name'] ); ?></h4>
							<?php if ( ! empty( $amenity['description'] ) ) : ?>
								<p><?php echo esc_html( $amenity['description'] ); ?></p>
							<?php endif; ?>
						</div>
						<?php
					endforeach;
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
	?>

	<!-- BOOKING CTA -->
	<section class="section-cta py-2xl">
		<div class="container text-center">
			<h2><?php esc_html_e( 'Ready to Experience This Property?', 'luxe' ); ?></h2>
			<p class="mt-md mb-lg">
				<?php esc_html_e( 'Discover what makes this destination unforgettable.', 'luxe' ); ?>
			</p>
			<a href="<?php echo esc_url( get_field( 'booking_url', get_the_ID() ) ?: '#contact' ); ?>" class="button button--inverted">
				<?php esc_html_e( 'Plan Your Visit', 'luxe' ); ?>
			</a>
		</div>
	</section>

	<!-- RELATED PROPERTIES -->
	<?php
	$related = new WP_Query( array(
		'post_type'      => 'property',
		'posts_per_page' => 3,
		'post__not_in'   => array( get_the_ID() ),
		'orderby'        => 'rand',
	) );

	if ( $related->have_posts() ) :
		?>
		<section class="section-related py-2xl">
			<div class="container">
				<h2><?php esc_html_e( 'Explore Other Properties', 'luxe' ); ?></h2>
				<div class="grid-3 mt-xl" data-card-stagger>
					<?php
					while ( $related->have_posts() ) :
						$related->the_post();
						?>
						<article class="card scroll-fade">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="image-parallax mb-md">
									<?php the_post_thumbnail( 'luxe-card' ); ?>
								</div>
							<?php endif; ?>
							<h3><?php the_title(); ?></h3>
							<?php the_excerpt(); ?>
							<a href="<?php the_permalink(); ?>" class="button button--gold mt-md">
								<?php esc_html_e( 'Discover', 'luxe' ); ?>
							</a>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
	?>

</article><!-- #post-<?php the_ID(); ?> -->
