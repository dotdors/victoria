<?php
/**
 * Template Part: Get Involved / CTA Section
 *
 * Campaign call-to-action — volunteer, donate, sign up.
 * Text and URLs filterable by site plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$section_title = apply_filters( 'dsp_cta_title',       __( 'Get Involved', 'dandysite-victoria' ) );
$section_text  = apply_filters( 'dsp_cta_text',        __( 'Join the movement. Volunteer your time, make a donation, or sign up to stay informed.', 'dandysite-victoria' ) );

$actions = apply_filters( 'dsp_cta_actions', [
    [
        'label'   => __( 'Volunteer', 'dandysite-victoria' ),
        'url'     => '#volunteer',
        'style'   => 'btn--white',
    ],
    [
        'label'   => __( 'Donate', 'dandysite-victoria' ),
        'url'     => '#donate',
        'style'   => 'btn--outline-white',
    ],
    [
        'label'   => __( 'Stay Informed', 'dandysite-victoria' ),
        'url'     => '#signup',
        'style'   => 'btn--white',
    ],
] );
?>

<section class="section-get-involved" id="get-involved">
    <div class="container">

        <h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>
        <p><?php echo esc_html( $section_text ); ?></p>

        <?php if ( ! empty( $actions ) ) : ?>
        <div class="get-involved-actions">
            <?php foreach ( $actions as $action ) :
                $style = isset( $action['style'] ) ? ' ' . esc_attr( $action['style'] ) : '';
            ?>
            <a href="<?php echo esc_url( $action['url'] ); ?>"
               class="btn<?php echo $style; ?>">
                <?php echo esc_html( $action['label'] ); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
