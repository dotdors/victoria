<?php
/**
 * Admin Columns — Posts list customization
 *
 * The Posts list is where external coverage gets managed, so the
 * columns reflect that workflow:
 *   - Removed: Author, Tags (noise for these sites)
 *   - Added:   Image (✓ = featured image set — publication logo or photo)
 *              Publication (dsp_publication_name meta)
 *              Blurb (✓ = manual excerpt / card blurb present)
 *
 * dandysite-victoria theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Column set — posts only (post type 'post'; leaves pages and CPTs alone)
function dsp_posts_list_columns( $columns ) {
    unset( $columns['author'], $columns['tags'] );

    $new = [];
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( 'title' === $key ) {
            $new['dsp_thumb']       = __( 'Image', 'dandysite-victoria' );
            $new['dsp_publication'] = __( 'Publication', 'dandysite-victoria' );
            $new['dsp_blurb']       = __( 'Blurb', 'dandysite-victoria' );
        }
    }
    return $new;
}
add_filter( 'manage_post_posts_columns', 'dsp_posts_list_columns' );

// Column content
function dsp_posts_list_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'dsp_thumb':
            echo has_post_thumbnail( $post_id )
                ? '<span class="dashicons dashicons-yes-alt" style="color:#00a32a;" title="' . esc_attr__( 'Featured image set', 'dandysite-victoria' ) . '"></span>'
                : '<span aria-hidden="true" style="color:#c3c4c7;">&mdash;</span>';
            break;

        case 'dsp_publication':
            $publication = get_post_meta( $post_id, 'dsp_publication_name', true );
            echo $publication
                ? esc_html( $publication )
                : '<span aria-hidden="true" style="color:#c3c4c7;">&mdash;</span>';
            break;

        case 'dsp_blurb':
            echo has_excerpt( $post_id )
                ? '<span class="dashicons dashicons-yes-alt" style="color:#00a32a;" title="' . esc_attr__( 'Card blurb present', 'dandysite-victoria' ) . '"></span>'
                : '<span aria-hidden="true" style="color:#c3c4c7;">&mdash;</span>';
            break;
    }
}
add_action( 'manage_post_posts_custom_column', 'dsp_posts_list_column_content', 10, 2 );

// Keep the checkmark columns narrow
function dsp_posts_list_column_css() {
    echo '<style>
        .fixed .column-dsp_thumb,
        .fixed .column-dsp_blurb { width: 64px; text-align: center; }
        .fixed .column-dsp_publication { width: 14%; }
    </style>';
}
add_action( 'admin_head-edit.php', 'dsp_posts_list_column_css' );
