<?php
/**
 * Homepage Settings
 *
 * Appearance → Homepage Settings
 *
 * Controls which sections appear on the homepage, their order, how many
 * items each section shows, and which categories feed each section.
 *
 * Options stored:
 *   dsp_hp_show_hero              bool    default true (header forces solid style when hero is off)
 *   dsp_hp_show_bio               bool    default true
 *   dsp_hp_bio_image_mode         string  'featured' | 'none' | 'background'  default 'featured'
 *   dsp_hp_bio_image_id           int     attachment ID override (0 = use featured image)
 *   dsp_hp_show_issues            bool    default true
 *   dsp_hp_show_articles          bool    default true
 *   dsp_hp_articles_count         int     default 4
 *   dsp_hp_articles_cats          array   default: all except in-the-news
 *   dsp_hp_show_news              bool    default true
 *   dsp_hp_news_count             int     default 4
 *   dsp_hp_news_cats              array   default: ['in-the-news']
 *   dsp_hp_show_endorsements      bool    default true
 *   dsp_hp_endorsements_count     int     default 6
 *   dsp_hp_endorsements_layout    string  'grid' | 'carousel'  default 'grid'
 *   dsp_hp_show_cta               bool    default true
 *   dsp_hp_cta_title              string  CTA heading; '' = theme default ('Get Involved')
 *   dsp_hp_cta_text               string  CTA paragraph; '' = theme default
 *   dsp_hp_cta_btn{1-3}_label     string  button label; blank = button hidden
 *   dsp_hp_cta_btn{1-3}_url       string  button URL
 *   dsp_hp_cta_btn{1-3}_style     string  'solid' | 'outline'
 *                                         (If no button options have ever been saved, the theme's
 *                                         default Volunteer/Donate/Stay Informed set is shown.)
 *   dsp_hp_articles_view_all_url  string  URL for the Articles section's View All button;
 *                                         '' = default (the blog page, or /blog/)
 *   dsp_hp_media_kit_id           int     attachment ID of the downloadable media kit (0 = none).
 *                                         When set, a "Download Media Kit" link renders at the
 *                                         bottom of the Get Involved / CTA section.
 *   dsp_hp_show_connect           bool    default true
 *   dsp_hp_connect_heading        string  default 'Get in Touch'
 *   dsp_hp_connect_text           string
 *   dsp_hp_connect_email          string
 *   dsp_hp_order_{section}        int     CSS order value per section (see defaults below)
 *   dsp_hp_bg_{section}           string  'default' | 'light' | 'surface' | 'dark'  default 'default'
 *                                         Applies a Surface Context class (see style.css) to the
 *                                         section wrapper. 'default' = the section's built-in
 *                                         background (issues = dark, get-involved = accent, etc.)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================
// SECTION ORDER DEFAULTS
// ============================================================
// Keys match the section slugs used in front-page.php.
// Values are CSS `order` numbers — spaced by 10 so a section can be
// slotted between two others without renumbering everything.

function dsp_hp_section_order_defaults() {
    return [
        'hero'         => 0,
        'bio'          => 10,
        'issues'       => 20,
        'articles'     => 30,
        'news'         => 40,
        'endorsements' => 50,
        'get-involved' => 60,
        'connect'      => 70,
    ];
}

// ============================================================
// SECTION BACKGROUND (Surface Context)
// ============================================================
// Each section can be assigned a background in Homepage Settings.
// Returns the context class (with a leading space) to append to the
// section wrapper's class attribute, or '' for the built-in default.
// Context classes are defined in style.css → SURFACE CONTEXTS.

function dsp_section_bg_class( $section ) {
    $value = get_option( 'dsp_hp_bg_' . str_replace( '-', '_', $section ), 'default' );
    $map = [
        'light'   => ' section--light',
        'surface' => ' section--surface',
        'dark'    => ' section--dark',
    ];
    $class = $map[ $value ] ?? '';
    return apply_filters( 'dsp_section_bg_class', $class, $section );
}

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

// Media library picker for the bio image override
function dsp_hp_admin_scripts( $hook ) {
    if ( $hook !== 'appearance_page_dsp-homepage-settings' ) return;
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'dsp_hp_admin_scripts' );

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
        'dsp_hp_show_hero',
        'dsp_hp_show_bio',
        'dsp_hp_show_issues',
        'dsp_hp_show_articles',
        'dsp_hp_show_news',
        'dsp_hp_show_endorsements',
        'dsp_hp_show_cta',
        'dsp_hp_show_connect',
    ];
    foreach ( $bools as $key ) {
        update_option( $key, isset( $_POST[ $key ] ) ? '1' : '0' );
    }

    // Integers
    $ints = [
        'dsp_hp_articles_count',
        'dsp_hp_news_count',
        'dsp_hp_endorsements_count',
        'dsp_hp_bio_image_id',
        'dsp_hp_media_kit_id',
    ];
    foreach ( $ints as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_option( $key, absint( $_POST[ $key ] ) );
        }
    }

    // Section order values
    foreach ( array_keys( dsp_hp_section_order_defaults() ) as $section ) {
        $field = 'dsp_hp_order_' . str_replace( '-', '_', $section );
        if ( isset( $_POST[ $field ] ) ) {
            update_option( $field, intval( $_POST[ $field ] ) );
        }
    }

    // Section backgrounds (whitelist; hero excluded)
    foreach ( array_keys( dsp_hp_section_order_defaults() ) as $section ) {
        if ( $section === 'hero' ) continue;
        $field = 'dsp_hp_bg_' . str_replace( '-', '_', $section );
        if ( isset( $_POST[ $field ] ) ) {
            $bg = in_array( $_POST[ $field ], [ 'default', 'light', 'surface', 'dark' ], true )
                ? $_POST[ $field ] : 'default';
            update_option( $field, $bg );
        }
    }

    // Bio image mode (whitelist)
    if ( isset( $_POST['dsp_hp_bio_image_mode'] ) ) {
        $mode = in_array( $_POST['dsp_hp_bio_image_mode'], [ 'featured', 'none', 'background' ], true )
            ? $_POST['dsp_hp_bio_image_mode'] : 'featured';
        update_option( 'dsp_hp_bio_image_mode', $mode );
    }

    // Endorsements layout (whitelist)
    if ( isset( $_POST['dsp_hp_endorsements_layout'] ) ) {
        $layout = in_array( $_POST['dsp_hp_endorsements_layout'], [ 'grid', 'carousel' ], true )
            ? $_POST['dsp_hp_endorsements_layout'] : 'grid';
        update_option( 'dsp_hp_endorsements_layout', $layout );
    }

    // Connect section text fields
    if ( isset( $_POST['dsp_hp_connect_heading'] ) ) {
        update_option( 'dsp_hp_connect_heading', sanitize_text_field( wp_unslash( $_POST['dsp_hp_connect_heading'] ) ) );
    }
    if ( isset( $_POST['dsp_hp_connect_text'] ) ) {
        update_option( 'dsp_hp_connect_text', sanitize_textarea_field( wp_unslash( $_POST['dsp_hp_connect_text'] ) ) );
    }

    // CTA section text fields
    if ( isset( $_POST['dsp_hp_cta_title'] ) ) {
        update_option( 'dsp_hp_cta_title', sanitize_text_field( wp_unslash( $_POST['dsp_hp_cta_title'] ) ) );
    }
    if ( isset( $_POST['dsp_hp_cta_text'] ) ) {
        update_option( 'dsp_hp_cta_text', sanitize_textarea_field( wp_unslash( $_POST['dsp_hp_cta_text'] ) ) );
    }

    // Articles section View All URL
    if ( isset( $_POST['dsp_hp_articles_view_all_url'] ) ) {
        update_option( 'dsp_hp_articles_view_all_url', esc_url_raw( wp_unslash( $_POST['dsp_hp_articles_view_all_url'] ) ) );
    }

    // CTA buttons (3 slots)
    for ( $i = 1; $i <= 3; $i++ ) {
        if ( isset( $_POST[ "dsp_hp_cta_btn{$i}_label" ] ) ) {
            update_option( "dsp_hp_cta_btn{$i}_label", sanitize_text_field( wp_unslash( $_POST[ "dsp_hp_cta_btn{$i}_label" ] ) ) );
        }
        if ( isset( $_POST[ "dsp_hp_cta_btn{$i}_url" ] ) ) {
            update_option( "dsp_hp_cta_btn{$i}_url", esc_url_raw( wp_unslash( $_POST[ "dsp_hp_cta_btn{$i}_url" ] ) ) );
        }
        if ( isset( $_POST[ "dsp_hp_cta_btn{$i}_style" ] ) ) {
            $btn_style = in_array( $_POST[ "dsp_hp_cta_btn{$i}_style" ], [ 'solid', 'outline' ], true )
                ? $_POST[ "dsp_hp_cta_btn{$i}_style" ] : 'solid';
            update_option( "dsp_hp_cta_btn{$i}_style", $btn_style );
        }
    }
    if ( isset( $_POST['dsp_hp_connect_email'] ) ) {
        update_option( 'dsp_hp_connect_email', sanitize_email( wp_unslash( $_POST['dsp_hp_connect_email'] ) ) );
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
    $bio_image_mode    = get_option( 'dsp_hp_bio_image_mode',     'featured' );
    $bio_image_id      = (int) get_option( 'dsp_hp_bio_image_id', 0 );
    $show_issues       = get_option( 'dsp_hp_show_issues',        '1' );
    $show_articles     = get_option( 'dsp_hp_show_articles',      '1' );
    $articles_count    = get_option( 'dsp_hp_articles_count',     4 );
    $articles_cats     = get_option( 'dsp_hp_articles_cats',      [] );
    $show_news         = get_option( 'dsp_hp_show_news',          '1' );
    $news_count        = get_option( 'dsp_hp_news_count',         4 );
    $news_cats         = get_option( 'dsp_hp_news_cats',          [ 'in-the-news' ] );
    $show_endorsements = get_option( 'dsp_hp_show_endorsements',  '1' );
    $end_count         = get_option( 'dsp_hp_endorsements_count', 6 );
    $end_layout        = get_option( 'dsp_hp_endorsements_layout','grid' );
    $show_cta          = get_option( 'dsp_hp_show_cta',           '1' );
    $show_hero         = get_option( 'dsp_hp_show_hero',          '1' );
    $show_connect      = get_option( 'dsp_hp_show_connect',       '1' );
    $connect_heading   = get_option( 'dsp_hp_connect_heading',    __( 'Get in Touch', 'dandysite-victoria' ) );
    $connect_text      = get_option( 'dsp_hp_connect_text',       '' );
    $connect_email     = get_option( 'dsp_hp_connect_email',      '' );

    $bio_image_preview = $bio_image_id ? wp_get_attachment_image_url( $bio_image_id, 'medium' ) : '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Homepage Settings', 'dandysite-victoria' ); ?></h1>
        <p class="description" style="margin-bottom:1.5em;">
            <?php esc_html_e( 'Control which sections appear on the homepage, their order, and how they behave. The hero section always displays first.', 'dandysite-victoria' ); ?>
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

            // ---- Section Order ----
            $order_labels = [
                'hero'         => __( 'Hero', 'dandysite-victoria' ),
                'bio'          => __( 'Bio / Meet the Candidate', 'dandysite-victoria' ),
                'issues'       => __( 'Issues & Positions', 'dandysite-victoria' ),
                'articles'     => __( 'Articles / Op-Eds', 'dandysite-victoria' ),
                'news'         => __( 'In the News', 'dandysite-victoria' ),
                'endorsements' => __( 'Endorsements', 'dandysite-victoria' ),
                'get-involved' => __( 'Get Involved / CTA', 'dandysite-victoria' ),
                'connect'      => __( 'Contact / Connect', 'dandysite-victoria' ),
            ];
            $bg_choices = [
                'default' => __( 'Default (built-in)', 'dandysite-victoria' ),
                'light'   => __( 'Light — page background', 'dandysite-victoria' ),
                'surface' => __( 'Surface — tinted', 'dandysite-victoria' ),
                'dark'    => __( 'Dark', 'dandysite-victoria' ),
            ];
            $order_rows = '<tr>
                <th style="text-align:left;padding:2px 12px 6px 0;">' . esc_html__( 'Section', 'dandysite-victoria' ) . '</th>
                <th style="text-align:left;padding:2px 12px 6px 0;">' . esc_html__( 'Order', 'dandysite-victoria' ) . '</th>
                <th style="text-align:left;padding:2px 0 6px;">' . esc_html__( 'Background', 'dandysite-victoria' ) . '</th>
            </tr>';
            foreach ( dsp_hp_section_order_defaults() as $key => $default ) {
                $field = 'dsp_hp_order_' . str_replace( '-', '_', $key );
                $value = (int) get_option( $field, $default );
                $disabled = ( $key === 'hero' ) ? 'readonly style="width:60px;background:#f0f0f1;"' : 'style="width:60px;"';

                // Background dropdown (hero keeps its own image/overlay system)
                if ( $key === 'hero' ) {
                    $bg_cell = '<span style="color:#888;">&mdash;</span>';
                } else {
                    $bg_field = 'dsp_hp_bg_' . str_replace( '-', '_', $key );
                    $bg_value = get_option( $bg_field, 'default' );
                    $bg_cell  = '<select name="' . esc_attr( $bg_field ) . '">';
                    foreach ( $bg_choices as $choice => $label ) {
                        $bg_cell .= '<option value="' . esc_attr( $choice ) . '" ' . selected( $bg_value, $choice, false ) . '>' . esc_html( $label ) . '</option>';
                    }
                    $bg_cell .= '</select>';
                }

                $order_rows .= '<tr>
                    <td style="padding:2px 12px 2px 0;">' . esc_html( $order_labels[ $key ] ) . '</td>
                    <td style="padding:2px 12px 2px 0;"><input type="number" name="' . esc_attr( $field ) . '" value="' . esc_attr( $value ) . '" step="1" ' . $disabled . '></td>
                    <td style="padding:2px 0;">' . $bg_cell . '</td>
                </tr>';
            }
            dsp_hp_section( 'Section Order & Backgrounds', '
                <p class="description" style="margin-bottom:8px;">' . esc_html__( 'Lower numbers appear first. Values map to CSS order properties, spaced by 10 so you can slot a section between two others. Hero is always first. Site plugins can still override with CSS.', 'dandysite-victoria' ) . '</p>
                <p class="description" style="margin-bottom:8px;">' . esc_html__( 'Background applies a surface context to the section: Light (page background), Surface (tinted), or Dark (uses the site\'s dark-surface colors). Default keeps each section\'s built-in background — Issues is dark and Get Involved uses the accent color by default.', 'dandysite-victoria' ) . '</p>
                <table>' . $order_rows . '</table>
            ' );

            // ---- Bio ----
            // ---- Hero ----
            dsp_hp_section( 'Hero', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_hero" value="1" ' . checked( $show_hero, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p class="description">' . esc_html__( 'When the hero is hidden, the header automatically uses the solid style on the homepage (there is nothing to overlay).', 'dandysite-victoria' ) . '</p>
            ' );

            dsp_hp_section( 'Bio / Meet the Candidate', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_bio" value="1" ' . checked( $show_bio, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p class="description">' . esc_html__( 'Pulls content from the page selected as the Bio page in Appearance → Theme Settings.', 'dandysite-victoria' ) . '</p>
                <p></p>
                <strong>' . esc_html__( 'Image display:', 'dandysite-victoria' ) . '</strong>
                <p style="margin:6px 0 0;">
                    <label style="display:block;margin-bottom:4px;">
                        <input type="radio" name="dsp_hp_bio_image_mode" value="featured" ' . checked( $bio_image_mode, 'featured', false ) . '>
                        ' . esc_html__( 'Show image beside the text (default)', 'dandysite-victoria' ) . '
                    </label>
                    <label style="display:block;margin-bottom:4px;">
                        <input type="radio" name="dsp_hp_bio_image_mode" value="none" ' . checked( $bio_image_mode, 'none', false ) . '>
                        ' . esc_html__( 'No image — text only', 'dandysite-victoria' ) . '
                    </label>
                    <label style="display:block;margin-bottom:4px;">
                        <input type="radio" name="dsp_hp_bio_image_mode" value="background" ' . checked( $bio_image_mode, 'background', false ) . '>
                        ' . esc_html__( 'Use image as section background (text overlays it)', 'dandysite-victoria' ) . '
                    </label>
                </p>
                <p></p>
                <strong>' . esc_html__( 'Image override:', 'dandysite-victoria' ) . '</strong>
                <p class="description" style="margin-bottom:8px;">' . esc_html__( 'By default the Bio page\'s Featured Image is used. Choose a different image here to override it (applies to both beside-text and background modes).', 'dandysite-victoria' ) . '</p>
                <div id="dsp-bio-image-preview" style="margin-bottom:8px;' . ( $bio_image_preview ? '' : 'display:none;' ) . '">
                    <img src="' . esc_url( $bio_image_preview ) . '" style="max-width:200px;height:auto;border:1px solid #ddd;border-radius:4px;">
                </div>
                <input type="hidden" name="dsp_hp_bio_image_id" id="dsp_hp_bio_image_id" value="' . esc_attr( $bio_image_id ) . '">
                <button type="button" class="button" id="dsp-bio-image-select">' . esc_html__( 'Choose Image', 'dandysite-victoria' ) . '</button>
                <button type="button" class="button" id="dsp-bio-image-remove" ' . ( $bio_image_id ? '' : 'style="display:none;"' ) . '>' . esc_html__( 'Remove Override', 'dandysite-victoria' ) . '</button>
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
                <p></p>
                <label style="display:block;">' . esc_html__( 'View All button links to:', 'dandysite-victoria' ) . '<br>
                    <input type="text" name="dsp_hp_articles_view_all_url" value="' . esc_attr( get_option( 'dsp_hp_articles_view_all_url', '' ) ) . '" class="regular-text" placeholder="' . esc_attr__( 'Default: the blog page', 'dandysite-victoria' ) . '">
                </label>
                <p class="description">' . esc_html__( 'Full URL or a path like /newsletter/. Leave blank for the default (your Posts page, or /blog/).', 'dandysite-victoria' ) . '</p>
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
                <p></p>
                <strong>' . esc_html__( 'Layout:', 'dandysite-victoria' ) . '</strong>
                <p style="margin:6px 0 0;">
                    <label style="display:block;margin-bottom:4px;">
                        <input type="radio" name="dsp_hp_endorsements_layout" value="grid" ' . checked( $end_layout, 'grid', false ) . '>
                        ' . esc_html__( 'Grid — cards side by side (default)', 'dandysite-victoria' ) . '
                    </label>
                    <label style="display:block;margin-bottom:4px;">
                        <input type="radio" name="dsp_hp_endorsements_layout" value="carousel" ' . checked( $end_layout, 'carousel', false ) . '>
                        ' . esc_html__( 'Carousel — one at a time, auto-cycling with a crossfade', 'dandysite-victoria' ) . '
                    </label>
                </p>
            ' );

            // ---- CTA ----
            $cta_title = get_option( 'dsp_hp_cta_title', '' );
            $cta_text  = get_option( 'dsp_hp_cta_text', '' );

            // If the button fields have never been saved, prefill the form
            // with the theme defaults so the first save preserves the
            // section exactly as it renders today.
            $cta_btn_defaults = [
                1 => [ __( 'Volunteer', 'dandysite-victoria' ),     '#volunteer', 'solid'   ],
                2 => [ __( 'Donate', 'dandysite-victoria' ),        '#donate',    'outline' ],
                3 => [ __( 'Stay Informed', 'dandysite-victoria' ), '#signup',    'solid'   ],
            ];
            $cta_never_saved = ( false === get_option( 'dsp_hp_cta_btn1_label', false ) );

            $media_kit_id   = (int) get_option( 'dsp_hp_media_kit_id', 0 );
            $media_kit_name = $media_kit_id ? basename( (string) get_attached_file( $media_kit_id ) ) : '';

            $cta_btn_rows = '<tr>
                <th style="text-align:left;padding:2px 12px 6px 0;">' . esc_html__( 'Label', 'dandysite-victoria' ) . '</th>
                <th style="text-align:left;padding:2px 12px 6px 0;">' . esc_html__( 'URL', 'dandysite-victoria' ) . '</th>
                <th style="text-align:left;padding:2px 0 6px;">' . esc_html__( 'Style', 'dandysite-victoria' ) . '</th>
            </tr>';
            for ( $i = 1; $i <= 3; $i++ ) {
                if ( $cta_never_saved ) {
                    list( $b_label, $b_url, $b_style ) = $cta_btn_defaults[ $i ];
                } else {
                    $b_label = get_option( "dsp_hp_cta_btn{$i}_label", '' );
                    $b_url   = get_option( "dsp_hp_cta_btn{$i}_url", '' );
                    $b_style = get_option( "dsp_hp_cta_btn{$i}_style", 'solid' );
                }
                $cta_btn_rows .= '<tr>
                    <td style="padding:2px 12px 2px 0;"><input type="text" name="dsp_hp_cta_btn' . $i . '_label" value="' . esc_attr( $b_label ) . '" style="width:160px;"></td>
                    <td style="padding:2px 12px 2px 0;"><input type="text" name="dsp_hp_cta_btn' . $i . '_url" value="' . esc_attr( $b_url ) . '" class="regular-text" placeholder="https:// or #anchor"></td>
                    <td style="padding:2px 0;"><select name="dsp_hp_cta_btn' . $i . '_style">
                        <option value="solid" '   . selected( $b_style, 'solid', false )   . '>' . esc_html__( 'Solid', 'dandysite-victoria' )   . '</option>
                        <option value="outline" ' . selected( $b_style, 'outline', false ) . '>' . esc_html__( 'Outline', 'dandysite-victoria' ) . '</option>
                    </select></td>
                </tr>';
            }
            dsp_hp_section( 'Get Involved / CTA', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_cta" value="1" ' . checked( $show_cta, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p></p>
                <label style="display:block;margin-bottom:8px;">' . esc_html__( 'Heading:', 'dandysite-victoria' ) . '<br>
                    <input type="text" name="dsp_hp_cta_title" value="' . esc_attr( $cta_title ) . '" class="regular-text" placeholder="' . esc_attr__( 'Get Involved', 'dandysite-victoria' ) . '">
                </label>
                <label style="display:block;margin-bottom:8px;">' . esc_html__( 'Text:', 'dandysite-victoria' ) . '<br>
                    <textarea name="dsp_hp_cta_text" rows="3" class="large-text" placeholder="' . esc_attr__( 'Join the movement. Volunteer your time, make a donation, or sign up to stay informed.', 'dandysite-victoria' ) . '">' . esc_textarea( $cta_text ) . '</textarea>
                </label>
                <p class="description">' . esc_html__( 'Leave heading/text blank to use the theme defaults shown as placeholders.', 'dandysite-victoria' ) . '</p>
                <strong>' . esc_html__( 'Buttons:', 'dandysite-victoria' ) . '</strong>
                <p class="description" style="margin:4px 0 8px;">' . esc_html__( 'Buttons with a blank label are hidden. If none of these fields have ever been saved, the theme default buttons (Volunteer / Donate / Stay Informed) are shown.', 'dandysite-victoria' ) . '</p>
                <table>' . $cta_btn_rows . '</table>
                <p></p>
                <strong>' . esc_html__( 'Media Kit:', 'dandysite-victoria' ) . '</strong>
                <p class="description" style="margin:4px 0 8px;">' . esc_html__( 'Upload a media kit (PDF or ZIP). When set, a "Download Media Kit" link appears at the bottom of this section.', 'dandysite-victoria' ) . '</p>
                <input type="hidden" name="dsp_hp_media_kit_id" id="dsp_hp_media_kit_id" value="' . esc_attr( $media_kit_id ) . '">
                <p id="dsp-media-kit-current" style="' . ( $media_kit_id ? '' : 'display:none;' ) . 'margin:0 0 8px;">
                    <span class="dashicons dashicons-media-document" style="vertical-align:middle;"></span>
                    <code id="dsp-media-kit-filename">' . esc_html( $media_kit_name ) . '</code>
                </p>
                <button type="button" class="button" id="dsp-media-kit-select">' . esc_html__( 'Choose File', 'dandysite-victoria' ) . '</button>
                <button type="button" class="button" id="dsp-media-kit-remove" ' . ( $media_kit_id ? '' : 'style="display:none;"' ) . '>' . esc_html__( 'Remove', 'dandysite-victoria' ) . '</button>
            ' );

            // ---- Connect ----
            dsp_hp_section( 'Contact / Connect', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_connect" value="1" ' . checked( $show_connect, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
                <p class="description">' . esc_html__( 'Heading, short paragraph, email link, and social icons. Social icons come from Appearance → Theme Settings → Social Links.', 'dandysite-victoria' ) . '</p>
                <p></p>
                <label style="display:block;margin-bottom:8px;">' . esc_html__( 'Heading:', 'dandysite-victoria' ) . '<br>
                    <input type="text" name="dsp_hp_connect_heading" value="' . esc_attr( $connect_heading ) . '" class="regular-text">
                </label>
                <label style="display:block;margin-bottom:8px;">' . esc_html__( 'Text:', 'dandysite-victoria' ) . '<br>
                    <textarea name="dsp_hp_connect_text" rows="3" class="large-text">' . esc_textarea( $connect_text ) . '</textarea>
                </label>
                <label style="display:block;">' . esc_html__( 'Email address:', 'dandysite-victoria' ) . '<br>
                    <input type="email" name="dsp_hp_connect_email" value="' . esc_attr( $connect_email ) . '" class="regular-text" placeholder="hello@example.com">
                </label>
                <p class="description">' . esc_html__( 'Rendered as a prominent mailto link. Leave blank to show socials only. (Contact form option planned for later.)', 'dandysite-victoria' ) . '</p>
            ' );
            ?>

            <?php submit_button( __( 'Save Homepage Settings', 'dandysite-victoria' ) ); ?>
        </form>
    </div>

    <script>
    // Media library picker for the bio image override
    jQuery(function($){
        var frame;
        $('#dsp-bio-image-select').on('click', function(e){
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: '<?php echo esc_js( __( 'Choose Bio Image', 'dandysite-victoria' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use this image', 'dandysite-victoria' ) ); ?>' },
                library: { type: 'image' },
                multiple: false
            });
            frame.on('select', function(){
                var att = frame.state().get('selection').first().toJSON();
                var url = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : att.url;
                $('#dsp_hp_bio_image_id').val(att.id);
                $('#dsp-bio-image-preview').show().find('img').attr('src', url);
                $('#dsp-bio-image-remove').show();
            });
            frame.open();
        });
        $('#dsp-bio-image-remove').on('click', function(e){
            e.preventDefault();
            $('#dsp_hp_bio_image_id').val('0');
            $('#dsp-bio-image-preview').hide();
            $(this).hide();
        });

        // Media library picker for the media kit (any file type)
        var kitFrame;
        $('#dsp-media-kit-select').on('click', function(e){
            e.preventDefault();
            if (kitFrame) { kitFrame.open(); return; }
            kitFrame = wp.media({
                title: '<?php echo esc_js( __( 'Choose Media Kit', 'dandysite-victoria' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use this file', 'dandysite-victoria' ) ); ?>' },
                multiple: false
            });
            kitFrame.on('select', function(){
                var att = kitFrame.state().get('selection').first().toJSON();
                $('#dsp_hp_media_kit_id').val(att.id);
                $('#dsp-media-kit-filename').text(att.filename || att.title);
                $('#dsp-media-kit-current').show();
                $('#dsp-media-kit-remove').show();
            });
            kitFrame.open();
        });
        $('#dsp-media-kit-remove').on('click', function(e){
            e.preventDefault();
            $('#dsp_hp_media_kit_id').val('0');
            $('#dsp-media-kit-current').hide();
            $(this).hide();
        });
    });
    </script>
    <?php
}
