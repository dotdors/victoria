<?php
/**
 * Template Name: Hero — Full Bleed
 * Template Post Type: page
 *
 * Full-viewport image hero with content centered over it.
 * Header style: set independently via Header Settings or the Overlay/Solid page templates.
 *
 * Hero content: set via the Hero Section meta box in the page editor.
 * Hero image:   set via the page's Featured Image.
 */

get_header();
?>



    <?php get_template_part( 'template-parts/hero', null, [ 'layout' => 'fullbleed' ] ); ?>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div class="page-hero-content">
            <?php the_content(); ?>
        </div>
    <?php endwhile; endif; ?>



<?php get_footer(); ?>
