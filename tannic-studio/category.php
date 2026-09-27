<?php
/**
 * Category archive template.
 *
 * @package TannicStudio
 */

get_header();

$category         = get_queried_object();
$paged            = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$journal_url      = tannic_get_posts_page_url();
$all_topics       = get_categories( array( 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC' ) );
$latest_in_topic  = get_posts(
    array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'cat'            => $category->term_id,
    )
);
$latest_month     = ! empty( $latest_in_topic ) ? get_the_date( 'F Y', $latest_in_topic[0] ) : '';
$featured_post_id = $paged === 1 ? tannic_get_journal_featured_post_id( $category->term_id ) : 0;
$featured_post    = $featured_post_id ? get_post( $featured_post_id ) : null;
?>

<div class="journal-page journal-page--category">
    <section class="journal-hero">
        <div class="journal-hero-inner">
            <div class="journal-hero-copy">
                <p class="journal-eyebrow mono"><?php esc_html_e( 'Topic', 'tannic-studio' ); ?></p>
                <h1 class="journal-title"><?php echo esc_html( single_cat_title( '', false ) ); ?></h1>
                <p class="journal-intro">
                    <?php echo esc_html( category_description() ? wp_strip_all_tags( category_description() ) : __( 'A concentrated slice of the Journal, collected around one topic.', 'tannic-studio' ) ); ?>
                </p>
            </div>
            <aside class="journal-signal-card" aria-label="<?php esc_attr_e( 'Topic overview', 'tannic-studio' ); ?>">
                <span class="journal-signal-label mono"><?php esc_html_e( 'In This Topic', 'tannic-studio' ); ?></span>
                <ul class="journal-signal-list">
                    <li>
                        <?php
                        printf(
                            /* translators: %s: number of posts in category */
                            esc_html__( '%s essays filed', 'tannic-studio' ),
                            esc_html( number_format_i18n( (int) $category->count ) )
                        );
                        ?>
                    </li>
                    <?php if ( $latest_month ) : ?>
                        <li>
                            <?php
                            printf(
                                /* translators: %s: month and year */
                                esc_html__( 'Latest: %s', 'tannic-studio' ),
                                esc_html( $latest_month )
                            );
                            ?>
                        </li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url( $journal_url ); ?>"><?php esc_html_e( 'View all journal entries', 'tannic-studio' ); ?></a></li>
                </ul>
            </aside>
        </div>
    </section>

    <nav class="journal-topics" aria-label="<?php esc_attr_e( 'Journal topics', 'tannic-studio' ); ?>">
        <a class="journal-topic" href="<?php echo esc_url( $journal_url ); ?>">
            <?php esc_html_e( 'All', 'tannic-studio' ); ?>
        </a>
        <?php foreach ( $all_topics as $topic ) : ?>
            <a class="journal-topic<?php echo (int) $topic->term_id === (int) $category->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $topic ) ); ?>">
                <?php echo esc_html( $topic->name ); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if ( $featured_post ) : ?>
        <?php
        $featured_image   = get_the_post_thumbnail_url( $featured_post, 'tannic-editorial' );
        $featured_excerpt = has_excerpt( $featured_post ) ? $featured_post->post_excerpt : wp_trim_words( wp_strip_all_tags( $featured_post->post_content ), 28 );
        ?>
        <section class="journal-feature-wrap">
            <article class="journal-feature">
                <a class="journal-feature-media" href="<?php echo esc_url( get_permalink( $featured_post ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $featured_post ) ); ?>">
                    <?php if ( $featured_image ) : ?>
                        <img src="<?php echo esc_url( $featured_image ); ?>" alt="<?php echo esc_attr( get_the_title( $featured_post ) ); ?>" loading="eager">
                    <?php else : ?>
                        <div class="journal-feature-placeholder" aria-hidden="true"></div>
                    <?php endif; ?>
                </a>
                <div class="journal-feature-content">
                    <p class="journal-feature-kicker mono"><?php echo esc_html( $category->name ); ?></p>
                    <h2 class="journal-feature-title">
                        <a href="<?php echo esc_url( get_permalink( $featured_post ) ); ?>">
                            <?php echo esc_html( get_the_title( $featured_post ) ); ?>
                        </a>
                    </h2>
                    <p class="journal-feature-excerpt"><?php echo esc_html( $featured_excerpt ); ?></p>
                    <div class="journal-meta">
                        <span><?php echo esc_html( get_the_date( 'F j, Y', $featured_post ) ); ?></span>
                        <span><?php echo esc_html( tannic_get_post_read_time( $featured_post->ID ) ); ?></span>
                        <span><?php echo esc_html( get_the_author_meta( 'display_name', $featured_post->post_author ) ); ?></span>
                    </div>
                    <a class="btn btn-outline" href="<?php echo esc_url( get_permalink( $featured_post ) ); ?>">
                        <?php esc_html_e( 'Read Article', 'tannic-studio' ); ?>
                    </a>
                </div>
            </article>
        </section>
    <?php endif; ?>

    <?php if ( have_posts() ) : ?>
        <section class="journal-archive" aria-label="<?php esc_attr_e( 'Topic articles', 'tannic-studio' ); ?>">
            <?php
            $row_count      = 0;
            $break_inserted = false;

            while ( have_posts() ) :
                the_post();

                if ( $featured_post_id && get_the_ID() === $featured_post_id ) {
                    continue;
                }

                $row_count++;
                ?>
                <article class="journal-row" data-reveal>
                    <a class="journal-row-link" href="<?php the_permalink(); ?>">
                        <div class="journal-row-date mono"><?php echo esc_html( get_the_date( 'm.d.Y' ) ); ?></div>
                        <div class="journal-row-main">
                            <p class="journal-row-category mono"><?php echo esc_html( $category->name ); ?></p>
                            <h3 class="journal-row-title"><?php the_title(); ?></h3>
                            <p class="journal-row-excerpt">
                                <?php
                                $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 24 );
                                echo esc_html( $excerpt );
                                ?>
                            </p>
                        </div>
                        <div class="journal-row-meta mono"><?php echo esc_html( tannic_get_post_read_time() ); ?></div>
                        <div class="journal-row-action mono"><?php esc_html_e( 'Read', 'tannic-studio' ); ?> &rarr;</div>
                    </a>
                </article>

                <?php if ( ! $break_inserted && 4 === $row_count ) : ?>
                    <?php $break_inserted = true; ?>
                    <section class="journal-break" aria-label="<?php esc_attr_e( 'Case study bridge', 'tannic-studio' ); ?>">
                        <div class="journal-break-inner">
                            <p class="journal-break-eyebrow mono"><?php esc_html_e( 'Related Work', 'tannic-studio' ); ?></p>
                            <h2 class="journal-break-title"><?php esc_html_e( 'Prefer seeing the thinking in a shipped project?', 'tannic-studio' ); ?></h2>
                            <p class="journal-break-copy"><?php esc_html_e( 'The Journal explains the thinking. The case studies show what happens when it gets built.', 'tannic-studio' ); ?></p>
                            <a class="btn btn-outline" href="<?php echo esc_url( home_url( '/work/' ) ); ?>"><?php esc_html_e( 'Browse Case Studies', 'tannic-studio' ); ?></a>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endwhile; ?>
        </section>

        <?php
        $pagination = paginate_links( array(
            'current'   => $paged,
            'total'     => max( 1, (int) $wp_query->max_num_pages ),
            'type'      => 'list',
            'prev_text' => esc_html__( 'Newer Posts', 'tannic-studio' ),
            'next_text' => esc_html__( 'Older Posts', 'tannic-studio' ),
        ) );
        if ( $pagination ) :
        ?>
            <nav class="journal-pagination" aria-label="<?php esc_attr_e( 'Topic pagination', 'tannic-studio' ); ?>">
                <?php echo wp_kses_post( $pagination ); ?>
            </nav>
        <?php endif; ?>
    <?php else : ?>
        <section class="journal-empty">
            <p><?php esc_html_e( 'No posts have been filed in this topic yet.', 'tannic-studio' ); ?></p>
        </section>
    <?php endif; ?>

    <section class="journal-cta">
        <p class="journal-cta-eyebrow mono"><?php esc_html_e( 'Keep Reading', 'tannic-studio' ); ?></p>
        <h2 class="journal-cta-title"><?php esc_html_e( 'Want the rest of the notes, not just this shelf?', 'tannic-studio' ); ?></h2>
        <a class="btn btn-outline" href="<?php echo esc_url( $journal_url ); ?>">
            <?php esc_html_e( 'View All Articles', 'tannic-studio' ); ?>
        </a>
    </section>
</div>

<?php
wp_reset_postdata();
get_footer();
