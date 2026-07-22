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

function dsp_register_endorsements_cpt() {
    register_post_type('dsp_endorsement', [
        'labels' => [
            'name'               => __('Endorsements', 'dandysite-victoria'),
            'singular_name'      => __('Endorsement', 'dandysite-victoria'),
            'menu_name'          => __('Endorsements', 'dandysite-victoria'),
            'add_new'            => __('Add New', 'dandysite-victoria'),
            'add_new_item'       => __('Add Endorsement', 'dandysite-victoria'),
            'edit_item'          => __('Edit Endorsement', 'dandysite-victoria'),
            'not_found'          => __('No endorsements found', 'dandysite-victoria'),
            'not_found_in_trash' => __('No endorsements in trash', 'dandysite-victoria'),
        ],
        'public'            => false,
        'show_ui'           => true,
        'show_in_menu'      => '1' === get_option( 'dsp_show_endorsements_menu', '1' ),
        'show_in_rest'      => false,  // no block editor — meta boxes save via classic $_POST
        'supports'          => ['title', 'thumbnail'],
        'menu_icon'         => 'dashicons-awards',
        'menu_position'     => 25,
    ]);
}
add_action('init', 'dsp_register_endorsements_cpt');

// ============================================================
// META BOX: ENDORSER DETAILS
// ============================================================

function dsp_endorsement_meta_box() {
    add_meta_box(
        'dsp_endorsement_details',
        __('Endorser Details', 'dandysite-victoria'),
        'dsp_endorsement_meta_box_callback',
        'dsp_endorsement',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dsp_endorsement_meta_box');

function dsp_endorsement_meta_box_callback($post) {
    wp_nonce_field('dsp_save_endorsement', 'dsp_endorsement_nonce');

    $title_org = get_post_meta($post->ID, 'dsp_endorser_title_org', true);
    $quote     = get_post_meta($post->ID, 'dsp_endorser_quote', true);
    $link      = get_post_meta($post->ID, 'dsp_endorser_link', true);
    $featured  = get_post_meta($post->ID, 'dsp_endorsement_featured', true);
    ?>
    <p style="margin: 0 0 1em; padding: 8px 12px; background: #f0f6fc; border-left: 4px solid #457b9d; font-size: 13px;">
        <strong><?php _e('Post Title = Endorser\'s Name', 'dandysite-victoria'); ?></strong><br>
        <?php _e('Enter the endorser\'s name in the Title field above — e.g. "The Houston Chronicle", "Mayor John Smith", or "Texas AFL-CIO". The fields below are all optional.', 'dandysite-victoria'); ?>
    </p>
    <table class="form-table">
        <tr>
            <th><label for="dsp_endorser_title_org"><?php _e('Role / Organization', 'dandysite-victoria'); ?></label></th>
            <td>
                <input type="text" id="dsp_endorser_title_org" name="dsp_endorser_title_org"
                       value="<?php echo esc_attr($title_org); ?>" class="regular-text" />
                <p class="description"><?php _e('Optional. The endorser\'s role or organization — shown beneath their name. e.g. "Editorial Board", "Mayor, City of Dallas", "International Brotherhood of Teamsters"', 'dandysite-victoria'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dsp_endorser_quote"><?php _e('Pull Quote', 'dandysite-victoria'); ?></label></th>
            <td>
                <textarea id="dsp_endorser_quote" name="dsp_endorser_quote"
                          rows="3" class="large-text"><?php echo esc_textarea($quote); ?></textarea>
                <p class="description"><?php _e('Optional. A short quote from the endorser to display on the card.', 'dandysite-victoria'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dsp_endorser_link"><?php _e('Link URL', 'dandysite-victoria'); ?></label></th>
            <td>
                <input type="url" id="dsp_endorser_link" name="dsp_endorser_link"
                       value="<?php echo esc_attr($link); ?>" class="regular-text"
                       placeholder="https://" />
                <p class="description"><?php _e('Optional. Links the endorser\'s name to an external page — e.g. their website, a news article about the endorsement, or the endorsing organization\'s site.', 'dandysite-victoria'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dsp_endorsement_featured"><?php _e('Show on Homepage', 'dandysite-victoria'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="dsp_endorsement_featured" name="dsp_endorsement_featured"
                           value="1" <?php checked($featured, '1'); ?> />
                    <?php _e('Include this endorsement in the homepage endorsements section.', 'dandysite-victoria'); ?>
                </label>
            </td>
        </tr>
    </table>
    <?php
}

// ============================================================
// SAVE META
// ============================================================

function dsp_save_endorsement_meta($post_id) {
    if (!isset($_POST['dsp_endorsement_nonce']) ||
        !wp_verify_nonce($_POST['dsp_endorsement_nonce'], 'dsp_save_endorsement')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['dsp_endorser_title_org'])) {
        update_post_meta($post_id, 'dsp_endorser_title_org', sanitize_text_field($_POST['dsp_endorser_title_org']));
    }
    if (isset($_POST['dsp_endorser_quote'])) {
        update_post_meta($post_id, 'dsp_endorser_quote', sanitize_textarea_field($_POST['dsp_endorser_quote']));
    }
    if (isset($_POST['dsp_endorser_link'])) {
        update_post_meta($post_id, 'dsp_endorser_link', esc_url_raw($_POST['dsp_endorser_link']));
    }
    update_post_meta($post_id, 'dsp_endorsement_featured', isset($_POST['dsp_endorsement_featured']) ? '1' : '0');
}
add_action('save_post_dsp_endorsement', 'dsp_save_endorsement_meta');

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
function dsp_get_endorsements($featured_only = false, $limit = 0) {
    $args = [
        'post_type'      => 'dsp_endorsement',
        'post_status'    => 'publish',
        'posts_per_page' => $limit ?: -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];
    if ($featured_only) {
        $args['meta_query'] = [[
            'key'   => 'dsp_endorsement_featured',
            'value' => '1',
        ]];
    }
    return get_posts($args);
}
