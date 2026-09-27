<?php
/**
 * Template Name: Who Held the Glass
 *
 * An interactive timeline of which wine countries dominated the global
 * market, by hemisphere, with cited historical events. Data lives in
 * data/who-held-the-glass.json; behaviour in js/who-held-the-glass.js.
 *
 * Illustrations are optional and load from assets/glass/ when present:
 * vine, mushroom and morel (.png, .webp, .jpg or .svg).
 *
 * @package TannicStudio
 */

get_header();

$vine     = tannic_glass_art( 'vine' );
$mushroom = tannic_glass_art( 'mushroom' );
$morel    = tannic_glass_art( 'morel' );
?>

<svg class="whg-defs" width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
        <filter id="whg-goo" x="-50%" y="-50%" width="200%" height="200%" color-interpolation-filters="sRGB">
            <feGaussianBlur in="SourceGraphic" stdDeviation="6" result="blur"/>
            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 22 -9" result="goo"/>
            <feComposite in="SourceGraphic" in2="goo" operator="atop"/>
        </filter>
        <filter id="whg-rough" x="-10%" y="-10%" width="120%" height="120%">
            <feTurbulence type="fractalNoise" baseFrequency="0.035" numOctaves="2" seed="7"/>
            <feDisplacementMap in="SourceGraphic" scale="2.4"/>
        </filter>
    </defs>
</svg>

