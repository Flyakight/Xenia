<?php
/**
 * Footer — Allocation Footer
 *
 * @package TannicStudio
 */
?>

</main><!-- #main-content -->

<footer class="site-footer" role="contentinfo">

    <!-- Brand + Newsletter (side-by-side) -->
    <div class="footer-top">
        <div class="footer-brand">
            <span class="footer-brand-mark">Tannic<span class="accent">.</span></span>
            <p class="footer-brand-tagline">
                <?php echo esc_html( get_theme_mod( 'tannic_footer_tagline', 'Digital experiences, carefully decanted.' ) ); ?>
            </p>
        </div>

        <section id="footer-newsletter" class="footer-newsletter" aria-label="<?php esc_attr_e( 'Newsletter signup', 'tannic-studio' ); ?>">
            <h2><?php echo esc_html( get_theme_mod( 'tannic_footer_newsletter_heading', 'Stay in the loop' ) ); ?></h2>
            <?php
            $newsletter_shortcode = get_theme_mod( 'tannic_footer_newsletter_shortcode', '' );
            if ( $newsletter_shortcode ) {
                echo do_shortcode( wp_kses_post( $newsletter_shortcode ) );
            }
            ?>
        </section>
    </div>

    <!-- Navigation Columns -->
    <div class="footer-columns">
        <div class="footer-grid container">

            <!-- Navigate -->
            <div class="footer-col">
                <h3><?php esc_html_e( 'Navigate', 'tannic-studio' ); ?></h3>
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'footer-menu',
                    'container'      => false,
                    'menu_class'     => '',
                    'fallback_cb'    => false,
                    'depth'          => 1,
                ) );
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'tannic-studio' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Services', 'tannic-studio' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'tannic-studio' ); ?></a>
            </div>

            <!-- Connect -->
            <div class="footer-col">
                <h3><?php esc_html_e( 'Connect', 'tannic-studio' ); ?></h3>
                <?php
                $social_platforms = array( 'instagram', 'linkedin', 'github', 'dribbble' );
                foreach ( $social_platforms as $platform ) :
                    $url = get_theme_mod( "tannic_social_{$platform}", '' );
                    if ( $url ) :
                ?>
                    <a href="<?php echo esc_url( $url ); ?>"
                       target="_blank"
                       rel="noopener noreferrer">
                        <?php echo esc_html( ucfirst( $platform ) ); ?>
                    </a>
                <?php
                    endif;
                endforeach;
                ?>
            </div>

            <!-- Contact -->
            <div class="footer-col">
                <h3><?php esc_html_e( 'Contact', 'tannic-studio' ); ?></h3>
                <?php
                $contact_email    = get_theme_mod( 'tannic_contact_email', 'hello@tannic.studio' );
                $contact_phone    = get_theme_mod( 'tannic_contact_phone', '' );
                $contact_location = get_theme_mod( 'tannic_contact_location', '' );
                ?>
                <?php if ( $contact_email ) : ?>
                    <a href="mailto:<?php echo esc_attr( $contact_email ); ?>">
                        <?php echo esc_html( $contact_email ); ?>
                    </a>
                <?php endif; ?>
                <?php if ( $contact_phone ) : ?>
                    <p><?php echo esc_html( $contact_phone ); ?></p>
                <?php endif; ?>
                <?php if ( $contact_location ) : ?>
                    <p><?php echo esc_html( $contact_location ); ?></p>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Legal / Credits -->
    <div class="footer-legal">
        <div class="footer-legal-inner container">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</p>
            <span id="local-time" aria-label="<?php esc_attr_e( 'Current local time', 'tannic-studio' ); ?>"></span>
        </div>
    </div>

</footer>

<?php
$human_booking_link  = get_theme_mod( 'tannic_human_booking_link', '' );
$human_booking_label = get_theme_mod( 'tannic_human_booking_label', 'No bot. Talk to Kate.' );
if ( $human_booking_link ) :
?>
<a class="human-cta-sticky"
   href="<?php echo esc_url( $human_booking_link ); ?>"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="<?php echo esc_attr( $human_booking_label ); ?>">
    <span class="human-cta-dot" aria-hidden="true"></span>
    <span class="human-cta-text"><?php echo esc_html( $human_booking_label ); ?></span>
</a>
<?php endif; ?>

<!-- Cursor Follower (decorative) -->
<div class="cursor-follower" aria-hidden="true"></div>

<?php wp_footer(); ?>
</body>
</html>
