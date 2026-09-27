<?php
/**
 * Template Name: Service Ledger
 *
 * The Ledger Configurator — services page with pricing calculator
 *
 * @package TannicStudio
 */

get_header();

$services = new WP_Query( array(
    'post_type'      => 'service',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
) );
?>

<?php
$services_intro = get_field( 'services_intro' );
$services_hero_image = get_field( 'services_hero_image' );
?>

<section class="services-hero" aria-label="<?php esc_attr_e( 'Services overview', 'tannic-studio' ); ?>">
    <div class="services-hero-inner">
        <div class="services-hero-content">
            <p class="services-hero-label mono"><?php esc_html_e( 'How We Work', 'tannic-studio' ); ?></p>
            <h1 class="services-hero-headline">
                <?php esc_html_e( 'The', 'tannic-studio' ); ?> <span class="highlight"><?php esc_html_e( 'Menu.', 'tannic-studio' ); ?></span>
            </h1>
            <?php if ( $services_intro ) : ?>
                <div class="services-hero-body"><?php echo wp_kses_post( $services_intro ); ?></div>
            <?php else : ?>
                <p class="services-hero-body"><?php esc_html_e( 'Every project starts with a conversation. Browse our service tiers below, select what fits your needs, and we\'ll build a custom proposal around your goals, timeline, and budget.', 'tannic-studio' ); ?></p>
            <?php endif; ?>
            <a href="#ledger-section" class="btn btn-outline services-hero-cta">
                <?php esc_html_e( 'Browse Services', 'tannic-studio' ); ?> &darr;
            </a>
        </div>
        <?php if ( $services_hero_image ) : ?>
            <div class="services-hero-image">
                <img src="<?php echo esc_url( $services_hero_image['url'] ); ?>"
                     alt="<?php echo esc_attr( $services_hero_image['alt'] ?: 'Tannic Studio Services' ); ?>"
                     loading="eager">
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="ledger-section" class="ledger-section">
    <div class="ledger-container">

        <!-- ====== LEFT: LEDGER COLUMN (70%) ====== -->
        <div class="ledger-column" role="list" aria-label="<?php esc_attr_e( 'Service offerings', 'tannic-studio' ); ?>">

            <div class="ledger-header-section">
                <p class="ledger-header-meta">Configuration Protocol</p>
                <h1 class="ledger-headline">Select Your <br><span class="highlight">Scope.</span></h1>
            </div>

            <div class="ledger-list">
                <?php if ( $services->have_posts() ) :
                    $index = 0;
                    while ( $services->have_posts() ) : $services->the_post();
                        $index++;
                        $cat_number          = get_field( 'service_category_number' ) ?: str_pad( $index, 2, '0', STR_PAD_LEFT ) . '.';
                        $price               = get_field( 'service_price' );
                        $price_label         = get_field( 'service_price_label' ) ?: ( $price ? 'From $' . number_format( $price / 1000 ) . 'k' : '' );
                        $weeks               = get_field( 'service_weeks' );
                        $strategic_heading   = get_field( 'service_strategic_heading' ) ?: 'Strategic Intent';
                        $description         = get_field( 'service_description' );
                        $specs               = get_field( 'service_specs' );
                        $deliverables_heading = get_field( 'service_deliverables_heading' ) ?: 'Deliverables';
                        $context_image       = get_field( 'service_context_image' );
                        $gallery_1           = get_field( 'service_gallery_1' );
                        $gallery_2           = get_field( 'service_gallery_2' );
                        $gallery_3           = get_field( 'service_gallery_3' );
                        $post_id             = get_the_ID();
                ?>

                    <article class="ledger-item" role="listitem"
                             data-service-id="<?php echo esc_attr( $post_id ); ?>"
                             data-service-title="<?php echo esc_attr( get_the_title() ); ?>"
                             data-service-price="<?php echo esc_attr( $price ); ?>"
                             data-service-weeks="<?php echo esc_attr( $weeks ); ?>">

                        <!-- Ledger Row (always visible) -->
                        <div class="ledger-row">
                            <div class="ledger-info">
                                <span class="ledger-cat"><?php echo esc_html( $cat_number ); ?></span>
                                <h2 class="ledger-title" id="ledger-title-<?php echo esc_attr( $post_id ); ?>">
                                    <?php the_title(); ?>
                                </h2>
                                <button class="ledger-expand"
                                        aria-expanded="false"
                                        aria-controls="ledger-body-<?php echo esc_attr( $post_id ); ?>">
                                    View Attributes <span class="ledger-expand-icon" aria-hidden="true">+</span>
                                </button>
                            </div>

                            <div class="ledger-meta">
                                <?php if ( $weeks ) : ?>
                                    <span class="ledger-meta-val"><?php echo esc_html( $weeks ); ?> Weeks</span>
                                <?php endif; ?>
                                <?php if ( $price_label ) : ?>
                                    <span class="ledger-meta-val"><?php echo esc_html( $price_label ); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="ledger-action">
                                <label class="wine-checkbox-wrapper">
                                    <input type="checkbox"
                                           class="add-checkbox"
                                           name="service_<?php echo esc_attr( $post_id ); ?>"
                                           value="1"
                                           aria-label="<?php printf( esc_attr__( 'Add %s to your allocation', 'tannic-studio' ), get_the_title() ); ?>">
                                    <div class="vintage-check">
                                        <div class="liquid-fill"></div>
                                        <svg class="wine-checkmark" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M5 12l5 5l10 -10" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <span class="wine-action-label">Add</span>
                                </label>
                            </div>
                        </div>

                        <!-- Accordion Body (frosted dashboard) -->
                        <div class="ledger-body" id="ledger-body-<?php echo esc_attr( $post_id ); ?>"
                             role="region"
                             aria-labelledby="ledger-title-<?php echo esc_attr( $post_id ); ?>"
                             hidden>

                            <!-- Context background image -->
                            <?php if ( $context_image ) : ?>
                                <div class="context-bg">
                                    <img src="<?php echo esc_url( $context_image['url'] ); ?>"
                                         alt=""
                                         loading="lazy">
                                    <div class="context-bg-overlay"></div>
                                </div>
                            <?php endif; ?>

                            <!-- Glass cards -->
                            <div class="glass-container">
                                <div class="glass-card text-card">
                                    <h4 class="glass-head"><?php echo esc_html( $strategic_heading ); ?></h4>
                                    <?php if ( $description ) : ?>
                                        <p class="glass-desc"><?php echo esc_html( $description ); ?></p>
                                    <?php endif; ?>
                                </div>

                                <?php
                                $deliverable_lines = array();
                                if ( $specs ) {
                                    $lines = explode( "\n", $specs );
                                    foreach ( $lines as $line ) {
                                        $line = trim( $line );
                                        if ( $line ) {
                                            $deliverable_lines[] = $line;
                                        }
                                    }
                                }

                                if ( ! empty( $deliverable_lines ) ) :
                                ?>
                                    <div class="glass-card text-card deliverables-card">
                                        <h4 class="glass-head"><?php echo esc_html( $deliverables_heading ); ?></h4>
                                        <ul class="glass-specs">
                                            <?php foreach ( $deliverable_lines as $line ) : ?>
                                                <li><?php echo esc_html( $line ); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </article>

                <?php
                    endwhile;
                    wp_reset_postdata();

                else :
                    /* ===== Fallback static services ===== */
                ?>

                    <article class="ledger-item" role="listitem"
                             data-service-id="1"
                             data-service-title="Brand Distillation"
                             data-service-price="5000"
                             data-service-weeks="4">
                        <div class="ledger-row">
                            <div class="ledger-info">
                                <span class="ledger-cat">01. Identity</span>
                                <h2 class="ledger-title" id="ledger-title-1">Brand Distillation</h2>
                                <button class="ledger-expand" aria-expanded="false" aria-controls="ledger-body-1">
                                    View Attributes <span class="ledger-expand-icon" aria-hidden="true">+</span>
                                </button>
                            </div>
                            <div class="ledger-meta">
                                <span class="ledger-meta-val">4 Weeks</span>
                                <span class="ledger-meta-val">From $5k</span>
                            </div>
                            <div class="ledger-action">
                                <label class="wine-checkbox-wrapper">
                                    <input type="checkbox" class="add-checkbox" value="1" aria-label="Add Brand Distillation">
                                    <div class="vintage-check">
                                        <div class="liquid-fill"></div>
                                        <svg class="wine-checkmark" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12l5 5l10 -10" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    </div>
                                    <span class="wine-action-label">Add</span>
                                </label>
                            </div>
                        </div>
                        <div class="ledger-body" id="ledger-body-1" role="region" aria-labelledby="ledger-title-1" hidden>
                            <div class="context-bg">
                                <img src="https://images.unsplash.com/photo-1600607686527-6fb886090705?auto=format&fit=crop&q=80" alt="" loading="lazy">
                                <div class="context-bg-overlay"></div>
                            </div>
                            <div class="glass-container">
                                <div class="glass-card text-card">
                                    <h4 class="glass-head">Strategic Intent</h4>
                                    <p class="glass-desc">We strip away the noise to find your brand's frequency. This isn't just a logo; it's a visual language that dictates how your guests feel before they even arrive.</p>
                                    <ul class="glass-specs">
                                        <li>+ Logo Suite (SVG/PNG)</li>
                                        <li>+ Color Theory &amp; Palette</li>
                                    </ul>
                                </div>
                                <div class="glass-card visual-card">
                                    <h4 class="glass-head">Deliverables</h4>
                                    <div class="mini-gallery">
                                        <div class="mini-img"><img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&q=80" alt="Swatch" loading="lazy"></div>
                                        <div class="mini-img"><img src="https://images.unsplash.com/photo-1634152962476-4b8a00e1915c?auto=format&fit=crop&q=80" alt="Typography" loading="lazy"></div>
                                        <div class="mini-img"><img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&q=80" alt="Mockup" loading="lazy"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="ledger-item" role="listitem"
                             data-service-id="2"
                             data-service-title="Flagship Web Build"
                             data-service-price="12000"
                             data-service-weeks="8">
                        <div class="ledger-row">
                            <div class="ledger-info">
                                <span class="ledger-cat">02. Infrastructure</span>
                                <h2 class="ledger-title" id="ledger-title-2">Flagship Web Build</h2>
                                <button class="ledger-expand" aria-expanded="false" aria-controls="ledger-body-2">
                                    View Attributes <span class="ledger-expand-icon" aria-hidden="true">+</span>
                                </button>
                            </div>
                            <div class="ledger-meta">
                                <span class="ledger-meta-val">8 Weeks</span>
                                <span class="ledger-meta-val">From $12k</span>
                            </div>
                            <div class="ledger-action">
                                <label class="wine-checkbox-wrapper">
                                    <input type="checkbox" class="add-checkbox" value="1" aria-label="Add Flagship Web Build">
                                    <div class="vintage-check">
                                        <div class="liquid-fill"></div>
                                        <svg class="wine-checkmark" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12l5 5l10 -10" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    </div>
                                    <span class="wine-action-label">Add</span>
                                </label>
                            </div>
                        </div>
                        <div class="ledger-body" id="ledger-body-2" role="region" aria-labelledby="ledger-title-2" hidden>
                            <div class="context-bg">
                                <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&q=80" alt="" loading="lazy">
                                <div class="context-bg-overlay"></div>
                            </div>
                            <div class="glass-container">
                                <div class="glass-card text-card">
                                    <h4 class="glass-head">Technical Architecture</h4>
                                    <p class="glass-desc">A cinematic, headless website. We decouple the frontend from the CMS to ensure 100/100 Lighthouse scores and seamless motion.</p>
                                    <ul class="glass-specs">
                                        <li>+ Next.js Framework</li>
                                        <li>+ Sanity/Shopify CMS</li>
                                    </ul>
                                </div>
                                <div class="glass-card visual-card">
                                    <h4 class="glass-head">Components</h4>
                                    <div class="mini-gallery">
                                        <div class="mini-img"><img src="https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?auto=format&fit=crop&q=80" alt="Code" loading="lazy"></div>
                                        <div class="mini-img"><img src="https://images.unsplash.com/photo-1555421689-d68471e189f2?auto=format&fit=crop&q=80" alt="UI" loading="lazy"></div>
                                        <div class="mini-img"><img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80" alt="Data" loading="lazy"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                <?php endif; ?>
            </div>
        </div>

        <!-- ====== RIGHT: MANIFEST SIDEBAR (30%, sticky) ====== -->
        <aside class="manifest-sidebar" aria-label="<?php esc_attr_e( 'Selected services manifest', 'tannic-studio' ); ?>">
            <div class="manifest-container">
                <div class="manifest-header">
                    <span class="manifest-label">ESTIMATE_LOG</span>
                    <span class="status-dot" aria-hidden="true"></span>
                </div>

                <div class="manifest-items" aria-live="polite">
                    <p class="manifest-empty">Initialize configuration...</p>
                </div>

                <div class="manifest-totals">
                    <div class="manifest-total-row">
                        <span>Timeline</span>
                        <span class="manifest-total-weeks mono"><?php esc_html_e( '0 Weeks', 'tannic-studio' ); ?></span>
                    </div>
                    <div class="manifest-total-row large">
                        <span>Investment</span>
                        <span class="manifest-total-price mono">$0</span>
                    </div>
                </div>

                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-accent manifest-cta">
                    Request Proposal &rarr;
                </a>
            </div>
        </aside>

    </div>
</section>

<?php get_footer(); ?>
