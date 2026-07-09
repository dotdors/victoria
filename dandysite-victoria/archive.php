<?php
/**
 * Archive Template — Dandysite Victoria
 * Handles category, tag, date, and author archives.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<div class="container archive-container">

    <header class="archive-header">
        <?php
        if ( is_category() ) {
            echo '<h1 class="archive-title">' . esc_html( single_cat_title( '', false ) ) . '</h1>';
            $cat_description = category_description();
            if ( $cat_description ) {
                echo '<div class="archive-description">' . wp_kses_post( $cat_description ) . '</div>';
            }
        } elseif ( is_tag() ) {
            echo '<h1 class="archive-title">' . esc_html( single_tag_title( '', false ) ) . '</h1>';
        } elseif ( is_author() ) {
            echo '<h1 class="archive-title">' . esc_html( get_the_author() ) . '</h1>';
        } elseif ( is_year() ) {
            echo '<h1 class="archive-title">' . esc_html( get_the_date( 'Y' ) ) . '</h1>';
        } elseif ( is_month() ) {
            echo '<h1 class="archive-title">' . esc_html( get_the_date( 'F Y' ) ) . '</h1>';
        } else {
            the_archive_title( '<h1 class="archive-title">', '</h1>' );
        }
        ?>
    </header>

    <?php if ( have_posts() ) : ?>

        <div class="news-grid archive-grid">
            <?php while ( have_posts() ) : the_post();
                $publication = get_post_meta( get_the_ID(), 'dsp_publication_name', true );
                $ext_url     = get_post_meta( get_the_ID(), 'dsp_external_url', true );
                $link_behavior = get_option( 'dsp_external_link_behavior', 'summary' );

                if ( $ext_url && $link_behavior === 'direct' ) {
                    $card_url    = $ext_url;
                    $card_target = ' target="_blank" rel="noopener noreferrer"';
                } else {
                    $card_url    = get_the_permalink();
                    $card_target = '';
                }

                $is_news = in_category( 'in-the-news' );
                $thumb_class = $is_news ? 'news-card__thumbnail news-card__thumbnail--contain' : 'news-card__thumbnail';
            ?>
            <article <?php post_class( 'news-card' ); ?>>

                <?php if ( has_post_thumbnail() ) : ?>
                <div class="<?php echo esc_attr( $thumb_class ); ?>">
                    <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>>
                        <?php the_post_thumbnail( 'medium', [
                            'loading' => 'lazy',
                        ] ); ?>
                    </a>
                </div>
                <?php endif; ?>

                <div class="news-card__body">

                    <?php if ( $publication ) : ?>
                    <div class="news-card__source"><?php echo esc_html( $publication ); ?></div>
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

                    <?php if ( apply_filters( 'dsp_show_post_date', true, get_the_ID() ) ) : ?>
                    <div class="news-card__date"><?php echo esc_html( get_the_date() ); ?></div>
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
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination( [
            'mid_size'  => 2,
            'prev_text' => '&larr; ' . esc_html__( 'Newer', 'dandysite-victoria' ),
            'next_text' => esc_html__( 'Older', 'dandysite-victoria' ) . ' &rarr;',
        ] ); ?>

    <?php else : ?>

        <p><?php esc_html_e( 'No posts found.', 'dandysite-victoria' ); ?></p>

    <?php endif; ?>

</div>

<?php get_footer();
