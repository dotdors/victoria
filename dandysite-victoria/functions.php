<?php
/**
 * Theme Name: Dandysite Victoria
 * Author: Dandysite
 * Version: 1.0.0
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 * Text Domain: dandysite-victoria
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme constants
define('DSP_THEME_VERSION', '1.0.0');
define('DSP_THEME_DIR', get_template_directory());
define('DSP_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function dsp_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script'
    ]);
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_theme_support('custom-spacing');

    add_editor_style('assets/css/editor-style.css');

    register_nav_menus([
        'primary' => __('Primary Menu', 'dandysite-victoria'),
        'footer'  => __('Footer Menu', 'dandysite-victoria'),
    ]);

    load_theme_textdomain('dandysite-victoria', DSP_THEME_DIR . '/languages');
}
add_action('after_setup_theme', 'dsp_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function dsp_enqueue_assets() {
    $style_path = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style(
        'dsp-style',
        get_stylesheet_uri(),
        [],
        file_exists($style_path) ? filemtime($style_path) : DSP_THEME_VERSION
    );

    $js_path = DSP_THEME_DIR . '/assets/js/main.js';
    if (file_exists($js_path)) {
        wp_enqueue_script('dsp-script', DSP_THEME_URI . '/assets/js/main.js', [], filemtime($js_path), true);
        wp_localize_script('dsp-script', 'dspAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('dsp_nonce')
        ]);
    }

    // Endorsements carousel — front page only, and only when carousel layout is selected
    $endorsements_js = DSP_THEME_DIR . '/assets/js/endorsements.js';
    if (is_front_page() && get_option('dsp_hp_endorsements_layout', 'grid') === 'carousel' && file_exists($endorsements_js)) {
        wp_enqueue_script('dsp-endorsements', DSP_THEME_URI . '/assets/js/endorsements.js', [], filemtime($endorsements_js), true);
    }

    $header_css = DSP_THEME_DIR . '/assets/css/header.css';
    if (file_exists($header_css)) {
        wp_enqueue_style('dsp-header-style', DSP_THEME_URI . '/assets/css/header.css', ['dsp-style'], filemtime($header_css));
    }

    $footer_css = DSP_THEME_DIR . '/assets/css/footer.css';
    if (file_exists($footer_css)) {
        wp_enqueue_style('dsp-footer-style', DSP_THEME_URI . '/assets/css/footer.css', ['dsp-style'], filemtime($footer_css));
    }

    if (is_front_page()) {
        $homepage_css = DSP_THEME_DIR . '/assets/css/homepage.css';
        if (file_exists($homepage_css)) {
            wp_enqueue_style('dsp-homepage-style', DSP_THEME_URI . '/assets/css/homepage.css', ['dsp-style'], filemtime($homepage_css));
        }
    }

    $header_js = DSP_THEME_DIR . '/assets/js/header.js';
    if (file_exists($header_js)) {
        wp_enqueue_script('dsp-header', DSP_THEME_URI . '/assets/js/header.js', [], filemtime($header_js), true);
    }
}
add_action('wp_enqueue_scripts', 'dsp_enqueue_assets');

/**
 * Fallback menu for when no menu is assigned
 */
function dsp_fallback_menu() {
    $pages = get_pages();
    if ($pages) {
        echo '<ul id="primary-menu" class="menu">';
        echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'dandysite-victoria') . '</a></li>';
        foreach ($pages as $page) {
            if ($page->post_title !== 'Home') {
                echo '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html($page->post_title) . '</a></li>';
            }
        }
        echo '</ul>';
    }
}

/**
 * Customize excerpt length
 */
function dsp_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'dsp_excerpt_length');

/**
 * Custom excerpt more
 */
function dsp_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'dsp_excerpt_more');

/**
 * Editor color palette — generic defaults; override in site plugin as needed
 */
