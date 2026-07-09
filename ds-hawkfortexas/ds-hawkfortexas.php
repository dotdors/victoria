<?php
/**
 * Plugin Name: DS Site - Hawk for Texas
 * Description: Site-specific branding, CPTs, and customizations for HawkForTexas.com
 * Version: 1.0.0
 * Author: Nancy Dorsner - Dabbled Studios
 * Author URI: https://dabbledstudios.com/
 * Text Domain: ds-hawkfortexas
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================
// CONSTANTS
// ============================================================

define('DSHFT_VERSION',    '1.0.0');
define('DSHFT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DSHFT_PLUGIN_URL', plugin_dir_url(__FILE__));

// ============================================================
// ASSETS
// ============================================================

/**
 * Enqueue site-specific styles and scripts
 */
function dshft_enqueue_assets() {
    // Google Fonts — Barlow Condensed (headings) + Barlow (body)
    wp_enqueue_style(
        'dshft-fonts',
        'https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap',
        [],
        null
    );

    // Main site stylesheet — overrides Victoria base styles
    $css_path = DSHFT_PLUGIN_DIR . 'assets/css/site.css';
    if (file_exists($css_path)) {
        wp_enqueue_style(
            'dshft-site-style',
            DSHFT_PLUGIN_URL . 'assets/css/site.css',
            ['dsp-style', 'dsp-header-style'],  // loads after both Victoria base and header CSS
            filemtime($css_path)
        );
    }

    // Site-specific JS (optional)
    $js_path = DSHFT_PLUGIN_DIR . 'assets/js/site.js';
    if (file_exists($js_path)) {
        wp_enqueue_script(
            'dshft-site-script',
            DSHFT_PLUGIN_URL . 'assets/js/site.js',
            [],
            filemtime($js_path),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'dshft_enqueue_assets');

// ============================================================
// EDITOR COLOR PALETTE OVERRIDE
// ============================================================
// Replaces Victoria's generic palette with campaign colors.

function dshft_editor_color_palette() {
    add_theme_support('editor-color-palette', [
        ['name' => __('Campaign Primary',   'ds-hawkfortexas'), 'slug' => 'campaign-primary',   'color' => '#003087'], // placeholder — update with real brand color
        ['name' => __('Campaign Secondary', 'ds-hawkfortexas'), 'slug' => 'campaign-secondary', 'color' => '#BF0A30'], // placeholder
        ['name' => __('White',              'ds-hawkfortexas'), 'slug' => 'white',               'color' => '#ffffff'],
        ['name' => __('Dark Text',          'ds-hawkfortexas'), 'slug' => 'dark-text',           'color' => '#222222'],
        ['name' => __('Light Background',   'ds-hawkfortexas'), 'slug' => 'light-bg',            'color' => '#f5f5f5'],
    ]);
}
add_action('after_setup_theme', 'dshft_editor_color_palette', 20); // priority 20 = runs after Victoria's palette

// ============================================================
// CUSTOMIZER SETTINGS (optional)
// ============================================================
// Uncomment and extend if you want Customizer controls:
// require_once DSHFT_PLUGIN_DIR . 'includes/customizer.php';

// ============================================================
// HIDE DATES ON PRE-LAUNCH POSTS
// All posts created before site launch are imported/old content
// where the WP publish date is not meaningful. Remove this
// filter once content is being created natively going forward.
// ============================================================

add_filter( 'dsp_show_post_date', function( $show, $post_id ) {
    $cutoff    = strtotime( '2026-04-24' );
    $post_date = get_post_time( 'U', false, $post_id );
    return $post_date >= $cutoff;
}, 10, 2 );
