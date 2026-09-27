<?php
/**
 * Tannic Studio — Theme Functions
 *
 * @package TannicStudio
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
   1. THEME SUPPORT
   ========================================================================== */

function tannic_theme_support() {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    add_image_size( 'tannic-hero', 800, 1067, true );       // 3:4
    add_image_size( 'tannic-accordion', 600, 900, true );    // 2:3
    add_image_size( 'tannic-parallax', 1600, 1000, true );   // 16:10
    add_image_size( 'tannic-gallery-sm', 400, 300, true );   // 4:3
    add_image_size( 'tannic-editorial', 1200, 800, true );   // 3:2
}
add_action( 'after_setup_theme', 'tannic_theme_support' );

/* ==========================================================================
   2. NAVIGATION MENUS
   ========================================================================== */

function tannic_register_menus() {
    register_nav_menus( array(
        'main-menu'   => __( 'Main Navigation', 'tannic-studio' ),
        'footer-menu' => __( 'Footer Navigation', 'tannic-studio' ),
    ) );
}
add_action( 'init', 'tannic_register_menus' );

/* ==========================================================================
   3. ASSET ENQUEUING
   ========================================================================== */

function tannic_asset_version( $relative_path ) {
    $file_path = get_theme_file_path( $relative_path );

    if ( $file_path && file_exists( $file_path ) ) {
        return (string) filemtime( $file_path );
    }

    return wp_get_theme()->get( 'Version' );
}

