<?php
/**
 * Template Part: In the News Section
 *
 * Displays recent posts. Respects the dsp_external_link_behavior option:
 *   'direct'  — card click goes straight to external URL (new tab)
 *   'summary' — card goes to on-site post; post has "Read at [Publication]" button (default)
 *
 * External article fields (set via Article Details meta box):
 *   dsp_publication_name   — source label shown on card
 *   dsp_external_url       — link to original
 *   dsp_pdf_attachment_id  — downloadable PDF (shown on single post, not card)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_count     = (int) get_option( 'dsp_hp_news_count', 4 );
$section_title  = apply_filters( 'dsp_news_title',       __( 'In the News', 'dandysite-victoria' ) );
$view_all_text  = apply_filters( 'dsp_news_view_all',    __( 'View All', 'dandysite-victoria' ) );
$link_behavior  = get_option( 'dsp_external_link_behavior', 'summary' );

// Get selected categories; default to in-the-news only
$news_cats = get_option( 'dsp_hp_news_cats', [ 'in-the-news' ] );
if ( empty( $news_cats ) ) {
    $news_cats = [ 'in-the-news' ];
}

$view_all_url = '';
$first_term = get_term_by( 'slug', $news_cats[0], 'category' );
if ( $first_term ) {
    $view_all_url = get_term_link( $first_term );
}
$view_all_url = apply_filters( 'dsp_news_view_all_url', $view_all_url ?: home_url( '/in-the-news/' ) );

$news_query = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $post_count,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => [[
        'taxonomy' => 'category',
        'field'    => 'slug',
        'terms'    => $news_cats,
        'operator' => 'IN',
    ]],
] );

if ( ! $news_query->have_posts() ) return;
?>

<section class="section-news<?php echo esc_attr( dsp_section_bg_class( 'news' ) ); ?>" id="news">
    <div class="container">

        <h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>

        <div class="news-grid">
            <?php while ( $news_query->have_posts() ) : $news_query->the_post();
                $publication = get_post_meta( get_the_ID(), 'dsp_publication_name', true );
                $ext_url     = get_post_meta( get_the_ID(), 'dsp_external_url', true );

                // Determine card href and target based on behavior setting
                if ( $ext_url && $link_behavior === 'direct' ) {
                    $card_url    = $ext_url;
                    $card_target = ' target="_blank" rel="noopener noreferrer"';
                } else {
                    $card_url    = get_the_permalink();
                    $card_target = '';
                }
                $thumb_class = in_category( 'in-the-news' )
                    ? 'news-card__thumbnail news-card__thumbnail--contain'
                    : 'news-card__thumbnail';
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

                    <?php if ( $publication ) : ?>
                    <div class="news-card__source"><?php echo esc_html( $publication ); ?></div>
                    <?php endif; ?>

                    <h3 class="news-card__title">
                        <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>>
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <?php if ( apply_filters( 'dsp_show_post_date', true, get_the_ID() ) ) : ?>
                    <div class="news-card__date">
                        <?php echo esc_html( get_the_date() ); ?>
                    </div>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>
                       class="btn" style="font-size:0.8rem; padding:0.5em 1.2em;">
                        <?php echo $ext_url && $link_behavior === 'direct'
                            ? esc_html__( 'Read Article', 'dandysite-victoria' )
                            : esc_html__( 'Read More', 'dandysite-victoria' );
                        ?>
                    </a>

                </div>

            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php if ( $view_all_url ) : ?>
        <div class="news-section__footer">
            <a href="<?php echo esc_url( $view_all_url ); ?>" class="btn btn--outline">
                <?php echo esc_html( $view_all_text ); ?>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>
