<?php
/**
 * Template Part: Endorsements Section
 *
 * Displays endorsements from the dsp_endorsement CPT (registered in Victoria).
 */

if ( ! defined( 'ABSPATH' ) ) exit;


$limit            = (int) get_option( 'dsp_hp_endorsements_count', 6 );
$endorsements     = dsp_get_endorsements( true, $limit ); // featured only, up to limit
if ( empty( $endorsements ) ) return;

$section_title    = apply_filters( 'dsp_endorsements_title',    __( 'Endorsements', 'dandysite-victoria' ) );
$section_subtitle = apply_filters( 'dsp_endorsements_subtitle', __( 'Trusted Community Leaders', 'dandysite-victoria' ) );

// Layout: 'grid' (side by side, default) or 'carousel' (one at a time, auto-cycling)
$layout     = get_option( 'dsp_hp_endorsements_layout', 'grid' );
$grid_class = 'endorsements-grid';
if ( $layout === 'carousel' && count( $endorsements ) > 1 ) {
    $grid_class .= ' endorsements-grid--carousel';
}
?>

<section class="section-endorsements<?php echo esc_attr( dsp_section_bg_class( 'endorsements' ) ); ?>" id="endorsements">
    <div class="container">

        <h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>
        <p class="section-subtitle"><?php echo esc_html( $section_subtitle ); ?></p>

        <div class="<?php echo esc_attr( $grid_class ); ?>" data-interval="7000">
            <?php foreach ( $endorsements as $endorsement ) :
                $quote     = get_post_meta( $endorsement->ID, 'dsp_endorser_quote', true );
                $title_org = get_post_meta( $endorsement->ID, 'dsp_endorser_title_org', true );
                $photo_url = get_the_post_thumbnail_url( $endorsement->ID, 'thumbnail' );
                $link      = get_post_meta( $endorsement->ID, 'dsp_endorser_link', true );
            ?>
            <div class="endorsement-card">

                <?php if ( $quote ) : ?>
                <p class="endorsement-card__quote"><?php echo esc_html( $quote ); ?></p>
                <?php endif; ?>

                <div class="endorsement-card__byline">
                    <?php if ( $photo_url ) : ?>
                    <img class="endorsement-card__photo"
                         src="<?php echo esc_url( $photo_url ); ?>"
                         alt="<?php echo esc_attr( get_the_title( $endorsement ) ); ?>"
                         loading="lazy">
                    <?php endif; ?>
                    <div>
                        <div class="endorsement-card__name">
                            <?php if ( $link ) : ?>
                            <a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo esc_html( get_the_title( $endorsement ) ); ?>
                            </a>
                            <?php else : ?>
                            <?php echo esc_html( get_the_title( $endorsement ) ); ?>
                            <?php endif; ?>
                        </div>
                        <?php if ( $title_org ) : ?>
                        <div class="endorsement-card__title-org">
                            <?php echo esc_html( $title_org ); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
