<?php
/**
 * Front Page — Hero + Portfolio Slider + Process + Services + CTA
 *
 * @package TannicStudio
 */

get_header();

/* ===== Hero ACF fields ===== */
$hero_headline    = get_field( 'hero_headline' ) ?: 'Tannic';
$hero_subtitle    = get_field( 'hero_subtitle' ) ?: 'studio';
$hero_tagline     = get_field( 'hero_tagline' ) ?: 'Built for taste-driven brands.';
$hero_cta_text    = get_field( 'hero_cta_text' ) ?: 'Work with us';
$hero_cta_link    = get_field( 'hero_cta_link' ) ?: '#';
$hero_image       = get_field( 'hero_image' );
$hero_typing_text = get_field( 'hero_typing_text' ) ?: "building.digital_identity(elevation: 100%);\n\n// Retaining loyal customers...\nstatus: active;\nmode: artisanal;";
?>


<!-- ====================================================================
     SECTION 1: CODE PANEL HERO
     ==================================================================== -->

<section class="hero" aria-label="<?php esc_attr_e( 'Introduction', 'tannic-studio' ); ?>">

    <!-- Floating terminal card — upper right -->
    <div class="hero-terminal hero-terminal--top" aria-hidden="true">
        <div class="hero-terminal-inner">
            <div class="terminal-dots">
                <span class="terminal-dot"></span>
                <span class="terminal-dot"></span>
                <span class="terminal-dot"></span>
            </div>
            <div class="terminal-code" data-typing-text="<?php echo esc_attr( $hero_typing_text ); ?>">
                <span id="typing-box"></span>
            </div>
        </div>
    </div>

    <!-- Floating terminal card — bottom left -->
    <div class="hero-terminal hero-terminal--bottom" aria-label="<?php esc_attr_e( 'Brand tagline', 'tannic-studio' ); ?>">
        <div class="hero-terminal-inner">
            <div class="terminal-dots">
                <span class="terminal-dot"></span>
                <span class="terminal-dot"></span>
                <span class="terminal-dot"></span>
            </div>
            <div class="terminal-code terminal-code--tagline">
                <p class="terminal-tagline"><?php echo esc_html( $hero_tagline ); ?></p>
                <p class="terminal-tagline-sub mono">&lt;strategy + design + development /&gt;</p>
            </div>
        </div>
    </div>

    <div class="hero-container">

        <!-- Left: Headline + CTA -->
        <div class="hero-content">
            <div class="hero-headline-wrap">
                <h1 class="hero-headline">
                    <span class="hero-headline-main hero-fill-word hero-fill-word--main" data-hero-fill-word><?php echo esc_html( $hero_headline ); ?></span>
                    <span class="hero-headline-script script hero-fill-word hero-fill-word--script" data-hero-fill-word><?php echo esc_html( $hero_subtitle ); ?></span>
                </h1>
                <?php if ( $hero_tagline ) : ?>
                    <p class="hero-mobile-value"><?php echo esc_html( $hero_tagline ); ?></p>
                <?php endif; ?>
                <p class="hero-mobile-code mono">&lt;strategy + design + development /&gt;</p>
            </div>

            <div class="hero-cta">
                <a href="<?php echo esc_url( $hero_cta_link ); ?>" class="btn btn-outline">
                    <?php echo esc_html( $hero_cta_text ); ?>
                </a>
            </div>
        </div>

        <!-- Right: Portrait -->
        <div class="hero-portrait">
            <?php if ( $hero_image ) : ?>
                <div class="hero-portrait-img">
                    <img src="<?php echo esc_url( $hero_image['url'] ); ?>"
                         alt="<?php echo esc_attr( $hero_image['alt'] ?: 'Tannic Studio' ); ?>"
                         width="<?php echo esc_attr( $hero_image['width'] ); ?>"
                         height="<?php echo esc_attr( $hero_image['height'] ); ?>"
                         loading="eager">
                </div>
                <div class="hero-portrait-glow" aria-hidden="true"></div>
            <?php else : ?>
                <div class="hero-portrait-img">
                    <img src="https://res.cloudinary.com/dncbf9zy4/image/upload/v1770064442/image_86_lbsdrl.png"
                         alt="Luxury Hospitality"
                         loading="eager">
                </div>
                <div class="hero-portrait-glow" aria-hidden="true"></div>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ====================================================================
     SECTION 2: EDITORIAL CASE STUDIES
     ==================================================================== -->

<?php
$editorial_projects = new WP_Query( array(
    'post_type'      => 'project',
    'posts_per_page' => 6,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        array(
            'key'     => '_thumbnail_id',
            'compare' => 'EXISTS',
        ),
    ),
) );
?>

