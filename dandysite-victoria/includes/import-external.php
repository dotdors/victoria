<?php
/**
 * External Posts CSV Importer
 *
 * Tools → Import External Posts. Bulk-creates posts for external
 * coverage/op-eds from a CSV (or tab-separated) file.
 *
 * Expected columns (header row, case-insensitive, any order):
 *   Title        (required)
 *   Publication  → dsp_publication_name meta
 *   URL          → dsp_external_url meta
 *   Date         → post date (any strtotime-parseable format)
 *   Category     → assigned; created by name if it doesn't exist
 *   Blurb        (optional) → post excerpt (card blurb)
 *
 * Behavior:
 *   - Delimiter auto-detected (comma or tab) from the header row.
 *   - Duplicate protection: rows are SKIPPED when an existing post has
 *     the same dsp_external_url, or (lacking a URL) the same title.
 *   - "Preview only" runs the whole import without creating anything.
 *   - Posts are created with status Publish and empty content — the
 *     external URL meta drives card links per the site's External
 *     Article Behavior setting.
 *
 * dandysite-victoria theme
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// =====================================================================
// ADMIN PAGE
// =====================================================================

function dsp_import_external_menu() {
    add_management_page(
        __( 'Import External Posts', 'dandysite-victoria' ),
        __( 'Import External Posts', 'dandysite-victoria' ),
        'manage_options',
        'dsp-import-external',
        'dsp_import_external_page'
    );
}
add_action( 'admin_menu', 'dsp_import_external_menu' );

function dsp_import_external_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $results = null;
    if ( isset( $_POST['dsp_import_nonce'] ) &&
         wp_verify_nonce( $_POST['dsp_import_nonce'], 'dsp_import_external' ) &&
         ! empty( $_FILES['dsp_import_file']['tmp_name'] ) ) {
        $dry_run = ! empty( $_POST['dsp_import_dry_run'] );
        $results = dsp_import_external_process( $_FILES['dsp_import_file']['tmp_name'], $dry_run );
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Import External Posts', 'dandysite-victoria' ); ?></h1>
        <p><?php esc_html_e( 'Upload a CSV (comma or tab separated) with columns: Title, Publication, URL, Date, Category, Blurb (optional). The header row is required; column order doesn\'t matter. Rows matching an existing post\'s external URL (or title, if no URL) are skipped.', 'dandysite-victoria' ); ?></p>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( 'dsp_import_external', 'dsp_import_nonce' ); ?>
            <p><input type="file" name="dsp_import_file" accept=".csv,.txt,.tsv" required></p>
            <p>
                <label>
                    <input type="checkbox" name="dsp_import_dry_run" value="1" checked>
                    <?php esc_html_e( 'Preview only (don\'t create anything yet)', 'dandysite-victoria' ); ?>
                </label>
            </p>
            <?php submit_button( __( 'Run Import', 'dandysite-victoria' ) ); ?>
        </form>

        <?php if ( $results ) : ?>
            <h2><?php echo $results['dry_run']
                ? esc_html__( 'Preview — nothing was created', 'dandysite-victoria' )
                : esc_html__( 'Import Results', 'dandysite-victoria' ); ?></h2>
            <table class="widefat striped" style="max-width:1000px;">
                <thead><tr>
                    <th><?php esc_html_e( 'Row', 'dandysite-victoria' ); ?></th>
                    <th><?php esc_html_e( 'Title', 'dandysite-victoria' ); ?></th>
                    <th><?php esc_html_e( 'Result', 'dandysite-victoria' ); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ( $results['rows'] as $row ) : ?>
                    <tr>
                        <td><?php echo (int) $row['line']; ?></td>
                        <td><?php echo esc_html( $row['title'] ); ?></td>
                        <td><?php echo esc_html( $row['message'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p>
                <strong><?php printf(
                    /* translators: counts */
                    esc_html__( '%1$d created, %2$d skipped, %3$d errors.', 'dandysite-victoria' ),
                    (int) $results['created'], (int) $results['skipped'], (int) $results['errors']
                ); ?></strong>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

// =====================================================================
// PROCESSOR
// =====================================================================

