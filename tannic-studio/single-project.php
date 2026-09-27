<?php
/**
 * Single Project — Immersive Case Study
 *
 * Full-bleed hero, cinematic content sections, exhibition gallery,
 * and destination-preview navigation.
 *
 * @package TannicStudio
 */

get_header();

while ( have_posts() ) : the_post();
    $client     = get_field( 'project_client' );
    $year       = get_field( 'project_year' );
    $tech_stack = get_field( 'project_tech_stack' );
    $tech_badge = get_field( 'project_tech_badge' );
    $url        = get_field( 'project_url' );
    $challenge  = get_field( 'project_challenge' );
    $solution   = get_field( 'project_solution' );
    $results    = get_field( 'project_results' );
    $challenge_media = tannic_get_chapter_media( 'challenge' );
    $solution_media  = tannic_get_chapter_media( 'solution' );
    $results_media   = tannic_get_chapter_media( 'results' );
    $gallery_1  = get_field( 'project_gallery_1' );
    $gallery_2  = get_field( 'project_gallery_2' );
    $gallery_3  = get_field( 'project_gallery_3' );
    $gallery_4  = get_field( 'project_gallery_4' );
    $gallery_images = array_filter( array( $gallery_1, $gallery_2, $gallery_3, $gallery_4 ) );
?>

<article class="case-study">

    <!-- ============================================================
         1. IMMERSIVE HERO — full-bleed image with glass overlay
         ============================================================ -->
    <header class="case-hero">
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="case-hero-img">
                <?php the_post_thumbnail( 'full', array( 'loading' => 'eager', 'data-parallax' => '0.08' ) ); ?>
            </div>
        <?php endif; ?>

        <div class="case-hero-gradient"></div>

        <!-- Floating tech terminal -->
        <?php if ( $tech_stack ) : ?>
            <div class="case-terminal" aria-hidden="true">
                <div class="case-terminal-inner">
                    <div class="terminal-dots">
                        <span class="terminal-dot"></span>
                        <span class="terminal-dot"></span>
                        <span class="terminal-dot"></span>
                    </div>
                    <div class="terminal-code">
