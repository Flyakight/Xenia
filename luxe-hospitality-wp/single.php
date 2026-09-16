<?php
/**
 * Single Post Template
 */
get_header();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<section class="hero">
			<?php the_post_thumbnail( 'luxe-full', array( 'class' => 'hero__background' ) ); ?>
			<div class="hero__content text-center">
				<p class="text-meta"><?php echo get_the_date(); ?></p>
				<h1 data-expand-text><?php the_title(); ?></h1>
			</div>
		</section>
	<?php else : ?>
		<section class="section-content py-2xl">
			<div class="container text-center">
				<h1><?php the_title(); ?></h1>
				<p class="text-meta"><?php echo get_the_date(); ?></p>
			</div>
		</section>
	<?php endif; ?>

	<section class="section-content py-2xl">
		<div class="container container-narrow">
			<div class="content-body scroll-fade">
				<?php the_content(); ?>
			</div>
		</div>
	</section>

</article>

<?php get_footer();
