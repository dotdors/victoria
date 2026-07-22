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

function dsp_register_positions_cpt() {
    register_post_type('dsp_position', [
        'labels' => [
            'name'               => __('Positions', 'dandysite-victoria'),
            'singular_name'      => __('Position', 'dandysite-victoria'),
            'menu_name'          => __('Issues & Positions', 'dandysite-victoria'),
            'add_new'            => __('Add New', 'dandysite-victoria'),
            'add_new_item'       => __('Add Position', 'dandysite-victoria'),
            'edit_item'          => __('Edit Position', 'dandysite-victoria'),
            'not_found'          => __('No positions found', 'dandysite-victoria'),
            'not_found_in_trash' => __('No positions in trash', 'dandysite-victoria'),
        ],
        'public'        => true,
        'has_archive'   => false,
        'show_ui'       => true,
        'show_in_menu'  => '1' === get_option( 'dsp_show_positions_menu', '1' ),
        'show_in_rest'  => true,
        'rewrite'       => ['slug' => 'issues'],
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'menu_icon'     => 'dashicons-megaphone',
        'menu_position' => 26,
    ]);
}
add_action('init', 'dsp_register_positions_cpt');

// ============================================================
// META BOX: POSITION DETAILS
// ============================================================

function dsp_position_meta_box() {
    add_meta_box(
        'dsp_position_details',
        __('Position Details', 'dandysite-victoria'),
        'dsp_position_meta_box_callback',
        'dsp_position',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dsp_position_meta_box');

function dsp_position_meta_box_callback($post) {
    wp_nonce_field('dsp_save_position', 'dsp_position_nonce');

    $summary    = get_post_meta($post->ID, 'dsp_position_summary', true);
    $icon_class = get_post_meta($post->ID, 'dsp_position_icon', true);
    $read_more  = get_post_meta($post->ID, 'dsp_position_link', true);
    $homepage   = get_post_meta($post->ID, 'dsp_position_on_homepage', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dsp_position_summary"><?php _e('Homepage Summary', 'dandysite-victoria'); ?></label></th>
            <td>
                <textarea id="dsp_position_summary" name="dsp_position_summary"
                          rows="2" class="large-text"><?php echo esc_textarea($summary); ?></textarea>
                <p class="description"><?php _e('Short summary shown on the homepage issues section (1–2 sentences). Leave blank to use the excerpt.', 'dandysite-victoria'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dsp_position_icon"><?php _e('Icon (Dashicon class)', 'dandysite-victoria'); ?></label></th>
            <td>
                <input type="text" id="dsp_position_icon" name="dsp_position_icon"
                       value="<?php echo esc_attr($icon_class); ?>" class="regular-text" placeholder="dashicons-heart" />
                <p class="description">
                    <?php _e('Optional. Use a dashicon class from ', 'dandysite-victoria'); ?>
                    <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">developer.wordpress.org/resource/dashicons</a>
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="dsp_position_link"><?php _e('Read More Link', 'dandysite-victoria'); ?></label></th>
            <td>
                <input type="url" id="dsp_position_link" name="dsp_position_link"
                       value="<?php echo esc_attr($read_more); ?>" class="regular-text"
                       placeholder="https://" />
                <p class="description"><?php _e('Optional. Link to a full post or external page for this position. If set, the "Read More" link on the issues card goes here instead of the position\'s own page. Useful when you\'ve written a full post that already covers this topic.', 'dandysite-victoria'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dsp_position_on_homepage"><?php _e('Show on Homepage', 'dandysite-victoria'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="dsp_position_on_homepage" name="dsp_position_on_homepage"
                           value="1" <?php checked($homepage, '1'); ?> />
                    <?php _e('Include in the homepage Issues section', 'dandysite-victoria'); ?>
                </label>
            </td>
        </tr>
    </table>
    <?php
}

// ============================================================
// SAVE META
// ============================================================

function dsp_save_position_meta($post_id) {
    if (!isset($_POST['dsp_position_nonce']) ||
        !wp_verify_nonce($_POST['dsp_position_nonce'], 'dsp_save_position')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['dsp_position_summary'])) {
        update_post_meta($post_id, 'dsp_position_summary', sanitize_textarea_field($_POST['dsp_position_summary']));
    }
    if (isset($_POST['dsp_position_icon'])) {
        update_post_meta($post_id, 'dsp_position_icon', sanitize_html_class($_POST['dsp_position_icon']));
    }
    if (isset($_POST['dsp_position_link'])) {
        update_post_meta($post_id, 'dsp_position_link', esc_url_raw($_POST['dsp_position_link']));
    }
    update_post_meta($post_id, 'dsp_position_on_homepage', isset($_POST['dsp_position_on_homepage']) ? '1' : '0');
}
add_action('save_post_dsp_position', 'dsp_save_position_meta');

// ============================================================
// TEMPLATE HELPER
// ============================================================

/**
 * Get positions for display
 *
 * @param bool $homepage_only  Only return positions flagged for homepage
 * @return WP_Post[]
 */
function dsp_get_positions($homepage_only = false) {
    $args = [
        'post_type'      => 'dsp_position',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];
    if ($homepage_only) {
        $args['meta_query'] = [[
            'key'   => 'dsp_position_on_homepage',
            'value' => '1',
        ]];
    }
    return get_posts($args);
}
// ============================================================
// PREVENT SCHEDULING
// The block editor can drift the post date forward and land
// the post in "Scheduled" status. Force publish + current date
// any time a position would otherwise be saved as future.
// ============================================================

add_filter( 'wp_insert_post_data', function( $data, $postarr ) {
    if ( $data['post_type'] !== 'dsp_position' ) {
        return $data;
    }
    if ( $data['post_status'] === 'future' ) {
        $data['post_status'] = 'publish';
        $data['post_date']          = current_time( 'mysql' );
        $data['post_date_gmt']      = current_time( 'mysql', true );
        $data['post_modified']      = current_time( 'mysql' );
        $data['post_modified_gmt']  = current_time( 'mysql', true );
    }
    return $data;
}, 10, 2 );

// ============================================================
// BLOCK EDITOR: HIDE SCHEDULE PANEL
// Cosmetic companion to the filter above — removes the date
// panel from the sidebar so there's no temptation to set one.
// ============================================================

function dsp_position_admin_script() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'dsp_position' || ! $screen->is_block_editor() ) {
        return;
    }
    wp_add_inline_script( 'wp-edit-post', "
        wp.domReady( function() {
            wp.data.dispatch( 'core/edit-post' ).removeEditorPanel( 'post-schedule' );
        } );
    " );
}
add_action( 'enqueue_block_editor_assets', 'dsp_position_admin_script' );
