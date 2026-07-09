<?php
/**
 * Theme Activation
 * Runs once when Victoria is activated.
 * Seeds default categories if they don't already exist.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function dsp_theme_activate() {
    dsp_seed_categories();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'dsp_theme_activate' );

/**
 * Seed default post categories.
 * Safe to re-run — skips any category that already exists.
 */
function dsp_seed_categories() {
    $categories = [
        [
            'name'        => 'Announcements',
            'slug'        => 'announcements',
            'description' => 'Campaign news, press releases, and official statements.',
        ],
        [
            'name'        => 'Op-Ed',
            'slug'        => 'op-ed',
            'description' => 'Opinion pieces and editorials written by the candidate, published on this site or elsewhere.',
        ],
        [
            'name'        => 'In the News',
            'slug'        => 'in-the-news',
            'description' => 'Coverage of the candidate by external news outlets and publications.',
        ],
        [
            'name'        => 'Position Papers',
            'slug'        => 'position-papers',
            'description' => 'In-depth policy positions and issue papers.',
        ],
    ];

    foreach ( $categories as $cat ) {
        if ( ! term_exists( $cat['slug'], 'category' ) ) {
            wp_insert_term( $cat['name'], 'category', [
                'slug'        => $cat['slug'],
                'description' => $cat['description'],
            ] );
        }
    }
}
