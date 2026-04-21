<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#primary"><?php esc_html_e('Skip to content', 'dandysite-victoria'); ?></a>

<div id="page" class="site">

    <header id="masthead" class="site-header" role="banner">
        <div class="header-inner">

            <!-- Logo -->
            <div class="header-logo">
                <?php dsp_display_header_logo(); ?>
            </div>

            <!-- Right side: nav + search icon + hamburger -->
            <div class="header-right">

                <!-- Desktop / Mobile Navigation -->
                <nav id="site-navigation" class="header-nav" aria-label="<?php esc_attr_e('Primary navigation', 'dandysite-victoria'); ?>">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'menu',
                        'container'      => false,
                        'fallback_cb'    => 'dsp_fallback_menu',
                    ]);
                    ?>
                </nav>

                <!-- Search Toggle -->
                <button class="header-search-toggle"
                        aria-controls="search-overlay"
                        aria-expanded="false"
                        aria-label="<?php esc_attr_e( 'Open search', 'dandysite-victoria' ); ?>">
                    <?php
                    $search_icon = get_template_directory() . '/assets/images/icon-search.svg';
                    if ( file_exists( $search_icon ) ) {
                        echo file_get_contents( $search_icon ); // phpcs:ignore WordPress.Security.EscapeOutput
                    }
                    ?>
                </button>

                <!-- Hamburger Toggle -->
                <button class="header-hamburger"
                        aria-controls="site-navigation"
                        aria-expanded="false"
                        aria-label="<?php esc_attr_e('Toggle menu', 'dandysite-victoria'); ?>">
                    <span class="hamburger-bar" aria-hidden="true"></span>
                    <span class="hamburger-bar" aria-hidden="true"></span>
                    <span class="hamburger-bar" aria-hidden="true"></span>
                </button>

            </div><!-- .header-right -->

        </div><!-- .header-inner -->
    </header><!-- #masthead -->

    <!-- Search Overlay -->
    <div id="search-overlay" class="search-overlay" role="dialog" aria-modal="true"
         aria-label="<?php esc_attr_e( 'Search', 'dandysite-victoria' ); ?>" hidden>
        <div class="search-overlay__card">

            <button class="search-overlay__close"
                    aria-label="<?php esc_attr_e( 'Close search', 'dandysite-victoria' ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" width="20" height="20">
                    <path d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414z"/>
                </svg>
            </button>

            <p class="search-overlay__title"><?php echo esc_html( sprintf( __( 'Search %s', 'dandysite-victoria' ), get_bloginfo( 'name' ) ) ); ?></p>

            <form role="search" method="get" class="search-overlay__form"
                  action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <div class="search-overlay__input-wrap">
                    <input
                        type="search"
                        id="search-overlay-input"
                        class="search-overlay__input"
                        name="s"
                        placeholder="Tell us what you're after…"
                        value="<?php echo esc_attr( get_search_query() ); ?>"
                        autocomplete="off"
                        aria-label="<?php esc_attr_e( 'Search', 'dandysite-victoria' ); ?>"
                    />
                    <button type="submit" class="search-overlay__submit"
                            aria-label="<?php esc_attr_e( 'Submit search', 'dandysite-victoria' ); ?>">
                        <?php
                        $search_icon = get_template_directory() . '/assets/images/icon-search.svg';
                        if ( file_exists( $search_icon ) ) {
                            echo file_get_contents( $search_icon ); // phpcs:ignore WordPress.Security.EscapeOutput
                        }
                        ?>
                    </button>
                </div>
            </form>

        </div><!-- .search-overlay__card -->
    </div><!-- #search-overlay -->

    <div class="header-spacer" aria-hidden="true"></div>

    <main id="primary" class="site-main">
