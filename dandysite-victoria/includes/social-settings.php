<?php
/**
 * Social Links Settings
 *
 * Adds a "Social Links" section to Appearance → Theme Settings.
 * Stores site-level social URLs as WordPress options.
 * Provides the [ds_socials] shortcode to render icon links anywhere.
 *
 * Adding more platforms later:
 *   1. Add a new entry to dsp_social_platforms() below.
 *   2. Drop the matching icon-{slug}.svg into themes/dandysite-victoria/assets/images/
 *   3. Register and add_settings_field for the new key — copy the pattern below.
 *   That's it. No template changes needed.
 *
 * SVG icons: themes/dandysite-victoria/assets/images/
 *   icon-instagram.svg
 *   icon-facebook.svg
 *
 * Usage:
 *   [ds_socials]                  — all saved platforms
 *   [ds_socials size="32"]        — custom icon size in px (default 24)
 *
 * dandysite-victoria theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// =====================================================================
// PLATFORM REGISTRY
// =====================================================================
// Central list of supported platforms.
// Each entry: option_key => [ label, icon_file, aria_label_pattern ]
// To add a platform: add an entry here + drop the SVG + register below.

function dsp_social_platforms() {
    return [
        'dsp_social_instagram' => [
            'label'      => __( 'Instagram URL', 'dandysite-victoria' ),
            'icon'       => 'icon-instagram.svg',
            'aria'       => 'Find us on Instagram',
            'network'    => 'Instagram',
        ],
        'dsp_social_facebook' => [
            'label'      => __( 'Facebook URL', 'dandysite-victoria' ),
            'icon'       => 'icon-facebook.svg',
            'aria'       => 'Find us on Facebook',
            'network'    => 'Facebook',
        ],
    ];
}


// =====================================================================
// REGISTER SETTINGS
// =====================================================================

function dsp_register_social_settings() {

    add_settings_section(
        'dsp_social_section',
        __( 'Social Links', 'dandysite-victoria' ),
        '__return_false',
        'dsp-theme-settings'
    );

    foreach ( dsp_social_platforms() as $key => $platform ) {
        register_setting( 'dsp_theme_settings', $key, [
            'sanitize_callback' => 'esc_url_raw',
            'default'           => '',
        ] );

        add_settings_field(
            $key,
            esc_html( $platform['label'] ),
            'dsp_social_url_field',
            'dsp-theme-settings',
            'dsp_social_section',
            [ 'option_key' => $key ]
        );
    }
}
add_action( 'admin_init', 'dsp_register_social_settings' );


// =====================================================================
// FIELD CALLBACK
// =====================================================================

function dsp_social_url_field( $args ) {
    $key   = $args['option_key'];
    $value = get_option( $key, '' );
    printf(
        '<input type="url" name="%s" value="%s" class="regular-text" placeholder="https://">',
        esc_attr( $key ),
        esc_attr( $value )
    );
}


// =====================================================================
// [ds_socials] SHORTCODE
// =====================================================================

/**
 * Inline an SVG from the theme images folder.
 * Returns empty string if file not found.
 *
 * @param string $filename  e.g. 'icon-instagram.svg'
 * @return string  Raw SVG markup or empty string.
 */
function dsp_inline_social_svg( $filename ) {
    $path = DSP_THEME_DIR . '/assets/images/' . $filename;
    if ( ! file_exists( $path ) ) {
        return '';
    }
    return file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
}

/**
 * [ds_socials] shortcode.
 *
 * Renders icon links for all social platforms that have a saved URL.
 * Skips any platform with no URL set — safe to use before all fields are filled.
 *
 * @param array $atts  size (int, px) — icon size, default 40.
 * @return string  HTML.
 */
function dsp_socials_shortcode( $atts ) {
    $atts = shortcode_atts( [ 'size' => 40 ], $atts, 'ds_socials' );
    $size = absint( $atts['size'] );

    $platforms = dsp_social_platforms();
    $items     = [];

    foreach ( $platforms as $key => $platform ) {
        $url     = get_option( $key, '' );
        if ( ! $url ) {
            continue;
        }

        $tooltip = sprintf( __( 'Find us on %s', 'dandysite-victoria' ), $platform['network'] );
        $svg     = dsp_inline_social_svg( $platform['icon'] );

        if ( ! $svg ) {
            $inner = '<span class="ds-socials__label">' . esc_html( $tooltip ) . '</span>';
        } else {
            $inner = '<span class="ds-socials__icon" style="width:' . $size . 'px;height:' . $size . 'px;" aria-hidden="true">'
                . $svg
                . '</span>'
                . '<span class="screen-reader-text">' . esc_html( $platform['aria'] ) . '</span>';
        }

        $items[] = '<a href="' . esc_url( $url ) . '"'
            . ' class="ds-socials__link"'
            . ' title="' . esc_attr( $tooltip ) . '"'
            . ' target="_blank"'
            . ' rel="noopener noreferrer">'
            . $inner
            . '</a>';
    }

    if ( empty( $items ) ) {
        return '';
    }

    return '<div class="ds-socials">' . implode( '', $items ) . '</div>';
}
add_shortcode( 'ds_socials', 'dsp_socials_shortcode' );

// Process shortcodes inside block-based widget areas.
// dynamic_sidebar() → do_blocks() renders block markup but doesn't run
// do_shortcode() on block content. We target only the block types where
// someone would realistically paste a shortcode.
add_filter( 'render_block', function( $block_content, $block ) {
    if ( is_admin() ) {
        return $block_content;
    }
    $shortcode_blocks = [ 'core/paragraph', 'core/html', 'core/shortcode', 'core/freeform' ];
    if ( in_array( $block['blockName'], $shortcode_blocks, true ) ) {
        return do_shortcode( $block_content );
    }
    return $block_content;
}, 10, 2 );

// Also cover classic text widgets just in case
add_filter( 'widget_text', 'do_shortcode' );
