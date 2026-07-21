<?php
/**
 * Plugin Name: DandyShare
 * Description: Lightweight native Web Share button for WordPress.
 * Version: 0.1.0
 * Author: Dandysite
 * License: GPL2+
 * Text Domain: dandy-share
 */

defined( 'ABSPATH' ) || exit;

define( 'DANDY_SHARE_VERSION', '0.2.0' );
define( 'DANDY_SHARE_URL', plugin_dir_url( __FILE__ ) );
define( 'DANDY_SHARE_PATH', plugin_dir_path( __FILE__ ) );

require_once DANDY_SHARE_PATH . 'includes/share.php';

add_action(
    'wp_enqueue_scripts',
    function () {
        wp_register_style(
            'dandy-share',
            DANDY_SHARE_URL . 'assets/dandy-share.css',
            [],
            filemtime( DANDY_SHARE_PATH . 'assets/dandy-share.css' )
        );

        wp_register_script(
            'dandy-share',
            DANDY_SHARE_URL . 'assets/dandy-share.js',
            [],
            filemtime( DANDY_SHARE_PATH . 'assets/dandy-share.js' ),
            true
        );
    }
);