function tannic_enqueue_assets() {
    // Google Fonts — single request, null version to preserve CSS2 multi-family URL
    wp_enqueue_style(
        'tannic-google-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Mrs+Saint+Delafield&family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500;600&display=swap',
        array(),
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'tannic-style',
        get_stylesheet_uri(),
        array( 'tannic-google-fonts' ),
        tannic_asset_version( 'style.css' )
    );

    // Global JS — deferred, all pages
    wp_enqueue_script(
        'tannic-main-js',
        get_template_directory_uri() . '/js/main.js',
        array(),
        tannic_asset_version( 'js/main.js' ),
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );

    wp_localize_script(
        'tannic-main-js',
        'tannicTheme',
        array(
            'footerCtaLabel' => get_theme_mod( 'tannic_footer_newsletter_button_label', 'Reach Out' ),
        )
    );

    // Ledger JS — services page only
    if ( is_page_template( 'page-services.php' ) ) {
        wp_enqueue_script(
            'tannic-ledger-js',
            get_template_directory_uri() . '/js/ledger.js',
            array(),
            tannic_asset_version( 'js/ledger.js' ),
            array( 'in_footer' => true, 'strategy' => 'defer' )
        );
    }

    // Contact JS — contact page only
    if ( is_page_template( 'page-contact.php' ) ) {
        wp_enqueue_script(
            'tannic-contact-js',
            get_template_directory_uri() . '/js/contact.js',
            array(),
            tannic_asset_version( 'js/contact.js' ),
            array( 'in_footer' => true, 'strategy' => 'defer' )
        );
    }

    // Who Held the Glass — interactive wine timeline page only
    if ( is_page_template( 'page-who-held-the-glass.php' ) ) {
        wp_enqueue_style(
            'tannic-glass-css',
            get_template_directory_uri() . '/css/who-held-the-glass.css',
            array( 'tannic-style' ),
            tannic_asset_version( 'css/who-held-the-glass.css' )
        );

        wp_enqueue_script(
            'tannic-glass-js',
            get_template_directory_uri() . '/js/who-held-the-glass.js',
            array(),
            tannic_asset_version( 'js/who-held-the-glass.js' ),
            array( 'in_footer' => true, 'strategy' => 'defer' )
        );

        $glass_data = file_get_contents( get_theme_file_path( 'data/who-held-the-glass.json' ) );
        if ( $glass_data ) {
            wp_add_inline_script( 'tannic-glass-js', 'window.tannicGlassData = ' . $glass_data . ';', 'before' );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'tannic_enqueue_assets' );

/**
 * URL of an optional Who Held the Glass illustration in assets/glass/,
 * or an empty string when the file hasn't been uploaded.
 */
function tannic_glass_art( $name ) {
    foreach ( array( 'png', 'webp', 'jpg', 'svg' ) as $ext ) {
        $relative = 'assets/glass/' . $name . '.' . $ext;
        if ( file_exists( get_theme_file_path( $relative ) ) ) {
            return get_theme_file_uri( $relative ) . '?v=' . tannic_asset_version( $relative );
        }
    }
    return '';
}

/**
 * Fallback favicon for local/dev and pre-launch states.
 *
 * Uses WP Admin Site Icon when available; otherwise outputs theme placeholder.
 */
function tannic_output_fallback_favicon() {
    if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
        return;
    }

    $favicon_svg  = get_theme_file_uri( 'assets/favicon.svg' );
    $favicon_32   = get_theme_file_uri( 'assets/favicon-32x32.png' );
    $favicon_48   = get_theme_file_uri( 'assets/favicon-48x48.png' );
    $favicon_ico  = get_theme_file_uri( 'assets/favicon.ico' );
    $apple_touch  = get_theme_file_uri( 'assets/apple-touch-icon.png' );

    echo '<link rel="icon" href="' . esc_url( $favicon_svg ) . '" type="image/svg+xml" />' . "\n";
    echo '<link rel="icon" href="' . esc_url( $favicon_32 ) . '" type="image/png" sizes="32x32" />' . "\n";
    echo '<link rel="icon" href="' . esc_url( $favicon_48 ) . '" type="image/png" sizes="48x48" />' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url( $apple_touch ) . '" sizes="180x180" />' . "\n";
    echo '<link rel="shortcut icon" href="' . esc_url( $favicon_ico ) . '" />' . "\n";
}
add_action( 'wp_head', 'tannic_output_fallback_favicon', 5 );

/* ==========================================================================
   3b. CONTENT SANITISATION HELPERS
   ========================================================================== */

/**
 * Allow iframe, video, source, and audio through wp_kses_post().
 *
 * WordPress strips these on both save and display by default.
 * This filter adds them globally so pasted embeds survive the
 * WYSIWYG round-trip (save → database → display).
 */
function tannic_allow_media_tags( $allowed, $context ) {
    if ( 'post' !== $context ) {
        return $allowed;
    }

    $allowed['iframe'] = array(
        'src'             => true,
        'width'           => true,
        'height'          => true,
        'frameborder'     => true,
        'allowfullscreen' => true,
        'allow'           => true,
        'style'           => true,
        'class'           => true,
        'id'              => true,
        'title'           => true,
        'loading'         => true,
        'sandbox'         => true,
    );

    $allowed['video'] = array(
        'src'         => true,
        'width'       => true,
        'height'      => true,
        'autoplay'    => true,
        'loop'        => true,
        'muted'       => true,
        'playsinline' => true,
        'controls'    => true,
        'preload'     => true,
        'poster'      => true,
        'class'       => true,
        'id'          => true,
        'style'       => true,
    );

    $allowed['source'] = array(
        'src'  => true,
        'type' => true,
    );

    $allowed['audio'] = array(
        'src'      => true,
        'autoplay' => true,
        'loop'     => true,
        'muted'    => true,
        'controls' => true,
        'preload'  => true,
        'class'    => true,
        'id'       => true,
    );

    return $allowed;
}
add_filter( 'wp_kses_allowed_html', 'tannic_allow_media_tags', 10, 2 );

/**
 * Collect chapter media from ACF fields into a clean array.
 *
 * @param  string $chapter  One of 'challenge', 'solution', 'results'.
 * @return array  Each item: [ 'type', 'src', 'alt', 'caption', 'file' ]
 */
function tannic_get_chapter_media( $chapter ) {
    $items = array();

    for ( $n = 1; $n <= 4; $n++ ) {
        $prefix = 'project_' . $chapter . '_media_' . $n;
        $type   = get_field( $prefix . '_type' );

        if ( ! $type ) continue;

        $item = array(
            'type'    => $type,
            'caption' => get_field( $prefix . '_caption' ),
            'src'     => '',
            'alt'     => '',
            'file'    => null,
        );

        if ( 'image' === $type ) {
            $img = get_field( $prefix . '_image' );
            if ( $img ) {
                $item['src'] = $img['url'];
                $item['alt'] = $img['alt'] ?: '';
            } else {
                continue; // skip empty slot
            }
        } elseif ( 'video' === $type ) {
            $vid = get_field( $prefix . '_video' );
            if ( $vid ) {
                $item['src']  = $vid['url'];
                $item['file'] = $vid;
            } else {
                continue;
            }
        } elseif ( 'embed' === $type ) {
            $embed = get_field( $prefix . '_embed' );
            if ( $embed ) {
                $item['src'] = $embed;
            } else {
                continue;
            }
        }

        $items[] = $item;
    }

    return $items;
}

/**
 * Render a chapter media strip.
 *
 * @param  array $media  Items from tannic_get_chapter_media().
 * @param  int   $total  Total count for nth-child grid hints.
 */
function tannic_render_chapter_media( $media ) {
    if ( empty( $media ) ) return;

    $count = count( $media );
    $index = 0;
    ?>
    <div class="chapter-media-strip" data-items="<?php echo esc_attr( $count ); ?>">
        <?php foreach ( $media as $item ) :
            $index++;
        ?>
            <figure class="chapter-media-item chapter-media-item--<?php echo esc_attr( $index ); ?> chapter-media-item--<?php echo esc_attr( $item['type'] ); ?>" data-reveal>
                <?php if ( 'image' === $item['type'] ) : ?>
                    <img src="<?php echo esc_url( $item['src'] ); ?>"
                         alt="<?php echo esc_attr( $item['alt'] ); ?>"
                         loading="lazy">
                <?php elseif ( 'video' === $item['type'] ) : ?>
                    <video autoplay loop muted playsinline preload="metadata">
                        <source src="<?php echo esc_url( $item['src'] ); ?>"
                                type="<?php echo esc_attr( $item['file']['mime_type'] ?? 'video/mp4' ); ?>">
                    </video>
                <?php elseif ( 'embed' === $item['type'] ) : ?>
                    <div class="chapter-media-embed">
                        <iframe src="<?php echo esc_url( $item['src'] ); ?>"
                                loading="lazy"
                                allowfullscreen
                                frameborder="0"></iframe>
                    </div>
                <?php endif; ?>

                <?php if ( $item['caption'] ) : ?>
                    <figcaption class="chapter-media-caption mono">
                        <?php echo esc_html( $item['caption'] ); ?>
                    </figcaption>
                <?php endif; ?>
            </figure>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Get the configured posts page URL, falling back gracefully.
 *
 * @return string
 */
function tannic_get_posts_page_url() {
    $posts_page_id = (int) get_option( 'page_for_posts' );

    if ( $posts_page_id ) {
        return get_permalink( $posts_page_id );
    }

    return home_url( '/' );
}

/**
 * Return the first assigned category for a post.
 *
 * @param int|null $post_id Post ID.
 * @return WP_Term|null
 */
function tannic_get_post_primary_category( $post_id = null ) {
    $post_id    = $post_id ?: get_the_ID();
    $categories = get_the_category( $post_id );

    if ( empty( $categories ) || is_wp_error( $categories ) ) {
        return null;
    }

    return $categories[0];
}

/**
 * Get a human-readable read time for a post.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function tannic_get_post_read_time( $post_id = null ) {
    $post_id = $post_id ?: get_the_ID();
    $minutes = 0;

    if ( function_exists( 'get_field' ) ) {
        $minutes = (int) get_field( 'post_read_time', $post_id );
    }

    if ( $minutes < 1 ) {
        $content    = get_post_field( 'post_content', $post_id );
        $word_count = str_word_count( wp_strip_all_tags( (string) $content ) );
        $minutes    = max( 1, (int) ceil( $word_count / 220 ) );
    }

    return sprintf(
        /* translators: %s: number of minutes */
        _n( '%s min read', '%s min read', $minutes, 'tannic-studio' ),
        number_format_i18n( $minutes )
    );
}

