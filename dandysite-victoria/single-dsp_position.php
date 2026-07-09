<?php
/**
 * Single Position Template
 * Displays a single Issues/Position post.
 * Styling via site plugin (ds-[sitename]/assets/css/site.css).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<main class="single-position" id="main">
    <?php while ( have_posts() ) : the_post(); ?>

    <article class="position-single">

        <header class="position-single__header">
            <div class="container">
                <?php
                $icon = get_post_meta( get_the_ID(), 'dsp_position_icon', true );
                if ( $icon ) : ?>
                    <div class="position-single__icon">
                        <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
                    </div>
                <?php endif; ?>
                <h1 class="position-single__title"><?php the_title(); ?></h1>
                <?php
                $summary = get_post_meta( get_the_ID(), 'dsp_position_summary', true );
                if ( $summary ) : ?>
                    <p class="position-single__summary"><?php echo esc_html( $summary ); ?></p>
                <?php endif; ?>
            </div>
        </header>

        <div class="position-single__content">
            <div class="container container--narrow">
                <?php the_content(); ?>
            </div>
        </div>

        <footer class="position-single__footer">
            <div class="container">
                <a href="<?php echo esc_url( home_url( '/#issues' ) ); ?>" class="btn btn--outline">
                    &larr; <?php esc_html_e( 'All Issues', 'dandysite-victoria' ); ?>
                </a>
            </div>
        </footer>

    </article>

    <?php endwhile; ?>
</main>

<?php get_footer();
