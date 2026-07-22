<?php
/**
 * Single Post Template — Dandysite Victoria
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<div class="container single-post-container">
    <?php while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>

            <header class="entry-header">

                <?php
                // Category labels
                $categories = get_the_category();
                if ( ! empty( $categories ) ) :
                ?>
                <div class="entry-categories">
                    <?php foreach ( $categories as $cat ) : ?>
                    <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
                       class="entry-category-label">
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <h1 class="entry-title"><?php the_title(); ?></h1>

                <?php if ( apply_filters( 'dsp_show_post_date', true, get_the_ID() ) ) : ?>
                <div class="entry-meta">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                    <span class="entry-share">
                        <?php if ( function_exists( 'dandy_share' ) ) : dandy_share(); endif; ?>
                    </span>
                </div>
                <?php endif; ?>

            </header>

            <?php
            // Featured image — global toggle in Appearance → Theme Settings,
            // still filterable per-site/per-post via 'dsp_show_single_featured_image'.
            $show_featured = get_option( 'dsp_single_featured_image', '1' ) === '1';
            $show_featured = apply_filters( 'dsp_show_single_featured_image', $show_featured, get_the_ID() );
            if ( $show_featured && has_post_thumbnail() ) :
            ?>
            <div class="entry-featured-image">
                <?php the_post_thumbnail( 'large' ); ?>
            </div>
            <?php endif; ?>

            <div class="entry-content content-wrapper">
                <?php the_content(); ?>
                <?php wp_link_pages(); ?>
                <?php if ( function_exists( 'dandy_share' ) ) : dandy_share(); endif; ?>
            </div>

            <footer class="entry-footer">
                <?php
                the_post_navigation( [
                    'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous', 'dandysite-victoria' ) . '</span><span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next', 'dandysite-victoria' ) . '</span><span class="nav-title">%title</span>',
                ] );
                ?>
            </footer>

        </article>

    <?php endwhile; ?>
</div>

<?php get_footer();
