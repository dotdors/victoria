<?php
/**
 * Header Style Meta
 * Per-page/per-CPT header style override.
 * Shows on all pages and CPTs as a simple radio in the editor sidebar.
 *
 * Options:
 *   ''        — use site default (Appearance → Theme Settings)
 *   'overlay' — transparent header overlays the page
 *   'solid'   — solid header sits above the page
 *
 * Works independently of page template, so hero layout and header
 * style can be set freely without conflicting.
 *
 * dandysite-victoria theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// =====================================================================
// META BOX
// =====================================================================

function dsp_add_header_style_meta_box() {
    // Developer-only meta box — hide when Developer Mode is off
    if ( ! get_option( 'dswg_developer_mode', 1 ) ) {
        return;
    }

    $post_types = array_merge(
        [ 'page', 'post' ],
        get_post_types( [ '_builtin' => false, 'public' => true ] )
    );

    add_meta_box(
        'dsp_header_style',
        __( 'Header Style', 'dandysite-victoria' ),
        'dsp_header_style_meta_box_callback',
        $post_types,
        'side',       // sidebar panel, not below the editor
        'default'
    );
}
add_action( 'add_meta_boxes', 'dsp_add_header_style_meta_box' );


function dsp_header_style_meta_box_callback( $post ) {
    wp_nonce_field( 'dsp_save_header_style', 'dsp_header_style_nonce' );

    $value   = get_post_meta( $post->ID, '_dsp_header_style', true );
    $default = get_option( 'dsp_header_default_style', 'solid' );
    $default_label = ucfirst( $default );
    ?>
    <fieldset style="margin: 0; padding: 0; border: none;">
        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer; font-weight: normal;">
            <input type="radio"
                   name="dsp_header_style"
                   value=""
                   <?php checked( $value, '' ); ?>>
            <?php
            printf(
                /* translators: %s: current default style e.g. "Solid" */
                esc_html__( 'Site default (%s)', 'dandysite-victoria' ),
                esc_html( $default_label )
            );
            ?>
        </label>
        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer; font-weight: normal;">
            <input type="radio"
                   name="dsp_header_style"
                   value="overlay"
                   <?php checked( $value, 'overlay' ); ?>>
            <?php esc_html_e( 'Overlay', 'dandysite-victoria' ); ?>
            <span style="color: #888; font-size: 11px;"><?php esc_html_e( '(transparent, floats over page)', 'dandysite-victoria' ); ?></span>
        </label>
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
            <input type="radio"
                   name="dsp_header_style"
                   value="solid"
                   <?php checked( $value, 'solid' ); ?>>
            <?php esc_html_e( 'Solid', 'dandysite-victoria' ); ?>
            <span style="color: #888; font-size: 11px;"><?php esc_html_e( '(background, content starts below)', 'dandysite-victoria' ); ?></span>
        </label>
    </fieldset>
    <?php
}


// =====================================================================
// SAVE
// =====================================================================

function dsp_save_header_style_meta( $post_id ) {
    if ( ! isset( $_POST['dsp_header_style_nonce'] ) ||
         ! wp_verify_nonce( $_POST['dsp_header_style_nonce'], 'dsp_save_header_style' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $allowed = [ '', 'overlay', 'solid' ];
    $value   = isset( $_POST['dsp_header_style'] ) ? $_POST['dsp_header_style'] : '';

    if ( in_array( $value, $allowed, true ) ) {
        if ( $value === '' ) {
            delete_post_meta( $post_id, '_dsp_header_style' );
        } else {
            update_post_meta( $post_id, '_dsp_header_style', $value );
        }
    }
}
add_action( 'save_post', 'dsp_save_header_style_meta' );