function dsp_editor_color_palette() {
    add_theme_support('editor-color-palette', [
        ['name' => __('Oxford Navy',  'dandysite-victoria'), 'slug' => 'oxford-navy',  'color' => '#1d3557'],
        ['name' => __('Cerulean',     'dandysite-victoria'), 'slug' => 'cerulean',     'color' => '#457b9d'],
        ['name' => __('Frosted Blue', 'dandysite-victoria'), 'slug' => 'frosted-blue', 'color' => '#a8dadc'],
        ['name' => __('Honeydew',     'dandysite-victoria'), 'slug' => 'honeydew',     'color' => '#f1faee'],
        ['name' => __('Punch Red',    'dandysite-victoria'), 'slug' => 'punch-red',    'color' => '#e63946'],
        ['name' => __('White',        'dandysite-victoria'), 'slug' => 'white',        'color' => '#ffffff'],
    ]);
}
add_action('after_setup_theme', 'dsp_editor_color_palette');

/**
 * Disable comments — political candidate sites don't use comments.
 * Remove support, hide admin menu, redirect comment pages.
 */
function dsp_disable_comments() {
    // Remove support from post types
    foreach ( [ 'post', 'page', 'attachment' ] as $type ) {
        if ( post_type_supports( $type, 'comments' ) ) {
            remove_post_type_support( $type, 'comments' );
            remove_post_type_support( $type, 'trackbacks' );
        }
    }
}
add_action( 'init', 'dsp_disable_comments' );

// Hide comments from admin menu and dashboard
add_action( 'admin_menu', function() {
    remove_menu_page( 'edit-comments.php' );
} );
add_action( 'admin_init', function() {
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
    // Close comments on all existing posts
    global $wpdb;
    // Remove discussion meta boxes from editor
    remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
    remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
    remove_meta_box( 'commentsdiv', 'post', 'normal' );
    remove_meta_box( 'commentsdiv', 'page', 'normal' );
} );

// Redirect any attempts to access comment pages
add_action( 'template_redirect', function() {
    if ( is_comment_feed() || is_singular() && comments_open() ) {
        wp_redirect( home_url( '/' ), 301 );
        exit;
    }
} );

// Remove comments from admin bar
add_action( 'wp_before_admin_bar_render', function() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
} );

// Return 0 for comment counts
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open',    '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );



/**
 * Clean up WordPress head
 */
function dsp_clean_head() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'dsp_clean_head');

/**
 * Custom Logo
 * Checks WP Customizer first, then assets/images/logo.{svg,png,jpg,jpeg}
 */
function dsp_get_custom_logo() {
    if (has_custom_logo()) {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
        $logo_alt = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name', 'display');
        return '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($logo_alt) . '" class="custom-logo">';
    }

    $directories = [
        ['path' => get_stylesheet_directory(), 'url' => get_stylesheet_directory_uri()],
        ['path' => get_template_directory(),   'url' => get_template_directory_uri()],
    ];

    foreach ($directories as $dir) {
        foreach (['svg', 'png', 'jpg', 'jpeg'] as $ext) {
            $logo_path = $dir['path'] . '/assets/images/logo.' . $ext;
            if (file_exists($logo_path)) {
                $class = ($ext === 'svg') ? 'custom-logo svg-logo' : 'custom-logo';
                return '<img src="' . esc_url($dir['url'] . '/assets/images/logo.' . $ext) . '" alt="' . esc_attr(get_bloginfo('name')) . '" class="' . esc_attr($class) . '">';
            }
        }
    }

    return false;
}

/**
 * Display logo or fall back to site title
 */
function dsp_display_logo() {
    $logo = dsp_get_custom_logo();
    if ($logo) {
        echo '<a href="' . esc_url(home_url('/')) . '" rel="home" class="custom-logo-link">' . $logo . '</a>';
    } else {
        echo '<h1 class="site-title"><a href="' . esc_url(home_url('/')) . '" rel="home">';
        bloginfo('name');
        echo '</a></h1>';
    }
}

/**
 * Theme Settings page
 * Intentionally minimal — site-specific settings belong in the ds-[sitename] plugin.
 */
function dsp_add_theme_settings() {
    add_theme_page(
        __('Theme Settings', 'dandysite-victoria'),
        __('Theme Settings', 'dandysite-victoria'),
        'manage_options',
        'dsp-theme-settings',
        'dsp_theme_settings_page'
    );
}
add_action('admin_menu', 'dsp_add_theme_settings');

