<?php
/**
 * CPT: Endorsements
 *
 * Fields: endorser name, title/organization, quote, photo (featured image)
 * Displayed as a grid on the homepage endorsements section.
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================
// REGISTER POST TYPE
// ============================================================

function dshft_register_endorsements_cpt() {
    register_post_type('dshft_endorsement', [
        'labels' => [
            'name'               => __('Endorsements', 'ds-hawkfortexas'),
            'singular_name'      => __('Endorsement', 'ds-hawkfortexas'),
            'menu_name'          => __('Endorsements', 'ds-hawkfortexas'),
            'add_new'            => __('Add New', 'ds-hawkfortexas'),
            'add_new_item'       => __('Add Endorsement', 'ds-hawkfortexas'),
            'edit_item'          => __('Edit Endorsement', 'ds-hawkfortexas'),
            'not_found'          => __('No endorsements found', 'ds-hawkfortexas'),
            'not_found_in_trash' => __('No endorsements in trash', 'ds-hawkfortexas'),
        ],
        'public'            => false,   // not publicly queryable — displayed via template functions
        'show_ui'           => true,    // visible in admin
        'show_in_menu'      => true,
        'show_in_rest'      => true,
        'supports'          => ['title', 'thumbnail'], // title = endorser name; photo = featured image
        'menu_icon'         => 'dashicons-awards',
        'menu_position'     => 25,
    ]);
}
add_action('init', 'dshft_register_endorsements_cpt');

// ============================================================
// META BOX: ENDORSER DETAILS
// ============================================================

function dshft_endorsement_meta_box() {
    add_meta_box(
        'dshft_endorsement_details',
        __('Endorser Details', 'ds-hawkfortexas'),
        'dshft_endorsement_meta_box_callback',
        'dshft_endorsement',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dshft_endorsement_meta_box');

function dshft_endorsement_meta_box_callback($post) {
    wp_nonce_field('dshft_save_endorsement', 'dshft_endorsement_nonce');

    $title_org = get_post_meta($post->ID, 'dshft_endorser_title_org', true);
    $quote     = get_post_meta($post->ID, 'dshft_endorser_quote', true);
    $featured  = get_post_meta($post->ID, 'dshft_endorsement_featured', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dshft_endorser_title_org"><?php _e('Title / Organization', 'ds-hawkfortexas'); ?></label></th>
            <td>
                <input type="text" id="dshft_endorser_title_org" name="dshft_endorser_title_org"
                       value="<?php echo esc_attr($title_org); ?>" class="regular-text" />
                <p class="description"><?php _e('e.g. "Mayor, City of Dallas" or "Texas AFL-CIO"', 'ds-hawkfortexas'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dshft_endorser_quote"><?php _e('Pull Quote', 'ds-hawkfortexas'); ?></label></th>
            <td>
                <textarea id="dshft_endorser_quote" name="dshft_endorser_quote"
                          rows="3" class="large-text"><?php echo esc_textarea($quote); ?></textarea>
                <p class="description"><?php _e('Optional short quote to display with the endorsement.', 'ds-hawkfortexas'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dshft_endorsement_featured"><?php _e('Featured', 'ds-hawkfortexas'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="dshft_endorsement_featured" name="dshft_endorsement_featured"
                           value="1" <?php checked($featured, '1'); ?> />
                    <?php _e('Show prominently on homepage', 'ds-hawkfortexas'); ?>
                </label>
            </td>
        </tr>
    </table>
    <?php
}

// ============================================================
// SAVE META
// ============================================================

function dshft_save_endorsement_meta($post_id) {
    if (!isset($_POST['dshft_endorsement_nonce']) ||
        !wp_verify_nonce($_POST['dshft_endorsement_nonce'], 'dshft_save_endorsement')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['dshft_endorser_title_org'])) {
        update_post_meta($post_id, 'dshft_endorser_title_org', sanitize_text_field($_POST['dshft_endorser_title_org']));
    }
    if (isset($_POST['dshft_endorser_quote'])) {
        update_post_meta($post_id, 'dshft_endorser_quote', sanitize_textarea_field($_POST['dshft_endorser_quote']));
    }
    update_post_meta($post_id, 'dshft_endorsement_featured', isset($_POST['dshft_endorsement_featured']) ? '1' : '0');
}
add_action('save_post_dshft_endorsement', 'dshft_save_endorsement_meta');

// ============================================================
// TEMPLATE HELPER
// ============================================================

/**
 * Get endorsements for display
 *
 * @param bool $featured_only  Only return endorsements marked as featured
 * @param int  $limit          Number to return (0 = all)
 * @return WP_Post[]
 */
function dshft_get_endorsements($featured_only = false, $limit = 0) {
    $args = [
        'post_type'      => 'dshft_endorsement',
        'post_status'    => 'publish',
        'posts_per_page' => $limit ?: -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];
    if ($featured_only) {
        $args['meta_query'] = [[
            'key'   => 'dshft_endorsement_featured',
            'value' => '1',
        ]];
    }
    return get_posts($args);
}
