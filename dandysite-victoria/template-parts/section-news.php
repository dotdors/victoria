<?php
/**
 * Template Part: In the News Section
 *
 * Displays recent posts from the configured news categories. Sticky
 * posts within those categories float to the front (dsp_sticky_first_query).
 *
 * Card rendering is shared with the Media Archive page template — see
 * template-parts/card-news.php. Card layout filters:
 *   dsp_news_card_category_eyebrow (bool, default false)
 *   dsp_news_card_date_format      (string, '' = site default)
 *
 * View All link resolution: a published page using the Media Archive
 * template wins; otherwise the first configured category's archive.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_count     = (int) get_option( 'dsp_hp_news_count', 4 );
$section_title  = apply_filters( 'dsp_news_title',       __( 'In the News', 'dandysite-victoria' ) );
$view_all_text  = apply_filters( 'dsp_news_view_all',    __( 'View All', 'dandysite-victoria' ) );
$link_behavior  = get_option( 'dsp_external_link_behavior', 'summary' );

$card_category_eyebrow = (bool) apply_filters( 'dsp_news_card_category_eyebrow', false );
$card_date_format      = apply_filters( 'dsp_news_card_date_format', '' );

$news_cats = dsp_get_news_categories();

// View all: Media Archive page (if one exists) > first category archive
$view_all_url = '';
$archive_page = get_pages( [
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'page-templates/page-media-archive.php',
    'number'     => 1,
] );
if ( $archive_page ) {
    $view_all_url = get_permalink( $archive_page[0] );
} else {
    $first_term = get_term_by( 'slug', $news_cats[0], 'category' );
    if ( $first_term ) {
        $view_all_url = get_term_link( $first_term );
    }
}
$view_all_url = apply_filters( 'dsp_news_view_all_url', $view_all_url ?: home_url( '/in-the-news/' ) );

$news_query = dsp_sticky_first_query( [
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
                get_template_part( 'template-parts/card-news', null, [
                    'link_behavior'    => $link_behavior,
                    'contain'          => true, // every post here matched the news categories — always logo treatment
                    'category_eyebrow' => $card_category_eyebrow,
                    'date_format'      => $card_date_format,
                ] );
            endwhile; wp_reset_postdata(); ?>
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
