<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_disable_comments_post_types_support()
{
    $post_types = get_post_types();

    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'theme_disable_comments_post_types_support');

function theme_disable_comments_status()
{
    return false;
}
add_filter('comments_open', 'theme_disable_comments_status', 20, 2);
add_filter('pings_open', 'theme_disable_comments_status', 20, 2);

function theme_disable_comments_hide_existing_comments($comments)
{
    return array();
}
add_filter('comments_array', 'theme_disable_comments_hide_existing_comments', 10, 2);

function theme_disable_comments_admin_menu()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'theme_disable_comments_admin_menu');

function theme_disable_comments_admin_menu_redirect()
{
    global $pagenow;

    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'theme_disable_comments_admin_menu_redirect');

function theme_disable_comments_admin_bar_render()
{
    global $wp_admin_bar;

    $wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'theme_disable_comments_admin_bar_render');
