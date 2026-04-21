<?php
/**
 * Template Part: In the News Section
 *
 * Shows recent blog posts — condensed to 3 or 4 cards.
 * Number of posts and section label filterable by site plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_count    = (int) apply_filters( 'dsp_news_count', 4 );
$section_title = apply_filters( 'dsp_news_title',   __( 'In the News', 'dandysite-victoria' ) );
$view_all_text = apply_filters( 'dsp_news_view_all', __( 'View All Articles', 'dandysite-victoria' ) );
$view_all_url  = apply_filters( 'dsp_news_view_all_url', get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) );

$news_query = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $post_count,
    'orderby'        => 'date',
    'order'          => 'DESC',
] );

if ( ! $news_query->have_posts() ) return;
?>

<section class="section-news" id="news">
    <div class="container">

        <h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>

        <div class="news-grid">
            <?php while ( $news_query->have_posts() ) : $news_query->the_post();
                // Check for a custom source meta (useful for press coverage)
                $source = get_post_meta( get_the_ID(), 'dsp_news_source', true );
            ?>
            <article class="news-card">

                <div class="news-card__thumbnail">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'medium', [ 'loading' => 'lazy' ] ); ?>
                        </a>
                    <?php else : ?>
                        <div class="news-card__thumbnail-placeholder">
                            <span aria-hidden="true">&#9650;</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="news-card__body">
                    <?php if ( $source ) : ?>
                    <div class="news-card__source"><?php echo esc_html( $source ); ?></div>
                    <?php endif; ?>

                    <h3 class="news-card__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>

                    <div class="news-card__date">
                        <?php echo esc_html( get_the_date() ); ?>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="btn" style="font-size:0.8rem; padding: 0.5em 1.2em;">
                        <?php esc_html_e( 'Read More', 'dandysite-victoria' ); ?>
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
