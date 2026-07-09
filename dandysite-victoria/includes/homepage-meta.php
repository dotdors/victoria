<?php
/**
 * Hero Meta
 * Meta box for hero section settings — headline, tagline, CTA, height.
 * Shows on all pages so any page using a hero template can be configured.
 * dandysite-victoria theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// =====================================================================
// REGISTER META BOX
// =====================================================================

function dsp_add_homepage_meta_box() {
    add_meta_box(
        'dsp_homepage_hero',
        __( 'Hero Section', 'dandysite-victoria' ),
        'dsp_homepage_hero_callback',
        'page',
        'normal',
        'high'
    );
}

/**
 * Control Hero Section meta box visibility.
 *
 * Always visible on:
 *   - The front page (homepage)
 *   - Any page with a non-default template selected
 *
 * Hidden on all other pages unless Developer Mode is on.
 */
add_action( 'do_meta_boxes', function( $post_type, $context ) {
    if ( $post_type !== 'page' ) {
        return;
    }

    $post_id       = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
    $front_page_id = (int) get_option( 'page_on_front' );
    $is_front      = $post_id && $post_id === $front_page_id;
    $template      = $post_id ? get_page_template_slug( $post_id ) : '';
    $has_template  = ! empty( $template );
    $dev_mode      = (bool) get_option( 'dswg_developer_mode', 1 );

    if ( ! $is_front && ! $has_template && ! $dev_mode ) {
        remove_meta_box( 'dsp_homepage_hero', 'page', $context );
    }
}, 10, 2 );
add_action( 'add_meta_boxes', 'dsp_add_homepage_meta_box' );


