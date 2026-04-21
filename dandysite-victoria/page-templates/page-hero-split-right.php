<?php
/**
 * Template Name: Hero — Split Right
 * Template Post Type: page
 *
 * Two-column hero: content panel on the left, image on the right.
 * Header style: set independently via the Header Style sidebar panel in the editor.
 *
 * Hero content: set via the Hero Section meta box in the page editor.
 * Hero image:   set via the page's Featured Image.
 */

get_header();
?>

<?php get_template_part( 'template-parts/hero', null, [ 'layout' => 'split-right' ] ); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="page-hero-content">
        <?php the_content(); ?>
    </div>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
