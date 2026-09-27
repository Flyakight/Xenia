<?php
/**
 * Single post template.
 *
 * @package TannicStudio
 */

get_header();
echo "<!-- SINGLE MARKER 2026-03-03 -->\n";
while ( have_posts() ) :
    the_post();

    $primary_category = tannic_get_post_primary_category();
    $read_time        = tannic_get_post_read_time();
    $related_args     = array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 3,
        'post__not_in'        => array( get_the_ID() ),
        'ignore_sticky_posts' => true,
    );

    if ( $primary_category ) {
        $related_args['cat'] = $primary_category->term_id;
    }

    $related_posts = new WP_Query( $related_args );
    ?>

    <article class="post-article">
        <header class="post-hero">
            <div class="post-hero-inner">
                <?php if ( $primary_category ) : ?>
                    <a class="post-eyebrow mono" href="<?php echo esc_url( get_category_link( $primary_category ) ); ?>">
                        <?php echo esc_html( $primary_category->name ); ?>
                    </a>
                <?php endif; ?>

                <h1 class="post-title"><?php the_title(); ?></h1>

                <?php if ( has_excerpt() ) : ?>
                    <p class="post-dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>

                <div class="post-meta">
                    <span><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
                    <span><?php echo esc_html( $read_time ); ?></span>
                    <span><?php echo esc_html( get_the_author() ); ?></span>
                </div>
            </div>

            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="post-hero-media">
                    <?php the_post_thumbnail( 'tannic-editorial', array( 'loading' => 'eager' ) ); ?>
                </figure>
            <?php endif; ?>
        </header>

        <div class="post-body-shell">
            <div class="post-body-grid">
                <aside class="post-sidebar">
                    <div class="post-sidebar-card">
                        <span class="post-sidebar-label mono"><?php esc_html_e( 'Article', 'tannic-studio' ); ?></span>
                        <ul class="post-sidebar-list">
                            <li><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></li>
                            <li><?php echo esc_html( $read_time ); ?></li>
                            <li><?php echo esc_html( get_the_author() ); ?></li>
                            <?php if ( $primary_category ) : ?>
                                <li><?php echo esc_html( $primary_category->name ); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </aside>

                <div class="post-content entry-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

        <?php if ( $related_posts->have_posts() ) : ?>
            <section class="post-related" aria-label="<?php esc_attr_e( 'Related posts', 'tannic-studio' ); ?>">
                <div class="post-related-inner">
                    <p class="post-related-eyebrow mono"><?php esc_html_e( 'Continue Reading', 'tannic-studio' ); ?></p>
                    <h2 class="post-related-title"><?php esc_html_e( 'More from the Journal', 'tannic-studio' ); ?></h2>
                    <div class="post-related-grid">
                        <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
                            <?php $related_category = tannic_get_post_primary_category(); ?>
                            <article class="post-related-card">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a class="post-related-media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                        <?php the_post_thumbnail( 'tannic-editorial', array( 'loading' => 'lazy' ) ); ?>
                                    </a>
                                <?php endif; ?>
                                <div class="post-related-copy">
                                    <?php if ( $related_category ) : ?>
                                        <p class="post-related-kicker mono"><?php echo esc_html( $related_category->name ); ?></p>
                                    <?php endif; ?>
                                    <h3 class="post-related-card-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <p class="post-related-excerpt">
                                        <?php
                                        $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 20 );
                                        echo esc_html( $excerpt );
                                        ?>
                                    </p>
                                    <div class="journal-meta">
                                        <span><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></span>
                                        <span><?php echo esc_html( tannic_get_post_read_time() ); ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section class="journal-cta">
            <p class="journal-cta-eyebrow mono"><?php esc_html_e( 'Start Something', 'tannic-studio' ); ?></p>
            <h2 class="journal-cta-title"><?php esc_html_e( 'If the thinking fits, the work probably will too.', 'tannic-studio' ); ?></h2>
            <a class="btn btn-outline" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
                <?php esc_html_e( 'Start a Project', 'tannic-studio' ); ?>
            </a>
        </section>
    </article>

    <?php
    wp_reset_postdata();
endwhile;

get_footer();