/**
 * Resolve the post ID to feature at the top of the Journal.
 *
 * @param int $category_id Optional category term ID.
 * @return int
 */
function tannic_get_journal_featured_post_id( $category_id = 0 ) {
    $base_args = array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 1,
        'ignore_sticky_posts' => false,
        'no_found_rows'       => true,
    );

    if ( $category_id ) {
        $base_args['cat'] = $category_id;
    }

    if ( function_exists( 'get_field' ) ) {
        $featured_query = new WP_Query(
            array_merge(
                $base_args,
                array(
                    'meta_key'   => 'post_featured_in_journal',
                    'meta_value' => 1,
                    'orderby'    => 'date',
                    'order'      => 'DESC',
                )
            )
        );

        if ( $featured_query->have_posts() ) {
            return (int) $featured_query->posts[0]->ID;
        }
    }

    $sticky_ids = get_option( 'sticky_posts' );
    if ( ! empty( $sticky_ids ) ) {
        $sticky_query = new WP_Query(
            array_merge(
                $base_args,
                array(
                    'post__in' => array_map( 'intval', $sticky_ids ),
                    'orderby'  => 'date',
                    'order'    => 'DESC',
                )
            )
        );

        if ( $sticky_query->have_posts() ) {
            return (int) $sticky_query->posts[0]->ID;
        }
    }

    $latest_query = new WP_Query(
        array_merge(
            $base_args,
            array(
                'orderby' => 'date',
                'order'   => 'DESC',
            )
        )
    );

    if ( $latest_query->have_posts() ) {
        return (int) $latest_query->posts[0]->ID;
    }

    return 0;
}

/* ==========================================================================
   4. CUSTOM POST TYPES
   ========================================================================== */

function tannic_register_cpt_service() {
    register_post_type( 'service', array(
        'labels' => array(
            'name'               => __( 'Services', 'tannic-studio' ),
            'singular_name'      => __( 'Service', 'tannic-studio' ),
            'add_new'            => __( 'Add New Service', 'tannic-studio' ),
            'add_new_item'       => __( 'Add New Service', 'tannic-studio' ),
            'edit_item'          => __( 'Edit Service', 'tannic-studio' ),
            'new_item'           => __( 'New Service', 'tannic-studio' ),
            'view_item'          => __( 'View Service', 'tannic-studio' ),
            'search_items'       => __( 'Search Services', 'tannic-studio' ),
            'not_found'          => __( 'No services found', 'tannic-studio' ),
            'not_found_in_trash' => __( 'No services found in trash', 'tannic-studio' ),
            'menu_name'          => __( 'Services', 'tannic-studio' ),
        ),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-clipboard',
        'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
        'has_archive'        => false,
        'rewrite'            => false,
        'menu_position'      => 20,
    ) );
}
add_action( 'init', 'tannic_register_cpt_service' );

function tannic_register_cpt_project() {
    register_post_type( 'project', array(
        'labels' => array(
            'name'               => __( 'Projects', 'tannic-studio' ),
            'singular_name'      => __( 'Project', 'tannic-studio' ),
            'add_new'            => __( 'Add New Project', 'tannic-studio' ),
            'add_new_item'       => __( 'Add New Project', 'tannic-studio' ),
            'edit_item'          => __( 'Edit Project', 'tannic-studio' ),
            'new_item'           => __( 'New Project', 'tannic-studio' ),
            'view_item'          => __( 'View Project', 'tannic-studio' ),
            'search_items'       => __( 'Search Projects', 'tannic-studio' ),
            'not_found'          => __( 'No projects found', 'tannic-studio' ),
            'not_found_in_trash' => __( 'No projects found in trash', 'tannic-studio' ),
            'menu_name'          => __( 'Projects', 'tannic-studio' ),
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'work', 'with_front' => false ),
        'menu_position'      => 21,
    ) );
}
add_action( 'init', 'tannic_register_cpt_project' );

// Flush rewrites on theme activation
function tannic_rewrite_flush() {
    tannic_register_cpt_service();
    tannic_register_cpt_project();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'tannic_rewrite_flush' );

/* ==========================================================================
   5. CUSTOMIZER SETTINGS (replaces ACF Options Page)
   ========================================================================== */

