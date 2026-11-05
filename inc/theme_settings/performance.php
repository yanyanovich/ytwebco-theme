<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Front-end cleanup: drop WP output the theme doesn't use (PageSpeed 95+ target).
 */
function theme_cleanup_head()
{
    // Emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('emoji_svg_url', '__return_false');

    // oEmbed discovery + host JS
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // Misc head links
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'theme_cleanup_head');

add_filter('tiny_mce_plugins', function ($plugins) {
    return is_array($plugins) ? array_diff($plugins, array('wpemoji')) : $plugins;
});

/**
 * Block-library CSS is already dequeued in theme_styles(); global styles from
 * theme.json are only needed in the editor, the front end has its own tokens.
 */
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_script('wp-embed');
}, 100);

/**
 * Inline CSS WordPress prints on its own: theme.json "global styles" (~14 KB, not used on the front end)
 * and the img auto-sizes fix. The theme ships its own tokens.
 */
add_action('init', function () {
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    remove_action('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles');
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
    remove_action('wp_head', 'wp_print_auto_sizes_contain_css_fix', 1);

    // Head links nobody reads: feeds (the site has none), REST discovery, canonical-less shortlink
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('template_redirect', 'rest_output_link_header', 11);
});
