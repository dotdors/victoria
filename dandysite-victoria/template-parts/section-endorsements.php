<?php
/**
 * Template Part: Endorsements Section
 *
 * Displays endorsements from the dshft_endorsement CPT.
 * Gracefully hidden when the CPT / helper function doesn't exist.
 *
 * Requires: dshft_get_endorsements() from ds-hawkfortexas plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'dshft_get_endorsements' ) ) return;

$endorsements = dshft_get_endorsements( true ); // featured only on homepage
if ( empty( $endorsements ) ) return;

$section_title    = apply_filters( 'dsp_endorsements_title',    __( 'Endorsements', 'dandysite-victoria' ) );
$section_subtitle = apply_filters( 'dsp_endorsements_subtitle', __( 'Trusted Leaders Supporting Hawk Dunlap', 'dandysite-victoria' ) );
?>

<section class="section-endorsements" id="endorsements">
    <div class="container">

        <h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>
        <p class="section-subtitle"><?php echo esc_html( $section_subtitle ); ?></p>

        <div class="endorsements-grid">
            <?php foreach ( $endorsements as $endorsement ) :
                $quote     = get_post_meta( $endorsement->ID, 'dshft_endorser_quote', true );
                $title_org = get_post_meta( $endorsement->ID, 'dshft_endorser_title_org', true );
                $photo_url = get_the_post_thumbnail_url( $endorsement->ID, 'thumbnail' );
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
                            <?php echo esc_html( get_the_title( $endorsement ) ); ?>
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
