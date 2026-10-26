<?php
if (!defined('ABSPATH')) {
    exit;
}

function ytweb_theme_register_example_post_type()
{
    register_taxonomy('example_taxonomy', array('example_post_type'), array(
        'labels' => array(
            'name' => _x('Categories', 'taxonomy general name', 'ytweb_theme'),
            'singular_name' => _x('Category', 'taxonomy singular name', 'ytweb_theme'),
            'search_items' => __('Search Categories', 'ytweb_theme'),
            'all_items' => __('All Categories', 'ytweb_theme'),
            'parent_item' => __('Parent Category', 'ytweb_theme'),
            'parent_item_colon' => __('Parent Category:', 'ytweb_theme'),
            'edit_item' => __('Edit Category', 'ytweb_theme'),
            'update_item' => __('Update Category', 'ytweb_theme'),
            'add_new_item' => __('Add new Category', 'ytweb_theme'),
            'new_item_name' => __('New Category', 'ytweb_theme'),
            'menu_name' => __('Categories', 'ytweb_theme'),
            'back_to_items' => __('Go to Categories', 'ytweb_theme'),
        ),
        'description' => '',
        'public' => true,
        'publicly_queryable' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'show_in_rest' => true,
        'hierarchical' => true,
        'rewrite' => array(
            'slug' => 'example_taxonomy',
            'hierarchical' => false,
            'with_front' => false,
            'feed' => false,
        ),
        'show_admin_column' => true,
    ));

    register_post_type('example_post_type', array(
        'menu_icon' => 'dashicons-admin-users',
        'label' => __('Example post', 'ytweb_theme'),
        'labels' => array(
            'name' => __('Example posts', 'ytweb_theme'),
            'singular_name' => __('Example post', 'ytweb_theme'),
            'add_new' => __('Add Post', 'ytweb_theme'),
            'add_new_item' => __('Add Post', 'ytweb_theme'),
            'edit_item' => __('Edit Post', 'ytweb_theme'),
            'new_item' => __('New Post', 'ytweb_theme'),
            'view_item' => __('View Post', 'ytweb_theme'),
            'search_items' => __('Find Post', 'ytweb_theme'),
            'not_found' => __('Posts not found', 'ytweb_theme'),
            'not_found_in_trash' => __('No Posts in trash', 'ytweb_theme'),
            'parent_item' => __('Parent Post', 'ytweb_theme'),
            'parent_item_colon' => __('Parent Post:', 'ytweb_theme'),
            'menu_name' => __('Example posts', 'ytweb_theme'),
            'back_to_items' => __('Go to Posts', 'ytweb_theme'),
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'example_post',
            'with_front' => false,
            'pages' => true,
            'feeds' => false,
            'feed' => false,
        ),
        'capability_type' => 'post',
        'has_archive' => 'example_post',
        'hierarchical' => true,
        'menu_position' => null,
        //'title', 'editor', 'comments', 'revisions', 'trackbacks', 'author', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', 'post-formats'
        'supports' => array('title', 'author', 'thumbnail', 'page-attributes', 'revisions'),
    ));
}
add_action('init', 'ytweb_theme_register_example_post_type');

function ytweb_theme_pre_get_posts_example_post_type($query)
{
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('example_post_type')) {
        return $query;
    }

    $query->set('orderby', 'date');
    $query->set('order', 'desc');
    $query->set('posts_per_page', 10);

    if (!empty($_GET['example_taxonomy'])) {
        $query->set('tax_query', array(
            array(
                'taxonomy' => 'example_taxonomy',
                'field' => 'slug',
                'terms' => array(sanitize_key($_GET['example_taxonomy'])),
            ),
        ));
    }

    return $query;
}
add_filter('pre_get_posts', 'ytweb_theme_pre_get_posts_example_post_type');
