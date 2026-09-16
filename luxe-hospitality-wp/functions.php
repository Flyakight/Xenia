<?php
/**
 * Luxe Hospitality Theme
 * A WordPress theme for high-end wineries, resorts, and luxury travel experiences
 */

define( 'LUXE_THEME_VERSION', '1.0.0' );

// ======================
// THEME SETUP
// ======================

function luxe_setup() {
	// Text domain
	load_theme_textdomain( 'luxe', get_template_directory() . '/languages' );

	// Featured image support
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1920, 1080, true );
	add_image_size( 'luxe-hero', 1920, 1080, true );
	add_image_size( 'luxe-card', 600, 450, true );
	add_image_size( 'luxe-full', 2560, 1440, true );

	// Gutenberg support
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	// HTML5
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );

	// Menu support
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'luxe' ),
		'footer'  => __( 'Footer Menu', 'luxe' ),
	) );
}
add_action( 'after_setup_theme', 'luxe_setup' );

// ======================
// ENQUEUE SCRIPTS & STYLES
// ======================

function luxe_enqueue_assets() {
	// Google Fonts: Cormorant Garamond + DM Sans
	wp_enqueue_style(
		'luxe-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap',
		array(),
		LUXE_THEME_VERSION
	);

	// GSAP for animations
	wp_enqueue_script(
		'gsap',
		'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
		array(),
		'3.12.5',
		false
	);

	wp_enqueue_script(
		'gsap-scroll-trigger',
		'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js',
		array( 'gsap' ),
		'3.12.5',
		false
	);

	// Main theme styles
	wp_enqueue_style(
		'luxe-style',
		get_template_directory_uri() . '/assets/css/style.css',
		array( 'luxe-fonts' ),
		LUXE_THEME_VERSION
	);

	// Tailwind CSS
	wp_enqueue_style(
		'tailwind',
		'https://cdn.tailwindcss.com',
		array(),
		LUXE_THEME_VERSION
	);

	// Main theme scripts
	wp_enqueue_script(
		'luxe-script',
		get_template_directory_uri() . '/assets/js/main.js',
		array( 'gsap', 'gsap-scroll-trigger' ),
		LUXE_THEME_VERSION,
		true
	);

	// Pass theme data to JS
	wp_localize_script( 'luxe-script', 'luxeTheme', array(
		'themeUri' => get_template_directory_uri(),
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'luxe_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'luxe_enqueue_assets' );

// ======================
// BLOCK EDITOR STYLES
// ======================

function luxe_block_editor_styles() {
	wp_enqueue_style(
		'luxe-block-editor',
		get_template_directory_uri() . '/assets/css/block-editor.css',
		array(),
		LUXE_THEME_VERSION
	);

	wp_enqueue_style(
		'luxe-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap'
	);
}
add_action( 'enqueue_block_editor_assets', 'luxe_block_editor_styles' );

// ======================
// ACF SETUP
// ======================

function luxe_acf_init() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( array(
			'page_title' => 'Theme Settings',
			'menu_title' => 'Theme Settings',
			'menu_slug'  => 'theme-settings',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-admin-generic',
			'position'   => 60,
		) );
	}
}
add_action( 'acf/init', 'luxe_acf_init' );

// ======================
// CUSTOM POST TYPES
// ======================

function luxe_register_post_types() {
	// Property post type
	register_post_type( 'property', array(
		'label'       => 'Properties',
		'public'      => true,
		'has_archive' => true,
		'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'     => array( 'slug' => 'properties' ),
		'menu_icon'   => 'dashicons-building',
		'show_in_rest' => true,
	) );

	// Experience post type
	register_post_type( 'experience', array(
		'label'       => 'Experiences',
		'public'      => true,
		'has_archive' => true,
		'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'     => array( 'slug' => 'experiences' ),
		'menu_icon'   => 'dashicons-heart',
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'luxe_register_post_types' );

// ======================
// CUSTOM TAXONOMIES
// ======================

function luxe_register_taxonomies() {
	register_taxonomy( 'property_type', 'property', array(
		'label' => 'Property Type',
		'rewrite' => array( 'slug' => 'property-type' ),
		'show_in_rest' => true,
	) );

	register_taxonomy( 'region', 'property', array(
		'label' => 'Region',
		'rewrite' => array( 'slug' => 'region' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'luxe_register_taxonomies' );

// ======================
// REMOVE DEFAULT GUTENBERG BLOCKS
// ======================

function luxe_disable_blocks() {
	$allowed = array(
		'core/paragraph',
		'core/heading',
		'core/image',
		'core/gallery',
		'core/group',
		'core/columns',
		'core/column',
		'core/buttons',
		'core/button',
		'core/spacer',
	);

	foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $block ) {
		if ( ! in_array( $block->name, $allowed ) ) {
			unregister_block_type( $block->name );
		}
	}
}
// Uncomment if you want strict block control:
// add_action( 'init', 'luxe_disable_blocks', 10 );

// ======================
// CUSTOM GUTENBERG BLOCK CATEGORIES
// ======================

function luxe_block_categories( $categories, $post ) {
	return array_merge( $categories, array(
		array(
			'slug'  => 'luxe-blocks',
			'title' => 'Luxe Hospitality',
		),
	) );
}
add_filter( 'block_categories_all', 'luxe_block_categories', 10, 2 );

// ======================
// HELPER FUNCTIONS
// ======================

/**
 * Get primary color from ACF options
 */
function luxe_primary_color() {
	return get_field( 'primary_color', 'option' ) ?: '#1a1410';
}

/**
 * Get accent color from ACF options
 */
function luxe_accent_color() {
	return get_field( 'accent_color', 'option' ) ?: '#d4af37';
}

/**
 * Safe image output with lazy loading
 */
function luxe_image( $attachment_id, $size = 'luxe-hero', $alt = '' ) {
	if ( ! $attachment_id ) return '';

	return sprintf(
		'<img src="%s" alt="%s" loading="lazy" decoding="async" />',
		wp_get_attachment_image_src( $attachment_id, $size )[0],
		esc_attr( $alt )
	);
}

// ======================
// CUSTOM EXCERPT LENGTH
// ======================

function luxe_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'luxe_excerpt_length' );

function luxe_excerpt_more( $more ) {
	return ' ...';
}
add_filter( 'excerpt_more', 'luxe_excerpt_more' );
