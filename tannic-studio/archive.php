<?php
/**
 * Generic archive fallback.
 *
 * @package TannicStudio
 */

get_header();
?>

<section class="journal-page journal-page--fallback">
    <section class="journal-hero">
        <div class="journal-hero-inner">
            <div class="journal-hero-copy">
                <p class="journal-eyebrow mono"><?php esc_html_e( 'Archive', 'tannic-studio' ); ?></p>
                <h1 class="journal-title"><?php the_archive_title(); ?></h1>
                <?php if ( get_the_archive_description() ) : ?>
                    <p class="journal-intro"><?php echo esc_html( wp_strip_all_tags( get_the_archive_description() ) ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if ( have_posts() ) : ?>
        <section class="journal-archive">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php $primary_category = tannic_get_post_primary_category(); ?>
                <article class="journal-row">
                    <a class="journal-row-link" href="<?php the_permalink(); ?>">
                        <div class="journal-row-date mono"><?php echo esc_html( get_the_date( 'm.d.Y' ) ); ?></div>
                        <div class="journal-row-main">
                            <?php if ( $primary_category ) : ?>
                                <p class="journal-row-category mono"><?php echo esc_html( $primary_category->name ); ?></p>
                            <?php endif; ?>
                            <h2 class="journal-row-title"><?php the_title(); ?></h2>
                            <p class="journal-row-excerpt">
                                <?php
                                $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 24 );
                                echo esc_html( $excerpt );
                                ?>
                            </p>
                        </div>
                        <div class="journal-row-action mono"><?php esc_html_e( 'Open', 'tannic-studio' ); ?> &rarr;</div>
                    </a>
                </article>
            <?php endwhile; ?>
        </section>
    <?php else : ?>
        <section class="journal-empty">
            <p><?php esc_html_e( 'Nothing is published here yet.', 'tannic-studio' ); ?></p>
        </section>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