function dsp_import_external_process( $filepath, $dry_run = true ) {
    $out = [ 'dry_run' => $dry_run, 'rows' => [], 'created' => 0, 'skipped' => 0, 'errors' => 0 ];

    $fh = fopen( $filepath, 'r' );
    if ( ! $fh ) {
        $out['rows'][] = [ 'line' => 0, 'title' => '', 'message' => __( 'Could not read the uploaded file.', 'dandysite-victoria' ) ];
        $out['errors']++;
        return $out;
    }

    // Header: strip BOM, detect delimiter
    $header_line = fgets( $fh );
    if ( false === $header_line ) {
        fclose( $fh );
        $out['rows'][] = [ 'line' => 0, 'title' => '', 'message' => __( 'Empty file.', 'dandysite-victoria' ) ];
        $out['errors']++;
        return $out;
    }
    $header_line = preg_replace( '/^\xEF\xBB\xBF/', '', $header_line );
    $delimiter   = ( substr_count( $header_line, "\t" ) > substr_count( $header_line, ',' ) ) ? "\t" : ',';
    $headers     = array_map( fn( $h ) => strtolower( trim( $h ) ), str_getcsv( $header_line, $delimiter ) );

    $col = array_flip( $headers ); // name → index
    if ( ! isset( $col['title'] ) ) {
        fclose( $fh );
        $out['rows'][] = [ 'line' => 1, 'title' => '', 'message' => __( 'No "Title" column found in the header row.', 'dandysite-victoria' ) ];
        $out['errors']++;
        return $out;
    }

    $get = function ( $row, $name ) use ( $col ) {
        return isset( $col[ $name ], $row[ $col[ $name ] ] ) ? trim( $row[ $col[ $name ] ] ) : '';
    };

    $line = 1;
    while ( ( $row = fgetcsv( $fh, 0, $delimiter ) ) !== false ) {
        $line++;
        if ( count( $row ) === 1 && '' === trim( (string) $row[0] ) ) continue; // blank line

        $title       = $get( $row, 'title' );
        $publication = $get( $row, 'publication' );
        $url         = $get( $row, 'url' );
        $date_raw    = $get( $row, 'date' );
        $category    = $get( $row, 'category' );
        $blurb       = $get( $row, 'blurb' );

        if ( '' === $title ) {
            $out['rows'][] = [ 'line' => $line, 'title' => '(none)', 'message' => __( 'Missing title — skipped.', 'dandysite-victoria' ) ];
            $out['errors']++;
            continue;
        }

        // Duplicate check: external URL first, then exact title
        $dupe = null;
        if ( $url ) {
            $existing = get_posts( [
                'post_type'   => 'post',
                'post_status' => 'any',
                'numberposts' => 1,
                'fields'      => 'ids',
                'meta_key'    => 'dsp_external_url',
                'meta_value'  => esc_url_raw( $url ),
            ] );
            if ( $existing ) $dupe = __( 'Skipped — a post with this external URL already exists.', 'dandysite-victoria' );
        }
        if ( ! $dupe && ! $url ) {
            $existing = get_page_by_title( $title, OBJECT, 'post' ); // phpcs:ignore
            if ( $existing ) $dupe = __( 'Skipped — a post with this title already exists.', 'dandysite-victoria' );
        }
        if ( $dupe ) {
            $out['rows'][] = [ 'line' => $line, 'title' => $title, 'message' => $dupe ];
            $out['skipped']++;
            continue;
        }

        // Date
        $timestamp = $date_raw ? strtotime( $date_raw ) : false;
        $post_date = $timestamp ? gmdate( 'Y-m-d 12:00:00', $timestamp ) : current_time( 'mysql' );
        $date_note = ( $date_raw && ! $timestamp ) ? __( ' (date not understood — used today)', 'dandysite-victoria' ) : '';

        // Category (create by name if needed)
        $cat_ids = [];
        if ( $category ) {
            $term = get_term_by( 'name', $category, 'category' );
            if ( ! $term && ! $dry_run ) {
                $created_term = wp_insert_term( $category, 'category' );
                if ( ! is_wp_error( $created_term ) ) {
                    $term = get_term( $created_term['term_id'], 'category' );
                }
            }
            if ( $term && ! is_wp_error( $term ) ) $cat_ids[] = (int) $term->term_id;
        }

        if ( $dry_run ) {
            $out['rows'][] = [
                'line'    => $line,
                'title'   => $title,
                'message' => sprintf(
                    /* translators: 1: publication 2: category 3: date */
                    __( 'Would create — %1$s / %2$s / %3$s%4$s', 'dandysite-victoria' ),
                    $publication ?: '—',
                    $category ?: __( 'no category', 'dandysite-victoria' ),
                    $timestamp ? gmdate( 'M j, Y', $timestamp ) : __( 'today', 'dandysite-victoria' ),
                    $date_note
                ),
            ];
            $out['created']++;
            continue;
        }

        $post_id = wp_insert_post( [
            'post_type'     => 'post',
            'post_status'   => 'publish',
            'post_title'    => $title,
            'post_content'  => '',
            'post_excerpt'  => $blurb,
            'post_date'     => $post_date,
            'post_category' => $cat_ids,
        ], true );

        if ( is_wp_error( $post_id ) ) {
            $out['rows'][] = [ 'line' => $line, 'title' => $title, 'message' => $post_id->get_error_message() ];
            $out['errors']++;
            continue;
        }

        if ( $url )         update_post_meta( $post_id, 'dsp_external_url', esc_url_raw( $url ) );
        if ( $publication ) update_post_meta( $post_id, 'dsp_publication_name', sanitize_text_field( $publication ) );

        $out['rows'][] = [ 'line' => $line, 'title' => $title, 'message' => __( 'Created.', 'dandysite-victoria' ) . $date_note ];
        $out['created']++;
    }

    fclose( $fh );
    return $out;
}
