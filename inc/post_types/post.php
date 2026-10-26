<?php
if (!defined('ABSPATH')) {
    exit;
}

function theme_order_post_type($query)
{
    $post_types = array('post');
    $post_type = $query->get('post_type');

    if ($query->is_main_query() && in_array($post_type, $post_types, true) && !isset($_GET['orderby'])) {
        $query->set('orderby', 'date');
        $query->set('order', 'desc');
    }

    if (!is_admin() && $query->is_main_query() && $query->is_post_type_archive('post')) {
        $query->set('posts_per_page', 6);
    }

    return $query;
}
add_filter('pre_get_posts', 'theme_order_post_type');

function theme_remove_posts_menu()
{
    remove_menu_page('edit.php');
}
add_action('admin_menu', 'theme_remove_posts_menu');