function dsp_homepage_hero_callback( $post ) {
    wp_nonce_field( 'dsp_save_homepage_hero', 'dsp_homepage_hero_nonce' );

    $eyebrow          = get_post_meta( $post->ID, 'dsp_hero_eyebrow', true );
    $headline         = get_post_meta( $post->ID, 'dsp_hero_headline', true );
    $tagline          = get_post_meta( $post->ID, 'dsp_hero_tagline', true );
    $cta_text         = get_post_meta( $post->ID, 'dsp_hero_cta_text', true );
    $cta_url          = get_post_meta( $post->ID, 'dsp_hero_cta_url', true );
    $hero_height      = get_post_meta( $post->ID, 'dsp_hero_height', true ) ?: '100vh';
    $show_logo        = get_post_meta( $post->ID, 'dsp_hero_show_logo', true );
    $logo_white       = get_post_meta( $post->ID, 'dsp_hero_logo_white', true );
    ?>
    <style>
        .dsp-hero-meta { display: grid; gap: 16px; max-width: 700px; margin-top: 8px; }
        .dsp-hero-meta label { font-weight: 600; display: block; margin-bottom: 4px; }
        .dsp-hero-meta .description { color: #666; font-size: 12px; margin-top: 4px; }
        .dsp-hero-meta input[type="text"],
        .dsp-hero-meta input[type="url"],
        .dsp-hero-meta textarea,
        .dsp-hero-meta select { width: 100%; }
        .dsp-hero-meta .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .dsp-hero-meta .field-hint { background: #f6f6f6; border-left: 3px solid #c3c4c7; padding: 8px 12px; font-size: 12px; color: #555; border-radius: 0 4px 4px 0; }
    </style>

    <div class="dsp-hero-meta">

        <div class="field-hint">
            <?php esc_html_e( 'These fields apply when a Hero page template is selected (Page Attributes → Template). Hero image = Featured Image.', 'dandysite-victoria' ); ?>
        </div>

        <div>
            <label for="dsp_hero_eyebrow"><?php esc_html_e( 'Eyebrow Text', 'dandysite-victoria' ); ?></label>
            <input type="text"
                   id="dsp_hero_eyebrow"
                   name="dsp_hero_eyebrow"
                   value="<?php echo esc_attr( $eyebrow ); ?>"
                   placeholder="<?php esc_attr_e( 'e.g. Experienced Oil & Gas Leader for Texas', 'dandysite-victoria' ); ?>">
            <p class="description"><?php esc_html_e( 'Small label shown above the headline. Leave blank to hide.', 'dandysite-victoria' ); ?></p>
        </div>

        <div>
            <label><?php esc_html_e( 'Logo in Hero', 'dandysite-victoria' ); ?></label>
            <label style="font-weight:normal; display:block; margin-bottom:4px;">
                <input type="checkbox" name="dsp_hero_show_logo" value="1" <?php checked( $show_logo, '1' ); ?>>
                <?php esc_html_e( 'Show site logo between eyebrow and headline', 'dandysite-victoria' ); ?>
            </label>
            <label style="font-weight:normal; display:block;">
                <input type="checkbox" name="dsp_hero_logo_white" value="1" <?php checked( $logo_white, '1' ); ?>>
                <?php esc_html_e( 'Force logo to white (use on dark/photo backgrounds)', 'dandysite-victoria' ); ?>
            </label>
        </div>

        <div>
            <label for="dsp_hero_headline"><?php esc_html_e( 'Headline', 'dandysite-victoria' ); ?></label>
            <input type="text"
                   id="dsp_hero_headline"
                   name="dsp_hero_headline"
                   value="<?php echo esc_attr( $headline ); ?>"
                   placeholder="<?php esc_attr_e( 'e.g. Every bottle reflects the land, the people, and generations of dedication.', 'dandysite-victoria' ); ?>">
            <p class="description"><?php esc_html_e( 'Main hero heading.', 'dandysite-victoria' ); ?></p>
        </div>

        <div>
            <label for="dsp_hero_tagline"><?php esc_html_e( 'Tagline / Subtext', 'dandysite-victoria' ); ?></label>
            <textarea id="dsp_hero_tagline"
                      name="dsp_hero_tagline"
                      rows="2"
                      placeholder="<?php esc_attr_e( 'Optional supporting text below the headline.', 'dandysite-victoria' ); ?>"><?php echo esc_textarea( $tagline ); ?></textarea>
        </div>

        <div class="field-row">
            <div>
                <label for="dsp_hero_cta_text"><?php esc_html_e( 'CTA Button Text', 'dandysite-victoria' ); ?></label>
                <input type="text"
                       id="dsp_hero_cta_text"
                       name="dsp_hero_cta_text"
                       value="<?php echo esc_attr( $cta_text ); ?>"
                       placeholder="<?php esc_attr_e( 'e.g. Meet Our Producers', 'dandysite-victoria' ); ?>">
                <p class="description"><?php esc_html_e( 'Leave blank to hide the button.', 'dandysite-victoria' ); ?></p>
            </div>
            <div>
                <label for="dsp_hero_cta_url"><?php esc_html_e( 'CTA Button URL', 'dandysite-victoria' ); ?></label>
                <input type="url"
                       id="dsp_hero_cta_url"
                       name="dsp_hero_cta_url"
                       value="<?php echo esc_attr( $cta_url ); ?>"
                       placeholder="https://">
            </div>
        </div>

        <div>
            <label for="dsp_hero_height"><?php esc_html_e( 'Hero Height', 'dandysite-victoria' ); ?></label>
            <select id="dsp_hero_height" name="dsp_hero_height">
                <option value="100vh" <?php selected( $hero_height, '100vh' ); ?>><?php esc_html_e( 'Full viewport (100vh)', 'dandysite-victoria' ); ?></option>
                <option value="90vh"  <?php selected( $hero_height, '90vh' );  ?>><?php esc_html_e( 'Tall (90vh)', 'dandysite-victoria' ); ?></option>
                <option value="85vh"  <?php selected( $hero_height, '85vh' );  ?>><?php esc_html_e( 'Large (85vh)', 'dandysite-victoria' ); ?></option>
                <option value="75vh"  <?php selected( $hero_height, '75vh' );  ?>><?php esc_html_e( 'Medium-tall (75vh)', 'dandysite-victoria' ); ?></option>
                <option value="60vh"  <?php selected( $hero_height, '60vh' );  ?>><?php esc_html_e( 'Medium (60vh)', 'dandysite-victoria' ); ?></option>
            </select>
            <p class="description"><?php esc_html_e( 'Used as min-height on split layouts, exact height on full-bleed.', 'dandysite-victoria' ); ?></p>
        </div>

    </div>
    <?php
}


// =====================================================================
// SAVE META
// =====================================================================

function dsp_save_homepage_hero( $post_id ) {
    if ( ! isset( $_POST['dsp_homepage_hero_nonce'] ) ||
         ! wp_verify_nonce( $_POST['dsp_homepage_hero_nonce'], 'dsp_save_homepage_hero' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $allowed_heights = [ '100vh', '90vh', '85vh', '75vh', '60vh' ];

    $fields = [
        'dsp_hero_eyebrow'  => 'sanitize_text_field',
        'dsp_hero_headline' => 'sanitize_text_field',
        'dsp_hero_tagline'  => 'sanitize_textarea_field',
        'dsp_hero_cta_text' => 'sanitize_text_field',
        'dsp_hero_cta_url'  => 'esc_url_raw',
    ];

    foreach ( $fields as $key => $sanitizer ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, $sanitizer( $_POST[ $key ] ) );
        }
    }

    // Checkboxes
    update_post_meta( $post_id, 'dsp_hero_show_logo',  isset( $_POST['dsp_hero_show_logo'] )  ? '1' : '0' );
    update_post_meta( $post_id, 'dsp_hero_logo_white', isset( $_POST['dsp_hero_logo_white'] ) ? '1' : '0' );

    if ( isset( $_POST['dsp_hero_height'] ) && in_array( $_POST['dsp_hero_height'], $allowed_heights, true ) ) {
        update_post_meta( $post_id, 'dsp_hero_height', $_POST['dsp_hero_height'] );
    }
}
add_action( 'save_post', 'dsp_save_homepage_hero' );


// =====================================================================
// HELPER — dsp_get_hero_meta()
// =====================================================================

/**
 * Get hero meta for the current page (or a given post ID).
 * Works on any page using a hero template, not just the front page.
 *
 * @param  int|null $post_id  Post ID. Defaults to current post.
 * @return array {
 *     headline, tagline, cta_text, cta_url, height, has_image, image_url
 * }
 */
function dsp_get_hero_meta( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    // Last resort for front page context before the loop
    if ( ! $post_id ) {
        $post_id = (int) get_option( 'page_on_front' );
    }

    $image_url = '';
    if ( has_post_thumbnail( $post_id ) ) {
        $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
    }

    return [
        'eyebrow'    => get_post_meta( $post_id, 'dsp_hero_eyebrow', true ),
        'headline'   => get_post_meta( $post_id, 'dsp_hero_headline', true ),
        'tagline'    => get_post_meta( $post_id, 'dsp_hero_tagline', true ),
        'cta_text'   => get_post_meta( $post_id, 'dsp_hero_cta_text', true ),
        'cta_url'    => get_post_meta( $post_id, 'dsp_hero_cta_url', true ),
        'height'     => get_post_meta( $post_id, 'dsp_hero_height', true ) ?: '100vh',
        'show_logo'  => get_post_meta( $post_id, 'dsp_hero_show_logo', true ),
        'logo_white' => get_post_meta( $post_id, 'dsp_hero_logo_white', true ),
        'has_image'  => ! empty( $image_url ),
        'image_url'  => $image_url,
    ];
}
