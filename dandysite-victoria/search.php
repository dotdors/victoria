
<?php
/**
 * search.php — Search Results
 *
 * Displays results for producers, wines, pages, and posts.
 * CPTs are added to the search query via dswg_include_cpts_in_search()
 * in ds-wineguy/includes/search-filter.php.
 *
 * Card variants:
 *   dswg_producer — green left border, name + location
 *   dswg_wine     — wine-red left border, name + type · producer
 *   page / post   — gold left border, title + excerpt
 *
 * Located: themes/dandysite-victoria/search.php
 */

get_header();

$search_query = get_search_query();
?>

<div class="search-results-page">

    <div class="container">

        <header class="search-results-page__header">
            <?php if ( $search_query ) : ?>
                <h1 class="search-results-page__heading">
                    <?php printf(
                        /* translators: %s: search query */
                        esc_html__( 'Results for: %s', 'dandysite-victoria' ),
                        '<span class="search-results-page__query">' . esc_html( $search_query ) . '</span>'
                    ); ?>
                </h1>
            <?php else : ?>
                <h1 class="search-results-page__heading">
                    <?php esc_html_e( 'Search', 'dandysite-victoria' ); ?>
                </h1>
            <?php endif; ?>

            <?php if ( have_posts() ) : ?>
                <p class="search-results-page__count">
                    <?php
                    global $wp_query;
                    $found = (int) $wp_query->found_posts;
                    printf(
                        esc_html( _n( '%s result', '%s results', $found, 'dandysite-victoria' ) ),
                        '<strong>' . number_format_i18n( $found ) . '</strong>'
                    );
                    ?>
                </p>
            <?php endif; ?>
        </header><!-- .search-results-page__header -->

        <?php if ( have_posts() ) : ?>

            <div class="search-results-grid">

                <?php while ( have_posts() ) : the_post();

                    $post_type   = get_post_type();
                    $post_id     = get_the_ID();
                    $title       = get_the_title();
                    $permalink   = get_permalink();

                    // Skip inactive wines quietly
                    if ( $post_type === 'dswg_wine' ) {
                        $active = get_post_meta( $post_id, 'dswg_wine_active', true );
                        if ( $active === '0' ) {
                            continue;
                        }
                    }

                    if ( $post_type === 'dswg_producer' ) :

                        $location = get_post_meta( $post_id, 'dswg_location', true );
                        ?>
                        <article class="search-result-card search-result-card--producer">
                            <span class="search-result-card__label"><?php esc_html_e( 'Producer', 'dandysite-victoria' ); ?></span>
                            <h2 class="search-result-card__title">
                                <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                            </h2>
                            <?php if ( $location ) : ?>
                                <p class="search-result-card__meta"><?php echo esc_html( $location ); ?></p>
                            <?php endif; ?>
                        </article>

                    <?php elseif ( $post_type === 'dswg_wine' ) :

                        $wine_types   = get_the_terms( $post_id, 'dswg_wine_type' );
                        $wine_type    = ( $wine_types && ! is_wp_error( $wine_types ) ) ? $wine_types[0]->name : '';
                        $producer_id  = (int) get_post_meta( $post_id, 'dswg_producer_id', true );
                        $producer_name = $producer_id ? get_the_title( $producer_id ) : '';

                        $meta_parts = array_filter( [ $wine_type, $producer_name ] );
                        $meta_line  = implode( ' · ', $meta_parts );
                        ?>
                        <article class="search-result-card search-result-card--wine">
                            <span class="search-result-card__label"><?php esc_html_e( 'Wine', 'dandysite-victoria' ); ?></span>
                            <h2 class="search-result-card__title">
                                <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                            </h2>
                            <?php if ( $meta_line ) : ?>
                                <p class="search-result-card__meta"><?php echo esc_html( $meta_line ); ?></p>
                            <?php endif; ?>
                        </article>

                    <?php else :

                        // page, post, or any other post type
                        $excerpt = get_the_excerpt();
                        ?>
                        <article class="search-result-card search-result-card--page">
                            <span class="search-result-card__label"><?php echo esc_html( get_post_type_object( $post_type )->labels->singular_name ); ?></span>
                            <h2 class="search-result-card__title">
                                <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                            </h2>
                            <?php if ( $excerpt ) : ?>
                                <p class="search-result-card__meta"><?php echo esc_html( wp_trim_words( $excerpt, 20, '&hellip;' ) ); ?></p>
                            <?php endif; ?>
                        </article>

                    <?php endif; ?>

                <?php endwhile; ?>

            </div><!-- .search-results-grid -->

            <?php
            the_posts_pagination( [
                'mid_size'  => 2,
                'prev_text' => __( '&laquo; Previous', 'dandysite-victoria' ),
                'next_text' => __( 'Next &raquo;', 'dandysite-victoria' ),
            ] );
            ?>

        <?php else : ?>

            <div class="search-results-page__no-results">
                <p><?php esc_html_e( 'No results found. Please try another word or phrase.', 'dandysite-victoria' ); ?></p>
                <form role="search" method="get" class="search-results-page__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="search-results-page__form-row">
                        <input
                            type="search"
                            class="search-results-page__input"
                            name="s"
                            placeholder="<?php esc_attr_e( 'Tell us what you\'re after…', 'dandysite-victoria' ); ?>"
                            value="<?php echo esc_attr( get_search_query() ); ?>"
                            autocomplete="off"
                            aria-label="<?php esc_attr_e( 'Search', 'dandysite-victoria' ); ?>"
                        />
                        <button type="submit" class="button">
                            <?php esc_html_e( 'Search', 'dandysite-victoria' ); ?>
                        </button>
                    </div>
                </form>
            </div>

        <?php endif; ?>

    </div><!-- .container -->

</div><!-- .search-results-page -->

<?php get_footer(); ?>