<section class="whg" aria-labelledby="whg-title">

    <div class="whg-atmosphere" aria-hidden="true">
        <?php if ( $vine ) : ?>
            <img class="whg-vine whg-vine--left" src="<?php echo esc_url( $vine ); ?>" alt="" loading="eager" decoding="async">
            <img class="whg-vine whg-vine--right" src="<?php echo esc_url( $vine ); ?>" alt="" loading="lazy" decoding="async">
        <?php endif; ?>
    </div>

    <div class="whg-inner">

        <header class="whg-hero">
            <p class="whg-eyebrow mono"><?php esc_html_e( 'A Tannic field study · 600 BC to today', 'tannic-studio' ); ?></p>
            <h1 id="whg-title" class="whg-title">
                <span class="whg-title-main"><?php esc_html_e( 'Who held', 'tannic-studio' ); ?></span>
                <span class="whg-title-script script"><?php esc_html_e( 'the glass', 'tannic-studio' ); ?></span>
            </h1>
            <p class="whg-lede"><?php esc_html_e( 'For two thousand years, the center of the wine world has kept moving: Rome, Bordeaux, Porto, Napa, the Barossa. Follow each country\'s rise and fall against the events that shaped it, from wars and plagues to trade treaties and one very useful copper spray.', 'tannic-studio' ); ?></p>
        </header>

        <section class="whg-board" id="whg-board" aria-labelledby="whg-board-title">
            <div class="whg-board-head">
                <h2 id="whg-board-title" class="whg-board-title"><?php esc_html_e( 'Rise and fall, by hemisphere', 'tannic-studio' ); ?></h2>

                <div class="whg-toggle" role="group" aria-label="<?php esc_attr_e( 'Hemisphere', 'tannic-studio' ); ?>">
                    <span class="whg-toggle-goo" aria-hidden="true">
                        <span class="whg-toggle-blob"></span>
                        <span class="whg-toggle-blob whg-toggle-blob--trail"></span>
                    </span>
                    <button type="button" class="whg-toggle-btn" data-hemi="N" aria-pressed="true"><?php esc_html_e( 'Northern', 'tannic-studio' ); ?></button>
                    <button type="button" class="whg-toggle-btn" data-hemi="S" aria-pressed="false"><?php esc_html_e( 'Southern', 'tannic-studio' ); ?></button>
                </div>
            </div>

            <div class="whg-legend-row">
                <div class="whg-legend" id="whg-legend" role="group" aria-label="<?php esc_attr_e( 'Spotlight a country', 'tannic-studio' ); ?>"></div>
                <button type="button" class="whg-cite whg-cite--method" id="whg-method" aria-expanded="false" aria-controls="whg-pop">
                    <span class="whg-cite-dot" aria-hidden="true">i</span>
                    <span><?php esc_html_e( 'How we scored this', 'tannic-studio' ); ?></span>
                </button>
            </div>

            <div class="whg-chart" id="whg-chart"></div>

            <div class="whg-detail" id="whg-detail">
                <svg class="whg-meter" viewBox="0 0 120 200" aria-hidden="true" focusable="false">
                    <defs>
                        <clipPath id="whg-bowl">
                            <path d="M29,17 C25,70 37,101 60,104 C83,101 95,70 91,17 Z"/>
                        </clipPath>
                    </defs>
                    <g clip-path="url(#whg-bowl)">
                        <g class="whg-meter-liquid">
                            <path class="whg-meter-wave" d="M-120,0 q15,-7 30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 t30,0 V140 H-120 Z"/>
                        </g>
                    </g>
                    <g class="whg-meter-line" filter="url(#whg-rough)" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M28,16 C24,70 36,102 60,105 C84,102 96,70 92,16"/>
                        <path d="M28,16 C42,11 78,11 92,16 C78,20 42,20 28,16"/>
                        <path d="M60,105 C59,130 61,152 60,174"/>
                        <path d="M32,180 C46,171 74,171 88,180 C74,185 46,185 32,180"/>
                    </g>
                </svg>
                <div class="whg-detail-body" aria-live="polite">
                    <p class="whg-detail-count mono" id="whg-count"></p>
                    <p class="whg-detail-year" id="whg-year"></p>
                    <h3 class="whg-detail-title" id="whg-event-title"></h3>
                    <p class="whg-detail-text"><span id="whg-text"></span> <span class="whg-cites" id="whg-cites"></span></p>
                </div>
            </div>

            <div class="whg-nav">
                <button type="button" class="whg-pill" id="whg-prev" aria-label="<?php esc_attr_e( 'Previous event', 'tannic-studio' ); ?>"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Earlier', 'tannic-studio' ); ?></button>
                <button type="button" class="whg-pill" id="whg-next" aria-label="<?php esc_attr_e( 'Next event', 'tannic-studio' ); ?>"><?php esc_html_e( 'Later', 'tannic-studio' ); ?> <span aria-hidden="true">&rarr;</span></button>
            </div>

            <div class="whg-pop" id="whg-pop" role="dialog" aria-modal="false" aria-labelledby="whg-pop-title" hidden>
                <div class="whg-pop-head">
                    <p class="whg-pop-title mono" id="whg-pop-title"></p>
                    <button type="button" class="whg-pop-close" id="whg-pop-close" aria-label="<?php esc_attr_e( 'Close sources', 'tannic-studio' ); ?>">&times;</button>
                </div>
                <div class="whg-pop-body" id="whg-pop-body"></div>
            </div>
        </section>

    </div>
</section>

<section class="whg-cellar" aria-labelledby="whg-cellar-title">
    <?php if ( $mushroom ) : ?>
        <img class="whg-specimen whg-specimen--mushroom" src="<?php echo esc_url( $mushroom ); ?>" alt="" loading="lazy" decoding="async" aria-hidden="true">
    <?php endif; ?>
    <?php if ( $morel ) : ?>
        <img class="whg-specimen whg-specimen--morel" src="<?php echo esc_url( $morel ); ?>" alt="" loading="lazy" decoding="async" aria-hidden="true">
    <?php endif; ?>

    <div class="whg-cellar-inner">
        <p class="whg-eyebrow mono"><?php esc_html_e( 'Every fact, poured out', 'tannic-studio' ); ?></p>
        <h2 id="whg-cellar-title" class="whg-cellar-title">
            <?php esc_html_e( 'The cellar', 'tannic-studio' ); ?> <span class="script"><?php esc_html_e( 'book', 'tannic-studio' ); ?></span>
        </h2>

        <div class="whg-notes" id="whg-notes"></div>

        <div class="whg-biblio" id="whg-biblio"></div>
    </div>
</section>

<?php
get_footer();
