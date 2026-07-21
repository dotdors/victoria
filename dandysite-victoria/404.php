<?php
/**
 * 404 Page — Dandysite Victoria
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<div class="container">
    <div class="error-404" style="text-align:center; max-width:560px; margin:0 auto;">

        <h1 style="font-size:6rem; line-height:1; margin-bottom:0.25rem; color:var(--color-primary);">404</h1>
        <p style="font-size:1.25rem; color:var(--color-text-light); margin-bottom:var(--spacing-lg);">
            <?php esc_html_e( 'Sorry, that page couldn\'t be found.', 'dandysite-victoria' ); ?>
        </p>

        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn" style="margin-bottom:var(--spacing-xl);">
            <?php esc_html_e( 'Go back to homepage', 'dandysite-victoria' ); ?>
        </a>

        <p style="color:var(--color-text-light); margin-bottom:var(--spacing-md);">
            <?php esc_html_e( 'Or try searching:', 'dandysite-victoria' ); ?>
        </p>

        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>"
              style="display:flex; gap:0.5rem; justify-content:center;">
            <input type="search" name="s"
                   placeholder="<?php esc_attr_e( 'Search…', 'dandysite-victoria' ); ?>"
                   style="flex:1; max-width:300px; padding:0.6em 0.9em; border:1px solid var(--color-border); border-radius:var(--border-radius); font-size:1rem;">
            <button type="submit" class="btn">
                <?php esc_html_e( 'Search', 'dandysite-victoria' ); ?>
            </button>
        </form>

    </div>
</div>

<?php get_footer();
