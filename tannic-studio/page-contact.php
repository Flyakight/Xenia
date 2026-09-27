<?php
/**
 * Template Name: Reservation Desk
 *
 * Contact page — background scene with floating intake form.
 *
 * @package TannicStudio
 */

get_header();

$contact_heading      = get_field( 'contact_heading' ) ?: 'Let\'s make something remarkable.';
$contact_subheading   = get_field( 'contact_subheading' ) ?: 'Tell us what you are building, who you are, and what kind of project support you need.';
$contact_background   = get_field( 'contact_background_image' );
$contact_person_photo = get_field( 'contact_person_photo' );
$contact_person_name  = get_field( 'contact_person_name' ) ?: 'Kate Kight';
$contact_person_title = get_field( 'contact_person_title' ) ?: 'Founder / Lead Developer';
$contact_cf7_id       = get_field( 'contact_form_id' );
$contact_hours        = get_field( 'contact_hours' );
$contact_email        = get_theme_mod( 'tannic_contact_email', 'hello@tannic.studio' );
$contact_phone        = get_theme_mod( 'tannic_contact_phone', '' );
$contact_location     = get_theme_mod( 'tannic_contact_location', '' );

$contact_background_url = '';
if ( is_array( $contact_background ) && ! empty( $contact_background['url'] ) ) {
    $contact_background_url = $contact_background['url'];
} else {
    $contact_background_url = 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&q=80&w=2200';
}

$contact_social_platforms = array( 'instagram', 'linkedin', 'github', 'dribbble' );
$human_booking_link   = get_theme_mod( 'tannic_human_booking_link', '' );
$cal_embed_url        = get_field( 'contact_cal_embed_url' );
$booking_link         = $cal_embed_url ?: $human_booking_link;
?>

