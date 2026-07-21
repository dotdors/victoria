<?php
/**
 * Template Name: Media Archive
 *
 * Paginated archive of the same posts the homepage In the News/Media
 * section shows — reads the identical category settings and renders the
 * identical cards (template-parts/card-news.php). Create a page, assign
 * this template, and the homepage section's View All button links to it
 * automatically.
 *
 * Page title is the H1; any page content renders as an intro above the
 * grid. Posts are chronological (stickies float on the homepage, not here).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$news_cats      = dsp_get_news_categories();
$link_behavior  = get_option( 'dsp_external_link_behavior', 'summary' );

$card_category_eyebrow = (bool) apply_filters( 'dsp_news_card_category_eyebrow', false );
$card_date_format      = apply_filters( 'dsp_news_card_date_format', '' );

$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$media_query = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => (int) get_option( 'posts_per_page', 12 ),
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => [[
        'taxonomy' => 'category',
        'field'    => 'slug',
        'terms'    => $news_cats,
        'operator' => 'IN',
    ]],
] );
?>

<div class="archive-container media-archive">
    <div class="container">

        <header class="archive-header">
            <h1 class="archive-title"><?php the_title(); ?></h1>
            <?php
            while ( have_posts() ) : the_post();
                $intro = trim( get_the_content() );
                if ( '' !== $intro ) {
                    echo '<div class="media-archive__intro">' . apply_filters( 'the_content', $intro ) . '</div>';
                }
            endwhile;
            wp_reset_postdata();
            ?>
        </header>

        <?php if ( $media_query->have_posts() ) : ?>

            <div class="news-grid">
                <?php while ( $media_query->have_posts() ) : $media_query->the_post();
                    get_template_part( 'template-parts/card-news', null, [
                        'link_behavior'    => $link_behavior,
                        'contain'          => true,
                        'category_eyebrow' => $card_category_eyebrow,
                        'date_format'      => $card_date_format,
                    ] );
                endwhile; wp_reset_postdata(); ?>
            </div>

            <?php if ( $media_query->max_num_pages > 1 ) : ?>
            <nav class="media-archive__pagination" aria-label="<?php esc_attr_e( 'Media archive pagination', 'dandysite-victoria' ); ?>">
                <?php echo paginate_links( [
                    'total'   => $media_query->max_num_pages,
                    'current' => $paged,
                ] ); ?>
            </nav>
            <?php endif; ?>

        <?php else : ?>
            <p><?php esc_html_e( 'No coverage yet.', 'dandysite-victoria' ); ?></p>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
