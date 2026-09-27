<?php
/**
 * Generic page template.
 *
 * @package TannicStudio
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>
    <article class="page-shell">
        <header class="page-hero">
            <div class="page-hero-inner">
                <p class="page-eyebrow mono"><?php esc_html_e( 'Page', 'tannic-studio' ); ?></p>
                <h1 class="page-title"><?php the_title(); ?></h1>
                <?php if ( has_excerpt() ) : ?>
                    <p class="page-intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>
            </div>
        </header>

        <div class="page-body">
            <div class="page-body-inner entry-content">
                <?php the_content(); ?>
            </div>
        </div>
    </article>
    <?php
endwhile;

get_footer();
