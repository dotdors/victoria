<?php
/**
 * Header Functions
 * Determines active header style per page and provides logo/config output.
 * dandysite-victoria theme
 */

if (!defined('ABSPATH')) {
    exit;
}


// =====================================================================
// HEADER STYLE DETECTION
// =====================================================================

/**
 * Get the active header style for the current page.
 * Priority: page template > default setting
 *
 * @return string 'overlay' | 'solid'
 */
function dsp_get_header_style() {
    $default = get_option('dsp_header_default_style', 'solid');

    // Hero hidden on the homepage → nothing to overlay; force solid.
    // Overrides per-page meta, since an overlay header would sit on
    // top of the first content section.
    if ( is_front_page() && '1' !== get_option( 'dsp_hp_show_hero', '1' ) ) {
        return 'solid';
    }

    // Per-page/CPT override via meta field (set in the Header Style sidebar panel)
    if (is_singular()) {
        $post_id = get_queried_object_id();
        $meta    = get_post_meta($post_id, '_dsp_header_style', true);
        if ($meta === 'overlay') return 'overlay';
        if ($meta === 'solid')   return 'solid';
    }

    return $default;
}

/**
 * Get the scroll-reveal style for the current header.
 *
 * @return string 'solid' | 'transparent'
 */
function dsp_get_scroll_reveal_style() {
    $style = dsp_get_header_style();
    if ($style === 'overlay') {
        return get_option('dsp_header_overlay_scroll_reveal', 'solid');
    }
    return get_option('dsp_header_solid_scroll_reveal', 'solid');
}


// =====================================================================
// BODY CLASSES
// =====================================================================

/**
 * Add header-related body classes.
 */
function dsp_header_body_classes($classes) {
    $style  = dsp_get_header_style();
    $reveal = dsp_get_scroll_reveal_style();

    $classes[] = 'header-style-' . $style;
    $classes[] = 'header-reveal-' . $reveal;

    return $classes;
}
add_filter('body_class', 'dsp_header_body_classes');


// =====================================================================
// LOGO OUTPUT
// =====================================================================

/**
 * Output the logo. One upload — CSS handles color inversion on dark backgrounds.
 * Fallback: Site Title text.
 */
function dsp_display_header_logo() {
    $site_name = get_bloginfo('name');
    $home_url  = esc_url(home_url('/'));
    $logo_html = '';

    // 1. WordPress custom logo (Appearance > Customize > Site Identity)
    if (has_custom_logo()) {
        $logo_id  = get_theme_mod('custom_logo');
        $url      = wp_get_attachment_image_url($logo_id, 'full');
        $alt      = get_post_meta($logo_id, '_wp_attachment_image_alt', true) ?: $site_name;
        if ($url) {
            $logo_html = sprintf(
                '<img src="%s" alt="%s" class="site-logo" loading="eager">',
                esc_url($url),
                esc_attr($alt)
            );
        }
    }

    // 2. File-based fallback (assets/images/logo.svg|png|jpg in child or parent theme)
    if (!$logo_html) {
        $dirs = [
            ['path' => get_stylesheet_directory(), 'url' => get_stylesheet_directory_uri()],
            ['path' => get_template_directory(),   'url' => get_template_directory_uri()],
        ];
        foreach ($dirs as $dir) {
            foreach (['svg', 'png', 'jpg', 'jpeg'] as $ext) {
                $file = $dir['path'] . '/assets/images/logo.' . $ext;
                if (file_exists($file)) {
                    $logo_html = sprintf(
                        '<img src="%s" alt="%s" class="site-logo" loading="eager">',
                        esc_url($dir['url'] . '/assets/images/logo.' . $ext),
                        esc_attr($site_name)
                    );
                    break 2;
                }
            }
        }
    }

    echo '<a href="' . $home_url . '" class="site-logo-link" rel="home">';
    if ($logo_html) {
        echo $logo_html;
    } else {
        // 3. Text fallback
        printf('<span class="site-logo-text">%s</span>', esc_html($site_name));
    }
    echo '</a>';
}


// =====================================================================
// LOCALIZE HEADER CONFIG FOR JS
// =====================================================================

/**
 * Pass header configuration to JavaScript.
 * Attached to wp_enqueue_scripts — see functions.php.
 */
function dsp_localize_header_config() {
    wp_localize_script('dsp-header', 'dspHeader', [
        'style'           => dsp_get_header_style(),
        'scrollReveal'    => dsp_get_scroll_reveal_style(),
        'navBreakpoint'   => dsp_get_nav_breakpoint_px(),
        'scrollThreshold' => (int) get_option('dsp_header_scroll_threshold', 80),
    ]);
}
add_action('wp_enqueue_scripts', 'dsp_localize_header_config', 20);


// =====================================================================
// SVG UPLOAD SUPPORT
// =====================================================================

/**
 * Allow SVG uploads in the media library.
 * Added here so it's available for all projects using dandysite-victoria.
 */
function dsp_allow_svg_uploads($mimes) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'dsp_allow_svg_uploads');

/**
 * Fix SVG display in the media library (WP doesn't show SVG thumbs by default).
 */
function dsp_fix_svg_thumb_display($response, $attachment) {
    if ($response['mime'] === 'image/svg+xml') {
        $response['sizes'] = [
            'full' => [
                'url'         => $response['url'],
                'width'       => 800,
                'height'      => 600,
                'orientation' => 'landscape',
            ],
        ];
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'dsp_fix_svg_thumb_display', 10, 2);