function tannic_customizer_settings( $wp_customize ) {

    /* --- Footer Section --- */
    $wp_customize->add_section( 'tannic_footer', array(
        'title'    => __( 'Footer Settings', 'tannic-studio' ),
        'priority' => 160,
    ) );

    $wp_customize->add_setting( 'tannic_footer_tagline', array(
        'default'           => 'Digital experiences, carefully decanted.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tannic_footer_tagline', array(
        'label'   => __( 'Footer Tagline', 'tannic-studio' ),
        'section' => 'tannic_footer',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'tannic_footer_newsletter_heading', array(
        'default'           => 'Stay in the loop',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tannic_footer_newsletter_heading', array(
        'label'   => __( 'Newsletter Heading', 'tannic-studio' ),
        'section' => 'tannic_footer',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'tannic_footer_newsletter_shortcode', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'tannic_footer_newsletter_shortcode', array(
        'label'       => __( 'Newsletter Form Shortcode (CF7)', 'tannic-studio' ),
        'description' => __( 'Paste your Contact Form 7 shortcode here.', 'tannic-studio' ),
        'section'     => 'tannic_footer',
        'type'        => 'textarea',
    ) );

    $wp_customize->add_setting( 'tannic_footer_newsletter_button_label', array(
        'default'           => 'Reach Out',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tannic_footer_newsletter_button_label', array(
        'label'   => __( 'Newsletter Button Label', 'tannic-studio' ),
        'section' => 'tannic_footer',
        'type'    => 'text',
    ) );

    /* --- Contact Info Section --- */
    $wp_customize->add_section( 'tannic_contact_info', array(
        'title'    => __( 'Contact Information', 'tannic-studio' ),
        'priority' => 161,
    ) );

    $wp_customize->add_setting( 'tannic_contact_email', array(
        'default'           => 'hello@tannic.studio',
        'sanitize_callback' => 'sanitize_email',
    ) );
    $wp_customize->add_control( 'tannic_contact_email', array(
        'label'   => __( 'Contact Email', 'tannic-studio' ),
        'section' => 'tannic_contact_info',
        'type'    => 'email',
    ) );

    $wp_customize->add_setting( 'tannic_contact_phone', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tannic_contact_phone', array(
        'label'   => __( 'Phone Number', 'tannic-studio' ),
        'section' => 'tannic_contact_info',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'tannic_contact_location', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tannic_contact_location', array(
        'label'   => __( 'Location / Address', 'tannic-studio' ),
        'section' => 'tannic_contact_info',
        'type'    => 'textarea',
    ) );

    $wp_customize->add_setting( 'tannic_human_booking_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'tannic_human_booking_link', array(
        'label'       => __( 'Human Booking Link', 'tannic-studio' ),
        'description' => __( 'Calendly or booking URL for the sticky "talk to a human" button.', 'tannic-studio' ),
        'section'     => 'tannic_contact_info',
        'type'        => 'url',
    ) );

    $wp_customize->add_setting( 'tannic_human_booking_label', array(
        'default'           => 'No bot. Talk to Kate.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tannic_human_booking_label', array(
        'label'   => __( 'Human Booking Button Label', 'tannic-studio' ),
        'section' => 'tannic_contact_info',
        'type'    => 'text',
    ) );

    /* --- Social Links Section --- */
    $wp_customize->add_section( 'tannic_social', array(
        'title'    => __( 'Social Links', 'tannic-studio' ),
        'priority' => 162,
    ) );

    $platforms = array( 'instagram', 'linkedin', 'github', 'dribbble' );
    foreach ( $platforms as $platform ) {
        $wp_customize->add_setting( "tannic_social_{$platform}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( "tannic_social_{$platform}", array(
            'label'   => ucfirst( $platform ) . ' URL',
            'section' => 'tannic_social',
            'type'    => 'url',
        ) );
    }
}
add_action( 'customize_register', 'tannic_customizer_settings' );

/* ==========================================================================
   6. ACF FIELD GROUPS (registered in code for version control)
   ========================================================================== */

function tannic_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    tannic_acf_hero_fields();
    tannic_acf_about_fields();
    tannic_acf_process_fields();
    tannic_acf_service_fields();
    tannic_acf_project_fields();
    tannic_acf_post_fields();
    tannic_acf_contact_fields();
    tannic_acf_services_page_fields();
}
add_action( 'acf/init', 'tannic_register_acf_fields' );

