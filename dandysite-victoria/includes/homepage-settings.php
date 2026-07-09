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
 *   dsp_hp_show_connect           bool    default true
 *   dsp_hp_connect_heading        string  default 'Get in Touch'
 *   dsp_hp_connect_text           string
 *   dsp_hp_connect_email          string
 *   dsp_hp_order_{section}        int     CSS order value per section (see defaults below)
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
            $order_rows = '';
            foreach ( dsp_hp_section_order_defaults() as $key => $default ) {
                $field = 'dsp_hp_order_' . str_replace( '-', '_', $key );
                $value = (int) get_option( $field, $default );
                $disabled = ( $key === 'hero' ) ? 'readonly style="width:60px;background:#f0f0f1;"' : 'style="width:60px;"';
                $order_rows .= '<tr>
                    <td style="padding:2px 12px 2px 0;">' . esc_html( $order_labels[ $key ] ) . '</td>
                    <td style="padding:2px 0;"><input type="number" name="' . esc_attr( $field ) . '" value="' . esc_attr( $value ) . '" step="1" ' . $disabled . '></td>
                </tr>';
            }
            dsp_hp_section( 'Section Order', '
                <p class="description" style="margin-bottom:8px;">' . esc_html__( 'Lower numbers appear first. Values map to CSS order properties, spaced by 10 so you can slot a section between two others. Hero is always first. Site plugins can still override with CSS.', 'dandysite-victoria' ) . '</p>
                <table>' . $order_rows . '</table>
            ' );

            // ---- Bio ----
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
            dsp_hp_section( 'Get Involved / CTA', '
                <label>
                    <input type="checkbox" name="dsp_hp_show_cta" value="1" ' . checked( $show_cta, '1', false ) . '>
                    ' . esc_html__( 'Show this section', 'dandysite-victoria' ) . '
                </label>
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
    });
    </script>
    <?php
}