function dsp_register_theme_settings() {
    add_settings_section(
        'dsp_features_section',
        __('Theme Features', 'dandysite-victoria'),
        'dsp_features_section_callback',
        'dsp-theme-settings'
    );

    // Bio section label
    register_setting( 'dsp_theme_settings', 'dsp_bio_section_label', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    ] );
    add_settings_field(
        'dsp_bio_section_label',
        __( 'Bio Section Eyebrow', 'dandysite-victoria' ),
        'dsp_bio_section_label_field',
        'dsp-theme-settings',
        'dsp_features_section'
    );

    // Bio page slug
    register_setting( 'dsp_theme_settings', 'dsp_bio_page_slug', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_title',
        'default'           => 'about',
    ] );
    add_settings_field(
        'dsp_bio_page_slug',
        __( 'Bio Page', 'dandysite-victoria' ),
        'dsp_bio_page_slug_field',
        'dsp-theme-settings',
        'dsp_features_section'
    );

    // Featured image on single posts
    register_setting( 'dsp_theme_settings', 'dsp_single_featured_image', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '1',
    ] );
    add_settings_field(
        'dsp_single_featured_image',
        __( 'Single Post Featured Image', 'dandysite-victoria' ),
        'dsp_single_featured_image_field',
        'dsp-theme-settings',
        'dsp_features_section'
    );

    // External article link behavior
    register_setting( 'dsp_theme_settings', 'dsp_external_link_behavior', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'summary',
    ] );
    add_settings_field(
        'dsp_external_link_behavior',
        __( 'External Article Behavior', 'dandysite-victoria' ),
        'dsp_external_link_behavior_field',
        'dsp-theme-settings',
        'dsp_features_section'
    );
}
add_action('admin_init', 'dsp_register_theme_settings');

function dsp_bio_section_label_field() {
    $value = get_option( 'dsp_bio_section_label', '' );
    ?>
    <input type="text" name="dsp_bio_section_label"
           value="<?php echo esc_attr( $value ); ?>"
           class="regular-text"
           placeholder="<?php esc_attr_e( 'About the Candidate', 'dandysite-victoria' ); ?>" />
    <p class="description">
        <?php esc_html_e( 'Small label shown above the name in the bio section. Leave blank to hide it entirely.', 'dandysite-victoria' ); ?>
    </p>
    <?php
}

function dsp_single_featured_image_field() {
    $value = get_option( 'dsp_single_featured_image', '1' );
    ?>
    <label>
        <input type="checkbox" name="dsp_single_featured_image" value="1" <?php checked( $value, '1' ); ?> />
        <?php esc_html_e( 'Show the featured image at the top of single blog posts', 'dandysite-victoria' ); ?>
    </label>
    <p class="description">
        <?php esc_html_e( 'Uncheck to hide featured images on single post pages sitewide. (Featured images still appear on cards and archives.)', 'dandysite-victoria' ); ?>
    </p>
    <?php
}

function dsp_bio_page_slug_field() {
    $slug  = get_option( 'dsp_bio_page_slug', 'about' );
    $pages = get_pages( [ 'post_status' => 'publish' ] );
    ?>
    <select name="dsp_bio_page_slug">
        <option value=""><?php esc_html_e( '— Select a page —', 'dandysite-victoria' ); ?></option>
        <?php foreach ( $pages as $page ) : ?>
        <option value="<?php echo esc_attr( $page->post_name ); ?>"
            <?php selected( $slug, $page->post_name ); ?>>
            <?php echo esc_html( $page->post_title ); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <p class="description">
        <?php esc_html_e( 'The page whose content appears in the "Meet the Candidate" bio section on the homepage. Defaults to a page with slug "about".', 'dandysite-victoria' ); ?>
    </p>
    <?php
}

function dsp_external_link_behavior_field() {
    $value = get_option( 'dsp_external_link_behavior', 'summary' );
    ?>
    <fieldset>
        <label style="display:block; margin-bottom:0.5em;">
            <input type="radio" name="dsp_external_link_behavior" value="summary" <?php checked( $value, 'summary' ); ?> />
            <strong><?php _e( 'Link to summary page', 'dandysite-victoria' ); ?></strong>
            &mdash; <?php _e( 'News card goes to the on-site post. The post shows a "Read at [Publication]" button linking to the original. Recommended — keeps content on your domain and survives link death.', 'dandysite-victoria' ); ?>
        </label>
        <label style="display:block;">
            <input type="radio" name="dsp_external_link_behavior" value="direct" <?php checked( $value, 'direct' ); ?> />
            <strong><?php _e( 'Link directly to source', 'dandysite-victoria' ); ?></strong>
            &mdash; <?php _e( 'News card clicks go straight to the external URL in a new tab. Simpler, but dependent on the external link staying live.', 'dandysite-victoria' ); ?>
        </label>
    </fieldset>
    <?php
}

