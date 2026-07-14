<?php
/**
 * Plugin Name: DS Site - Sarah Stogner
 * Description: Site-specific branding and customizations for SarahStogner.com — officeholder mode (143rd District Attorney). Editorial ink-and-cream design.
 * Version: 1.0.0
 * Author: Nancy Dorsner - Dabbled Studios
 * Author URI: https://dabbledstudios.com/
 * Text Domain: ds-sarahstogner
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================
// CONSTANTS
// ============================================================

define('DSSS_VERSION',    '1.0.0');
define('DSSS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DSSS_PLUGIN_URL', plugin_dir_url(__FILE__));

// ============================================================
// ASSETS
// ============================================================

/**
 * Enqueue site-specific styles and scripts
 */
function dsss_enqueue_assets() {
    // Google Fonts — Besley (headings) + Source Serif 4 (body)
    wp_enqueue_style(
        'dsss-fonts',
        'https://fonts.googleapis.com/css2?family=Besley:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&display=swap',
        [],
        null
    );

    // Main site stylesheet — overrides Victoria base styles
    $css_path = DSSS_PLUGIN_DIR . 'assets/css/site.css';
    if (file_exists($css_path)) {
        wp_enqueue_style(
            'dsss-site-style',
            DSSS_PLUGIN_URL . 'assets/css/site.css',
            ['dsp-style', 'dsp-header-style'],  // loads after both Victoria base and header CSS
            filemtime($css_path)
        );
    }

    // Site-specific JS (optional)
    $js_path = DSSS_PLUGIN_DIR . 'assets/js/site.js';
    if (file_exists($js_path)) {
        wp_enqueue_script(
            'dsss-site-script',
            DSSS_PLUGIN_URL . 'assets/js/site.js',
            [],
            filemtime($js_path),
            true
        );
    }
}
// Priority 20: guarantees site.css always enqueues (and prints) after
// every Victoria stylesheet. See ds-hawkfortexas for the full rationale.
add_action('wp_enqueue_scripts', 'dsss_enqueue_assets', 20);

// ============================================================
// EDITOR COLOR PALETTE OVERRIDE
// ============================================================
// Replaces Victoria's generic palette with Sarah's editorial palette.

function dsss_editor_color_palette() {
    add_theme_support('editor-color-palette', [
        ['name' => __('Ink Slate',       'ds-sarahstogner'), 'slug' => 'ink-slate',       'color' => '#22303C'],
        ['name' => __('Oxblood',         'ds-sarahstogner'), 'slug' => 'oxblood',         'color' => '#9E3E33'],
        ['name' => __('Saddle Brown',    'ds-sarahstogner'), 'slug' => 'saddle-brown',    'color' => '#8A6A4F'],
        ['name' => __('Dusty Terracotta','ds-sarahstogner'), 'slug' => 'dusty-terracotta','color' => '#E2A69B'],
        ['name' => __('Cream',           'ds-sarahstogner'), 'slug' => 'cream',           'color' => '#F7F4EE'],
        ['name' => __('Warm White',      'ds-sarahstogner'), 'slug' => 'warm-white',      'color' => '#FFFDF9'],
        ['name' => __('Warm Charcoal',   'ds-sarahstogner'), 'slug' => 'warm-charcoal',   'color' => '#2B2B28'],
    ]);
}
add_action('after_setup_theme', 'dsss_editor_color_palette', 20); // priority 20 = runs after Victoria's palette

// ============================================================
// SECTION TEXT — officeholder mode wording
// ============================================================
// Victoria's defaults are campaign-flavored. Repurpose the sections
// for writing/commentary + speaking engagements.

add_filter( 'dsp_articles_title',        fn() => __( 'Newsletter', 'ds-sarahstogner' ) );
add_filter( 'dsp_articles_view_all',     fn() => __( 'Newsletter Archive', 'ds-sarahstogner' ) );
add_filter( 'dsp_news_title',            fn() => __( 'In the Media', 'ds-sarahstogner' ) );

// In the Media cards: category (Coverage / Op-Ed) as the eyebrow,
// publication moved beside a compact date
add_filter( 'dsp_news_card_category_eyebrow', '__return_true' );
add_filter( 'dsp_news_card_date_format', fn() => 'm.d.Y' );
add_filter( 'dsp_endorsements_title',    fn() => __( 'Praise', 'ds-sarahstogner' ) );
add_filter( 'dsp_endorsements_subtitle', fn() => '' );

// Get Involved → "Book Sarah" — content lives in Appearance → Homepage
// Settings → Get Involved / CTA. On activation we SEED those options
// (only if unset — re-activating never clobbers edits made in wp-admin).
register_activation_hook( __FILE__, 'dsss_seed_cta_defaults' );
function dsss_seed_cta_defaults() {
    $seeds = [
        'dsp_hp_cta_title'      => __( 'Book Sarah', 'ds-sarahstogner' ),
        'dsp_hp_cta_text'       => __( 'Sarah is available for speaking engagements, panels, media appearances, and consulting on oil & gas accountability, rural justice, and public-interest law.', 'ds-sarahstogner' ),
        'dsp_hp_cta_btn1_label' => __( 'Request Speaking', 'ds-sarahstogner' ),
        'dsp_hp_cta_btn1_url'   => '#connect',
        'dsp_hp_cta_btn1_style' => 'outline',
        'dsp_hp_cta_btn2_label' => __( 'Media Inquiries', 'ds-sarahstogner' ),
        'dsp_hp_cta_btn2_url'   => '#connect',
        'dsp_hp_cta_btn2_style' => 'outline',
        'dsp_hp_cta_btn3_label' => '',
        'dsp_hp_cta_btn3_url'   => '',
        'dsp_hp_cta_btn3_style' => 'solid',
    ];
    foreach ( $seeds as $key => $value ) {
        if ( false === get_option( $key, false ) ) {
            add_option( $key, $value );
        }
    }
}

// ============================================================
// HIDE DATES ON PRE-LAUNCH POSTS (optional)
// ============================================================
// Uncomment if imported/migrated content has meaningless WP publish
// dates, same pattern as ds-hawkfortexas. Set cutoff to launch date.
//
// add_filter( 'dsp_show_post_date', function( $show, $post_id ) {
//     $cutoff    = strtotime( '2026-08-01' );
//     $post_date = get_post_time( 'U', false, $post_id );
//     return $post_date >= $cutoff;
// }, 10, 2 );
