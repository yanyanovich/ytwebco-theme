<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Nested tree of a menu location, shared by the desktop nav, the mobile menu and the footer.
 * Polylang swaps the menu assigned to a location per language, so this stays language-agnostic.
 *
 * Item description (Appearance → Menus → Screen Options → Description) is the subtitle
 * shown in the Services dropdown, e.g. "Stores, checkout, payments".
 *
 * @return array<int, array{id: int, title: string, url: string, description: string, target: string, classes: string[], current: bool, children: array}>
 */
function theme_get_menu_tree($location)
{
    static $cache = array();

    if (isset($cache[$location])) {
        return $cache[$location];
    }

    $locations = get_nav_menu_locations();

    if (empty($locations[$location])) {
        return $cache[$location] = array();
    }

    $items = wp_get_nav_menu_items($locations[$location], array('update_post_term_cache' => false));

    if (empty($items)) {
        return $cache[$location] = array();
    }

    // Sets current-menu-item / current-menu-ancestor classes like wp_nav_menu() does
    _wp_menu_item_classes_by_context($items);

    $by_parent = array();
    foreach ($items as $item) {
        $by_parent[(int)$item->menu_item_parent][] = $item;
    }

    $build = function ($parent_id) use (&$build, $by_parent) {
        $branch = array();

        foreach ($by_parent[$parent_id] ?? array() as $item) {
            $children = $build((int)$item->ID);

            // WP strips the #fragment before comparing URLs, so "/#process" would count as
            // "current" on the homepage. Section anchors are never the current page.
            $is_current = !empty($item->current) && strpos($item->url, '#') === false;
            $has_current_child = in_array(true, array_column($children, 'current'), true);

            $branch[] = array(
                'id' => (int)$item->ID,
                'title' => $item->title,
                'url' => $item->url,
                'description' => trim((string)$item->description),
                'target' => $item->target,
                'classes' => array_filter((array)$item->classes),
                'current' => $is_current || $has_current_child,
                'children' => $children,
            );
        }

        return $branch;
    };

    return $cache[$location] = $build(0);
}

/**
 * Zero-padded index used in menus: 01, 02…
 */
function theme_menu_index($index)
{
    return str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT);
}

/**
 * Link attributes for a menu item (target, rel, aria-current).
 */
function theme_menu_link_attrs(array $item)
{
    $attrs = ' href="' . esc_url($item['url']) . '"';

    if (!empty($item['target'])) {
        $attrs .= ' target="' . esc_attr($item['target']) . '" rel="noopener"';
    }

    if (!empty($item['current']) && empty($item['children'])) {
        $attrs .= ' aria-current="page"';
    }

    return $attrs;
}
