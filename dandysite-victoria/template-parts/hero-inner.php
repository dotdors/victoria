<?php
/**
 * Hero Inner Content
 * Logo, headline, tagline, CTA button.
 * Shared by all hero layouts via hero.php.
 *
 * Expected $args:
 *   'hero'         => array from dsp_get_hero_meta()
 *   'logo_variant' => string passed to dsp_logo_img()
 *                     'mono-white' | 'mono' | 'full'
 *
 * Logo fallback chain for 'full':
 *   dsp_logo_full → dsp_logo_mono (dark) → WP custom logo → site name text
 *   This prevents falling back to a white logo file set in the Customizer.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$hero         = $args['hero']         ?? [];
$logo_variant = $args['logo_variant'] ?? 'full';

// Resolve the best available logo variant before rendering.
// If the requested variant isn't uploaded, try the next best option
// rather than letting dsp_logo_img() fall through to WP custom logo
// (which may be a white/mono file inappropriate for the current context).
$has_full      = (bool) dsp_get_logo_url( 'full' );
$has_mono      = (bool) dsp_get_logo_url( 'mono' );
$resolved_variant = null;

if ( $logo_variant === 'mono-white' ) {
    // Fullbleed dark bg: mono-white preferred, full color acceptable
    if ( $has_mono ) {
        $resolved_variant = 'mono-white';
    } elseif ( $has_full ) {
        $resolved_variant = 'full';
    }
} else {
    // Split light panel: full color preferred, mono (dark) as fallback
    // Never use mono-white on a light background
    if ( $has_full ) {
        $resolved_variant = 'full';
    } elseif ( $has_mono ) {
        $resolved_variant = 'mono'; // dark, not inverted — readable on light bg
    }
}
?>

<?php if ( $resolved_variant ) : ?>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="hero__logo-link">
        <?php dsp_logo_img( $resolved_variant, 'hero__logo' ); ?>
    </a>
<?php endif; ?>

<?php if ( ! empty( $hero['headline'] ) ) : ?>
    <h1 class="hero__headline">
        <?php echo esc_html( $hero['headline'] ); ?>
    </h1>
<?php endif; ?>

<?php if ( ! empty( $hero['tagline'] ) ) : ?>
    <p class="hero__tagline">
        <?php echo esc_html( $hero['tagline'] ); ?>
    </p>
<?php endif; ?>

<?php if ( ! empty( $hero['cta_text'] ) && ! empty( $hero['cta_url'] ) ) : ?>
    <a href="<?php echo esc_url( $hero['cta_url'] ); ?>" class="hero__cta">
        <?php echo esc_html( $hero['cta_text'] ); ?>
    </a>
<?php endif; ?>
