<?php
/**
 * Template Part: Issues / Positions Section
 *
 * Displays positions from the dsp_position CPT (registered in Victoria).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$positions = dsp_get_positions( true ); // homepage-flagged only
if ( empty( $positions ) ) return;

$section_title    = apply_filters( 'dsp_issues_title',    __( 'Issues & Positions', 'dandysite-victoria' ) );
$section_subtitle = apply_filters( 'dsp_issues_subtitle', __( 'Where I Stand', 'dandysite-victoria' ) );
?>

<section class="section-issues<?php echo esc_attr( dsp_section_bg_class( 'issues' ) ); ?>" id="issues">
    <div class="container">

        <h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>
        <p class="section-subtitle"><?php echo esc_html( $section_subtitle ); ?></p>

        <div class="issues-grid">
            <?php foreach ( $positions as $position ) :
                $icon    = get_post_meta( $position->ID, 'dsp_position_icon', true );
                $summary = get_post_meta( $position->ID, 'dsp_position_summary', true )
                           ?: $position->post_excerpt;
                $read_more_link = get_post_meta( $position->ID, 'dsp_position_link', true )
                                  ?: get_permalink( $position );
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

                <a href="<?php echo esc_url( $read_more_link ); ?>" class="issue-card__link"><?php esc_html_e( 'Read More', 'dandysite-victoria' ); ?> &rarr;</a>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
