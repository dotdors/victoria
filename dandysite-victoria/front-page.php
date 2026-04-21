<?php
/**
 * Front Page Template — Dandysite Victoria
 *
 * Section order:
 *   1. Hero         — full-bleed candidate photo + headline
 *   2. Bio          — Meet the candidate (pulls from About page)
 *   3. Issues       — Positions CPT (provided by site plugin)
 *   4. News         — Recent blog posts (condensed)
 *   5. Endorsements — Endorsements CPT (provided by site plugin)
 *   6. Get Involved — CTA / volunteer / donate
 *
 * Hero layout is controlled by the Page Attributes template dropdown
 * on the page set as the static front page.
 *
 * CPT-driven sections (Issues, Endorsements) silently skip themselves
 * if the site plugin isn't active — safe to deploy before the plugin is ready.
 *
 * To reorder, add, or remove sections on a specific site, use the
 * 'dsp_homepage_sections' filter in the site plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// --- Hero layout ---
$template_map = [
    'page-templates/page-hero-fullbleed.php'  => 'fullbleed',
    'page-templates/page-hero-split-left.php' => 'split-left',
    'page-templates/page-hero-split-right.php'=> 'split-right',
];
$assigned = get_page_template_slug( get_option( 'page_on_front' ) );
$layout   = $template_map[ $assigned ] ?? 'fullbleed';

// --- Section list — filterable by site plugin ---
$sections = apply_filters( 'dsp_homepage_sections', [
    'hero',
    'bio',
    'issues',
    'news',
    'endorsements',
    'get-involved',
] );

get_header();

foreach ( $sections as $section ) :
    switch ( $section ) :

        case 'hero':
            get_template_part( 'template-parts/hero', null, [ 'layout' => $layout ] );
            break;

        case 'bio':
            get_template_part( 'template-parts/section-bio' );
            break;

        case 'issues':
            get_template_part( 'template-parts/section-issues' );
            break;

        case 'news':
            get_template_part( 'template-parts/section-news' );
            break;

        case 'endorsements':
            get_template_part( 'template-parts/section-endorsements' );
            break;

        case 'get-involved':
            get_template_part( 'template-parts/section-get-involved' );
            break;

        default:
            // Allow site plugins to render custom sections
            do_action( 'dsp_homepage_section_' . sanitize_key( $section ) );
            break;

    endswitch;
endforeach;

get_footer();