function dsp_theme_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('dsp_theme_settings');
            do_settings_sections('dsp-theme-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function dsp_features_section_callback() {
    echo '<p>' . esc_html__('Theme-level settings. Site-specific options live in the site plugin (ds-[sitename]).', 'dandysite-victoria') . '</p>';
}

// ===== FAVICON =====
// Place favicon files in the WordPress root directory.
// Do NOT set a Site Icon in Appearance > Customize — these tags handle it.
function dsp_favicon_links() {
    ?>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="/site.webmanifest" />
    <?php
}
add_action('wp_head', 'dsp_favicon_links', 1);

// ===== CUSTOM POST TYPES =====
require_once DSP_THEME_DIR . '/includes/cpts/cpt-endorsements.php';
require_once DSP_THEME_DIR . '/includes/cpts/cpt-positions.php';

// ===== POST META (article details) =====
require_once DSP_THEME_DIR . '/includes/post-meta.php';

// ===== THEME ACTIVATION =====
require_once DSP_THEME_DIR . '/includes/theme-activation.php';

// ===== HOMEPAGE SETTINGS =====
require_once DSP_THEME_DIR . '/includes/homepage-settings.php';

// ===== HEADER SYSTEM =====
require_once DSP_THEME_DIR . '/includes/header-settings.php';
require_once DSP_THEME_DIR . '/includes/header-functions.php';
require_once DSP_THEME_DIR . '/includes/header-meta.php';
require_once DSP_THEME_DIR . '/includes/footer-settings.php';
require_once DSP_THEME_DIR . '/includes/social-settings.php';

// ===== SITE IDENTITY (logo variants) =====
require_once DSP_THEME_DIR . '/includes/site-identity.php';

// ===== HOMEPAGE =====
require_once DSP_THEME_DIR . '/includes/homepage-meta.php';

// ===== DEVELOPMENT & DEBUGGING =====
// Uncomment during development, comment out for production:
// include_once DSP_THEME_DIR . '/debug-helper.php';

/**
 * Navmenu shortcode — outputs any registered WP menu by location or name
 * Usage: [navmenu theme_location="primary"]
 */
function nd_navmenu_shortcode($atts): string {
    $defaults = [
        'menu'            => '',
        'container'       => 'div',
        'container_class' => '',
        'container_id'    => '',
        'menu_class'      => 'menu',
        'menu_id'         => '',
        'fallback_cb'     => 'wp_page_menu',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'depth'           => 0,
        'walker'          => '',
        'theme_location'  => '',
    ];
    $atts = shortcode_atts($defaults, $atts, 'navmenu');
    if (is_string($atts['walker']) && class_exists($atts['walker'])) {
        $atts['walker'] = new $atts['walker']();
    } else {
        $atts['walker'] = '';
    }
    $atts['echo'] = false;
    return wp_nav_menu($atts);
}
add_shortcode('navmenu', 'nd_navmenu_shortcode');

/**
 * Footer Widget Areas
 */
function victoria_register_footer_widgets() {
    register_sidebar([
        'name'          => __('Footer Widgets (Primary)', 'dandysite-victoria'),
        'id'            => 'footer-widgets',
        'description'   => __('Main footer widget row. Layout controlled by Footer Layout setting.', 'dandysite-victoria'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget__title">',
        'after_title'   => '</h4>',
    ]);
    register_sidebar([
        'name'          => __('Footer Widgets (Secondary)', 'dandysite-victoria'),
        'id'            => 'footer-widgets-secondary',
        'description'   => __('Optional second row below primary footer widgets. Centered by default.', 'dandysite-victoria'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget__title">',
        'after_title'   => '</h4>',
    ]);
}
add_action('widgets_init', 'victoria_register_footer_widgets');
