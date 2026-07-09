<?php
/**
 * Post Meta: Article Details
 *
 * Adds optional fields to standard posts:
 *   - Publication Name  (shown as source label on cards and single post byline)
 *   - External URL      (where article was published or links to original)
 *   - PDF Attachment    (uploaded via media library — for archiving paywalled/print content)
 *
 * These fields are all optional. Posts without them behave as normal WP posts.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================
// META BOX REGISTRATION
// ============================================================

function dsp_register_article_meta_box() {
    add_meta_box(
        'dsp_article_details',
        __( 'Article Details', 'dandysite-victoria' ),
        'dsp_article_meta_box_callback',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'dsp_register_article_meta_box' );

// ============================================================
// META BOX CALLBACK
// ============================================================

function dsp_article_meta_box_callback( $post ) {
    wp_nonce_field( 'dsp_save_article_meta', 'dsp_article_nonce' );

    $publication = get_post_meta( $post->ID, 'dsp_publication_name', true );
    $ext_url     = get_post_meta( $post->ID, 'dsp_external_url', true );
    $pdf_id      = get_post_meta( $post->ID, 'dsp_pdf_attachment_id', true );
    $hide_date   = get_post_meta( $post->ID, 'dsp_hide_date', true );
    $pdf_url     = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';
    $pdf_title   = $pdf_id ? get_the_title( $pdf_id ) : '';
    ?>
    <p style="margin: 0 0 1em; padding: 8px 12px; background: #f0f6fc; border-left: 4px solid #457b9d; font-size: 13px;">
        <?php _e( 'All fields are optional. Use these for articles published externally, press coverage, or pieces you want to archive with a PDF.', 'dandysite-victoria' ); ?>
    </p>
    <table class="form-table">
        <tr>
            <th><label for="dsp_publication_name"><?php _e( 'Publication Name', 'dandysite-victoria' ); ?></label></th>
            <td>
                <input type="text" id="dsp_publication_name" name="dsp_publication_name"
                       value="<?php echo esc_attr( $publication ); ?>" class="regular-text"
                       placeholder="<?php esc_attr_e( 'e.g. Dallas Morning News, Texas Tribune', 'dandysite-victoria' ); ?>" />
                <p class="description"><?php _e( 'Shown as a source label on news cards and as a byline on the single post view. Use the Featured Image for the publication\'s logo.', 'dandysite-victoria' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dsp_external_url"><?php _e( 'External URL', 'dandysite-victoria' ); ?></label></th>
            <td>
                <input type="url" id="dsp_external_url" name="dsp_external_url"
                       value="<?php echo esc_attr( $ext_url ); ?>" class="regular-text"
                       placeholder="https://" />
                <p class="description">
                    <?php _e( 'Link to the original article. How this is used depends on the <strong>External Article Behavior</strong> setting in Theme Settings.', 'dandysite-victoria' ); ?>
                    <a href="<?php echo esc_url( admin_url( 'themes.php?page=dsp-theme-settings' ) ); ?>" target="_blank">
                        <?php _e( 'View setting →', 'dandysite-victoria' ); ?>
                    </a>
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="dsp_pdf_attachment_id"><?php _e( 'PDF Attachment', 'dandysite-victoria' ); ?></label></th>
            <td>
                <div id="dsp-pdf-wrap">
                    <?php if ( $pdf_id && $pdf_url ) : ?>
                    <p id="dsp-pdf-current">
                        <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank">
                            <?php echo esc_html( $pdf_title ?: basename( $pdf_url ) ); ?>
                        </a>
                        <button type="button" id="dsp-pdf-remove" class="button-link" style="margin-left:1em; color:#b32d2e;">
                            <?php _e( 'Remove', 'dandysite-victoria' ); ?>
                        </button>
                    </p>
                    <?php endif; ?>
                    <button type="button" id="dsp-pdf-upload" class="button">
                        <?php echo $pdf_id ? __( 'Replace PDF', 'dandysite-victoria' ) : __( 'Upload / Select PDF', 'dandysite-victoria' ); ?>
                    </button>
                </div>
                <input type="hidden" id="dsp_pdf_attachment_id" name="dsp_pdf_attachment_id"
                       value="<?php echo esc_attr( $pdf_id ); ?>" />
                <p class="description"><?php _e( 'Optional. Attach a PDF of the article — useful for paywalled or print content. Shown as a download link on the post.', 'dandysite-victoria' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><?php _e( 'Hide Date', 'dandysite-victoria' ); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="dsp_hide_date" value="1" <?php checked( $hide_date, '1' ); ?> />
                    <?php _e( 'Don\'t show the publish date on this post', 'dandysite-victoria' ); ?>
                </label>
                <p class="description"><?php _e( 'Useful for imported or external articles where the WordPress date is not meaningful.', 'dandysite-victoria' ); ?></p>
            </td>
        </tr>
    </table>

    <script>
    jQuery( function( $ ) {
        var frame;

        $( '#dsp-pdf-upload' ).on( 'click', function( e ) {
            e.preventDefault();
            if ( frame ) { frame.open(); return; }
            frame = wp.media({
                title:    '<?php echo esc_js( __( 'Select or Upload PDF', 'dandysite-victoria' ) ); ?>',
                button:   { text: '<?php echo esc_js( __( 'Use this PDF', 'dandysite-victoria' ) ); ?>' },
                library:  { type: 'application/pdf' },
                multiple: false
            });
            frame.on( 'select', function() {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                $( '#dsp_pdf_attachment_id' ).val( attachment.id );
                $( '#dsp-pdf-upload' ).text( '<?php echo esc_js( __( 'Replace PDF', 'dandysite-victoria' ) ); ?>' );
                if ( $( '#dsp-pdf-current' ).length ) {
                    $( '#dsp-pdf-current' ).html(
                        '<a href="' + attachment.url + '" target="_blank">' + ( attachment.title || attachment.filename ) + '</a>' +
                        ' <button type="button" id="dsp-pdf-remove" class="button-link" style="margin-left:1em;color:#b32d2e;"><?php echo esc_js( __( 'Remove', 'dandysite-victoria' ) ); ?></button>'
                    );
                } else {
                    $( '#dsp-pdf-wrap' ).prepend(
                        '<p id="dsp-pdf-current"><a href="' + attachment.url + '" target="_blank">' + ( attachment.title || attachment.filename ) + '</a>' +
                        ' <button type="button" id="dsp-pdf-remove" class="button-link" style="margin-left:1em;color:#b32d2e;"><?php echo esc_js( __( 'Remove', 'dandysite-victoria' ) ); ?></button></p>'
                    );
                }
                bindRemove();
            });
            frame.open();
        });

        function bindRemove() {
            $( '#dsp-pdf-remove' ).off( 'click' ).on( 'click', function() {
                $( '#dsp_pdf_attachment_id' ).val( '' );
                $( '#dsp-pdf-current' ).remove();
                $( '#dsp-pdf-upload' ).text( '<?php echo esc_js( __( 'Upload / Select PDF', 'dandysite-victoria' ) ); ?>' );
            });
        }
        bindRemove();
    });
    </script>
    <?php
}

// ============================================================
// SAVE META
// ============================================================

function dsp_save_article_meta( $post_id ) {
    if ( ! isset( $_POST['dsp_article_nonce'] ) ||
         ! wp_verify_nonce( $_POST['dsp_article_nonce'], 'dsp_save_article_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['dsp_publication_name'] ) ) {
        update_post_meta( $post_id, 'dsp_publication_name', sanitize_text_field( $_POST['dsp_publication_name'] ) );
    }
    if ( isset( $_POST['dsp_external_url'] ) ) {
        update_post_meta( $post_id, 'dsp_external_url', esc_url_raw( $_POST['dsp_external_url'] ) );
    }
    if ( isset( $_POST['dsp_pdf_attachment_id'] ) ) {
        update_post_meta( $post_id, 'dsp_pdf_attachment_id', absint( $_POST['dsp_pdf_attachment_id'] ) );
    }
    update_post_meta( $post_id, 'dsp_hide_date', isset( $_POST['dsp_hide_date'] ) ? '1' : '0' );
}
add_action( 'save_post_post', 'dsp_save_article_meta' );

// ============================================================
// SINGLE POST: PUBLICATION BYLINE
// Outputs publication name + external link below post title
// on single post view. Hooked into the_content filter so it
// works without modifying single.php.
// ============================================================

function dsp_article_publication_byline( $content ) {
    if ( ! is_single() || get_post_type() !== 'post' ) {
        return $content;
    }

    $publication = get_post_meta( get_the_ID(), 'dsp_publication_name', true );
    $ext_url     = get_post_meta( get_the_ID(), 'dsp_external_url', true );
    $pdf_id      = get_post_meta( get_the_ID(), 'dsp_pdf_attachment_id', true );

    if ( ! $publication && ! $ext_url && ! $pdf_id ) {
        return $content;
    }

    $byline = '<div class="article-publication-bar">';

    if ( $publication ) {
        $byline .= '<span class="article-publication-bar__source">';
        $byline .= esc_html( $publication );
        $byline .= '</span>';
    }

    if ( $ext_url ) {
        $byline .= '<a href="' . esc_url( $ext_url ) . '" class="article-publication-bar__link btn btn--outline" target="_blank" rel="noopener noreferrer">';
        $label   = $publication
            ? sprintf( __( 'Read at %s', 'dandysite-victoria' ), esc_html( $publication ) )
            : __( 'Read Original', 'dandysite-victoria' );
        $byline .= $label;
        $byline .= '</a>';
    }

    if ( $pdf_id ) {
        $pdf_url = wp_get_attachment_url( $pdf_id );
        if ( $pdf_url ) {
            $byline .= '<a href="' . esc_url( $pdf_url ) . '" class="article-publication-bar__pdf btn btn--outline" target="_blank" rel="noopener noreferrer">';
            $byline .= __( 'Download PDF', 'dandysite-victoria' );
            $byline .= '</a>';
        }
    }

    $byline .= '</div>';

    return $byline . $content;
}
add_filter( 'the_content', 'dsp_article_publication_byline' );
