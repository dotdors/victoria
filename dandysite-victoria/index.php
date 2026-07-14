<?php
/**
 * Index / Blog Posts Page — Dandysite Victoria
 * Used when a static page is set as the posts page.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<div class="container archive-container">

    <header class="archive-header">
        <?php if ( is_home() && ! is_front_page() ) : ?>
            <h1 class="archive-title"><?php single_post_title(); ?></h1>
        <?php endif; ?>
    </header>

    <?php if ( have_posts() ) : ?>

        <div class="news-grid">
            <?php while ( have_posts() ) : the_post();

                $publication   = get_post_meta( get_the_ID(), 'dsp_publication_name', true );
                $ext_url       = get_post_meta( get_the_ID(), 'dsp_external_url', true );
                $link_behavior = get_option( 'dsp_external_link_behavior', 'summary' );
                $show_date     = apply_filters( 'dsp_show_post_date', true, get_the_ID() );

                // Card link
                if ( $ext_url && $link_behavior === 'direct' ) {
                    $card_url    = $ext_url;
                    $card_target = ' target="_blank" rel="noopener noreferrer"';
                } else {
                    $card_url    = get_the_permalink();
                    $card_target = '';
                }

                // Image treatment: contain for in-the-news, cover otherwise
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

                    <?php dsp_card_blurb(); ?>

                    <?php if ( $show_date ) : ?>
                    <div class="news-card__date"><?php echo esc_html( get_the_date() ); ?></div>
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $card_target; ?>
                       class="btn" style="font-size:0.8rem; padding:0.5em 1.2em; margin-top:auto;">
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
