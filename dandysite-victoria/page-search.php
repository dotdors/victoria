<?php
/**
 * Template Name: Search Page
 * Template for displaying the search form
 */
get_header(); ?>

<div class="container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="featured-image">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content content-wrapper">
                <?php the_content(); ?>
                  <div class="search-content">
                
               
                <p class="search-subtitle">Find stuff and more</p>
                
                <div class="search-form-wrapper">
                    <form role="search" method="get" class="search-form-enhanced" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="search-input-container">
                            <input 
                                type="search" 
                                class="search-input-minimal" 
                                placeholder="Search our producers, wines, and more..." 
                                value="<?php echo get_search_query(); ?>" 
                                name="s" 
                                autocomplete="off"
                            />
                            <button type="submit" class="search-button-minimal" aria-label="Search">
                                <svg width="24" height="24" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="none">
                                    <path fill="currentColor" fill-rule="evenodd" d="M4 9a5 5 0 1110 0A5 5 0 014 9zm5-7a7 7 0 104.2 12.6.999.999 0 00.093.107l3 3a1 1 0 001.414-1.414l-3-3a.999.999 0 00-.107-.093A7 7 0 009 2z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                <?php
                wp_link_pages([
                    'before' => '<div class="page-links">' . __('Pages:', 'dandysite-portfolio'),
                    'after'  => '</div>',
                ]);
                ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
