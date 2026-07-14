<?php
/**
 * Template Part: News Card (shared)
 *
 * Used by the In the News/Media homepage section and the Media Archive
 * page template so cards render identically in both places.
 *
 * Args:
 *   link_behavior    'direct' | 'summary' — dsp_external_link_behavior
 *   contain          bool — logo treatment (contain) for the thumbnail
 *   category_eyebrow bool — show the primary category as the eyebrow and
 *                    move the publication into a meta line beside the date
 *   date_format      string — PHP date format ('' = site default)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$a = wp_parse_args( $args ?? [], [
    'link_behavior'    => 'summary',
    'contain'          => false,
    'category_eyebrow' => false,
    'date_format'      => '',
] );

$publication = get_post_meta( get_the_ID(), 'dsp_publication_name', true );
$ext_url     = get_post_meta( get_the_ID(), 'dsp_external_url', true );

// Card href/target per External Article Behavior
if ( $ext_url && $a['link_behavior'] === 'direct' ) {
    $card_url    = $ext_url;
    $card_target = ' target="_blank" rel="noopener noreferrer"';
} else {
    $card_url    = get_the_permalink();
    $card_target = '';
}

$thumb_class = $a['contain']
    ? 'news-card__thumbnail news-card__thumbnail--contain'
    : 'news-card__thumbnail';

// Eyebrow: category (when enabled) or publication (default)
$eyebrow = '';
if ( $a['category_eyebrow'] ) {
    $cats = get_the_category();
    if ( $cats ) {
        $eyebrow = $cats[0]->name;
    }
} elseif ( $publication ) {
    $eyebrow = $publication;
}

$show_date = apply_filters( 'dsp_show_post_date', true, get_the_ID() );
$date_out  = $show_date ? get_the_date( $a['date_format'] ) : '';
?>
<article class="news-card<?php echo $ext_url ? ' news-card--external' : ''; ?>">

    <div class="<?php echo esc_attr( $thumb_class ); ?>">
        <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>>
                <?php the_post_thumbnail( 'medium', [ 'loading' => 'lazy' ] ); ?>
            </a>
        <?php else : ?>
            <div class="news-card__thumbnail-placeholder">
                <span aria-hidden="true">&#9650;</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="news-card__body">

        <?php if ( $eyebrow ) : ?>
        <div class="news-card__source"><?php echo esc_html( $eyebrow ); ?></div>
        <?php endif; ?>

        <h3 class="news-card__title">
            <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>>
                <?php the_title(); ?>
            </a>
        </h3>

        <?php dsp_card_blurb(); ?>

        <?php if ( $a['category_eyebrow'] ) : ?>
            <?php if ( $publication || $date_out ) : ?>
            <div class="news-card__meta">
                <?php if ( $publication ) : ?>
                    <span class="news-card__meta-source"><?php echo esc_html( $publication ); ?></span>
                <?php endif; ?>
                <?php if ( $date_out ) : ?>
                    <span class="news-card__date"><?php echo esc_html( $date_out ); ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php elseif ( $date_out ) : ?>
            <div class="news-card__date">
                <?php echo esc_html( $date_out ); ?>
            </div>
        <?php endif; ?>

        <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>
           class="btn" style="font-size:0.8rem; padding:0.5em 1.2em;">
            <?php echo $ext_url && $a['link_behavior'] === 'direct'
                ? esc_html__( 'Read Article', 'dandysite-victoria' )
                : esc_html__( 'Read More', 'dandysite-victoria' );
            ?>
        </a>

    </div>

</article>
