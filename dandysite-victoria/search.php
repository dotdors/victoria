<?php
/**
 * Search Results — Dandysite Victoria
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$search_query = get_search_query();
?>

<div class="container archive-container">

    <header class="archive-header">
        <?php if ( $search_query ) : ?>
            <h1 class="archive-title">
                <?php printf(
                    esc_html__( 'Results for: %s', 'dandysite-victoria' ),
                    '<span>' . esc_html( $search_query ) . '</span>'
                ); ?>
            </h1>
        <?php else : ?>
            <h1 class="archive-title"><?php esc_html_e( 'Search', 'dandysite-victoria' ); ?></h1>
        <?php endif; ?>

        <?php if ( have_posts() ) :
            global $wp_query;
            $found = (int) $wp_query->found_posts;
            ?>
            <p class="archive-description">
                <?php printf(
                    esc_html( _n( '%s result', '%s results', $found, 'dandysite-victoria' ) ),
                    '<strong>' . number_format_i18n( $found ) . '</strong>'
                ); ?>
            </p>
        <?php endif; ?>
    </header>

    <?php if ( have_posts() ) : ?>

        <div class="news-grid">
            <?php while ( have_posts() ) : the_post();

                $publication   = get_post_meta( get_the_ID(), 'dsp_publication_name', true );
                $ext_url       = get_post_meta( get_the_ID(), 'dsp_external_url', true );
                $link_behavior = get_option( 'dsp_external_link_behavior', 'summary' );
                $show_date     = apply_filters( 'dsp_show_post_date', true, get_the_ID() );

                if ( $ext_url && $link_behavior === 'direct' ) {
                    $card_url    = $ext_url;
                    $card_target = ' target="_blank" rel="noopener noreferrer"';
                } else {
                    $card_url    = get_the_permalink();
                    $card_target = '';
                }

                $is_news     = in_category( 'in-the-news' );
                $thumb_class = $is_news
                    ? 'news-card__thumbnail news-card__thumbnail--contain'
                    : 'news-card__thumbnail';

                $post_type_obj = get_post_type_object( get_post_type() );
                $type_label    = $post_type_obj ? $post_type_obj->labels->singular_name : '';
            ?>
            <article <?php post_class( 'news-card' ); ?>>

                <?php if ( has_post_thumbnail() ) : ?>
                <div class="<?php echo esc_attr( $thumb_class ); ?>">
                    <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>>
                        <?php the_post_thumbnail( 'medium', [ 'loading' => 'lazy' ] ); ?>
                    </a>
                </div>
                <?php endif; ?>

                <div class="news-card__body">

                    <?php if ( $publication ) : ?>
                    <div class="news-card__source"><?php echo esc_html( $publication ); ?></div>
                    <?php elseif ( $type_label && get_post_type() !== 'post' ) : ?>
                    <div class="news-card__source"><?php echo esc_html( $type_label ); ?></div>
                    <?php else :
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

                    <?php
                    $excerpt = get_the_excerpt();
                    if ( $excerpt ) : ?>
                    <p style="font-size:0.88rem; color:var(--color-text-light); line-height:1.5; margin-bottom:0.75rem;">
                        <?php echo esc_html( wp_trim_words( $excerpt, 18, '&hellip;' ) ); ?>
                    </p>
                    <?php endif; ?>

                    <?php if ( $show_date ) : ?>
                    <div class="news-card__date"><?php echo esc_html( get_the_date() ); ?></div>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>
                       class="btn" style="font-size:0.8rem; padding:0.5em 1.2em; margin-top:auto;">
                        <?php esc_html_e( 'Read More', 'dandysite-victoria' ); ?>
                    </a>

                </div>

            </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination( [
            'mid_size'  => 2,
            'prev_text' => '&larr; ' . esc_html__( 'Newer', 'dandysite-victoria' ),
            'next_text' => esc_html__( 'Older', 'dandysite-victoria' ) . ' &rarr;',
        ] ); ?>

    <?php else : ?>

        <div class="search-no-results">
            <p><?php esc_html_e( 'No results found. Try a different search term.', 'dandysite-victoria' ); ?></p>
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>"
                  style="display:flex; gap:0.5rem; margin-top:1.5rem; max-width:480px;">
                <input type="search" name="s"
                       placeholder="<?php esc_attr_e( 'Search…', 'dandysite-victoria' ); ?>"
                       value="<?php echo esc_attr( get_search_query() ); ?>"
                       style="flex:1; padding:0.6em 0.9em; border:1px solid var(--color-border); border-radius:var(--border-radius); font-size:1rem;">
                <button type="submit" class="btn">
                    <?php esc_html_e( 'Search', 'dandysite-victoria' ); ?>
                </button>
            </form>
        </div>

    <?php endif; ?>

</div>

<?php get_footer();
