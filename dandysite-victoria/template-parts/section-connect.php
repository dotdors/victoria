<?php
/**
 * Template Part: Contact / Connect Section
 *
 * Heading, short paragraph, prominent email link, and social icons.
 * All content configured in Appearance → Homepage Settings.
 *
 * Options:
 *   dsp_hp_connect_heading   string  default 'Get in Touch'
 *   dsp_hp_connect_text      string  short paragraph
 *   dsp_hp_connect_email     string  email address (rendered as mailto link)
 *
 * Social icons reuse the [ds_socials] platform registry — any platform
 * with a saved URL in Theme Settings → Social Links appears automatically.
 *
 * Copy-to-clipboard fallback: mailto: links do nothing if the visitor
 * has no mail client configured. The data-mailto-copy attribute below
 * is picked up by assets/js/main.js to copy the address to the
 * clipboard on click (in addition to, not instead of, the mailto
 * navigation) and show a brief confirmation toast.
 *
 * Future: optional contact form can replace the email link — planned,
 * starting with the mailto + clipboard-fallback approach.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$heading = get_option( 'dsp_hp_connect_heading', __( 'Get in Touch', 'dandysite-victoria' ) );
$text    = get_option( 'dsp_hp_connect_text', '' );
$email   = get_option( 'dsp_hp_connect_email', '' );

$heading = apply_filters( 'dsp_connect_heading', $heading );
$text    = apply_filters( 'dsp_connect_text', $text );
$email   = apply_filters( 'dsp_connect_email', $email );

$socials = function_exists( 'dsp_socials_shortcode' ) ? dsp_socials_shortcode( [ 'size' => 32 ] ) : '';

// Nothing meaningful to show
if ( ! $email && ! $socials && ! $text ) return;
?>

<section class="section-connect<?php echo esc_attr( dsp_section_bg_class( 'connect' ) ); ?>" id="connect">
    <div class="container">

        <h2 class="section-title"><?php echo esc_html( $heading ); ?></h2>

        <?php if ( $text ) : ?>
        <p class="section-connect__text"><?php echo esc_html( $text ); ?></p>
        <?php endif; ?>

        <?php if ( $email ) : ?>
        <a class="section-connect__email"
           href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"
           data-mailto-copy="<?php echo esc_attr( $email ); ?>"
           title="<?php echo esc_attr( sprintf( __( 'Opens your email app — or click to copy: %s', 'dandysite-victoria' ), $email ) ); ?>">
            <?php echo esc_html( antispambot( $email ) ); ?>
        </a>
        <?php endif; ?>

        <?php if ( $socials ) : ?>
        <div class="section-connect__socials">
            <?php echo $socials; // Sanitized within dsp_socials_shortcode ?>
        </div>
        <?php endif; ?>

    </div>
</section>
