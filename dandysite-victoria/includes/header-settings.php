<?php
/**
 * Header Settings
 * Adds a Header section to the existing Theme Settings page.
 * dandysite-victoria theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// =====================================================================
// REGISTER SETTINGS
// =====================================================================

function dsp_register_header_settings() {

    // --- Individual settings ---

    $header_options = [
        'dsp_header_default_style'          => 'solid',     // 'overlay' | 'solid'
        'dsp_header_nav_breakpoint'         => 'tablet',    // 'always' | 'tablet' | 'mobile' | custom int
        'dsp_header_nav_breakpoint_custom'  => '1100',      // px value when breakpoint = 'custom'
        'dsp_header_overlay_scroll_reveal'  => 'solid',     // 'solid' | 'transparent'
        'dsp_header_solid_scroll_reveal'    => 'solid',     // 'solid' | 'transparent'
        'dsp_header_scroll_threshold'       => '80',        // px scrolled before hiding
    ];

    foreach ($header_options as $key => $default) {
        register_setting('dsp_theme_settings', $key, [
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => $default,
        ]);
    }

    // --- Settings section ---

    add_settings_section(
        'dsp_header_section',
        __('Header Settings', 'dandysite-victoria'),
        'dsp_header_section_callback',
        'dsp-theme-settings'
    );

    // --- Fields ---

    add_settings_field(
        'dsp_header_default_style',
        __('Default Header Style', 'dandysite-victoria'),
        'dsp_header_default_style_field',
        'dsp-theme-settings',
        'dsp_header_section'
    );

    add_settings_field(
        'dsp_header_nav_breakpoint',
        __('Navigation Breakpoint', 'dandysite-victoria'),
        'dsp_header_nav_breakpoint_field',
        'dsp-theme-settings',
        'dsp_header_section'
    );

    add_settings_field(
        'dsp_header_scroll_behavior',
        __('Scroll-Reveal Behavior', 'dandysite-victoria'),
        'dsp_header_scroll_behavior_field',
        'dsp-theme-settings',
        'dsp_header_section'
    );
}
add_action('admin_init', 'dsp_register_header_settings');


// =====================================================================
// SECTION & FIELD CALLBACKS
// =====================================================================

function dsp_header_section_callback() {
    echo '<p>' . esc_html__('Configure header style and scroll behavior. Logo is set via Appearance → Customize → Site Identity. For overlay headers, the logo is automatically inverted to white via CSS filter.', 'dandysite-victoria') . '</p>';
    echo '<p>' . esc_html__('Per-page override: choose the "Overlay Header" or "Solid Header" page template in the page editor.', 'dandysite-victoria') . '</p>';
}

/**
 * Default header style
 */
function dsp_header_default_style_field() {
    $value = get_option('dsp_header_default_style', 'solid');
    ?>
    <fieldset>
        <label style="display: block; margin-bottom: 8px;">
            <input type="radio" name="dsp_header_default_style" value="solid" <?php checked($value, 'solid'); ?>>
            <strong><?php esc_html_e('Solid', 'dandysite-victoria'); ?></strong>
            &mdash; <?php esc_html_e('Header sits above content with a solid background. Applies to all pages by default.', 'dandysite-victoria'); ?>
        </label>
        <label style="display: block; margin-bottom: 8px;">
            <input type="radio" name="dsp_header_default_style" value="overlay" <?php checked($value, 'overlay'); ?>>
            <strong><?php esc_html_e('Transparent Overlay', 'dandysite-victoria'); ?></strong>
            &mdash; <?php esc_html_e('Header overlays the hero/content with a transparent background. Light logo and text are used.', 'dandysite-victoria'); ?>
        </label>
    </fieldset>
    <p class="description"><?php esc_html_e('Per-page override: assign the "Overlay Header" or "Solid Header" page template to any page.', 'dandysite-victoria'); ?></p>
    <?php
}

/**
 * Nav breakpoint
 */
