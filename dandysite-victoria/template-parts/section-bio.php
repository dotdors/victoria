<?php
/**
 * Template Part: Bio Section (Meet the Candidate)
 *
 * Expects a page called "About" or "Meet Hawk" with:
 *   - Featured Image: candidate photo
 *   - Content: bio text
 *
 * Falls back gracefully if no about page exists.
 * Site plugin can filter the page slug via 'dsp_bio_page_slug'.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$slug = apply_filters( 'dsp_bio_page_slug', 'about' );
$bio_page = get_page_by_path( $slug );

// Nothing to show
if ( ! $bio_page ) return;

$bio_title    = get_the_title( $bio_page );
$bio_content  = apply_filters( 'the_content', $bio_page->post_content );
$bio_image    = get_the_post_thumbnail( $bio_page, 'large', [ 'class' => 'bio-image' ] );
$bio_link     = get_permalink( $bio_page );

// Pull quote — stored as post meta 'dsp_bio_pullquote', or first blockquote in content
$pullquote = get_post_meta( $bio_page->ID, 'dsp_bio_pullquote', true );
$section_label = apply_filters( 'dsp_bio_section_label', __( 'About the Candidate', 'dandysite-victoria' ) );
$read_more     = apply_filters( 'dsp_bio_read_more_text', __( 'Read More', 'dandysite-victoria' ) );
?>

<section class="section-bio" id="meet-hawk">
    <div class="container">

        <?php if ( $bio_image ) : ?>
        <div class="section-bio__image">
            <?php echo $bio_image; ?>
        </div>
        <?php endif; ?>

        <div class="section-bio__text">
            <span class="section-label"><?php echo esc_html( $section_label ); ?></span>
            <h2><?php echo esc_html( $bio_title ); ?></h2>

            <div class="section-bio__content">
                <?php echo wp_kses_post( $bio_content ); ?>
            </div>

            <?php if ( $pullquote ) : ?>
            <blockquote class="section-bio__pullquote">
                <?php echo esc_html( $pullquote ); ?>
            </blockquote>
            <?php endif; ?>

            <a href="<?php echo esc_url( $bio_link ); ?>" class="btn btn--outline">
                <?php echo esc_html( $read_more ); ?>
            </a>
        </div>

    </div>
</section>
