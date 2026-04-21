<?php
/**
 * Site Identity Settings
 * Standalone options page for managing logo variants.
 * dandysite-victoria theme
 *
 * Logo variants:
 *   full  — Full color logo (light backgrounds, footer, solid header)
 *   mono  — 1-color dark logo (CSS derives white version via filter)
 *   words — Wordmark only, no icon (optional, for compact contexts)
 *
 * The mono logo approach:
 *   Upload one dark/single-color file (doesn't need to be pure black —
 *   any dark tone works). CSS filter brightness(0) collapses it to true
 *   black, then invert(1) flips to white. This gives you both the dark
 *   and white treatments from a single file, and works regardless of the
 *   original tone (warm black, dark brown, dark navy, etc.).
 *
 *   Dark treatment:  filter: none
 *   White treatment: filter: brightness(0) invert(1)
 *
 * Usage in templates:
 *   dsp_logo_img( 'mono' )          — dark version, no filter
 *   dsp_logo_img( 'mono-white' )    — same file, CSS-inverted to white
 *   dsp_logo_img( 'full' )          — full color version
 *   dsp_get_logo_url( 'mono' )      — URL only (apply your own filter in CSS)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// =====================================================================
// ADMIN MENU
// =====================================================================

function dsp_add_site_identity_page() {
    add_theme_page(
        __( 'Site Identity', 'dandysite-victoria' ),
        __( 'Site Identity', 'dandysite-victoria' ),
        'manage_options',
        'dsp-site-identity',
        'dsp_site_identity_page'
    );
}
add_action( 'admin_menu', 'dsp_add_site_identity_page' );


// =====================================================================
// REGISTER SETTINGS
// =====================================================================

function dsp_register_site_identity_settings() {

    $logo_options = [
        'dsp_logo_full'  => '',   // Attachment ID — full color logo
        'dsp_logo_mono'  => '',   // Attachment ID — 1-color dark logo (white derived via CSS)
    ];

    foreach ( $logo_options as $key => $default ) {
        register_setting( 'dsp_site_identity', $key, [
            'sanitize_callback' => 'absint',
            'default'           => $default,
        ] );
    }

    add_settings_section(
        'dsp_logos_section',
        __( 'Logo Variants', 'dandysite-victoria' ),
        'dsp_logos_section_callback',
        'dsp-site-identity'
    );

    add_settings_field(
        'dsp_logo_full',
        __( 'Full Color Logo', 'dandysite-victoria' ),
        'dsp_logo_field_callback',
        'dsp-site-identity',
        'dsp_logos_section',
        [
            'option' => 'dsp_logo_full',
            'label'  => __( 'Used on light backgrounds, footer, solid header.', 'dandysite-victoria' ),
            'type'   => 'full',
        ]
    );

    add_settings_field(
        'dsp_logo_mono',
        __( '1-Color Logo', 'dandysite-victoria' ),
        'dsp_logo_field_callback',
        'dsp-site-identity',
        'dsp_logos_section',
        [
            'option' => 'dsp_logo_mono',
            'label'  => __( 'Single-color dark version (any dark tone — does not need to be pure black). CSS automatically derives the white version, so no second file is needed. PNG or SVG with transparent background.', 'dandysite-victoria' ),
            'type'   => 'mono',
        ]
    );

}
add_action( 'admin_init', 'dsp_register_site_identity_settings' );


// =====================================================================
// SECTION & FIELD CALLBACKS
// =====================================================================

function dsp_logos_section_callback() {
    echo '<p>' . esc_html__( 'Upload logo variants for different display contexts. PNG or SVG with transparent background recommended for all variants.', 'dandysite-victoria' ) . '</p>';
    echo '<p>' . esc_html__( 'SVG files are supported and preferred for sharpness at all sizes.', 'dandysite-victoria' ) . '</p>';
}

function dsp_logo_field_callback( $args ) {
    $option    = $args['option'];
    $label     = $args['label'];
    $type      = $args['type'] ?? 'full';
    $attach_id = (int) get_option( $option, 0 );
    $img_url   = $attach_id ? wp_get_attachment_image_url( $attach_id, 'medium' ) : '';
    $input_id  = 'dsp-logo-' . sanitize_key( $option );
    $has_img   = ! empty( $img_url );
    ?>
    <div class="dsp-logo-field" id="<?php echo esc_attr( $input_id ); ?>-wrap">

        <?php if ( $type === 'mono' ) : ?>
            <!-- Dual preview: dark on light bg, same file CSS-inverted on dark bg -->
            <div style="display: flex; gap: 8px; margin-bottom: 12px;">

                <div style="flex: 1; max-width: 180px;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">
                        <?php esc_html_e( 'Dark (original)', 'dandysite-victoria' ); ?>
                    </div>
                    <div style="min-height: 80px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 12px;">
                        <img id="<?php echo esc_attr( $input_id ); ?>-preview-dark"
                             src="<?php echo $has_img ? esc_url( $img_url ) : ''; ?>"
                             style="max-width: 100%; max-height: 70px; object-fit: contain; <?php echo ! $has_img ? 'display:none;' : ''; ?>"
                             alt="">
                        <span id="<?php echo esc_attr( $input_id ); ?>-empty-dark"
                              style="color: #aaa; font-size: 12px; text-align: center; <?php echo $has_img ? 'display:none;' : ''; ?>">
                            <?php esc_html_e( 'No logo', 'dandysite-victoria' ); ?>
                        </span>
                    </div>
                </div>

                <div style="flex: 1; max-width: 180px;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">
                        <?php esc_html_e( 'White (CSS-derived)', 'dandysite-victoria' ); ?>
                    </div>
                    <div style="min-height: 80px; background: #2A2420; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 12px;">
                        <img id="<?php echo esc_attr( $input_id ); ?>-preview-white"
                             src="<?php echo $has_img ? esc_url( $img_url ) : ''; ?>"
                             style="max-width: 100%; max-height: 70px; object-fit: contain; filter: brightness(0) invert(1); <?php echo ! $has_img ? 'display:none;' : ''; ?>"
                             alt="">
                        <span id="<?php echo esc_attr( $input_id ); ?>-empty-white"
                              style="color: #666; font-size: 12px; text-align: center; <?php echo $has_img ? 'display:none;' : ''; ?>">
                            <?php esc_html_e( 'No logo', 'dandysite-victoria' ); ?>
                        </span>
                    </div>
                </div>

            </div>

            <input type="hidden"
                   id="<?php echo esc_attr( $input_id ); ?>"
                   name="<?php echo esc_attr( $option ); ?>"
                   value="<?php echo esc_attr( $attach_id ?: '' ); ?>"
                   data-mono="1">

        <?php else : ?>
            <!-- Standard single preview -->
            <div style="display: inline-flex; margin-bottom: 12px;">
                <div style="width: 200px; min-height: 80px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; padding: 12px;">
                    <img id="<?php echo esc_attr( $input_id ); ?>-preview"
                         src="<?php echo $has_img ? esc_url( $img_url ) : ''; ?>"
                         style="max-width: 100%; max-height: 80px; object-fit: contain; <?php echo ! $has_img ? 'display:none;' : ''; ?>"
                         alt="">
                    <span id="<?php echo esc_attr( $input_id ); ?>-empty"
                          style="color: #999; font-size: 12px; text-align: center; <?php echo $has_img ? 'display:none;' : ''; ?>">
                        <?php esc_html_e( 'No logo uploaded', 'dandysite-victoria' ); ?>
                    </span>
                </div>
            </div>
            <br>

            <input type="hidden"
                   id="<?php echo esc_attr( $input_id ); ?>"
                   name="<?php echo esc_attr( $option ); ?>"
                   value="<?php echo esc_attr( $attach_id ?: '' ); ?>">

        <?php endif; ?>

        <button type="button"
                class="button dsp-logo-upload"
                data-input-id="<?php echo esc_attr( $input_id ); ?>"
                data-type="<?php echo esc_attr( $type ); ?>"
                data-title="<?php esc_attr_e( 'Select Logo', 'dandysite-victoria' ); ?>"
                data-button="<?php esc_attr_e( 'Use This Logo', 'dandysite-victoria' ); ?>">
            <?php echo $attach_id ? esc_html__( 'Change Logo', 'dandysite-victoria' ) : esc_html__( 'Upload Logo', 'dandysite-victoria' ); ?>
        </button>

        <button type="button"
                class="button dsp-logo-remove"
                data-input-id="<?php echo esc_attr( $input_id ); ?>"
                data-type="<?php echo esc_attr( $type ); ?>"
                style="margin-left: 6px; color: #b32d2e; <?php echo ! $attach_id ? 'display:none;' : ''; ?>">
            <?php esc_html_e( 'Remove', 'dandysite-victoria' ); ?>
        </button>

        <p class="description" style="margin-top: 8px; max-width: 420px;">
            <?php echo esc_html( $label ); ?>
        </p>

    </div>
    <?php
}


// =====================================================================
// PAGE OUTPUT
// =====================================================================

function dsp_site_identity_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p style="color: #666; max-width: 600px;">
            <?php esc_html_e( 'Manage logo files used across the site. Each context pulls the appropriate variant automatically. Upload once — the theme handles dark/light switching.', 'dandysite-victoria' ); ?>
        </p>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'dsp_site_identity' );
            do_settings_sections( 'dsp-site-identity' );
            submit_button( __( 'Save Logos', 'dandysite-victoria' ) );
            ?>
        </form>

        <hr style="margin: 2em 0;">
        <h2 style="font-size: 1.1em;"><?php esc_html_e( 'Navigation Logo (Wordmark)', 'dandysite-victoria' ); ?></h2>
        <p style="color: #666; max-width: 600px;">
            <?php esc_html_e( 'The text logo displayed in the header navigation is managed separately via ', 'dandysite-victoria' ); ?>
            <strong><?php esc_html_e( 'Appearance → Customize → Site Identity', 'dandysite-victoria' ); ?></strong>
            <?php esc_html_e( '. This is the standard WordPress logo setting and controls the wordmark shown in the desktop menu.', 'dandysite-victoria' ); ?>
        </p>
    </div>
    <?php
}


// =====================================================================
// ADMIN ASSETS — media uploader
// =====================================================================

function dsp_site_identity_admin_assets( $hook ) {
    if ( $hook !== 'appearance_page_dsp-site-identity' ) {
        return;
    }
    wp_enqueue_media();
    wp_add_inline_script( 'jquery', dsp_site_identity_uploader_js() );
}
add_action( 'admin_enqueue_scripts', 'dsp_site_identity_admin_assets' );

function dsp_site_identity_uploader_js() {
    ob_start();
    ?>
    jQuery(function($) {

        // ---- Upload ----
        $(document).on('click', '.dsp-logo-upload', function(e) {
            e.preventDefault();
            var btn     = $(this);
            var inputId = btn.data('input-id');
            var type    = btn.data('type');

            var frame = wp.media({
                title:    btn.data('title'),
                button:   { text: btn.data('button') },
                multiple: false,
                library:  { type: ['image'] }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var url = attachment.url;

                $('#' + inputId).val(attachment.id);

                if (type === 'mono') {
                    // Sync both previews from the same file
                    $('#' + inputId + '-preview-dark').attr('src', url).show();
                    $('#' + inputId + '-preview-white').attr('src', url).show();
                    $('#' + inputId + '-empty-dark').hide();
                    $('#' + inputId + '-empty-white').hide();
                } else {
                    $('#' + inputId + '-preview').attr('src', url).show();
                    $('#' + inputId + '-empty').hide();
                }

                btn.text('<?php echo esc_js( __( 'Change Logo', 'dandysite-victoria' ) ); ?>');
                btn.next('.dsp-logo-remove').show();
            });

            frame.open();
        });

        // ---- Remove ----
        $(document).on('click', '.dsp-logo-remove', function(e) {
            e.preventDefault();
            var btn     = $(this);
            var inputId = btn.data('input-id');
            var type    = btn.data('type');

            $('#' + inputId).val('');

            if (type === 'mono') {
                $('#' + inputId + '-preview-dark').attr('src', '').hide();
                $('#' + inputId + '-preview-white').attr('src', '').hide();
                $('#' + inputId + '-empty-dark').show();
                $('#' + inputId + '-empty-white').show();
            } else {
                $('#' + inputId + '-preview').attr('src', '').hide();
                $('#' + inputId + '-empty').show();
            }

            btn.prev('.dsp-logo-upload').text('<?php echo esc_js( __( 'Upload Logo', 'dandysite-victoria' ) ); ?>');
            btn.hide();
        });

    });
    <?php
    return ob_get_clean();
}


// =====================================================================
// PUBLIC HELPERS
// =====================================================================

/**
 * Get the URL for a logo variant.
 *
 * @param  string       $variant  'full' | 'mono' | 'mono-white'
 *                                'mono-white' resolves to the mono file URL
 *                                (caller applies the CSS filter).
 * @return string|false           URL or false if not set.
 */