function dsp_header_nav_breakpoint_field() {
    $value  = get_option('dsp_header_nav_breakpoint', 'tablet');
    $custom = get_option('dsp_header_nav_breakpoint_custom', '1100');
    ?>
    <fieldset>
        <label style="display: block; margin-bottom: 6px;">
            <input type="radio" name="dsp_header_nav_breakpoint" value="always" <?php checked($value, 'always'); ?>>
            <?php esc_html_e('Always show hamburger (no inline nav)', 'dandysite-victoria'); ?>
        </label>
        <label style="display: block; margin-bottom: 6px;">
            <input type="radio" name="dsp_header_nav_breakpoint" value="tablet" <?php checked($value, 'tablet'); ?>>
            <?php esc_html_e('Tablet and below (≤1024px)', 'dandysite-victoria'); ?>
        </label>
        <label style="display: block; margin-bottom: 6px;">
            <input type="radio" name="dsp_header_nav_breakpoint" value="mobile" <?php checked($value, 'mobile'); ?>>
            <?php esc_html_e('Mobile only (≤768px)', 'dandysite-victoria'); ?>
        </label>
        <label style="display: block; margin-bottom: 6px;">
            <input type="radio" name="dsp_header_nav_breakpoint" value="custom" <?php checked($value, 'custom'); ?>>
            <?php esc_html_e('Custom value:', 'dandysite-victoria'); ?>
            <input type="number" name="dsp_header_nav_breakpoint_custom"
                   value="<?php echo esc_attr($custom); ?>"
                   min="320" max="2560" step="1"
                   style="width: 80px; margin-left: 6px;">
            <span>px</span>
        </label>
    </fieldset>
    <p class="description"><?php esc_html_e('Below this width, the full nav is replaced with a hamburger menu.', 'dandysite-victoria'); ?></p>
    <?php
}

/**
 * Scroll reveal behavior
 */
function dsp_header_scroll_behavior_field() {
    $overlay_reveal = get_option('dsp_header_overlay_scroll_reveal', 'solid');
    $solid_reveal   = get_option('dsp_header_solid_scroll_reveal', 'solid');
    $threshold      = get_option('dsp_header_scroll_threshold', '80');
    ?>
    <table class="form-table" style="margin: 0;">
        <tr>
            <th style="padding: 4px 16px 4px 0; font-weight: normal; white-space: nowrap;">
                <?php esc_html_e('Overlay header — on scroll up:', 'dandysite-victoria'); ?>
            </th>
            <td style="padding: 4px 0;">
                <label style="margin-right: 16px;">
                    <input type="radio" name="dsp_header_overlay_scroll_reveal" value="solid" <?php checked($overlay_reveal, 'solid'); ?>>
                    <?php esc_html_e('Return as solid with background', 'dandysite-victoria'); ?>
                </label>
                <label>
                    <input type="radio" name="dsp_header_overlay_scroll_reveal" value="transparent" <?php checked($overlay_reveal, 'transparent'); ?>>
                    <?php esc_html_e('Return as transparent / glassy', 'dandysite-victoria'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th style="padding: 4px 16px 4px 0; font-weight: normal; white-space: nowrap;">
                <?php esc_html_e('Solid header — on scroll up:', 'dandysite-victoria'); ?>
            </th>
            <td style="padding: 4px 0;">
                <label style="margin-right: 16px;">
                    <input type="radio" name="dsp_header_solid_scroll_reveal" value="solid" <?php checked($solid_reveal, 'solid'); ?>>
                    <?php esc_html_e('Return as solid with background', 'dandysite-victoria'); ?>
                </label>
                <label>
                    <input type="radio" name="dsp_header_solid_scroll_reveal" value="transparent" <?php checked($solid_reveal, 'transparent'); ?>>
                    <?php esc_html_e('Return as transparent / glassy', 'dandysite-victoria'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th style="padding: 4px 16px 4px 0; font-weight: normal; white-space: nowrap;">
                <?php esc_html_e('Hide threshold:', 'dandysite-victoria'); ?>
            </th>
            <td style="padding: 4px 0;">
                <input type="number" name="dsp_header_scroll_threshold"
                       value="<?php echo esc_attr($threshold); ?>"
                       min="0" max="500" step="10" style="width: 70px;">
                <span><?php esc_html_e('px scrolled before header hides', 'dandysite-victoria'); ?></span>
            </td>
        </tr>
    </table>
    <?php
}


// =====================================================================
// ADMIN ASSETS (media uploader for light logo)
// =====================================================================

function dsp_header_admin_assets($hook) {
    if ($hook !== 'appearance_page_dsp-theme-settings') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'dsp-header-admin',
        get_template_directory_uri() . '/assets/js/header-admin.js',
        [],
        filemtime(get_template_directory() . '/assets/js/header-admin.js'),
        true
    );
}
add_action('admin_enqueue_scripts', 'dsp_header_admin_assets');


// =====================================================================
// HELPER: Get resolved nav breakpoint in px
// =====================================================================

function dsp_get_nav_breakpoint_px() {
    $breakpoint = get_option('dsp_header_nav_breakpoint', 'tablet');
    switch ($breakpoint) {
        case 'always':  return 9999;
        case 'tablet':  return 1024;
        case 'mobile':  return 768;
        case 'custom':  return (int) get_option('dsp_header_nav_breakpoint_custom', 1100);
        default:        return 1024;
    }
}
