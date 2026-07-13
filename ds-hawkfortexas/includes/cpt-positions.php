<?php
/**
 * CPT: Positions (Issues)
 *
 * Each position has a title, short summary, optional long-form content,
 * and an optional icon/image.
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================
// REGISTER POST TYPE
// ============================================================

function dshft_register_positions_cpt() {
    register_post_type('dshft_position', [
        'labels' => [
            'name'               => __('Positions', 'ds-hawkfortexas'),
            'singular_name'      => __('Position', 'ds-hawkfortexas'),
            'menu_name'          => __('Issues & Positions', 'ds-hawkfortexas'),
            'add_new'            => __('Add New', 'ds-hawkfortexas'),
            'add_new_item'       => __('Add Position', 'ds-hawkfortexas'),
            'edit_item'          => __('Edit Position', 'ds-hawkfortexas'),
            'not_found'          => __('No positions found', 'ds-hawkfortexas'),
            'not_found_in_trash' => __('No positions in trash', 'ds-hawkfortexas'),
        ],
        'public'        => true,          // publicly queryable — each position can have its own page
        'has_archive'   => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'show_in_rest'  => true,
        'rewrite'       => ['slug' => 'issues'],
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'menu_icon'     => 'dashicons-megaphone',
        'menu_position' => 26,
    ]);
}
add_action('init', 'dshft_register_positions_cpt');

// ============================================================
// META BOX: POSITION DETAILS
// ============================================================

function dshft_position_meta_box() {
    add_meta_box(
        'dshft_position_details',
        __('Position Details', 'ds-hawkfortexas'),
        'dshft_position_meta_box_callback',
        'dshft_position',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dshft_position_meta_box');

function dshft_position_meta_box_callback($post) {
    wp_nonce_field('dshft_save_position', 'dshft_position_nonce');

    $summary    = get_post_meta($post->ID, 'dshft_position_summary', true);
    $icon_class = get_post_meta($post->ID, 'dshft_position_icon', true);
    $homepage   = get_post_meta($post->ID, 'dshft_position_on_homepage', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dshft_position_summary"><?php _e('Homepage Summary', 'ds-hawkfortexas'); ?></label></th>
            <td>
                <textarea id="dshft_position_summary" name="dshft_position_summary"
                          rows="2" class="large-text"><?php echo esc_textarea($summary); ?></textarea>
                <p class="description"><?php _e('Short summary shown on the homepage issues section (1–2 sentences). Leave blank to use the excerpt.', 'ds-hawkfortexas'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dshft_position_icon"><?php _e('Icon (Dashicon class)', 'ds-hawkfortexas'); ?></label></th>
            <td>
                <input type="text" id="dshft_position_icon" name="dshft_position_icon"
                       value="<?php echo esc_attr($icon_class); ?>" class="regular-text" placeholder="dashicons-heart" />
                <p class="description">
                    <?php _e('Optional. Use a dashicon class from ', 'ds-hawkfortexas'); ?>
                    <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">developer.wordpress.org/resource/dashicons</a>
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="dshft_position_on_homepage"><?php _e('Show on Homepage', 'ds-hawkfortexas'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="dshft_position_on_homepage" name="dshft_position_on_homepage"
                           value="1" <?php checked($homepage, '1'); ?> />
                    <?php _e('Include in the homepage Issues section', 'ds-hawkfortexas'); ?>
                </label>
            </td>
        </tr>
    </table>
    <?php
}

// ============================================================
// SAVE META
// ============================================================

function dshft_save_position_meta($post_id) {
    if (!isset($_POST['dshft_position_nonce']) ||
        !wp_verify_nonce($_POST['dshft_position_nonce'], 'dshft_save_position')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['dshft_position_summary'])) {
        update_post_meta($post_id, 'dshft_position_summary', sanitize_textarea_field($_POST['dshft_position_summary']));
    }
    if (isset($_POST['dshft_position_icon'])) {
        update_post_meta($post_id, 'dshft_position_icon', sanitize_html_class($_POST['dshft_position_icon']));
    }
    update_post_meta($post_id, 'dshft_position_on_homepage', isset($_POST['dshft_position_on_homepage']) ? '1' : '0');
}
add_action('save_post_dshft_position', 'dshft_save_position_meta');

// ============================================================
// TEMPLATE HELPER
// ============================================================

/**
 * Get positions for display
 *
 * @param bool $homepage_only  Only return positions flagged for homepage
 * @return WP_Post[]
 */
function dshft_get_positions($homepage_only = false) {
    $args = [
        'post_type'      => 'dshft_position',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];
    if ($homepage_only) {
        $args['meta_query'] = [[
            'key'   => 'dshft_position_on_homepage',
            'value' => '1',
        ]];
    }
    return get_posts($args);
}
