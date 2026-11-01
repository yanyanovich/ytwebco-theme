<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Case studies: /work/{slug}/ (UA: /uk/work/{slug}/), archive /work/.
 * case_type groups them for the Work filter (Online stores / Websites / Integrations & apps).
 */
function ytweb_theme_register_case_post_type()
{
    register_taxonomy('case_type', array('case'), array(
        'labels' => array(
            'name' => _x('Case types', 'taxonomy general name', 'ytweb_theme'),
            'singular_name' => _x('Case type', 'taxonomy singular name', 'ytweb_theme'),
            'search_items' => __('Search Case types', 'ytweb_theme'),
            'all_items' => __('All Case types', 'ytweb_theme'),
            'edit_item' => __('Edit Case type', 'ytweb_theme'),
            'update_item' => __('Update Case type', 'ytweb_theme'),
            'add_new_item' => __('Add new Case type', 'ytweb_theme'),
            'new_item_name' => __('New Case type', 'ytweb_theme'),
            'menu_name' => __('Case types', 'ytweb_theme'),
            'back_to_items' => __('Go to Case types', 'ytweb_theme'),
        ),
        'description' => '',
        // Filter chips on /work/ only, no thin term archives
        'public' => false,
        'publicly_queryable' => false,
        'show_in_nav_menus' => false,
        'show_ui' => true,
        'show_tagcloud' => false,
        'show_in_rest' => true,
        'hierarchical' => true,
        'rewrite' => false,
        'show_admin_column' => true,
    ));

    register_post_type('case', array(
        'menu_icon' => 'dashicons-portfolio',
        'label' => __('Cases', 'ytweb_theme'),
        'labels' => array(
            'name' => __('Cases', 'ytweb_theme'),
            'singular_name' => __('Case', 'ytweb_theme'),
            'add_new' => __('Add Case', 'ytweb_theme'),
            'add_new_item' => __('Add Case', 'ytweb_theme'),
            'edit_item' => __('Edit Case', 'ytweb_theme'),
            'new_item' => __('New Case', 'ytweb_theme'),
            'view_item' => __('View Case', 'ytweb_theme'),
            'search_items' => __('Find Case', 'ytweb_theme'),
            'not_found' => __('Cases not found', 'ytweb_theme'),
            'not_found_in_trash' => __('No Cases in trash', 'ytweb_theme'),
            'menu_name' => __('Cases', 'ytweb_theme'),
            'back_to_items' => __('Go to Cases', 'ytweb_theme'),
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'work',
            'with_front' => false,
            'pages' => false,
            'feeds' => false,
            'feed' => false,
        ),
        'capability_type' => 'post',
        'has_archive' => 'work',
        'hierarchical' => false,
        'menu_position' => 21,
        // Menu order sets the order of the Work grid
        'supports' => array('title', 'editor', 'thumbnail', 'page-attributes', 'revisions'),
    ));
}
add_action('init', 'ytweb_theme_register_case_post_type');

// Cases and their types have EN + UA versions
add_filter('pll_get_post_types', function ($post_types) {
    $post_types['case'] = 'case';

    return $post_types;
});

add_filter('pll_get_taxonomies', function ($taxonomies) {
    $taxonomies['case_type'] = 'case_type';

    return $taxonomies;
});

/**
 * Card data for the case grid (Home, Work archive, related cases).
 *
 * @return array{title: string, url: string, domain: string, excerpt: string, label: string, metric: string, stack: string[], brand: string, brand_2: string, image_id: int}
 */
function theme_get_case_card($post_id)
{
    $stack = array();
    foreach ((array)get_field('case_stack', $post_id) as $row) {
        if (!empty($row['name'])) {
            $stack[] = $row['name'];
        }
    }

    return array(
        'title' => get_the_title($post_id),
        'url' => get_permalink($post_id),
        'domain' => (string)get_field('case_domain', $post_id),
        'excerpt' => (string)get_field('case_excerpt', $post_id),
        'label' => theme_get_case_card_label($post_id),
        'metric' => (string)get_field('case_metric', $post_id),
        'stack' => $stack,
        'brand' => (string)get_field('case_brand_color', $post_id),
        'brand_2' => (string)get_field('case_brand_color_2', $post_id),
        'image_id' => (int)get_field('case_card_image', $post_id),
    );
}

/**
 * Short label for the Work archive card: "Card label", else the first part of the category
 * ("E-commerce · WooCommerce" → "E-commerce").
 */
function theme_get_case_card_label($post_id)
{
    $label = trim((string)get_field('case_card_label', $post_id));

    if ($label === '') {
        $parts = explode('·', (string)get_field('case_category', $post_id));
        $label = trim($parts[0]);
    }

    return $label;
}

/**
 * Cases in grid order (menu order), current language only (Polylang filters the query).
 *
 * @return int[]
 */
function theme_get_case_ids()
{
    static $ids = null;

    if ($ids === null) {
        $ids = get_posts(array(
            'post_type' => 'case',
            'posts_per_page' => -1,
            'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'),
            'fields' => 'ids',
            'no_found_rows' => true,
        ));
    }

    return $ids;
}

/**
 * Previous / next case for the case page; the list loops at both ends.
 *
 * @return array{prev: int, next: int}
 */
function theme_get_case_neighbours($post_id)
{
    $ids = theme_get_case_ids();
    $index = array_search((int)$post_id, $ids, true);

    if ($index === false || count($ids) < 2) {
        return array('prev' => 0, 'next' => 0);
    }

    $count = count($ids);

    return array(
        'prev' => $ids[($index - 1 + $count) % $count],
        'next' => $ids[($index + 1) % $count],
    );
}

// The Work archive renders every case on one page (the filter works without paging)
add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_post_type_archive('case')) {
        $query->set('posts_per_page', -1);
        $query->set('orderby', array('menu_order' => 'ASC', 'title' => 'ASC'));
    }
});

