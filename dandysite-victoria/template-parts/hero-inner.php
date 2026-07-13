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

// ---- Body content (page blocks) — FRONT PAGE ONLY ----
// On the front page, the page's block content is otherwise unused
// (front-page.php renders the homepage sections), so if content
// exists, render it in the hero under the headline/tagline.
// Scoped to is_front_page(): the standalone hero page templates
// already output the_content() below the hero, and rendering it
// here too would duplicate it.
if ( is_front_page() && ! empty( $hero['post_id'] ) ) :
    $hero_page = get_post( $hero['post_id'] );
    if ( $hero_page && '' !== trim( $hero_page->post_content ) ) : ?>
        <div class="hero__body">
            <?php echo apply_filters( 'the_content', $hero_page->post_content ); ?>
        </div>
    <?php endif;
endif;

// ---- CTA ----
if ( ! empty( $hero['cta_text'] ) && ! empty( $hero['cta_url'] ) ) : ?>
    <a href="<?php echo esc_url( $hero['cta_url'] ); ?>" class="hero__cta btn btn--white">
        <?php echo esc_html( $hero['cta_text'] ); ?>
    </a>
<?php endif;
