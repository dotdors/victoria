<?php
/**
 * Footer Settings
 * Registers footer layout and color options in the theme settings panel.
 * dandysite-victoria theme
 */

if (!defined('ABSPATH')) {
    exit;
}


// =====================================================================
// REGISTER SETTINGS
// =====================================================================

function dsp_register_footer_settings() {

    // Section
    add_settings_section(
        'dsp_footer_section',
        __('Footer', 'dandysite-victoria'),
        '__return_false',
        'dsp-theme-settings'
    );

    // Widget max-width
    register_setting('dsp_theme_settings', 'dsp_footer_widget_max_width', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    ]);
    add_settings_field(
        'dsp_footer_widget_max_width',
        __('Widget Max Width', 'dandysite-victoria'),
        'dsp_footer_widget_max_width_field',
        'dsp-theme-settings',
        'dsp_footer_section'
    );

    // Show logo
    register_setting('dsp_theme_settings', 'dsp_footer_show_logo', [
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ]);
    add_settings_field(
        'dsp_footer_show_logo',
        __('Footer Logo', 'dandysite-victoria'),
        'dsp_footer_show_logo_field',
        'dsp-theme-settings',
        'dsp_footer_section'
    );

    // Layout
    register_setting('dsp_theme_settings', 'dsp_footer_layout', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'left',
    ]);
    add_settings_field(
        'dsp_footer_layout',
        __('Footer Layout', 'dandysite-victoria'),
        'dsp_footer_layout_field',
        'dsp-theme-settings',
        'dsp_footer_section'
    );

    // Dark mode
    register_setting('dsp_theme_settings', 'dsp_footer_dark', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'light',
    ]);
    add_settings_field(
        'dsp_footer_dark',
        __('Footer Color', 'dandysite-victoria'),
        'dsp_footer_dark_field',
        'dsp-theme-settings',
        'dsp_footer_section'
    );
}
add_action('admin_init', 'dsp_register_footer_settings');


// =====================================================================
// FIELD CALLBACKS
// =====================================================================

function dsp_footer_widget_max_width_field() {
    $value = get_option('dsp_footer_widget_max_width', '');
    printf(
        '<input type="text" name="dsp_footer_widget_max_width" value="%s" placeholder="e.g. 220px or 18rem" style="width:200px">',
        esc_attr($value)
    );
    echo '<p class="description">' . esc_html__('Optional. Limits how wide each widget column can grow. Leave blank for no limit.', 'dandysite-victoria') . '</p>';
}

/**
 * Output inline CSS for footer widget max-width if set.
 */
function dsp_footer_inline_css() {
    $max_width = get_option('dsp_footer_widget_max_width', '');
    if (!$max_width) return;
    printf(
        '<style>.footer-widget { max-width: %s; }</style>',
        esc_html($max_width)
    );
}
add_action('wp_head', 'dsp_footer_inline_css');

function dsp_footer_show_logo_field() {
    $value = get_option('dsp_footer_show_logo', 0);
    printf(
        '<label><input type="checkbox" name="dsp_footer_show_logo" value="1"%s> %s</label>',
        checked(1, $value, false),
        esc_html__('Show site logo in footer', 'dandysite-victoria')
    );
    echo '<p class="description">' . esc_html__('Uses the same logo as the header. Upload a different image via the widget area if needed.', 'dandysite-victoria') . '</p>';
}

function dsp_footer_layout_field() {
    $value = get_option('dsp_footer_layout', 'left');
    $options = [
        'left'    => __('Left justified (content-width columns)', 'dandysite-victoria'),
        'center'  => __('Centered (columns grouped in center)', 'dandysite-victoria'),
        'spaced'  => __('Spaced even (columns spread full width)', 'dandysite-victoria'),
    ];
    foreach ($options as $key => $label) {
        printf(
            '<label style="display:block;margin-bottom:6px"><input type="radio" name="dsp_footer_layout" value="%s"%s> %s</label>',
            esc_attr($key),
            checked($value, $key, false),
            esc_html($label)
        );
    }
    echo '<p class="description">' . esc_html__('Controls how widget columns are distributed across the footer.', 'dandysite-victoria') . '</p>';
}

function dsp_footer_dark_field() {
    $value = get_option('dsp_footer_dark', 'light');
    $options = [
        'light' => __('Light (matches site background)', 'dandysite-victoria'),
        'dark'  => __('Dark (dark background, light text)', 'dandysite-victoria'),
    ];
    foreach ($options as $key => $label) {
        printf(
            '<label style="display:block;margin-bottom:6px"><input type="radio" name="dsp_footer_dark" value="%s"%s> %s</label>',
            esc_attr($key),
            checked($value, $key, false),
            esc_html($label)
        );
    }
}


// =====================================================================
// BODY CLASS
// =====================================================================

function dsp_footer_body_classes($classes) {
    $layout    = get_option('dsp_footer_layout', 'left');
    $color     = get_option('dsp_footer_dark', 'light');
    $show_logo = get_option('dsp_footer_show_logo', 0);

    $classes[] = 'footer-layout-' . $layout;
    if ($color === 'dark') {
        $classes[] = 'footer-dark';
    }
    if ($show_logo) {
        $classes[] = 'footer-show-logo';
    }

    return $classes;
}
add_filter('body_class', 'dsp_footer_body_classes');