<section class="contact-section">
    <div class="contact-scene">
        <img class="contact-scene-image"
             src="<?php echo esc_url( $contact_background_url ); ?>"
             alt=""
             aria-hidden="true"
             loading="eager">
    </div>

    <div class="contact-container">
        <div class="contact-info">
            <p class="contact-label"><?php esc_html_e( 'Contact Us', 'tannic-studio' ); ?></p>
            <h1><?php echo esc_html( $contact_heading ); ?></h1>
            <p class="contact-sub"><?php echo esc_html( $contact_subheading ); ?></p>
            <?php if ( $booking_link ) : ?>
                <a class="contact-booking-link btn btn--outline"
                   href="<?php echo esc_url( $booking_link ); ?>"
                   target="_blank"
                   rel="noopener noreferrer">
                    <?php esc_html_e( 'Book a 30-minute call', 'tannic-studio' ); ?> &rarr;
                </a>
            <?php else : ?>
                <p class="contact-schedule-note"><?php esc_html_e( 'Add a booking link in Customizer or set the Cal.com URL in this page ACF to show scheduling here.', 'tannic-studio' ); ?></p>
            <?php endif; ?>

            <div class="contact-details-grid">
                <?php if ( $contact_location || $contact_hours ) : ?>
                    <div class="contact-detail-card">
                        <h3><?php esc_html_e( 'Location', 'tannic-studio' ); ?></h3>
                        <?php if ( $contact_location ) : ?>
                            <p><?php echo esc_html( $contact_location ); ?></p>
                        <?php endif; ?>
                        <?php if ( $contact_hours ) : ?>
                            <p><?php echo esc_html( $contact_hours ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="contact-detail-card">
                    <h3><?php esc_html_e( 'Social', 'tannic-studio' ); ?></h3>
                    <?php
                    $has_social = false;
                    foreach ( $contact_social_platforms as $platform ) :
                        $url = get_theme_mod( "tannic_social_{$platform}", '' );
                        if ( $url ) :
                            $has_social = true;
                    ?>
                        <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html( ucfirst( $platform ) ); ?>
                        </a>
                    <?php
                        endif;
                    endforeach;
                    if ( ! $has_social ) :
                    ?>
                        <p><?php esc_html_e( 'Add social links in Appearance > Customize > Social Links.', 'tannic-studio' ); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ( $contact_email || $contact_phone ) : ?>
                    <div class="contact-detail-card">
                        <h3><?php esc_html_e( 'Direct', 'tannic-studio' ); ?></h3>
                        <?php if ( $contact_email ) : ?>
                            <a href="mailto:<?php echo esc_attr( $contact_email ); ?>">
                                <?php echo esc_html( $contact_email ); ?>
                            </a>
                        <?php endif; ?>
                        <?php if ( $contact_phone ) : ?>
                            <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>">
                                <?php echo esc_html( $contact_phone ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="contact-detail-card contact-human-card">
                    <h3><?php esc_html_e( 'Real Human', 'tannic-studio' ); ?></h3>
                    <div class="contact-human-row">
                        <?php if ( is_array( $contact_person_photo ) && ! empty( $contact_person_photo['url'] ) ) : ?>
                            <img class="contact-human-photo"
                                 src="<?php echo esc_url( $contact_person_photo['sizes']['thumbnail'] ?? $contact_person_photo['url'] ); ?>"
                                 alt="<?php echo esc_attr( $contact_person_name ); ?>">
                        <?php endif; ?>
                        <div class="contact-human-meta">
                            <p class="contact-human-name"><?php echo esc_html( $contact_person_name ); ?></p>
                            <p class="contact-human-title"><?php echo esc_html( $contact_person_title ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form-shell">
            <h2><?php esc_html_e( 'Tell Us What You Need', 'tannic-studio' ); ?></h2>
            <p class="contact-form-intro"><?php esc_html_e( 'Share your goals and we will recommend the right path forward.', 'tannic-studio' ); ?></p>

            <div class="contact-form-wrap">
                <?php
                $brevo_form_shortcode = get_field( 'contact_brevo_form_shortcode' );
                if ( $brevo_form_shortcode ) {
                    echo do_shortcode( wp_kses_post( $brevo_form_shortcode ) );
                } elseif ( $contact_cf7_id ) {
                    if ( false !== strpos( (string) $contact_cf7_id, '[contact-form-7' ) ) {
                        echo do_shortcode( wp_kses_post( $contact_cf7_id ) );
                    } else {
                        echo do_shortcode( '[contact-form-7 id="' . esc_attr( $contact_cf7_id ) . '"]' );
                    }
                } elseif ( shortcode_exists( 'contact-form-7' ) ) {
                    $cf7_forms = get_posts( array(
                        'post_type'      => 'wpcf7_contact_form',
                        'posts_per_page' => 1,
                    ) );

                    if ( ! empty( $cf7_forms ) ) {
                        echo do_shortcode( '[contact-form-7 id="' . esc_attr( $cf7_forms[0]->ID ) . '"]' );
                    } else {
                        tannic_fallback_contact_form();
                    }
                } else {
                    tannic_fallback_contact_form();
                }
                ?>
            </div>

        </div>
    </div>
</section>

<?php
/**
 * Fallback contact form when CF7 is not installed.
 */
function tannic_fallback_contact_form() {
    ?>
    <form class="fallback-form contact-fallback-form" method="post" action="#" novalidate>
        <div class="contact-form-grid">
            <p>
                <label for="contact-name"><?php esc_html_e( 'Tell us who you are', 'tannic-studio' ); ?></label>
                <input id="contact-name" type="text" name="your-name" placeholder="Your name" required>
            </p>
            <p>
                <label for="contact-company"><?php esc_html_e( 'Company', 'tannic-studio' ); ?></label>
                <input id="contact-company" type="text" name="your-company" placeholder="Company name">
            </p>
            <p>
                <label for="contact-email"><?php esc_html_e( 'Email', 'tannic-studio' ); ?></label>
                <input id="contact-email" type="email" name="your-email" placeholder="you@company.com" required>
            </p>
            <p>
                <label for="contact-phone"><?php esc_html_e( 'Phone', 'tannic-studio' ); ?></label>
                <input id="contact-phone" type="tel" name="your-phone" placeholder="(555) 555-5555">
            </p>
        </div>

        <p>
            <label for="contact-project-type"><?php esc_html_e( 'What kind of project are you looking for?', 'tannic-studio' ); ?></label>
            <select id="contact-project-type" name="project-type" required>
                <option value=""><?php esc_html_e( 'Select a project type', 'tannic-studio' ); ?></option>
                <option value="web-design"><?php esc_html_e( 'Web Design', 'tannic-studio' ); ?></option>
                <option value="logo-branding"><?php esc_html_e( 'Logo &amp; Branding', 'tannic-studio' ); ?></option>
                <option value="web-app"><?php esc_html_e( 'Web App / Product', 'tannic-studio' ); ?></option>
                <option value="retainer"><?php esc_html_e( 'Ongoing Retainer', 'tannic-studio' ); ?></option>
                <option value="other"><?php esc_html_e( 'Something Else', 'tannic-studio' ); ?></option>
            </select>
        </p>

        <p>
            <label for="contact-message"><?php esc_html_e( 'Tell us about your project', 'tannic-studio' ); ?></label>
            <textarea id="contact-message" name="your-message" rows="6" placeholder="Goals, timeline, budget range, and anything else that helps us understand your vision." required></textarea>
        </p>

        <p class="contact-submit-row">
            <input type="submit" value="<?php esc_attr_e( 'Send a Message', 'tannic-studio' ); ?>">
        </p>
    </form>
    <?php
}

get_footer();
?>
