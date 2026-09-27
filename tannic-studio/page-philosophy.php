<?php
/**
 * Template Name: Code & Cabernet
 *
 * The philosophy / about page — extracted from the original homepage.
 * Displays Kate Kight's bio alongside a featured project accordion gallery.
 *
 * @package TannicStudio
 */

get_header();

/* ===== About ACF fields ===== */
$about_desk_label          = get_field( 'about_desk_label' ) ?: 'From the Desk of Kate Kight';
$about_headline            = get_field( 'about_headline' ) ?: 'Code &';
$about_headline_highlight  = get_field( 'about_headline_highlight' ) ?: 'Cabernet.';
$about_lead                = get_field( 'about_lead' ) ?: 'Tannic Studio exists at the intersection of rigorous technical architecture and the organic joy of a great meal.';
$about_bio                 = get_field( 'about_bio' );
$about_sign_name           = get_field( 'about_sign_name' ) ?: 'Kate Kight';
$about_sign_role           = get_field( 'about_sign_role' ) ?: 'Founder / Lead Dev';

$featured_ids = get_posts( array(
    'post_type'      => 'project',
    'posts_per_page' => 3,
    'fields'         => 'ids',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => array(
        'relation' => 'OR',
        array(
            'key'     => 'project_featured_homepage',
            'value'   => '1',
            'compare' => '=',
        ),
        array(
            'key'     => 'featured_on_homepage',
            'value'   => '1',
            'compare' => '=',
        ),
    ),
) );

