<?php
/**
 * Template Part: Articles / Op-Eds Section
 *
 * Shows posts from categories selected in Homepage Settings.
 * Default: all categories except "in-the-news".
 *
 * Respects dsp_external_link_behavior for external article links.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_count    = (int) get_option( 'dsp_hp_articles_count', 4 );
$section_title = apply_filters( 'dsp_articles_title', __( 'Articles & Op-Eds', 'dandysite-victoria' ) );
$view_all_text = apply_filters( 'dsp_articles_view_all', __( 'View All Articles', 'dandysite-victoria' ) );
$link_behavior = get_option( 'dsp_external_link_behavior', 'summary' );

// Get selected categories; default to all except in-the-news
$selected_cats = get_option( 'dsp_hp_articles_cats', [] );

if ( empty( $selected_cats ) ) {
    // Default: all categories except in-the-news
    $all_cats = get_categories( [ 'hide_empty' => true, 'fields' => 'slugs' ] );
    $selected_cats = array_values( array_diff( $all_cats, [ 'in-the-news' ] ) );
}

if ( empty( $selected_cats ) ) return;

$articles_query = dsp_sticky_first_query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $post_count,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => [[
        'taxonomy' => 'category',
        'field'    => 'slug',
        'terms'    => $selected_cats,
        'operator' => 'IN',
    ]],
] );

if ( ! $articles_query->have_posts() ) return;

// View all URL — Homepage Settings value wins; default is the blog page,
// falling back to /blog/. Filter runs last for programmatic overrides.
$view_all_url = trim( (string) get_option( 'dsp_hp_articles_view_all_url', '' ) );
if ( '' === $view_all_url ) {
    $blog_page_id = get_option( 'page_for_posts' );
    $view_all_url = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
}
$view_all_url = apply_filters( 'dsp_articles_view_all_url', $view_all_url );
?>

<section class="section-articles<?php echo esc_attr( dsp_section_bg_class( 'articles' ) ); ?>" id="articles">
    <div class="container">

        <h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>

        <div class="news-grid">
            <?php while ( $articles_query->have_posts() ) : $articles_query->the_post();
                $publication = get_post_meta( get_the_ID(), 'dsp_publication_name', true );
                $ext_url     = get_post_meta( get_the_ID(), 'dsp_external_url', true );

                if ( $ext_url && $link_behavior === 'direct' ) {
                    $card_url    = $ext_url;
                    $card_target = ' target="_blank" rel="noopener noreferrer"';
                } else {
                    $card_url    = get_the_permalink();
                    $card_target = '';
                }
            ?>
            <article class="news-card<?php echo $ext_url ? ' news-card--external' : ''; ?>">

                <div class="news-card__thumbnail">
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
                    <?php else :
                        // Show category as label if no publication
                        $cats = get_the_category();
                        if ( ! empty( $cats ) ) : ?>
                        <div class="news-card__source"><?php echo esc_html( $cats[0]->name ); ?></div>
                        <?php endif;
                    endif; ?>

                    <h3 class="news-card__title">
                        <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>>
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <?php dsp_card_blurb(); ?>

                    <?php if ( apply_filters( 'dsp_show_post_date', true, get_the_ID() ) ) : ?>
                    <div class="news-card__date">
                        <?php echo esc_html( get_the_date() ); ?>
                    </div>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>
                       class="btn" style="font-size:0.8rem; padding:0.5em 1.2em;">
                        <?php echo ( $ext_url && $link_behavior === 'direct' )
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
