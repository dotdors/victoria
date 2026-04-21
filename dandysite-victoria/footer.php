<?php
/**
 * Footer Template
 * dandysite-victoria theme
 *
 * Layout class (footer-layout-left/center/spaced) and dark mode (footer-dark)
 * are added to <body> by footer-settings.php and controlled via footer.css.
 */
?>

    </main><!-- #primary -->

    <footer id="colophon" class="site-footer" role="contentinfo">

        <?php
        $has_primary   = is_active_sidebar('footer-widgets');
        $has_secondary = is_active_sidebar('footer-widgets-secondary');
        $has_nav       = has_nav_menu('footer');

        if ($has_primary || $has_secondary || $has_nav) :
        ?>

        <div class="footer-main">
            <div class="container">

                <?php if ($has_primary) : ?>
                <div class="footer-primary">

                    <!-- Footer Logo -->
                    <div class="footer-logo">
                        <?php
                        $logo_id  = (int) get_option( 'dsp_logo_full', 0 );
                        $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
                        $logo_alt = $logo_id ? ( get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) ?: get_bloginfo( 'name' ) ) : '';

                        if ( $logo_url ) {
                            printf(
                                '<a href="%s" rel="home"><img src="%s" alt="%s" class="footer-logo__img" loading="lazy"></a>',
                                esc_url( home_url( '/' ) ),
                                esc_url( $logo_url ),
                                esc_attr( $logo_alt )
                            );
                        } else {
                            printf(
                                '<a href="%s" class="footer-logo__text" rel="home">%s</a>',
                                esc_url( home_url( '/' ) ),
                                esc_html( get_bloginfo( 'name' ) )
                            );
                        }
                        ?>
                    </div><!-- .footer-logo -->

                    <!-- Primary Widget Area -->
                    <div class="footer-widgets">
                        <?php dynamic_sidebar('footer-widgets'); ?>
                    </div><!-- .footer-widgets -->

                </div><!-- .footer-primary -->
                <?php endif; ?>

                <?php if ($has_secondary) : ?>
                <!-- Secondary Widget Area — centered by default, style via CSS -->
                <div class="footer-secondary">
                    <?php dynamic_sidebar('footer-widgets-secondary'); ?>
                </div><!-- .footer-secondary -->
                <?php endif; ?>

                <?php if ($has_nav) : ?>
                <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer navigation', 'dandysite-victoria'); ?>">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'menu_class'     => 'footer-nav__menu',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ]);
                    ?>
                </nav>
                <?php endif; ?>

            </div><!-- .container -->
        </div><!-- .footer-main -->

        <?php endif; ?>

        <!-- Copyright Bar -->
        <div class="site-colophon">
            <div class="container">
                <p>
                    &copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?> |
                    Website by <a href="https://dabbledstudios.com/" target="_blank" rel="nofollow" title="website credit: dabbledstudios | Atlanta GA">dabbledstudios</a> |
                    <a href="<?php echo esc_url(home_url('/website-info')); ?>" title="website information">Website Info</a><?php
                    if (is_user_logged_in()) : ?> | <?php wp_register('', ''); ?><?php endif; ?> | <?php wp_loginout(); ?>
                </p>
            </div>
        </div><!-- .site-colophon -->

    </footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
