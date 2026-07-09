<?php
/**
 * Homepage Settings
 *
 * Appearance → Homepage Settings
 *
 * Controls which sections appear on the homepage, how many items
 * each section shows, and which categories feed each section.
 *
 * Options stored:
 *   dsp_hp_show_bio               bool    default true
 *   dsp_hp_show_issues            bool    default true
 *   dsp_hp_show_articles          bool    default true
 *   dsp_hp_articles_count         int     default 4
 *   dsp_hp_articles_cats          array   default: all except in-the-news
 *   dsp_hp_show_news              bool    default true
 *   dsp_hp_news_count             int     default 4
 *   dsp_hp_news_cats              array   default: ['in-the-news']
 *   dsp_hp_show_endorsements      bool    default true
 *   dsp_hp_endorsements_count     int     default 6
 *   dsp_hp_show_cta               bool    default true
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================
// REGISTER ADMIN PAGE
// ============================================================

function dsp_hp_add_admin_page() {
    add_theme_page(
        __( 'Homepage Settings', 'dandysite-victoria' ),
        __( 'Homepage Settings', 'dandysite-victoria' ),
        'manage_options',
        'dsp-homepage-settings',
        'dsp_hp_settings_page'
    );
}
add_action( 'admin_menu', 'dsp_hp_add_admin_page' );

// ============================================================
// SAVE HANDLER
// ============================================================

function dsp_hp_save_settings() {
    if ( ! isset( $_POST['dsp_hp_nonce'] ) ||
         ! wp_verify_nonce( $_POST['dsp_hp_nonce'], 'dsp_hp_save' ) ) {
        return;
    }
    if ( ! current_user_can( 'manage_options' ) ) return;

    // Booleans (checkboxes)
    $bools = [
        'dsp_hp_show_bio',
        'dsp_hp_show_issues',
        'dsp_hp_show_articles',
        'dsp_hp_show_news',
        'dsp_hp_show_endorsements',
        'dsp_hp_show_cta',
    ];
    foreach ( $bools as $key ) {
        update_option( $key, isset( $_POST[ $key ] ) ? '1' : '0' );
    }

    // Integers
    $ints = [
        'dsp_hp_articles_count',
        'dsp_hp_news_count',
        'dsp_hp_endorsements_count',
    ];
    foreach ( $ints as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_option( $key, absint( $_POST[ $key ] ) );
        }
    }

    // Category arrays
    $cat_fields = [ 'dsp_hp_articles_cats', 'dsp_hp_news_cats' ];
    foreach ( $cat_fields as $key ) {
        $value = isset( $_POST[ $key ] ) ? array_map( 'sanitize_key', (array) $_POST[ $key ] ) : [];
        update_option( $key, $value );
    }

    wp_redirect( add_query_arg(
        [ 'page' => 'dsp-homepage-settings', 'settings-updated' => '1' ],
        admin_url( 'themes.php' )
    ) );
    exit;
}
add_action( 'admin_post_dsp_hp_save', 'dsp_hp_save_settings' );

// ============================================================
// SETTINGS PAGE OUTPUT
// ============================================================

function dsp_hp_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    // Handle save
    if ( isset( $_GET['settings-updated'] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Homepage settings saved.', 'dandysite-victoria' ) . '</p></div>';
    }

    $all_cats = get_categories( [ 'hide_empty' => false ] );

    // Current values with defaults
    $show_bio          = get_option( 'dsp_hp_show_bio',           '1' );
    $show_issues       = get_option( 'dsp_hp_show_issues',        '1' );
    $show_articles     = get_option( 'dsp_hp_show_articles',      '1' );
    $articles_count    = get_option( 'dsp_hp_articles_count',     4 );
    $articles_cats     = get_option( 'dsp_hp_articles_cats',      [] );
    $show_news         = get_option( 'dsp_hp_show_news',          '1' );
    $news_count        = get_option( 'dsp_hp_news_count',         4 );
    $news_cats         = get_option( 'dsp_hp_news_cats',          [ 'in-the-news' ] );
    $show_endorsements = get_option( 'dsp_hp_show_endorsements',  '1' );
    $end_count         = get_option( 'dsp_hp_endorsements_count', 6 );
    $show_cta          = get_option( 'dsp_hp_show_cta',           '1' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Homepage Settings', 'dandysite-victoria' ); ?></h1>
        <p class="description" style="margin-bottom:1.5em;">
            <?php esc_html_e( 'Control which sections appear on the homepage and how they behave. The hero section always displays. To reorder sections, use CSS order properties in your site plugin.', 'dandysite-victoria' ); ?>
        </p>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'dsp_hp_save', 'dsp_hp_nonce' ); ?>
            <input type="hidden" name="action" value="dsp_hp_save">

            <?php
            // ---- helper to output a section ----
            function dsp_hp_section( $title, $content ) {
                echo '<div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;max-width:720px;">';
                echo '<h2 style="margin-top:0;font-size:1.1rem;border-bottom:1px solid #eee;padding-bottom:0.5rem;">' . esc_html( $title ) . '</h2>';
                echo $content;
                echo '</div>';
            }

            // ---- Bio ----
            dsp_hp_section( 'Bio / Meet the Candidate', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_bio" value="1" ' . checked( $show_bio, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p class="description">' . esc_html__( 'Pulls content from the page selected as the Bio page in Appearance → Theme Settings.', 'dandysite-victoria' ) . '</p>
            ' );

            // ---- Issues ----
            dsp_hp_section( 'Issues &amp; Positions', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_issues" value="1" ' . checked( $show_issues, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p class="description">' . esc_html__( 'Shows positions flagged "Show on Homepage" in the Positions admin.', 'dandysite-victoria' ) . '</p>
            ' );

            // ---- Articles ----
            $cat_checkboxes_articles = '';
            foreach ( $all_cats as $cat ) {
                $checked = empty( $articles_cats )
                    ? ( $cat->slug !== 'in-the-news' ? 'checked' : '' )  // default: all except in-the-news
                    : ( in_array( $cat->slug, $articles_cats ) ? 'checked' : '' );
                $cat_checkboxes_articles .= '<label style="display:block;margin-bottom:4px;">
                    <input type="checkbox" name="dsp_hp_articles_cats[]" value="' . esc_attr( $cat->slug ) . '" ' . $checked . '>
                    ' . esc_html( $cat->name ) . ' <span style="color:#888;font-size:12px;">(' . $cat->count . ' posts)</span>
                </label>';
            }
            dsp_hp_section( 'Articles / Op-Eds', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_articles" value="1" ' . checked( $show_articles, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p></p>
                <label>' . esc_html__( 'Number of articles to show:', 'dandysite-victoria' ) . '
                    <input type="number" name="dsp_hp_articles_count" value="' . esc_attr( $articles_count ) . '" min="1" max="12" style="width:60px;margin-left:8px;">
                </label>
                <p></p>
                <strong>' . esc_html__( 'Categories to include:', 'dandysite-victoria' ) . '</strong>
                <p class="description" style="margin-bottom:8px;">' . esc_html__( 'Default: all categories except "In the News".', 'dandysite-victoria' ) . '</p>
                ' . $cat_checkboxes_articles . '
            ' );

            // ---- In the News ----
            $cat_checkboxes_news = '';
            foreach ( $all_cats as $cat ) {
                $checked = empty( $news_cats )
                    ? ( $cat->slug === 'in-the-news' ? 'checked' : '' )
                    : ( in_array( $cat->slug, $news_cats ) ? 'checked' : '' );
                $cat_checkboxes_news .= '<label style="display:block;margin-bottom:4px;">
                    <input type="checkbox" name="dsp_hp_news_cats[]" value="' . esc_attr( $cat->slug ) . '" ' . $checked . '>
                    ' . esc_html( $cat->name ) . ' <span style="color:#888;font-size:12px;">(' . $cat->count . ' posts)</span>
                </label>';
            }
            dsp_hp_section( 'In the News', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_news" value="1" ' . checked( $show_news, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p></p>
                <label>' . esc_html__( 'Number of items to show:', 'dandysite-victoria' ) . '
                    <input type="number" name="dsp_hp_news_count" value="' . esc_attr( $news_count ) . '" min="1" max="12" style="width:60px;margin-left:8px;">
                </label>
                <p></p>
                <strong>' . esc_html__( 'Categories to include:', 'dandysite-victoria' ) . '</strong>
                <p class="description" style="margin-bottom:8px;">' . esc_html__( 'Default: "In the News" only.', 'dandysite-victoria' ) . '</p>
                ' . $cat_checkboxes_news . '
            ' );

            // ---- Endorsements ----
            dsp_hp_section( 'Endorsements', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_endorsements" value="1" ' . checked( $show_endorsements, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p></p>
                <label>' . esc_html__( 'Number of endorsements to show:', 'dandysite-victoria' ) . '
                    <input type="number" name="dsp_hp_endorsements_count" value="' . esc_attr( $end_count ) . '" min="1" max="24" style="width:60px;margin-left:8px;">
                </label>
                <p class="description">' . esc_html__( 'Only endorsements marked "Show on Homepage" are shown, up to this number.', 'dandysite-victoria' ) . '</p>
            ' );

            // ---- CTA ----
            dsp_hp_section( 'Get Involved / CTA', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_cta" value="1" ' . checked( $show_cta, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
            ' );
            ?>

            <?php submit_button( __( 'Save Homepage Settings', 'dandysite-victoria' ) ); ?>
        </form>
    </div>
    <?php
}
