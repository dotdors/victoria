<?php
/**
 * Archive/Index — Endorsements
 * Full endorsements listing page (all, not just featured).
 * Linked from the homepage endorsements section if desired.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$endorsements = dsp_get_endorsements( false ); // all, not just featured
?>

<main class="archive-endorsements" id="main">

    <header class="archive-endorsements__header">
        <div class="container">
            <h1><?php esc_html_e( 'Endorsements', 'dandysite-victoria' ); ?></h1>
            <p><?php esc_html_e( 'Community and civic leaders who support our campaign.', 'dandysite-victoria' ); ?></p>
        </div>
    </header>

    <section class="section-endorsements section-endorsements--full">
        <div class="container">

            <?php if ( ! empty( $endorsements ) ) : ?>
            <div class="endorsements-grid">
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

            <?php else : ?>
            <p><?php esc_html_e( 'Endorsements coming soon.', 'dandysite-victoria' ); ?></p>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer();
