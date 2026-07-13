<?php
/**
 * Template Part: Get Involved / CTA Section
 *
 * Call-to-action — campaign (volunteer/donate) or officeholder
 * (speaking/booking) depending on configuration.
 *
 * Heading, text, and buttons are editable in Appearance → Homepage
 * Settings → Get Involved / CTA. Blank heading/text falls back to the
 * theme defaults below. If the button fields have never been saved,
 * the default button set is used. The dsp_cta_* filters still run
 * last, so site plugins can override programmatically if needed.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Heading + text: settings first, theme default if blank
$section_title = get_option( 'dsp_hp_cta_title', '' );
if ( '' === trim( $section_title ) ) {
    $section_title = __( 'Get Involved', 'dandysite-victoria' );
}
$section_title = apply_filters( 'dsp_cta_title', $section_title );

$section_text = get_option( 'dsp_hp_cta_text', '' );
if ( '' === trim( $section_text ) ) {
    $section_text = __( 'Join the movement. Volunteer your time, make a donation, or sign up to stay informed.', 'dandysite-victoria' );
}
$section_text = apply_filters( 'dsp_cta_text', $section_text );

// Buttons: build from settings; a button renders if label + URL are set.
// If no button option has ever been saved, use the theme defaults.
$style_map = [
    'solid'   => 'btn--white',
    'outline' => 'btn--outline-white',
];

$actions = [];
if ( false !== get_option( 'dsp_hp_cta_btn1_label', false ) ) {
    for ( $i = 1; $i <= 3; $i++ ) {
        $label = trim( (string) get_option( "dsp_hp_cta_btn{$i}_label", '' ) );
        $url   = trim( (string) get_option( "dsp_hp_cta_btn{$i}_url", '' ) );
        if ( '' === $label || '' === $url ) continue;
        $style = get_option( "dsp_hp_cta_btn{$i}_style", 'solid' );
        $actions[] = [
            'label' => $label,
            'url'   => $url,
            'style' => $style_map[ $style ] ?? 'btn--white',
        ];
    }
} else {
    $actions = [
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
    ];
}
$actions = apply_filters( 'dsp_cta_actions', $actions );

// Media kit download link (uploaded in Homepage Settings)
$media_kit_id    = (int) get_option( 'dsp_hp_media_kit_id', 0 );
$media_kit_url   = $media_kit_id ? wp_get_attachment_url( $media_kit_id ) : '';
$media_kit_label = apply_filters( 'dsp_media_kit_label', __( 'Download Media Kit', 'dandysite-victoria' ) );
?>

<section class="section-get-involved<?php echo esc_attr( dsp_section_bg_class( 'get-involved' ) ); ?>" id="get-involved">
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

        <?php if ( $media_kit_url ) : ?>
        <p class="get-involved__media-kit">
            <a href="<?php echo esc_url( $media_kit_url ); ?>" download>
                &darr;&nbsp;<?php echo esc_html( $media_kit_label ); ?>
            </a>
        </p>
        <?php endif; ?>

    </div>
</section>