<span style="color:var(--col-gold)">project</span>.config = {
  client: <span style="color:var(--col-accent)">"<?php echo esc_html( $client ?: get_the_title() ); ?>"</span>,
  year:   <span style="color:var(--col-accent)">"<?php echo esc_html( $year ?: '2025' ); ?>"</span>,
  stack:  [<span style="color:var(--col-accent)">"<?php echo esc_html( str_replace( ', ', '", "', $tech_stack ) ); ?>"</span>],
  status: <span style="color:var(--col-gold)">deployed</span>
};</div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hero text content -->
        <div class="case-hero-content">
            <?php if ( $client || $year ) : ?>
                <p class="case-label mono">
                    <?php
                    $label_parts = array_filter( array( $client, $year ) );
                    echo esc_html( implode( ' — ', $label_parts ) );
                    ?>
                </p>
            <?php endif; ?>

            <h1 class="case-hero-title"><?php the_title(); ?></h1>

            <?php if ( has_excerpt() ) : ?>
                <p class="case-hero-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <?php endif; ?>

            <div class="case-hero-meta">
                <?php if ( $tech_badge ) : ?>
                    <span class="code-badge"><?php echo esc_html( $tech_badge ); ?></span>
                <?php endif; ?>
                <?php if ( $url ) : ?>
                    <a href="<?php echo esc_url( $url ); ?>" class="btn btn-outline" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Visit Live Site', 'tannic-studio' ); ?> &rarr;
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="case-hero-scroll" aria-hidden="true">
            <span class="mono"><?php esc_html_e( 'Scroll', 'tannic-studio' ); ?></span>
            <div class="case-hero-scroll-line"></div>
        </div>
    </header>


    <!-- ============================================================
         2. PROJECT SPECS — dark horizontal strip
         ============================================================ -->
    <section class="case-specs" data-reveal>
        <div class="case-specs-inner">
            <?php if ( $client ) : ?>
                <div class="case-spec">
                    <span class="case-spec-label mono"><?php esc_html_e( 'Client', 'tannic-studio' ); ?></span>
                    <span class="case-spec-value"><?php echo esc_html( $client ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( $year ) : ?>
                <div class="case-spec">
                    <span class="case-spec-label mono"><?php esc_html_e( 'Year', 'tannic-studio' ); ?></span>
                    <span class="case-spec-value"><?php echo esc_html( $year ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( $tech_stack ) : ?>
                <div class="case-spec">
                    <span class="case-spec-label mono"><?php esc_html_e( 'Stack', 'tannic-studio' ); ?></span>
                    <span class="case-spec-value"><?php echo esc_html( $tech_stack ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( $url ) : ?>
                <div class="case-spec">
                    <span class="case-spec-label mono"><?php esc_html_e( 'URL', 'tannic-studio' ); ?></span>
                    <a href="<?php echo esc_url( $url ); ?>" class="case-spec-value case-spec-link" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( wp_parse_url( $url, PHP_URL_HOST ) ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>


    <!-- ============================================================
         3. THE CHALLENGE — dark cinematic section
         ============================================================ -->
    <?php if ( $challenge ) : ?>
        <section class="case-chapter case-chapter--dark" data-reveal>
            <div class="case-chapter-inner">
                <div class="case-chapter-label">
                    <span class="case-chapter-number mono">01</span>
                    <span class="case-chapter-tag mono"><?php esc_html_e( 'The Challenge', 'tannic-studio' ); ?></span>
                </div>
                <div class="case-chapter-content">
                    <h2 class="case-chapter-title"><?php esc_html_e( 'The Challenge', 'tannic-studio' ); ?></h2>
                    <div class="case-chapter-body"><?php echo wp_kses_post( $challenge ); ?></div>
                    <?php tannic_render_chapter_media( $challenge_media ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>


    <!-- ============================================================
         4. GALLERY — full-bleed exhibition
         ============================================================ -->
    <?php if ( ! empty( $gallery_images ) ) : ?>
        <section class="case-exhibition" aria-label="<?php esc_attr_e( 'Project gallery', 'tannic-studio' ); ?>">
            <div class="case-exhibition-grid">
                <?php $gal_index = 0;
                foreach ( $gallery_images as $gimg ) :
                    $gal_index++;
                ?>
                    <figure class="case-exhibition-item case-exhibition-item--<?php echo esc_attr( $gal_index ); ?>" data-reveal>
                        <img src="<?php echo esc_url( $gimg['url'] ); ?>"
                             alt="<?php echo esc_attr( $gimg['alt'] ?: get_the_title() . ' — detail ' . $gal_index ); ?>"
                             loading="lazy"
                             data-parallax="0.1">
                    </figure>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>


    <!-- ============================================================
         5. THE SOLUTION — light editorial section
         ============================================================ -->
    <?php if ( $solution ) : ?>
        <section class="case-chapter case-chapter--light" data-reveal>
            <div class="case-chapter-inner">
                <div class="case-chapter-label">
                    <span class="case-chapter-number mono">02</span>
                    <span class="case-chapter-tag mono"><?php esc_html_e( 'The Solution', 'tannic-studio' ); ?></span>
                </div>
                <div class="case-chapter-content">
                    <h2 class="case-chapter-title"><?php esc_html_e( 'The Solution', 'tannic-studio' ); ?></h2>
                    <div class="case-chapter-body"><?php echo wp_kses_post( $solution ); ?></div>
                    <?php tannic_render_chapter_media( $solution_media ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>


    <!-- ============================================================
         6. THE RESULTS — dark cinematic section
         ============================================================ -->
    <?php if ( $results ) : ?>
        <section class="case-chapter case-chapter--dark" data-reveal>
            <div class="case-chapter-inner">
                <div class="case-chapter-label">
                    <span class="case-chapter-number mono">03</span>
                    <span class="case-chapter-tag mono"><?php esc_html_e( 'The Results', 'tannic-studio' ); ?></span>
                </div>
                <div class="case-chapter-content">
                    <h2 class="case-chapter-title"><?php esc_html_e( 'The Results', 'tannic-studio' ); ?></h2>
                    <div class="case-chapter-body"><?php echo wp_kses_post( $results ); ?></div>
                    <?php tannic_render_chapter_media( $results_media ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>


    <!-- ============================================================
         7. NEXT PROJECT — destination preview nav
         ============================================================ -->
    <?php
    $next = get_next_post();
    $prev = get_previous_post();
    // If no next, loop back to first; if no prev, loop to last
    if ( ! $next ) {
        $all = get_posts( array( 'post_type' => 'project', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'ASC' ) );
        $next = ! empty( $all ) ? $all[0] : null;
    }
    if ( ! $prev ) {
        $all = get_posts( array( 'post_type' => 'project', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC' ) );
        $prev = ! empty( $all ) ? $all[0] : null;
    }
    // Don't link to self
    if ( $next && $next->ID === get_the_ID() ) $next = null;
    if ( $prev && $prev->ID === get_the_ID() ) $prev = null;
    ?>

    <?php if ( $next ) :
        $next_thumb = get_the_post_thumbnail_url( $next->ID, 'tannic-editorial' );
        $next_client = get_field( 'project_client', $next->ID );
    ?>
        <nav class="case-next" aria-label="<?php esc_attr_e( 'Next project', 'tannic-studio' ); ?>">
            <?php if ( $next_thumb ) : ?>
                <div class="case-next-bg" style="background-image: url('<?php echo esc_url( $next_thumb ); ?>')"></div>
            <?php endif; ?>
            <div class="case-next-overlay"></div>
            <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="case-next-content">
                <span class="case-next-label mono"><?php esc_html_e( 'Next Project', 'tannic-studio' ); ?></span>
                <span class="case-next-title"><?php echo esc_html( $next->post_title ); ?></span>
                <?php if ( $next_client ) : ?>
                    <span class="case-next-client"><?php echo esc_html( $next_client ); ?></span>
                <?php endif; ?>
                <span class="btn btn-outline"><?php esc_html_e( 'View Case Study', 'tannic-studio' ); ?> &rarr;</span>
            </a>
        </nav>
    <?php endif; ?>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