<?php if ( $editorial_projects->have_posts() ) : ?>
<section id="work" class="editorial-section" aria-label="<?php esc_attr_e( 'Selected work', 'tannic-studio' ); ?>">

    <div class="editorial-header">
        <p class="editorial-label mono"><?php esc_html_e( 'Case Studies', 'tannic-studio' ); ?></p>
        <h2 class="editorial-headline"><?php esc_html_e( 'Selected', 'tannic-studio' ); ?> <span class="editorial-headline-accent"><?php esc_html_e( 'Work.', 'tannic-studio' ); ?></span></h2>
    </div>

    <?php $card_index = 0;
    while ( $editorial_projects->have_posts() ) : $editorial_projects->the_post();
        $card_index++;
        $thumb_url  = get_the_post_thumbnail_url( get_the_ID(), 'tannic-editorial' );
        $tech_badge = get_field( 'project_tech_badge' );
        $tech_stack = get_field( 'project_tech_stack' );
        $year       = get_field( 'project_year' );
        $client     = get_field( 'project_client' );
    ?>
        <article class="editorial-card" data-reveal>
            <div class="editorial-card-image">
                <?php if ( $thumb_url ) : ?>
                    <img src="<?php echo esc_url( $thumb_url ); ?>"
                         alt="<?php echo esc_attr( get_the_title() ); ?>"
                         loading="lazy"
                         data-parallax="0.12">
                <?php endif; ?>
            </div>
            <div class="editorial-card-content">
                <span class="editorial-card-number"><?php echo esc_html( str_pad( $card_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
                <?php if ( $tech_badge ) : ?>
                    <span class="code-badge"><?php echo esc_html( $tech_badge ); ?></span>
                <?php endif; ?>
                <h3 class="editorial-card-title"><?php the_title(); ?></h3>
                <?php if ( has_excerpt() ) : ?>
                    <p class="editorial-card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>
                <div class="editorial-card-meta">
                    <?php if ( $tech_stack ) : ?>
                        <span class="hud-tech"><?php echo esc_html( $tech_stack ); ?></span>
                    <?php endif; ?>
                    <?php if ( $year ) : ?>
                        <span class="hud-year"><?php echo esc_html( $year ); ?></span>
                    <?php endif; ?>
                    <?php if ( $client ) : ?>
                        <span class="hud-tech"><?php echo esc_html( $client ); ?></span>
                    <?php endif; ?>
                </div>
                <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                    <?php esc_html_e( 'View Case Study', 'tannic-studio' ); ?>
                </a>
            </div>
        </article>
    <?php endwhile; wp_reset_postdata(); ?>

    <div class="editorial-footer">
        <a href="<?php echo esc_url( home_url( '/work/' ) ); ?>" class="btn btn-outline">
            <?php esc_html_e( 'View all recent work', 'tannic-studio' ); ?>
        </a>
    </div>

</section>
<?php endif; ?>


<!-- ====================================================================
     SECTION 3: SPLIT-LAYOUT PROCESS
     ==================================================================== -->

<?php
/* Process step ACF fields */
$process_steps = array();
for ( $i = 1; $i <= 5; $i++ ) {
    $process_steps[] = array(
        'number' => get_field( 'process_step_' . $i . '_number' ) ?: str_pad( $i, 2, '0', STR_PAD_LEFT ),
        'title'  => get_field( 'process_step_' . $i . '_title' ) ?: '',
        'body'   => get_field( 'process_step_' . $i . '_body' ) ?: '',
        'image'  => get_field( 'process_step_' . $i . '_image' ),
    );
}

/* Fallback defaults if ACF fields are empty */
$default_steps = array(
    array( 'number' => '01', 'title' => 'Learn the Terroir', 'body' => 'Like making a great wine, we learn the terroir first. Where are you growing? Who are you serving? What are the constraints that will bring your brand to life? What audience do we not want, so we can make sure we are bringing in only the people who will appreciate every depth of note of your brand?' ),
    array( 'number' => '02', 'title' => 'Get to Work', 'body' => "We turn over every stone to find ways to make your brand sing. You won\xe2\x80\x99t find repurposed templates or old ideas here \xe2\x80\x94 we are breaking molds and setting the world on fire." ),
    array( 'number' => '03', 'title' => 'Build', 'body' => "With meticulous precision, we ensure you understand what\xe2\x80\x99s going into your bottle \xe2\x80\x94 or your site \xe2\x80\x94 every step of the way. Because gatekeeping is old world, and in this new world we believe that every idea deserves sunshine. So we\xe2\x80\x99ll skip the technical details if you want, but we aren\xe2\x80\x99t here to make ourselves feel smarter than you \xe2\x80\x94 we\xe2\x80\x99re here to build something that is already yours \xe2\x80\x94 so you deserve to understand every piece of code that goes into it." ),
    array( 'number' => '04', 'title' => 'Ship & Iterate', 'body' => "Our humble vineyard (ok, our lil agency started in Baltimore, MD) got its start building nonprofit dashboards for Google Grants, so we understand the power and pleasure of analytics. We don\xe2\x80\x99t stop working when we hit publish \xe2\x80\x94 that\xe2\x80\x99s just the first press. The real work begins when we see how our work interacts with your audience." ),
    array( 'number' => '05', 'title' => 'Sip & Grow', 'body' => "We sit back and sip to see how you\xe2\x80\x99ll grow \xe2\x80\x94 because we know how powerful a digital presence can be in building a loyal fanbase who continues to come home to you." ),
);

foreach ( $process_steps as $idx => &$step ) {
    if ( empty( $step['title'] ) && isset( $default_steps[ $idx ] ) ) {
        $step = $default_steps[ $idx ];
    }
}
unset( $step );
?>

<section id="process" class="process-section" aria-label="<?php esc_attr_e( 'Our process', 'tannic-studio' ); ?>">

    <!-- Left: Sticky Sidebar -->
    <aside class="process-sidebar">
        <div class="process-sidebar-header">
            <div class="process-brand">TANNIC<span class="text-accent">.</span><span class="text-accent">STUDIO</span></div>
            <div class="process-tagline"><?php esc_html_e( 'Est. 2026 / Baltimore, MD', 'tannic-studio' ); ?></div>
        </div>

        <nav class="process-nav" aria-label="<?php esc_attr_e( 'Process steps', 'tannic-studio' ); ?>">
            <ul>
                <?php foreach ( $process_steps as $idx => $step ) : ?>
                    <li>
                        <a href="#process-step-<?php echo esc_attr( $idx + 1 ); ?>"<?php echo $idx === 0 ? ' class="active"' : ''; ?>>
                            <?php echo esc_html( $step['number'] . '. ' . $step['title'] ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="process-sidebar-footer">
            <div class="process-tech-spec">
                <div class="process-code-snippet">
                    <code>current_pour: <span class="text-gold">'Cabernet'</span>;</code><br>
                    <code>mood: <span class="text-accent">refined</span>;</code>
                </div>
            </div>
            <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-outline">
                <?php esc_html_e( 'Inquire', 'tannic-studio' ); ?>
            </a>
        </div>
    </aside>

    <!-- Right: Scrollable Content Stream -->
    <div class="process-stream">

        <!-- Intro Panel -->
        <div class="process-panel process-intro-panel" id="process-intro">
            <h2 class="process-headline">
                <?php esc_html_e( 'Our', 'tannic-studio' ); ?> <br>
                <span class="process-headline-accent"><?php esc_html_e( 'Process.', 'tannic-studio' ); ?></span>
            </h2>
            <p class="process-lead">
                <?php esc_html_e( 'We blend full-stack technical precision with the organic, sensory experience of building something that truly belongs to you.', 'tannic-studio' ); ?>
            </p>
            <div class="process-scroll-hint" aria-hidden="true">&darr; <?php esc_html_e( 'Scroll for tasting', 'tannic-studio' ); ?></div>
        </div>

        <!-- Step Panels -->
        <?php foreach ( $process_steps as $idx => $step ) :
            $has_image = ! empty( $step['image'] );
        ?>
            <div class="process-panel process-step-panel<?php echo $has_image ? ' has-image' : ''; ?>" id="process-step-<?php echo esc_attr( $idx + 1 ); ?>">
                <div class="process-step-text">
                    <div class="process-step-number"><?php echo esc_html( $step['number'] ); ?></div>
                    <h3 class="process-step-title"><?php echo esc_html( $step['title'] ); ?></h3>
                    <p class="process-step-body"><?php echo esc_html( $step['body'] ); ?></p>
                </div>
                <?php if ( $has_image ) : ?>
                    <div class="process-step-image">
                        <img src="<?php echo esc_url( $step['image']['url'] ); ?>"
                             alt="<?php echo esc_attr( $step['image']['alt'] ?: $step['title'] ); ?>"
                             loading="lazy">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>

</section>


<!-- ====================================================================
     SECTION 4: SERVICES SHOWCASE + CONTACT CTA
     ==================================================================== -->

<?php
$home_services = new WP_Query( array(
    'post_type'      => 'service',
    'posts_per_page' => 6,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
) );
?>

<?php if ( $home_services->have_posts() ) : ?>
<section id="services" class="home-services" aria-label="<?php esc_attr_e( 'Our services', 'tannic-studio' ); ?>">
    <div class="home-services-container">
        <p class="home-services-label"><?php esc_html_e( 'What We Pour', 'tannic-studio' ); ?></p>
        <h2 class="home-services-headline"><?php esc_html_e( 'Services', 'tannic-studio' ); ?></h2>
        <p class="home-services-intro">
            <?php esc_html_e( 'A motion-driven ledger of what we build, how each engagement unfolds, and what ships at the end.', 'tannic-studio' ); ?>
        </p>

        <div class="home-services-ledger" data-service-ledger>
            <div class="home-services-nav" aria-label="<?php esc_attr_e( 'Services carousel controls', 'tannic-studio' ); ?>">
                <button type="button" class="home-services-nav-btn home-services-nav-btn--prev" data-service-prev>
                    <span aria-hidden="true">&larr;</span>
                    <span><?php esc_html_e( 'Previous', 'tannic-studio' ); ?></span>
                </button>
                <button type="button" class="home-services-nav-btn home-services-nav-btn--next" data-service-next>
                    <span><?php esc_html_e( 'Next', 'tannic-studio' ); ?></span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </div>

            <div class="home-services-track" data-service-track>
            <?php
            $service_index = 0;
            while ( $home_services->have_posts() ) : $home_services->the_post();
                $service_index++;
                $cat_number  = get_field( 'service_category_number' );
                $price_label = get_field( 'service_price_label' );
                $description = get_field( 'service_description' );
                $weeks       = get_field( 'service_weeks' );
                $specs       = get_field( 'service_specs' );
                $spec_lines  = preg_split( '/\r\n|\r|\n/', (string) $specs );
                $spec_lines  = array_filter( array_map( 'trim', $spec_lines ) );
                $deliverable_lines = array_slice( array_values( $spec_lines ), 0, 3 );
                $service_title = get_the_title();
                $services_url = home_url( '/services/#ledger-section' );
            ?>
                <article class="home-service-ledger-item<?php echo 1 === $service_index ? ' is-active' : ''; ?>"
                         data-service-ledger-item
                         data-reveal
                         tabindex="0"
                         aria-label="<?php echo esc_attr( $service_title ); ?>">
                    <div class="home-service-ledger-head">
                        <?php if ( $cat_number ) : ?>
                            <span class="home-service-cat"><?php echo esc_html( $cat_number ); ?></span>
                        <?php endif; ?>
                        <?php if ( $weeks ) : ?>
                            <span class="home-service-timeline"><?php echo esc_html( $weeks ); ?> <?php esc_html_e( 'weeks', 'tannic-studio' ); ?></span>
                        <?php endif; ?>
                    </div>

                    <h3 class="home-service-title"><?php echo esc_html( $service_title ); ?></h3>

                    <?php if ( $description ) : ?>
                        <p class="home-service-desc"><?php echo esc_html( wp_trim_words( $description, 28 ) ); ?></p>
                    <?php endif; ?>

                    <?php if ( ! empty( $deliverable_lines ) ) : ?>
                        <ul class="home-service-deliverables" aria-label="<?php esc_attr_e( 'Service deliverables', 'tannic-studio' ); ?>">
                            <?php foreach ( $deliverable_lines as $line ) : ?>
                                <li><?php echo esc_html( ltrim( $line, "+-• \t" ) ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="home-service-ledger-foot">
                        <?php if ( $price_label ) : ?>
                            <span class="home-service-price"><?php echo esc_html( $price_label ); ?></span>
                        <?php endif; ?>
                        <a class="home-service-link" href="<?php echo esc_url( $services_url ); ?>">
                            <?php esc_html_e( 'Explore service', 'tannic-studio' ); ?> &rarr;
                        </a>
                    </div>

                    <div class="home-service-progress" aria-hidden="true">
                        <span class="home-service-progress-bar"></span>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <div class="home-services-ledger-hint mono" aria-hidden="true">
                <?php esc_html_e( 'Scroll or drag to explore the ledger', 'tannic-studio' ); ?>
            </div>
        </div>

        <div class="home-services-cta">
            <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" class="btn btn-outline">
                <?php esc_html_e( 'View Full Menu', 'tannic-studio' ); ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact CTA -->
<section class="home-cta" aria-label="<?php esc_attr_e( 'Get in touch', 'tannic-studio' ); ?>">
    <div class="home-cta-inner">
        <h2 class="home-cta-headline">
            <?php esc_html_e( 'Ready to uncork something', 'tannic-studio' ); ?> <br>
            <span class="home-cta-accent"><?php esc_html_e( 'remarkable?', 'tannic-studio' ); ?></span>
        </h2>
        <p class="home-cta-sub">
            <?php esc_html_e( 'Every great label starts with a conversation. Let\'s talk about what you\'re building.', 'tannic-studio' ); ?>
        </p>
        <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-accent">
            <?php esc_html_e( 'Start a Conversation', 'tannic-studio' ); ?>
        </a>
    </div>
</section>


<?php get_footer(); ?>
