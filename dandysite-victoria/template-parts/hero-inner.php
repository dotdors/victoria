<?php
/**
 * Hero Inner Content
 * Eyebrow → Logo (optional) → Headline → Tagline → CTA
 *
 * Expected $args:
 *   'hero'         => array from dsp_get_hero_meta()
 *   'logo_variant' => string passed to dsp_logo_img() — 'mono-white' | 'mono' | 'full'
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$hero         = $args['hero']         ?? [];
$logo_variant = $args['logo_variant'] ?? 'full';

// ---- Eyebrow ----
if ( ! empty( $hero['eyebrow'] ) ) : ?>
    <span class="hero__eyebrow"><?php echo esc_html( $hero['eyebrow'] ); ?></span>
<?php endif;

// ---- Logo (optional, between eyebrow and headline) ----
if ( ! empty( $hero['show_logo'] ) && $hero['show_logo'] === '1' ) :
    // Use mono-white variant if white logo requested (dsp_logo_img applies the filter),
    // otherwise full color. dsp_logo_img handles site identity → WP custom logo → text fallback.
    $variant = ( ! empty( $hero['logo_white'] ) && $hero['logo_white'] === '1' )
        ? 'mono-white'
        : 'full';
    ?>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="hero__logo-link">
        <?php dsp_logo_img( $variant, 'hero__logo' ); ?>
    </a>
<?php endif;

// ---- Headline ----
if ( ! empty( $hero['headline'] ) ) : ?>
    <h1 class="hero__headline"><?php echo esc_html( $hero['headline'] ); ?></h1>
<?php endif;

// ---- Tagline ----
if ( ! empty( $hero['tagline'] ) ) : ?>
    <p class="hero__tagline"><?php echo esc_html( $hero['tagline'] ); ?></p>
<?php endif;

// ---- CTA ----
if ( ! empty( $hero['cta_text'] ) && ! empty( $hero['cta_url'] ) ) : ?>
    <a href="<?php echo esc_url( $hero['cta_url'] ); ?>" class="hero__cta btn btn--white">
        <?php echo esc_html( $hero['cta_text'] ); ?>
    </a>
<?php endif;
