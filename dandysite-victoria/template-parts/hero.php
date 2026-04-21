<?php
/**
 * Hero Partial
 * Shared hero markup for all hero layout templates.
 * Called via get_template_part( 'template-parts/hero', null, [ 'layout' => $layout ] )
 *
 * Layouts:
 *   'fullbleed'   — full-viewport image, content centered over it
 *   'split-left'  — image left col, content panel right col
 *   'split-right' — image right col, content panel left col
 *
 * Hero image:   Page Featured Image
 * Hero content: dsp_get_hero_meta() — reads meta fields from current page
 * Logos:        dsp_logo_img() — from Appearance → Site Identity
 *
 * TO ADD A NEW LAYOUT: add a case below and corresponding CSS in homepage.css.
 * No other files need to change.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$layout = $args['layout'] ?? 'fullbleed';
$hero   = dsp_get_hero_meta();

// Logo variant: mono-white on dark (fullbleed), mono (dark) on light panel (split)
$has_mono     = (bool) dsp_get_logo_url( 'mono' );
$has_full     = (bool) dsp_get_logo_url( 'full' );
$logo_variant = match( $layout ) {
    'fullbleed' => $has_mono ? 'mono-white' : 'full',
    default     => 'full',  // split layouts: always full color on light panel
};

// =====================================================================
// FULL BLEED LAYOUT
// =====================================================================

if ( $layout === 'fullbleed' ) :
    $classes = [ 'hero', 'hero--fullbleed' ];
    if ( ! $hero['has_image'] ) {
        $classes[] = 'hero--no-image';
    }
    ?>
    <section
        class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
        style="height: <?php echo esc_attr( $hero['height'] ); ?>;"
        aria-label="<?php esc_attr_e( 'Hero', 'dandysite-victoria' ); ?>">

        <?php if ( $hero['has_image'] ) : ?>
            <div class="hero__bg"
                 style="background-image: url('<?php echo esc_url( $hero['image_url'] ); ?>');"
                 role="img"
                 aria-hidden="true"></div>
        <?php endif; ?>

        <div class="hero__overlay" aria-hidden="true"></div>

        <div class="hero__content">
            <?php get_template_part( 'template-parts/hero-inner', null, [ 'hero' => $hero, 'logo_variant' => $logo_variant ] ); ?>
        </div>

    </section>

<?php
// =====================================================================
// SPLIT LAYOUTS (left / right)
// =====================================================================

elseif ( $layout === 'split-left' || $layout === 'split-right' ) :
    $image_side = ( $layout === 'split-left' ) ? 'left' : 'right';
    $classes    = [ 'hero', 'hero--split', 'hero--split-' . $image_side ];
    if ( ! $hero['has_image'] ) {
        $classes[] = 'hero--no-image';
    }
    ?>
    <section
        class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
        style="min-height: <?php echo esc_attr( $hero['height'] ); ?>;"
        aria-label="<?php esc_attr_e( 'Hero', 'dandysite-victoria' ); ?>">

        <div class="hero__image-col">
            <?php if ( $hero['has_image'] ) : ?>
                <img src="<?php echo esc_url( $hero['image_url'] ); ?>"
                     alt=""
                     class="hero__image"
                     loading="eager">
            <?php else : ?>
                <div class="hero__image-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>

        <div class="hero__panel">
            <div class="hero__content">
                <?php get_template_part( 'template-parts/hero-inner', null, [ 'hero' => $hero, 'logo_variant' => $logo_variant ] ); ?>
            </div>
        </div>

    </section>

<?php endif; ?>