function dsp_get_logo_url( $variant = 'full' ) {
    // mono-white is a virtual variant — same file, filter applied in CSS
    if ( $variant === 'mono-white' ) {
        $variant = 'mono';
    }

    $option_map = [
        'full'  => 'dsp_logo_full',
        'mono'  => 'dsp_logo_mono',
    ];

    if ( ! isset( $option_map[ $variant ] ) ) {
        return false;
    }

    $attach_id = (int) get_option( $option_map[ $variant ], 0 );
    if ( ! $attach_id ) {
        return false;
    }

    return wp_get_attachment_image_url( $attach_id, 'full' ) ?: false;
}


/**
 * Output a logo <img> tag.
 *
 * Variants:
 *   'full'        — full color, no filter
 *   'mono'        — 1-color dark, no filter  (dark bg: use as-is)
 *   'mono-white'  — 1-color, inverted to white via CSS filter  (dark bg: white logo)
 *
 * @param string $variant  See above.
 * @param string $class    CSS class(es) on the <img>.
 * @param string $size     WP image size. Default 'full'.
 */
function dsp_logo_img( $variant = 'full', $class = 'site-logo', $size = 'full' ) {

    $invert = ( $variant === 'mono-white' );
    $base   = $invert ? 'mono' : $variant;

    $option_map = [
        'full'  => 'dsp_logo_full',
        'mono'  => 'dsp_logo_mono',
    ];

    if ( ! isset( $option_map[ $base ] ) ) {
        return;
    }

    $attach_id = (int) get_option( $option_map[ $base ], 0 );
    $site_name = get_bloginfo( 'name' );
    // Hardcoded safe string — no user input
    $style     = $invert ? ' style="filter: brightness(0) invert(1);"' : '';

    if ( $attach_id ) {
        $url = wp_get_attachment_image_url( $attach_id, $size );
        $alt = get_post_meta( $attach_id, '_wp_attachment_image_alt', true ) ?: $site_name;
        if ( $url ) {
            printf(
                '<img src="%s" alt="%s" class="%s" loading="eager"%s>',
                esc_url( $url ),
                esc_attr( $alt ),
                esc_attr( $class ),
                $style
            );
            return;
        }
    }

    // Fallback to WP custom logo
    if ( has_custom_logo() ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        $url     = wp_get_attachment_image_url( $logo_id, $size );
        $alt     = get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) ?: $site_name;
        if ( $url ) {
            printf(
                '<img src="%s" alt="%s" class="%s" loading="eager"%s>',
                esc_url( $url ),
                esc_attr( $alt ),
                esc_attr( $class ),
                $style
            );
            return;
        }
    }

    // Last resort: site name text
    printf( '<span class="site-logo-text %s">%s</span>', esc_attr( $class ), esc_html( $site_name ) );
}
