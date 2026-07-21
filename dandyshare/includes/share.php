<?php

defined( 'ABSPATH' ) || exit;

/**
 * Render a DandyShare button.
 *
 * @param array $args Optional overrides.
 */
function dandy_share( array $args = [] ) {

    wp_enqueue_style( 'dandy-share' );
    wp_enqueue_script( 'dandy-share' );

    $defaults = [
        'title' => get_the_title(),
        'text'  => wp_strip_all_tags( get_the_excerpt() ),
        'url'   => get_permalink(),
    ];

    $share = wp_parse_args( $args, $defaults );

    ?>

    <button
        class="dandy-share"
        hidden
        data-title="<?php echo esc_attr( $share['title'] ); ?>"
        data-text="<?php echo esc_attr( $share['text'] ); ?>"
        data-url="<?php echo esc_url( $share['url'] ); ?>"
        type="button"
    >

        <svg
            aria-hidden="true"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <circle cx="18" cy="5" r="3"/>
            <circle cx="6" cy="12" r="3"/>
            <circle cx="18" cy="19" r="3"/>
            <path d="M8.7 10.7l6.6-3.4M8.7 13.3l6.6 3.4"/>
        </svg>

        <span>Share</span>

    </button>

    <?php
}

add_shortcode(
    'dandy_share',
    function () {

        ob_start();

        dandy_share();

        return ob_get_clean();

    }
);