<?php
/**
 * Front Page Template — Dandysite Victoria
 *
 * Section order (reorder via CSS `order` property in site plugin):
 *   1. Hero         — always shown
 *   2. Bio          — toggle in Homepage Settings
 *   3. Issues       — toggle in Homepage Settings
 *   4. Articles     — toggle in Homepage Settings (categories configurable)
 *   5. In the News  — toggle in Homepage Settings (categories configurable)
 *   6. Endorsements — toggle in Homepage Settings
 *   7. Get Involved — toggle in Homepage Settings
 *
 * To reorder sections on a specific site without touching this file,
 * use CSS order properties in the site plugin:
 *   .section-news { order: 3; }
 *   .section-articles { order: 4; }
 *
 * To add a custom section, filter dsp_homepage_sections and handle
 * via the dsp_homepage_section_{slug} action hook.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Hero layout from page template assignment
$template_map = [
    'page-templates/page-hero-fullbleed.php'   => 'fullbleed',
    'page-templates/page-hero-split-left.php'  => 'split-left',
    'page-templates/page-hero-split-right.php' => 'split-right',
];
$assigned = get_page_template_slug( get_option( 'page_on_front' ) );
$layout   = $template_map[ $assigned ] ?? 'fullbleed';

// Build section list from Homepage Settings — hero always on
$sections = [ 'hero' ];

if ( get_option( 'dsp_hp_show_bio',          '1' ) === '1' ) $sections[] = 'bio';
if ( get_option( 'dsp_hp_show_issues',        '1' ) === '1' ) $sections[] = 'issues';
if ( get_option( 'dsp_hp_show_articles',      '1' ) === '1' ) $sections[] = 'articles';
if ( get_option( 'dsp_hp_show_news',          '1' ) === '1' ) $sections[] = 'news';
if ( get_option( 'dsp_hp_show_endorsements',  '1' ) === '1' ) $sections[] = 'endorsements';
if ( get_option( 'dsp_hp_show_cta',           '1' ) === '1' ) $sections[] = 'get-involved';

// Allow site plugin to add, remove, or reorder
$sections = apply_filters( 'dsp_homepage_sections', $sections );

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

        case 'articles':
            get_template_part( 'template-parts/section-articles' );
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
            do_action( 'dsp_homepage_section_' . sanitize_key( $section ) );
            break;

    endswitch;
endforeach;

get_footer();