if ( ! empty( $featured_ids ) ) {
    $accordion_projects = new WP_Query( array(
        'post_type'      => 'project',
        'post__in'       => $featured_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => 3,
    ) );
} else {
    $accordion_projects = new WP_Query( array(
        'post_type'      => 'project',
        'posts_per_page' => 3,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );
}
?>

<section id="about" class="about-section" aria-label="<?php esc_attr_e( 'About the studio', 'tannic-studio' ); ?>">

    <!-- Left: Bio Anchor -->
    <aside class="about-bio">
        <div class="about-bio-inner">
            <p class="about-meta"><?php echo esc_html( $about_desk_label ); ?></p>

            <h2 class="about-headline">
                <?php echo esc_html( $about_headline ); ?> <br>
                <span class="highlight"><?php echo esc_html( $about_headline_highlight ); ?></span>
            </h2>

            <div class="about-body">
                <p class="about-lead"><?php echo esc_html( $about_lead ); ?></p>
                <?php if ( $about_bio ) : ?>
                    <?php echo wp_kses_post( $about_bio ); ?>
                <?php endif; ?>
            </div>

            <div class="about-signature">
                <span class="about-sign-name"><?php echo esc_html( $about_sign_name ); ?></span>
                <span class="about-sign-role"><?php echo esc_html( $about_sign_role ); ?></span>
            </div>
        </div>
    </aside>

    <!-- Right: Gallery Accordion -->
    <div class="gallery-accordion-wrap">
        <span class="accordion-side-label mono" aria-hidden="true"><?php esc_html_e( 'View Our Work', 'tannic-studio' ); ?></span>
        <p class="accordion-section-label mono"><?php esc_html_e( 'Featured Work', 'tannic-studio' ); ?></p>
        <div class="gallery-accordion" role="list" aria-label="<?php esc_attr_e( 'Featured projects', 'tannic-studio' ); ?>">
        <?php if ( $accordion_projects->have_posts() ) :
            $strip_index = 0;
            while ( $accordion_projects->have_posts() ) : $accordion_projects->the_post();
                $strip_index++;
                $project_image = get_the_post_thumbnail_url( get_the_ID(), 'tannic-accordion' );
                if ( ! $project_image ) {
                    $project_image_arr = get_field( 'project_gallery_1' );
                    if ( is_array( $project_image_arr ) && ! empty( $project_image_arr['url'] ) ) {
                        $project_image = $project_image_arr['url'];
                    }
                }
                $project_badge = get_field( 'project_tech_badge' );
                $project_desc  = get_the_excerpt();
                $project_link  = get_permalink();
                $project_title = get_the_title();
        ?>
            <article class="accordion-strip" role="listitem" tabindex="0"
               aria-label="<?php echo esc_attr( $project_title ); ?>">

                <div class="strip-bg">
                    <?php if ( $project_image ) : ?>
                        <img src="<?php echo esc_url( $project_image ); ?>"
                             alt="<?php echo esc_attr( $project_title ); ?>"
                             loading="lazy">
                    <?php endif; ?>
                    <div class="strip-overlay"></div>
                </div>

                <div class="strip-label-closed">
                    <span class="strip-num"><?php echo esc_html( str_pad( $strip_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
                    <span class="strip-v-text"><?php echo esc_html( $project_title ); ?></span>
                    <span class="strip-v-cta"><?php esc_html_e( 'View Work', 'tannic-studio' ); ?></span>
                </div>

                <div class="strip-content">
                    <div class="strip-content-header">
                        <?php if ( $project_badge ) : ?>
                            <span class="code-badge"><?php echo esc_html( $project_badge ); ?></span>
                        <?php endif; ?>
                        <h3 class="strip-title"><?php the_title(); ?></h3>
                    </div>
                    <?php if ( $project_desc ) : ?>
                        <p class="strip-desc"><?php echo esc_html( $project_desc ); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( $project_link ); ?>" class="strip-cta-link">
                        <?php esc_html_e( 'View Work Sample', 'tannic-studio' ); ?> &rarr;
                    </a>
                </div>
            </article>
        <?php
            endwhile;
            wp_reset_postdata();
        else :
            /* Fallback static strips when no projects exist */
        ?>
            <div class="accordion-strip" role="listitem" tabindex="0" aria-label="Global Context">
                <div class="strip-bg">
                    <img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&q=80" alt="Swiss Alps" loading="lazy">
                    <div class="strip-overlay"></div>
                </div>
                <div class="strip-label-closed">
                    <span class="strip-num">01</span>
                    <span class="strip-v-text">Global_Context</span>
                    <span class="strip-v-cta"><?php esc_html_e( 'View Work', 'tannic-studio' ); ?></span>
                </div>
                <div class="strip-content">
                    <div class="strip-content-header">
                        <span class="code-badge">loc: zurich_04</span>
                        <h3 class="strip-title">The Art of Arrival</h3>
                    </div>
                    <p class="strip-desc">I study how luxury hotels handle the first 5 minutes of a guest's stay. I translate that friction-less welcome into your homepage load sequence.</p>
                    <a href="<?php echo esc_url( home_url( '/work/' ) ); ?>" class="strip-cta-link">
                        <?php esc_html_e( 'View Work Sample', 'tannic-studio' ); ?> &rarr;
                    </a>
                </div>
            </div>

            <div class="accordion-strip" role="listitem" tabindex="0" aria-label="Sensory Input">
                <div class="strip-bg">
                    <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80" alt="Cocktail" loading="lazy">
                    <div class="strip-overlay"></div>
                </div>
                <div class="strip-label-closed">
                    <span class="strip-num">02</span>
                    <span class="strip-v-text">Sensory_Input</span>
                    <span class="strip-v-cta"><?php esc_html_e( 'View Work', 'tannic-studio' ); ?></span>
                </div>
                <div class="strip-content">
                    <div class="strip-content-header">
                        <span class="code-badge">input: organic</span>
                        <h3 class="strip-title">Unfiltered Design</h3>
                    </div>
                    <p class="strip-desc">Most websites are over-processed. Like a natural wine, I prefer design that retains its texture, grit, and authentic character.</p>
                    <a href="<?php echo esc_url( home_url( '/work/' ) ); ?>" class="strip-cta-link">
                        <?php esc_html_e( 'View Work Sample', 'tannic-studio' ); ?> &rarr;
                    </a>
                </div>
            </div>

            <div class="accordion-strip" role="listitem" tabindex="0" aria-label="Source Code">
                <div class="strip-bg">
                    <img src="https://images.unsplash.com/photo-1485230405346-71acb9518d9c?auto=format&fit=crop&q=80" alt="Portrait" loading="lazy">
                    <div class="strip-overlay"></div>
                </div>
                <div class="strip-label-closed">
                    <span class="strip-num">03</span>
                    <span class="strip-v-text">Source_Code</span>
                    <span class="strip-v-cta"><?php esc_html_e( 'View Work', 'tannic-studio' ); ?></span>
                </div>
                <div class="strip-content">
                    <div class="strip-content-header">
                        <span class="code-badge">user: kate_k</span>
                        <h3 class="strip-title">The Architect</h3>
                    </div>
                    <p class="strip-desc">A developer who speaks Sommelier. I bridge the gap between your creative vision and the raw code required to execute it.</p>
                    <a href="<?php echo esc_url( home_url( '/work/' ) ); ?>" class="strip-cta-link">
                        <?php esc_html_e( 'View Work Sample', 'tannic-studio' ); ?> &rarr;
                    </a>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>

</section>

<?php get_footer(); ?>
