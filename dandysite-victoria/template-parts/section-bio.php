<?php
/**
 * Template Part: Bio Section (Meet the Candidate)
 *
 * Expects a page called "About" or similar, configured in Theme Settings.
 *   - Featured Image: candidate photo
 *   - Content: bio text
 *
 * Image display is controlled in Appearance → Homepage Settings:
 *   dsp_hp_bio_image_mode   'featured' (default) | 'none' | 'background'
 *   dsp_hp_bio_image_id     attachment ID — overrides the featured image
 *
 * Falls back gracefully if no about page exists.
 * Site plugin can filter the page slug via 'dsp_bio_page_slug'.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$slug = get_option( 'dsp_bio_page_slug', 'about' );
$slug = apply_filters( 'dsp_bio_page_slug', $slug ); // still filterable by site plugin
$bio_page = get_page_by_path( $slug );

// Nothing to show
if ( ! $bio_page ) return;

$bio_title    = get_the_title( $bio_page );
$bio_content  = apply_filters( 'the_content', $bio_page->post_content );
$bio_link     = get_permalink( $bio_page );

// --- Image mode + optional override ---
$image_mode  = get_option( 'dsp_hp_bio_image_mode', 'featured' ); // featured | none | background
$override_id = (int) get_option( 'dsp_hp_bio_image_id', 0 );

$image_id = $override_id ?: get_post_thumbnail_id( $bio_page );

$bio_image = '';
$bg_url    = '';
if ( $image_id && $image_mode === 'featured' ) {
    $bio_image = wp_get_attachment_image( $image_id, 'large', false, [ 'class' => 'bio-image' ] );
} elseif ( $image_id && $image_mode === 'background' ) {
    $bg_url = wp_get_attachment_image_url( $image_id, 'full' );
}

// Pull quote — stored as post meta 'dsp_bio_pullquote', or first blockquote in content
$pullquote = get_post_meta( $bio_page->ID, 'dsp_bio_pullquote', true );
$section_label = get_option( 'dsp_bio_section_label', '' );
$section_label = apply_filters( 'dsp_bio_section_label', $section_label );
$read_more     = apply_filters( 'dsp_bio_read_more_text', __( 'Read More', 'dandysite-victoria' ) );

$section_classes = 'section-bio';
if ( $bg_url ) {
    $section_classes .= ' section-bio--bg';
} elseif ( ! $bio_image ) {
    $section_classes .= ' section-bio--no-image';
}
?>

<section class="<?php echo esc_attr( $section_classes ); ?>" id="meet-candidate"
    <?php if ( $bg_url ) : ?>style="background-image: url('<?php echo esc_url( $bg_url ); ?>');"<?php endif; ?>>
    <div class="container">

        <?php if ( $bio_image ) : ?>
        <div class="section-bio__image">
            <?php echo $bio_image; ?>
        </div>
        <?php endif; ?>

        <div class="section-bio__text">
        <?php if ( $section_label ) : ?>
            <span class="section-label"><?php echo esc_html( $section_label ); ?></span>
        <?php endif; ?>
            <h2><?php echo esc_html( $bio_title ); ?></h2>

            <div class="section-bio__content">
                <?php echo wp_kses_post( $bio_content ); ?>
            </div>

            <?php if ( $pullquote ) : ?>
            <blockquote class="section-bio__pullquote">
                <?php echo esc_html( $pullquote ); ?>
            </blockquote>
            <?php endif; ?>

        </div>

    </div>
</section>