/* --- 6a. Hero Section (Front Page) --- */
function tannic_acf_hero_fields() {
    acf_add_local_field_group( array(
        'key'      => 'group_tannic_hero',
        'title'    => 'Hero Section',
        'fields'   => array(
            array(
                'key'           => 'field_hero_headline',
                'label'         => 'Headline',
                'name'          => 'hero_headline',
                'type'          => 'text',
                'default_value' => 'Tannic',
                'instructions'  => 'Main headline (displayed in Fraunces serif).',
            ),
            array(
                'key'           => 'field_hero_subtitle',
                'label'         => 'Subtitle',
                'name'          => 'hero_subtitle',
                'type'          => 'text',
                'default_value' => 'studio',
                'instructions'  => 'Script subtitle (displayed in Mrs Saint Delafield).',
            ),
            array(
                'key'          => 'field_hero_tagline',
                'label'        => 'Tagline',
                'name'         => 'hero_tagline',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => 'Short paragraph below the headline.',
            ),
            array(
                'key'           => 'field_hero_cta_text',
                'label'         => 'CTA Button Text',
                'name'          => 'hero_cta_text',
                'type'          => 'text',
                'default_value' => 'Work with us',
            ),
            array(
                'key'   => 'field_hero_cta_link',
                'label' => 'CTA Button Link',
                'name'  => 'hero_cta_link',
                'type'  => 'url',
            ),
            array(
                'key'           => 'field_hero_image',
                'label'         => 'Portrait Image',
                'name'          => 'hero_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions'  => 'Right-side portrait (3:4 aspect ratio recommended).',
            ),
            array(
                'key'           => 'field_hero_typing_text',
                'label'         => 'Typing Animation Text',
                'name'          => 'hero_typing_text',
                'type'          => 'textarea',
                'rows'          => 8,
                'instructions'  => 'Code-style text for the typing animation in the terminal card.',
                'default_value' => "building.digital_identity(elevation: 100%);\n\n// Retaining loyal customers...\nstatus: active;\nmode: artisanal;",
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'page_type',
                    'operator' => '==',
                    'value'    => 'front_page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
}

/* --- 6b. About Section (Front Page) --- */
function tannic_acf_about_fields() {
    acf_add_local_field_group( array(
        'key'      => 'group_tannic_about',
        'title'    => 'About Section',
        'fields'   => array(
            array(
                'key'           => 'field_about_desk_label',
                'label'         => 'Desk Label',
                'name'          => 'about_desk_label',
                'type'          => 'text',
                'default_value' => 'From the Desk of Kate Kight',
                'instructions'  => 'Small mono label above the headline.',
            ),
            array(
                'key'           => 'field_about_headline',
                'label'         => 'Headline',
                'name'          => 'about_headline',
                'type'          => 'text',
                'default_value' => 'Code & Cabernet.',
            ),
            array(
                'key'           => 'field_about_headline_highlight',
                'label'         => 'Headline Highlight Word',
                'name'          => 'about_headline_highlight',
                'type'          => 'text',
                'default_value' => 'Cabernet.',
                'instructions'  => 'The word to render in italic coral accent color.',
            ),
            array(
                'key'          => 'field_about_lead',
                'label'        => 'Lead Paragraph',
                'name'         => 'about_lead',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => 'Bold intro paragraph.',
            ),
            array(
                'key'          => 'field_about_bio',
                'label'        => 'Bio Content',
                'name'         => 'about_bio',
                'type'         => 'wysiwyg',
                'tabs'         => 'all',
                'toolbar'      => 'full',
                'media_upload' => 0,
                'instructions' => 'Biography/about content.',
            ),
            array(
                'key'          => 'field_about_sign_name',
                'label'        => 'Signature Name',
                'name'         => 'about_sign_name',
                'type'         => 'text',
                'default_value' => 'Kate Kight',
            ),
            array(
                'key'          => 'field_about_sign_role',
                'label'        => 'Signature Role',
                'name'         => 'about_sign_role',
                'type'         => 'text',
                'default_value' => 'Founder / Lead Dev',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'page_template',
                    'operator' => '==',
                    'value'    => 'page-philosophy.php',
                ),
            ),
        ),
        'menu_order' => 1,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
}

/* --- 6b-ii. Process Section (Front Page) --- */
function tannic_acf_process_fields() {
    $steps = array(
        1 => array(
            'number' => '01',
            'title'  => 'Learn the Terroir',
            'body'   => 'Like making a great wine, we learn the terroir first. Where are you growing? Who are you serving? What are the constraints that will bring your brand to life? What audience do we not want, so we can make sure we are bringing in only the people who will appreciate every depth of note of your brand?',
        ),
        2 => array(
            'number' => '02',
            'title'  => 'Get to Work',
            'body'   => "We turn over every stone to find ways to make your brand sing. You won\xe2\x80\x99t find repurposed templates or old ideas here \xe2\x80\x94 we are breaking molds and setting the world on fire.",
        ),
        3 => array(
            'number' => '03',
            'title'  => 'Build',
            'body'   => "With meticulous precision, we ensure you understand what\xe2\x80\x99s going into your bottle \xe2\x80\x94 or your site \xe2\x80\x94 every step of the way. Because gatekeeping is old world, and in this new world we believe that every idea deserves sunshine. So we\xe2\x80\x99ll skip the technical details if you want, but we aren\xe2\x80\x99t here to make ourselves feel smarter than you \xe2\x80\x94 we\xe2\x80\x99re here to build something that is already yours \xe2\x80\x94 so you deserve to understand every piece of code that goes into it.",
        ),
        4 => array(
            'number' => '04',
            'title'  => 'Ship & Iterate',
            'body'   => "Our humble vineyard (ok, our lil agency started in Baltimore, MD) got its start building nonprofit dashboards for Google Grants, so we understand the power and pleasure of analytics. We don\xe2\x80\x99t stop working when we hit publish \xe2\x80\x94 that\xe2\x80\x99s just the first press. The real work begins when we see how our work interacts with your audience.",
        ),
        5 => array(
            'number' => '05',
            'title'  => 'Sip & Grow',
            'body'   => "We sit back and sip to see how you\xe2\x80\x99ll grow \xe2\x80\x94 because we know how powerful a digital presence can be in building a loyal fanbase who continues to come home to you.",
        ),
    );

    $fields = array();

    foreach ( $steps as $i => $defaults ) {
        $fields[] = array(
            'key'           => 'field_process_step_' . $i . '_number',
            'label'         => 'Step ' . $i . ' Number',
            'name'          => 'process_step_' . $i . '_number',
            'type'          => 'text',
            'default_value' => $defaults['number'],
        );
        $fields[] = array(
            'key'           => 'field_process_step_' . $i . '_title',
            'label'         => 'Step ' . $i . ' Title',
            'name'          => 'process_step_' . $i . '_title',
            'type'          => 'text',
            'default_value' => $defaults['title'],
        );
        $fields[] = array(
            'key'           => 'field_process_step_' . $i . '_body',
            'label'         => 'Step ' . $i . ' Body',
            'name'          => 'process_step_' . $i . '_body',
            'type'          => 'textarea',
            'rows'          => 4,
            'default_value' => $defaults['body'],
        );
        $fields[] = array(
            'key'           => 'field_process_step_' . $i . '_image',
            'label'         => 'Step ' . $i . ' Image',
            'name'          => 'process_step_' . $i . '_image',
            'type'          => 'image',
            'return_format' => 'array',
            'preview_size'  => 'medium',
            'instructions'  => 'Optional image for this process step.',
        );
    }

    acf_add_local_field_group( array(
        'key'      => 'group_tannic_process',
        'title'    => 'Process Section',
        'fields'   => $fields,
        'location' => array(
            array(
                array(
                    'param'    => 'page_type',
                    'operator' => '==',
                    'value'    => 'front_page',
                ),
            ),
        ),
        'menu_order' => 1,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
}

/* --- 6c. Service Details (CPT: service) --- */
function tannic_acf_service_fields() {
    acf_add_local_field_group( array(
        'key'      => 'group_tannic_service',
        'title'    => 'Service Details',
        'fields'   => array(
            array(
                'key'          => 'field_service_category_number',
                'label'        => 'Category Number',
                'name'         => 'service_category_number',
                'type'         => 'text',
                'instructions' => 'e.g., "01. Identity"',
            ),
            array(
                'key'          => 'field_service_price',
                'label'        => 'Price (USD)',
                'name'         => 'service_price',
                'type'         => 'number',
                'min'          => 0,
                'instructions' => 'Whole dollars. Used in the cost calculator.',
            ),
            array(
                'key'          => 'field_service_price_label',
                'label'        => 'Price Label',
                'name'         => 'service_price_label',
                'type'         => 'text',
                'instructions' => 'Display text, e.g. "From $5k"',
            ),
            array(
                'key'          => 'field_service_weeks',
                'label'        => 'Timeline (Weeks)',
                'name'         => 'service_weeks',
                'type'         => 'number',
                'min'          => 0,
                'instructions' => 'Estimated delivery in weeks.',
            ),
            array(
                'key'          => 'field_service_strategic_heading',
                'label'        => 'Glass Card Heading',
                'name'         => 'service_strategic_heading',
                'type'         => 'text',
                'default_value' => 'Strategic Intent',
                'instructions' => 'Heading for the main glass card.',
            ),
            array(
                'key'          => 'field_service_description',
                'label'        => 'Description',
                'name'         => 'service_description',
                'type'         => 'textarea',
                'rows'         => 4,
                'instructions' => 'Description shown in the glass dashboard.',
            ),
            array(
                'key'          => 'field_service_specs',
                'label'        => 'Specifications',
                'name'         => 'service_specs',
                'type'         => 'textarea',
                'rows'         => 6,
                'instructions' => 'One specification per line (e.g., "+ Logo Suite (SVG/PNG)").',
            ),
            array(
                'key'          => 'field_service_deliverables_heading',
                'label'        => 'Deliverables Card Heading',
                'name'         => 'service_deliverables_heading',
                'type'         => 'text',
                'default_value' => 'Deliverables',
            ),
            array(
                'key'           => 'field_service_context_image',
                'label'         => 'Context Background Image',
                'name'          => 'service_context_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions'  => 'Background image for the expanded accordion area.',
            ),
            array(
                'key'           => 'field_service_gallery_1',
                'label'         => 'Gallery Image 1',
                'name'          => 'service_gallery_1',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ),
            array(
                'key'           => 'field_service_gallery_2',
                'label'         => 'Gallery Image 2',
                'name'          => 'service_gallery_2',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ),
            array(
                'key'           => 'field_service_gallery_3',
                'label'         => 'Gallery Image 3',
                'name'          => 'service_gallery_3',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'service',
                ),
            ),
        ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
}

/* --- 6d. Project Details (CPT: project) --- */
function tannic_acf_project_fields() {
    acf_add_local_field_group( array(
        'key'      => 'group_tannic_project',
        'title'    => 'Project Details',
        'fields'   => array(
            array(
                'key'   => 'field_project_client',
                'label' => 'Client Name',
                'name'  => 'project_client',
                'type'  => 'text',
            ),
            array(
                'key'          => 'field_project_year',
                'label'        => 'Year',
                'name'         => 'project_year',
                'type'         => 'text',
                'instructions' => 'e.g., "2025"',
            ),
            array(
                'key'          => 'field_project_tech_stack',
                'label'        => 'Tech Stack',
                'name'         => 'project_tech_stack',
                'type'         => 'text',
                'instructions' => 'e.g., "React, Node.js, PostgreSQL"',
            ),
            array(
                'key'          => 'field_project_tech_badge',
                'label'        => 'Tech Badge (Short)',
                'name'         => 'project_tech_badge',
                'type'         => 'text',
                'instructions' => 'Short badge for accordion strip, e.g., "loc: zurich_04"',
            ),
            array(
                'key'   => 'field_project_url',
                'label' => 'Live URL',
                'name'  => 'project_url',
                'type'  => 'url',
            ),
            array(
                'key'          => 'field_project_challenge',
                'label'        => 'The Challenge',
                'name'         => 'project_challenge',
                'type'         => 'wysiwyg',
                'tabs'         => 'all',
                'toolbar'      => 'full',
                'media_upload' => 1,
            ),
            array(
                'key'          => 'field_project_solution',
                'label'        => 'The Solution',
                'name'         => 'project_solution',
                'type'         => 'wysiwyg',
                'tabs'         => 'all',
                'toolbar'      => 'full',
                'media_upload' => 1,
            ),
            array(
                'key'          => 'field_project_results',
                'label'        => 'The Results',
                'name'         => 'project_results',
                'type'         => 'wysiwyg',
                'tabs'         => 'all',
                'toolbar'      => 'full',
                'media_upload' => 1,
            ),

            /* --- Challenge Media (4 slots) --- */
            array( 'key' => 'field_challenge_media_1_type', 'label' => 'Challenge Media 1 Type', 'name' => 'project_challenge_media_1_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_challenge_media_1_image', 'label' => 'Challenge Media 1 Image', 'name' => 'project_challenge_media_1_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_1_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_challenge_media_1_video', 'label' => 'Challenge Media 1 Video', 'name' => 'project_challenge_media_1_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_1_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_challenge_media_1_embed', 'label' => 'Challenge Media 1 Embed URL', 'name' => 'project_challenge_media_1_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_1_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_challenge_media_1_caption', 'label' => 'Challenge Media 1 Caption', 'name' => 'project_challenge_media_1_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_1_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_challenge_media_2_type', 'label' => 'Challenge Media 2 Type', 'name' => 'project_challenge_media_2_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_challenge_media_2_image', 'label' => 'Challenge Media 2 Image', 'name' => 'project_challenge_media_2_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_2_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_challenge_media_2_video', 'label' => 'Challenge Media 2 Video', 'name' => 'project_challenge_media_2_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_2_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_challenge_media_2_embed', 'label' => 'Challenge Media 2 Embed URL', 'name' => 'project_challenge_media_2_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_2_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_challenge_media_2_caption', 'label' => 'Challenge Media 2 Caption', 'name' => 'project_challenge_media_2_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_2_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_challenge_media_3_type', 'label' => 'Challenge Media 3 Type', 'name' => 'project_challenge_media_3_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_challenge_media_3_image', 'label' => 'Challenge Media 3 Image', 'name' => 'project_challenge_media_3_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_3_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_challenge_media_3_video', 'label' => 'Challenge Media 3 Video', 'name' => 'project_challenge_media_3_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_3_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_challenge_media_3_embed', 'label' => 'Challenge Media 3 Embed URL', 'name' => 'project_challenge_media_3_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_3_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_challenge_media_3_caption', 'label' => 'Challenge Media 3 Caption', 'name' => 'project_challenge_media_3_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_3_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_challenge_media_4_type', 'label' => 'Challenge Media 4 Type', 'name' => 'project_challenge_media_4_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_challenge_media_4_image', 'label' => 'Challenge Media 4 Image', 'name' => 'project_challenge_media_4_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_4_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_challenge_media_4_video', 'label' => 'Challenge Media 4 Video', 'name' => 'project_challenge_media_4_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_4_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_challenge_media_4_embed', 'label' => 'Challenge Media 4 Embed URL', 'name' => 'project_challenge_media_4_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_4_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_challenge_media_4_caption', 'label' => 'Challenge Media 4 Caption', 'name' => 'project_challenge_media_4_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_challenge_media_4_type', 'operator' => '!=', 'value' => '' ) ) ) ),

            /* --- Solution Media (4 slots) --- */
            array( 'key' => 'field_solution_media_1_type', 'label' => 'Solution Media 1 Type', 'name' => 'project_solution_media_1_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_solution_media_1_image', 'label' => 'Solution Media 1 Image', 'name' => 'project_solution_media_1_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_1_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_solution_media_1_video', 'label' => 'Solution Media 1 Video', 'name' => 'project_solution_media_1_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_1_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_solution_media_1_embed', 'label' => 'Solution Media 1 Embed URL', 'name' => 'project_solution_media_1_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_1_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_solution_media_1_caption', 'label' => 'Solution Media 1 Caption', 'name' => 'project_solution_media_1_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_1_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_solution_media_2_type', 'label' => 'Solution Media 2 Type', 'name' => 'project_solution_media_2_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_solution_media_2_image', 'label' => 'Solution Media 2 Image', 'name' => 'project_solution_media_2_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_2_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_solution_media_2_video', 'label' => 'Solution Media 2 Video', 'name' => 'project_solution_media_2_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_2_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_solution_media_2_embed', 'label' => 'Solution Media 2 Embed URL', 'name' => 'project_solution_media_2_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_2_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_solution_media_2_caption', 'label' => 'Solution Media 2 Caption', 'name' => 'project_solution_media_2_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_2_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_solution_media_3_type', 'label' => 'Solution Media 3 Type', 'name' => 'project_solution_media_3_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_solution_media_3_image', 'label' => 'Solution Media 3 Image', 'name' => 'project_solution_media_3_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_3_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_solution_media_3_video', 'label' => 'Solution Media 3 Video', 'name' => 'project_solution_media_3_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_3_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_solution_media_3_embed', 'label' => 'Solution Media 3 Embed URL', 'name' => 'project_solution_media_3_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_3_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_solution_media_3_caption', 'label' => 'Solution Media 3 Caption', 'name' => 'project_solution_media_3_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_3_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_solution_media_4_type', 'label' => 'Solution Media 4 Type', 'name' => 'project_solution_media_4_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_solution_media_4_image', 'label' => 'Solution Media 4 Image', 'name' => 'project_solution_media_4_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_4_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_solution_media_4_video', 'label' => 'Solution Media 4 Video', 'name' => 'project_solution_media_4_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_4_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_solution_media_4_embed', 'label' => 'Solution Media 4 Embed URL', 'name' => 'project_solution_media_4_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_4_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_solution_media_4_caption', 'label' => 'Solution Media 4 Caption', 'name' => 'project_solution_media_4_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_solution_media_4_type', 'operator' => '!=', 'value' => '' ) ) ) ),

            /* --- Results Media (4 slots) --- */
            array( 'key' => 'field_results_media_1_type', 'label' => 'Results Media 1 Type', 'name' => 'project_results_media_1_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_results_media_1_image', 'label' => 'Results Media 1 Image', 'name' => 'project_results_media_1_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_1_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_results_media_1_video', 'label' => 'Results Media 1 Video', 'name' => 'project_results_media_1_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_1_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_results_media_1_embed', 'label' => 'Results Media 1 Embed URL', 'name' => 'project_results_media_1_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_1_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_results_media_1_caption', 'label' => 'Results Media 1 Caption', 'name' => 'project_results_media_1_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_1_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_results_media_2_type', 'label' => 'Results Media 2 Type', 'name' => 'project_results_media_2_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_results_media_2_image', 'label' => 'Results Media 2 Image', 'name' => 'project_results_media_2_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_2_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_results_media_2_video', 'label' => 'Results Media 2 Video', 'name' => 'project_results_media_2_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_2_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_results_media_2_embed', 'label' => 'Results Media 2 Embed URL', 'name' => 'project_results_media_2_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_2_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_results_media_2_caption', 'label' => 'Results Media 2 Caption', 'name' => 'project_results_media_2_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_2_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_results_media_3_type', 'label' => 'Results Media 3 Type', 'name' => 'project_results_media_3_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_results_media_3_image', 'label' => 'Results Media 3 Image', 'name' => 'project_results_media_3_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_3_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_results_media_3_video', 'label' => 'Results Media 3 Video', 'name' => 'project_results_media_3_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_3_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_results_media_3_embed', 'label' => 'Results Media 3 Embed URL', 'name' => 'project_results_media_3_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_3_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_results_media_3_caption', 'label' => 'Results Media 3 Caption', 'name' => 'project_results_media_3_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_3_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array( 'key' => 'field_results_media_4_type', 'label' => 'Results Media 4 Type', 'name' => 'project_results_media_4_type', 'type' => 'select', 'choices' => array( '' => 'None', 'image' => 'Image', 'video' => 'Video', 'embed' => 'Embed (iframe)' ), 'default_value' => '', 'wrapper' => array( 'width' => '25' ) ),
            array( 'key' => 'field_results_media_4_image', 'label' => 'Results Media 4 Image', 'name' => 'project_results_media_4_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_4_type', 'operator' => '==', 'value' => 'image' ) ) ) ),
            array( 'key' => 'field_results_media_4_video', 'label' => 'Results Media 4 Video', 'name' => 'project_results_media_4_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_4_type', 'operator' => '==', 'value' => 'video' ) ) ) ),
            array( 'key' => 'field_results_media_4_embed', 'label' => 'Results Media 4 Embed URL', 'name' => 'project_results_media_4_embed', 'type' => 'url', 'instructions' => 'Full URL for iframe embed (CodePen, Loom, YouTube, etc.)', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_4_type', 'operator' => '==', 'value' => 'embed' ) ) ) ),
            array( 'key' => 'field_results_media_4_caption', 'label' => 'Results Media 4 Caption', 'name' => 'project_results_media_4_caption', 'type' => 'text', 'conditional_logic' => array( array( array( 'field' => 'field_results_media_4_type', 'operator' => '!=', 'value' => '' ) ) ) ),
            array(
                'key'           => 'field_project_featured_homepage',
                'label'         => 'Feature on Homepage Accordion',
                'name'          => 'featured_on_homepage',
                'type'          => 'true_false',
                'default_value' => 0,
                'ui'            => 1,
                'instructions'  => 'Show this project in the homepage accordion gallery.',
            ),
            array(
                'key'           => 'field_project_gallery_1',
                'label'         => 'Gallery Image 1',
                'name'          => 'project_gallery_1',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ),
            array(
                'key'           => 'field_project_gallery_2',
                'label'         => 'Gallery Image 2',
                'name'          => 'project_gallery_2',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ),
            array(
                'key'           => 'field_project_gallery_3',
                'label'         => 'Gallery Image 3',
                'name'          => 'project_gallery_3',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ),
            array(
                'key'           => 'field_project_gallery_4',
                'label'         => 'Gallery Image 4',
                'name'          => 'project_gallery_4',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'project',
                ),
            ),
        ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
}

/* --- 6e. Post Details (Posts) --- */
function tannic_acf_post_fields() {
    acf_add_local_field_group( array(
        'key'      => 'group_tannic_post',
        'title'    => 'Journal Post Details',
        'fields'   => array(
            array(
                'key'           => 'field_post_featured_in_journal',
                'label'         => 'Feature in Journal',
                'name'          => 'post_featured_in_journal',
                'type'          => 'true_false',
                'default_value' => 0,
                'ui'            => 1,
                'instructions'  => 'Pins this post to the featured Journal slot ahead of sticky/latest fallbacks.',
            ),
            array(
                'key'          => 'field_post_read_time',
                'label'        => 'Read Time (Minutes)',
                'name'         => 'post_read_time',
                'type'         => 'number',
                'min'          => 1,
                'instructions' => 'Optional manual override. Leave blank to calculate automatically from the post content.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'post',
                ),
            ),
        ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
}

/* --- 6f. Contact Page Fields --- */
function tannic_acf_contact_fields() {
    acf_add_local_field_group( array(
        'key'      => 'group_tannic_contact',
        'title'    => 'Contact Page Settings',
        'fields'   => array(
            array(
                'key'           => 'field_contact_heading',
                'label'         => 'Heading',
                'name'          => 'contact_heading',
                'type'          => 'text',
                'default_value' => 'Let\'s make something remarkable.',
            ),
            array(
                'key'   => 'field_contact_subheading',
                'label' => 'Subheading',
                'name'  => 'contact_subheading',
                'type'  => 'textarea',
                'rows'  => 3,
            ),
            array(
                'key'           => 'field_contact_background_image',
                'label'         => 'Background Image',
                'name'          => 'contact_background_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'large',
                'instructions'  => 'Full-bleed background image shown behind the contact layout.',
            ),
            array(
                'key'           => 'field_contact_person_photo',
                'label'         => 'Person Photo',
                'name'          => 'contact_person_photo',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions'  => 'Photo shown in the "real human" contact card.',
            ),
            array(
                'key'           => 'field_contact_person_name',
                'label'         => 'Person Name',
                'name'          => 'contact_person_name',
                'type'          => 'text',
                'default_value' => 'Kate Kight',
            ),
            array(
                'key'           => 'field_contact_person_title',
                'label'         => 'Person Title',
                'name'          => 'contact_person_title',
                'type'          => 'text',
                'default_value' => 'Founder / Lead Developer',
            ),
            array(
                'key'          => 'field_contact_form_id',
                'label'        => 'CF7 Form ID',
                'name'         => 'contact_form_id',
                'type'         => 'text',
                'instructions' => 'Enter the Contact Form 7 form ID number.',
            ),
            array(
                'key'          => 'field_contact_hours',
                'label'        => 'Office Hours',
                'name'         => 'contact_hours',
                'type'         => 'text',
                'instructions' => 'e.g., "Mon-Fri, 9am-5pm EST"',
            ),
            array(
                'key'          => 'field_contact_brevo_form_shortcode',
                'label'        => 'Brevo Form Shortcode',
                'name'         => 'contact_brevo_form_shortcode',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => 'Paste your Brevo (Sendinblue) embed shortcode or HTML here. When filled, this overrides the fallback form.',
            ),
            array(
                'key'          => 'field_contact_cal_embed_url',
                'label'        => 'Cal.com Booking URL',
                'name'         => 'contact_cal_embed_url',
                'type'         => 'url',
                'instructions' => 'Your Cal.com event link, e.g. https://cal.com/yourname/30min — this will be embedded as an inline calendar.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'page_template',
                    'operator' => '==',
                    'value'    => 'page-contact.php',
                ),
            ),
        ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
}

/* --- 6g. Services Page Fields --- */
function tannic_acf_services_page_fields() {
    acf_add_local_field_group( array(
        'key'      => 'group_tannic_services_page',
        'title'    => 'Services Page Hero',
        'fields'   => array(
            array(
                'key'          => 'field_services_intro',
                'label'        => 'Services Intro',
                'name'         => 'services_intro',
                'type'         => 'wysiwyg',
                'instructions' => 'Explain how to work with you. Displayed in the services page hero.',
                'tabs'         => 'all',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ),
            array(
                'key'           => 'field_services_hero_image',
                'label'         => 'Hero Image',
                'name'          => 'services_hero_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions'  => 'Image displayed alongside the intro text.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'page_template',
                    'operator' => '==',
                    'value'    => 'page-services.php',
                ),
            ),
        ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
}
