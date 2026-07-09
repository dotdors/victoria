<?php
/**
 * Front Page Template — Dandysite Victoria
 *
 * Sections (visibility toggled in Appearance → Homepage Settings):
 *   Hero          — always shown, pinned to the top
 *   Bio           — Meet the candidate
 *   Issues        — Positions CPT
 *   Articles      — Op-eds / written work (categories configurable)
 *   In the News   — Press coverage (categories configurable)
 *   Endorsements  — Endorsements CPT (grid or carousel)
 *   Get Involved  — CTA buttons
 *   Connect       — Contact heading, email, social links
 *
 * ORDERING: Sections are wrapped in .homepage-sections (flex column) and
 * each carries an inline CSS `order` value from Homepage Settings.
 * The hero is order 0 and always first. Site plugins can still override
 * with CSS (higher specificity or !important) or the filters below.
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
if ( get_option( 'dsp_hp_show_connect',       '1' ) === '1' ) $sections[] = 'connect';

// Allow site plugin to add, remove, or reorder
$sections = apply_filters( 'dsp_homepage_sections', $sections );

// --- Section order (CSS order values, editable in Homepage Settings) ---
$order_defaults = dsp_hp_section_order_defaults();
$order_css = ".homepage-sections{display:flex;flex-direction:column;}";
foreach ( $order_defaults as $key => $default ) {
    $order    = (int) get_option( 'dsp_hp_order_' . str_replace( '-', '_', $key ), $default );
    $selector = ( $key === 'hero' ) ? '.homepage-sections > .hero' : '.homepage-sections > .section-' . $key;
    $order_css .= $selector . '{order:' . $order . ';}';
}
$order_css = apply_filters( 'dsp_homepage_order_css', $order_css );

get_header();
?>
<style id="dsp-section-order"><?php echo strip_tags( $order_css ); ?></style>
<div class="homepage-sections">
<?php
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

        case 'connect':
            get_template_part( 'template-parts/section-connect' );
            break;

        default:
            do_action( 'dsp_homepage_section_' . sanitize_key( $section ) );
            break;

    endswitch;
endforeach;
?>
</div><!-- .homepage-sections -->
<?php
get_footer();
