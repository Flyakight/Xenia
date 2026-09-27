<?php
/**
 * Header — Glass Navigation
 *
 * @package TannicStudio
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip Navigation (WCAG) -->
<a class="skip-link" href="#main-content">
    <?php esc_html_e( 'Skip to content', 'tannic-studio' ); ?>
</a>

<header class="site-header" role="banner">
    <nav class="glass-nav" role="navigation" aria-label="<?php esc_attr_e( 'Main Navigation', 'tannic-studio' ); ?>">
        <div class="nav-container">

            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo" aria-label="<?php esc_attr_e( 'Tannic Studio — Home', 'tannic-studio' ); ?>">
                TANNIC<span class="accent">.</span><span class="accent">STUDIO</span>
            </a>

            <button class="nav-toggle"
                    aria-expanded="false"
                    aria-controls="main-navigation"
                    aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'tannic-studio' ); ?>">
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
            </button>

            <div id="main-navigation" class="nav-menu">
                <?php
                $menu_args = array(
                    'container'   => false,
                    'menu_class'  => 'nav-list',
                    'fallback_cb' => false,
                    'depth'       => 1,
                );

                $locations = get_nav_menu_locations();
                if ( ! empty( $locations['main-menu'] ) ) {
                    $menu_args['theme_location'] = 'main-menu';
                    wp_nav_menu( $menu_args );
                } else {
                    $menus = wp_get_nav_menus();
                    $menu_id_with_items = 0;

                    foreach ( $menus as $menu_obj ) {
                        $items = wp_get_nav_menu_items( $menu_obj->term_id );
                        if ( ! empty( $items ) ) {
                            $menu_id_with_items = $menu_obj->term_id;
                            break;
                        }
                    }

                    if ( $menu_id_with_items ) {
                        $menu_args['menu'] = $menu_id_with_items;
                        wp_nav_menu( $menu_args );
                    }
                }
                ?>
            </div>

        </div>
    </nav>
</header>

<main id="main-content" role="main">
