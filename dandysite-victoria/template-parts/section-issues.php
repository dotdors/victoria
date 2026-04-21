<?php
/**
 * Template Part: Issues / Positions Section
 *
 * Displays positions from the dshft_position CPT (provided by ds-[sitename] plugin).
 * Gracefully hidden when the CPT or helper function doesn't exist.
 *
 * Requires: dshft_get_positions() from ds-hawkfortexas plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Bail silently if the site plugin hasn't registered this CPT yet
if ( ! function_exists( 'dshft_get_positions' ) ) return;

$positions = dshft_get_positions( true ); // homepage-flagged only
if ( empty( $positions ) ) return;

$section_title    = apply_filters( 'dsp_issues_title',    __( 'Issues & Positions', 'dandysite-victoria' ) );
$section_subtitle = apply_filters( 'dsp_issues_subtitle', __( 'Where I Stand', 'dandysite-victoria' ) );
?>

<section class="section-issues" id="issues">
    <div class="container">

        <h2 class="section-title section-title--white"><?php echo esc_html( $section_title ); ?></h2>
        <p class="section-subtitle section-subtitle--white"><?php echo esc_html( $section_subtitle ); ?></p>

        <div class="issues-grid">
            <?php foreach ( $positions as $position ) :
                $icon    = get_post_meta( $position->ID, 'dshft_position_icon', true );
                $summary = get_post_meta( $position->ID, 'dshft_position_summary', true )
                           ?: $position->post_excerpt;
                $link    = get_permalink( $position );
            ?>
            <div class="issue-card">
                <?php if ( $icon ) : ?>
                <div class="issue-card__icon">
                    <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
                </div>
                <?php endif; ?>

                <h3 class="issue-card__title">
                    <?php echo esc_html( get_the_title( $position ) ); ?>
                </h3>

                <?php if ( $summary ) : ?>
                <p class="issue-card__summary">
                    <?php echo esc_html( $summary ); ?>
                </p>
                <?php endif; ?>

                <a href="<?php echo esc_url( $link ); ?>" class="issue-card__link">
                    <?php esc_html_e( 'Read More', 'dandysite-victoria' ); ?> &rarr;
                </a>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
