<?php
/**
 * Section Embed Shortcode — [ds_section]
 *
 * Renders any homepage section on a regular page or post.
 *
 * Usage:
 *   [ds_section name="news"]
 *   [ds_section name="get-involved" bg="dark"]
 *
 * Attributes:
 *   name  (required)  bio | issues | articles | news | endorsements |
 *                     get-involved | connect
 *   bg    (optional)  default | light | surface | dark
 *                     Overrides the Homepage Settings background for
 *                     this render only. Omit to inherit the homepage
 *                     setting.
 *
 * Notes:
 * - Section template parts are self-contained (queries, settings,
 *   site-plugin filters), so they render identically to the homepage.
 * - The homepage show/hide checkboxes do NOT affect embeds — a section
 *   hidden on the homepage can still be embedded on other pages.
 * - Output is wrapped in .ds-embedded-section, which breaks out of the
 *   page content column to run full width (see style.css).
 * - Sections carry their homepage element IDs (e.g. #news), so avoid
 *   embedding the same section twice on one page.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function dsp_section_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'name' => '',
        'bg'   => '',
    ], $atts, 'ds_section' );

    $allowed = [ 'bio', 'issues', 'articles', 'news', 'endorsements', 'get-involved', 'connect' ];
    $name    = sanitize_key( str_replace( '_', '-', $atts['name'] ) );

    if ( ! in_array( $name, $allowed, true ) ) {
        return current_user_can( 'edit_posts' )
            ? '<p><em>[ds_section] — unknown section "' . esc_html( $atts['name'] ) . '". Use one of: ' . esc_html( implode( ', ', $allowed ) ) . '</em></p>'
            : '';
    }

    // Optional per-embed background override, applied through the same
    // filter dsp_section_bg_class() already runs.
    $bg        = in_array( $atts['bg'], [ 'default', 'light', 'surface', 'dark' ], true ) ? $atts['bg'] : '';
    $bg_filter = null;

    if ( $bg ) {
        $map = [
            'default' => '',
            'light'   => ' section--light',
            'surface' => ' section--surface',
            'dark'    => ' section--dark',
        ];
        $bg_filter = function ( $class, $section ) use ( $name, $map, $bg ) {
            return ( $section === $name ) ? $map[ $bg ] : $class;
        };
        add_filter( 'dsp_section_bg_class', $bg_filter, 20, 2 );
    }

    ob_start();
    get_template_part( 'template-parts/section-' . $name );
    $html = ob_get_clean();

    if ( $bg_filter ) {
        remove_filter( 'dsp_section_bg_class', $bg_filter, 20 );
    }

    if ( '' === trim( $html ) ) {
        return ''; // section rendered nothing (e.g. no posts) — stay silent
    }

    return '<div class="ds-embedded-section">' . $html . '</div>';
}
add_shortcode( 'ds_section', 'dsp_section_shortcode' );
