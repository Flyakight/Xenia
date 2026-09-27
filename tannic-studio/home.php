<?php
/**
 * Journal index template.
 *
 * @package TannicStudio
 */

get_header();

$paged           = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$journal_url     = tannic_get_posts_page_url();
$posts_page_id   = (int) get_option( 'page_for_posts' );
$journal_title   = $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Journal', 'tannic-studio' );
$journal_intro   = __( 'Notes on taste, strategy, and digital presence for brands that want their site to feel considered before it says a word.', 'tannic-studio' );
$all_topics      = get_categories( array( 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC' ) );
$topic_names     = wp_list_pluck( array_slice( $all_topics, 0, 3 ), 'name' );
$posts_count     = (int) wp_count_posts( 'post' )->publish;
$latest_post     = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1 ) );
$latest_month    = ! empty( $latest_post ) ? get_the_date( 'F Y', $latest_post[0] ) : '';
$featured_post   = null;
$featured_post_id = $paged === 1 ? tannic_get_journal_featured_post_id() : 0;

if ( $featured_post_id ) {
    $featured_post = get_post( $featured_post_id );
}
?>

<div class="journal-page">
    <section class="journal-hero">
        <div class="journal-hero-inner">
            <div class="journal-hero-copy">
                <p class="journal-eyebrow mono"><?php echo esc_html( $journal_title ); ?></p>
                <h1 class="journal-title"><?php esc_html_e( 'Notes on taste, strategy, and digital presence.', 'tannic-studio' ); ?></h1>
                <p class="journal-intro"><?php echo esc_html( $journal_intro ); ?></p>
            </div>
            <aside class="journal-signal-card" aria-label="<?php esc_attr_e( 'Journal overview', 'tannic-studio' ); ?>">
                <span class="journal-signal-label mono"><?php esc_html_e( 'Field Notes', 'tannic-studio' ); ?></span>
                <ul class="journal-signal-list">
                    <li>
                        <?php
                        printf(
                            /* translators: %s: number of published posts */
                            esc_html__( '%s essays published', 'tannic-studio' ),
                            esc_html( number_format_i18n( $posts_count ) )
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
                    <?php if ( ! empty( $topic_names ) ) : ?>
                        <li><?php echo esc_html( implode( ' / ', $topic_names ) ); ?></li>
                    <?php endif; ?>
                </ul>
            </aside>
        </div>
    </section>

    <nav class="journal-topics" aria-label="<?php esc_attr_e( 'Journal topics', 'tannic-studio' ); ?>">
        <a class="journal-topic is-active" href="<?php echo esc_url( $journal_url ); ?>">
            <?php esc_html_e( 'All', 'tannic-studio' ); ?>
        </a>
        <?php foreach ( $all_topics as $topic ) : ?>
            <a class="journal-topic" href="<?php echo esc_url( get_category_link( $topic ) ); ?>">
                <?php echo esc_html( $topic->name ); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if ( $featured_post ) : ?>
        <?php
        $featured_category = tannic_get_post_primary_category( $featured_post->ID );
        $featured_image    = get_the_post_thumbnail_url( $featured_post, 'tannic-editorial' );
        $featured_excerpt  = has_excerpt( $featured_post ) ? $featured_post->post_excerpt : wp_trim_words( wp_strip_all_tags( $featured_post->post_content ), 28 );
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
                    <?php if ( $featured_category ) : ?>
                        <p class="journal-feature-kicker mono"><?php echo esc_html( $featured_category->name ); ?></p>
                    <?php endif; ?>
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
        <section class="journal-archive" aria-label="<?php esc_attr_e( 'Recent articles', 'tannic-studio' ); ?>">
            <?php
            $row_count      = 0;
            $break_inserted = false;

            while ( have_posts() ) :
                the_post();

                if ( $featured_post_id && get_the_ID() === $featured_post_id ) {
                    continue;
                }

                $row_count++;
                $primary_category = tannic_get_post_primary_category();
                ?>
                <article class="journal-row" data-reveal>
                    <a class="journal-row-link" href="<?php the_permalink(); ?>">
                        <div class="journal-row-date mono"><?php echo esc_html( get_the_date( 'm.d.Y' ) ); ?></div>
                        <div class="journal-row-main">
                            <?php if ( $primary_category ) : ?>
                                <p class="journal-row-category mono"><?php echo esc_html( $primary_category->name ); ?></p>
                            <?php endif; ?>
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
                    <section class="journal-break" aria-label="<?php esc_attr_e( 'Newsletter invitation', 'tannic-studio' ); ?>">
                        <div class="journal-break-inner">
                            <p class="journal-break-eyebrow mono"><?php esc_html_e( 'Stay Close', 'tannic-studio' ); ?></p>
                            <h2 class="journal-break-title"><?php esc_html_e( 'Occasional notes on branding, websites, and what actually moves people.', 'tannic-studio' ); ?></h2>
                            <p class="journal-break-copy"><?php esc_html_e( 'When there is something worth saying, it lands in the footer form below. No weekly filler.', 'tannic-studio' ); ?></p>
                            <a class="btn btn-outline" href="#footer-newsletter"><?php esc_html_e( 'Join the list', 'tannic-studio' ); ?></a>
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
            <nav class="journal-pagination" aria-label="<?php esc_attr_e( 'Journal pagination', 'tannic-studio' ); ?>">
                <?php echo wp_kses_post( $pagination ); ?>
            </nav>
        <?php endif; ?>
    <?php else : ?>
        <section class="journal-empty">
            <p><?php esc_html_e( 'The journal is empty for now. The first post can land as soon as you are ready.', 'tannic-studio' ); ?></p>
        </section>
    <?php endif; ?>

    <section class="journal-cta">
        <p class="journal-cta-eyebrow mono"><?php esc_html_e( 'Start Something', 'tannic-studio' ); ?></p>
        <h2 class="journal-cta-title"><?php esc_html_e( 'Reading something that sounds like your brand?', 'tannic-studio' ); ?></h2>
        <a class="btn btn-outline" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
            <?php esc_html_e( 'Start a Project', 'tannic-studio' ); ?>
        </a>
    </section>
</div>

<?php
wp_reset_postdata();
get_footer();
